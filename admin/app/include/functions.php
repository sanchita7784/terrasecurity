<?php

function debug($data)
{
    $bt = debug_backtrace();
    $caller = array_shift($bt);
    
    echo "<pre>";
    echo "<b>" . $caller["file"] . " : " . $caller["line"] . "</b><br/>";
    print_r($data);
    echo "</pre>";
}

/**
 * return array to where sql string
 * @param type $conditions
 * @return string
 */
function get_where($conditions)
{
    $where = array();
    
    $raw_where = '';
    
    foreach($conditions as $operator => $data)
    {
        foreach($data as $arr)
        {
            if (isset($arr["field"]) && isset($arr["value"]))
            {
                $arr["op"] = isset($arr["op"]) ? $arr["op"] : "=";
                
                $where[] = $arr["field"] . " " . $arr["op"] . " '" . $arr["value"] . "'";
            }
            else
            {
                $where[] = get_where($arr);
            }            
        }
        
        $raw_where .= "(" . implode(" $operator ",  $where) . ")";
    }
    
    return $raw_where;
}

function str_contain($str, $needle, $start = false, $end = false)
{
    $str = strtolower(trim($str));
    $needle = strtolower(trim($needle));
    
    if ($start !== false)
    {
        $str = substr($str, $start);
    }
    
    if ($end !== false)
    {
        $str = substr($str, 0, $end);
    }
    
    return strpos($str, $needle) !== false;
}

function get_DDL_query_type($query, $db, $db_required)
{
    $query = strtoupper($query);
    
    if (!$db_required)
    {
        $find = "CREATE TABLE";
        if (str_contain($query, $find, 0, strlen($find)))
        {
            return "TABLE";
        }
    }
    
    $find = "CREATE TABLE";
    $find = $db ? $find . " $db." : $find;
    if (str_contain($query, $find, 0, strlen($find)))
    {
        return "TABLE";
    }
    
    if (!$db_required)
    {
        $find = "ALTER TABLE";
        if (str_contain($query, $find, 0, strlen($find)))
        {
            return "TABLE";
        }
    }
    
    $find = "ALTER TABLE";
    $find = $db ? $find . " $db." : $find;
    if (str_contain($query, $find, 0, strlen($find)))
    {
        return "TABLE";
    }
    
    if (!$db_required)
    {
        $find = "DROP TABLE";
        if (str_contain($query, $find, 0, strlen($find)))
        {
            return "TABLE";
        }
    }
    
    $find = "DROP TABLE";
    $find = $db ? $find . " $db." : $find;
    if (str_contain($query, $find, 0, strlen($find)))
    {
        return "TABLE";
    }
    
    $q = substr($query, 0, strpos($query, "AS"));
    if (str_contain($q, "VIEW"))
    {
        $result = "";
        
        if (str_contain($q, "CREATE OR REPLACE", 0, strlen('CREATE OR REPLACE')))
        {
            $result = "CREATE OR REPLACE";
        }
        else if (str_contain($q, "CREATE VIEW", 0, strlen('CREATE VIEW')))
        {
            $result = "CREATE VIEW";
        }
        else if (str_contain($q, "ALTER VIEW", 0, strlen('ALTER VIEW')))
        {
            $result = "ALTER VIEW";
        }

        if ($result)
        {
            if ($db_required)
            {
                if (str_contain($q, "$db."))
                {
                    return $result;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return $result;
            }
        }
    }
    
    if (str_contain($query, "DROP VIEW", 0, strlen('DROP VIEW')))
    {
        if ($db_required)
        {
            if (str_contain($query, "$db."))
            {
                return "DROP VIEW";
            }
            else
            {
                return false;
            }
        }
        else
        {
            return "DROP VIEW";
        }
    }
    
    $q = substr($query, 0, strpos($query, "BEGIN"));
    if (str_contain($q, "PROCEDURE"))
    {
        $result = "";
        
        if (str_contain($q, "CREATE", 0, strlen('CREATE')))
        {
            $result = "CREATE PROCEDURE";
        }
        
        if (str_contain($q, "ALTER", 0, strlen('ALTER')))
        {
            $result = "ALTER PROCEDURE";
        }
        
        if ($result)
        {
            if ($db_required)
            {
                if (str_contain($q, "$db."))
                {
                    return $result;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return $result;
            }
        }
    }
    
    if (str_contain($query, "DROP PROCEDURE", 0, strlen('DROP PROCEDURE')))
    {
        if ($db_required)
        {
            if (str_contain($query, "$db."))
            {
                return "DROP PROCEDURE";
            }
            else
            {
                return false;
            }
        }
        else
        {
            return "DROP PROCEDURE";
        }
    }
    
    $q = substr($query, 0, strpos($query, "RETURNS"));
    if (str_contain($q, "FUNCTION"))
    {
        $result = "";
        
        if (str_contain($q, "CREATE FUNCTION", 0, strlen('CREATE FUNCTION')))
        {
            $result = "CREATE FUNCTION";
        }
        
        if (str_contain($q, "ALTER FUNCTION", 0, strlen('ALTER FUNCTION')))
        {
            $result = "ALTER FUNCTION";
        }
        
        if ($result)
        {
            if ($db_required)
            {
                if (str_contain($q, "$db."))
                {
                    return $result;
                }
                else
                {
                    return false;
                }
            }
            else
            {
                return $result;
            }
        }
    }
    
    if (str_contain($query, "DROP FUNCTION", 0, strlen('DROP FUNCTION')))
    {
        if ($db_required)
        {
            if (str_contain($query, "$db."))
            {
                return "DROP FUNCTION";
            }
            else
            {
                return false;
            }
        }
        else
        {
            return "DROP FUNCTION";
        }
    }
    
    return false;
}

function get_DML_query_type($query, $table, $db, $db_required)
{
    $query = strtoupper($query);
    
    if (!$db_required)
    {
        $find = "INSERT INTO $table";    
        if (str_contain($query, $find, 0, strlen($find)))
        {
            return "INSERT";
        }
    }
    
    $find = "INSERT INTO $db.$table";
    if (str_contain($query, $find, 0, strlen($find)))
    {
        return "INSERT";
    }
    
    if (!$db_required)
    {
        $find = "UPDATE $table";    
        if (str_contain($query, $find, 0, strlen($find)))
        {
            return "UPDATE";
        }
    }
    
    $find = "UPDATE $db.$table";
    if (str_contain($query, $find, 0, strlen($find)))
    {
        return "UPDATE";
    }
    
    if (!$db_required)
    {
        $find = "DELETE FROM $table";    
        if (str_contain($query, $find, 0, strlen($find)))
        {
            return "DELETE";
        }
    }
    
    $find = "DELETE FROM $db.$table";
    if (str_contain($query, $find, 0, strlen($find)))
    {
        return "DELETE";
    }
    
    if (!$db_required)
    {
        $find = "TRUNCATE TABLE $table";    
        if (str_contain($query, $find, 0, strlen($find)))
        {
            return "TRUNCATE";
        }
    }
    
    $find = "TRUNCATE TABLE $db.$table";
    if (str_contain($query, $find, 0, strlen($find)))
    {
        return "TRUNCATE";
    }
    
    return false;
}

function git_version($path)
{
    $stringfromfile = file("$path/.git/HEAD");

    $firstLine = $stringfromfile[0]; //get the string from the array

    $explodedstring = explode("/", $firstLine, 3); //seperate out by the "/" in the string

    $branchname = $explodedstring[2]; //get the one that is always the branch name
    
    return strtolower(trim($branchname));
}

function get_name_from_ddl_sql($query, $ddl_type)
{
    $find = $ddl_type;
    $name = "";
    
    switch($ddl_type)
    {
        case "CREATE PROCEDURE":
            if (str_contain($query, $find, 0, strlen($find)))
            {
                $name = substr($query, strlen($find));
                $name = substr($name, 0, strpos($name, "("));
            }
            break;
        case "DROP PROCEDURE":
            if (str_contain($query, $find, 0, strlen($find)))
            {
                $name = substr($query, strlen($find));
            }
            break;
            
        case "CREATE FUNCTION":
            if (str_contain($query, $find, 0, strlen($find)))
            {
                $name = substr($query, strlen($find));
                $name = substr($name, 0, strpos($name, "("));
            }
            break;
        case "DROP FUNCTION":
            if (str_contain($query, $find, 0, strlen($find)))
            {
                $name = substr($query, strlen($find));
            }
            break;
            
        case "CREATE VIEW":
            if (str_contain($query, $find, 0, strlen($find)))
            {
                $name = substr($query, strlen($find));
                $name = substr($name, 0, strpos($name, "AS"));
            }
            break;
        case "CREATE OR REPLACE":
            if (str_contain($query, $find, 0, strlen($find)))
            {
                $name = substr($query, strlen($find));
                $name = substr($name, 0, strpos($name, "AS"));
            }
            break;
        case "DROP VIEW":
            if (str_contain($query, $find, 0, strlen($find)))
            {
                $name = substr($query, strlen($find));
            }
            break;
    }
    
    return strtolower(trim($name));
}

function d($arg)
{
    $callBy = debug_backtrace()[0];

    echo "<pre>";
    echo "<b>" . $callBy['file'] . "</b> At Line : " . $callBy['line'];
    echo "<br/>";

    if (is_string($arg)) {
        echo htmlspecialchars($arg);
    } else if (is_bool($arg)) {
        echo $arg ? "True" : "False";
    } else if (is_null($arg)) {
        echo "NULL";
    } else {
        print_r($arg);
    }

    echo "</pre>"; 
}

function url($resource, array $query_params = [])
{
    $q = BASE_URL . "index.php?";

    $query_params = array_merge($_GET, $query_params);

    $query_params['r'] = $resource;

    http_build_query($query_params);

    return $q . http_build_query($query_params);
}

function url_without_query_params($resource)
{
    return BASE_URL . "index.php?r=" . $resource;
}

function redirect($resource)
{
    header("Location:" . url($resource));
    exit;
}

function is_user_login()
{
    if (isset($_SESSION['auth_user']['id']))
    {
        return true;
    }

    redirect("login");
}


function download_start($file, $content_type, $delete_after_download = false)
{
    header('Content-Description: File Transfer');
    header("Content-Type: $content_type");
    header('Content-Disposition: attachment; filename="' . basename($file) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . @filesize($file));
    readfile($file);
    if ($delete_after_download) {
        unlink($file);
    }
    exit;
}

function curl_get_request($url, $headers = [])
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $res = curl_exec($ch);
    curl_close($ch);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }

    return $res;
}

function curl_post_request($url, $params, $headers = [])
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $res = curl_exec($ch);

    if (curl_errno($ch)) {
        throw new Exception(curl_error($ch));
    }

    curl_close($ch);

    return $res;
}

function str_class_name_without_namespace($class_name)
{
    if (is_object($class_name))
    {
        $class_name = get_class($class_name);
    }

    if (is_string($class_name) && strpos($class_name, "\\") >= 0) {
        $arr = explode("\\", $class_name);

        if ($arr) {
            $class_name = end($arr);

            return $class_name;
        }
    }

    return null;
}

function str_space_before_every_capital_letter($str)
{
    return trim(preg_replace('/(?<!\ )[A-Z]/', ' $0', $str));
}

function str_class_name_to_human_text($class_name)
{
    $str = str_class_name_without_namespace($class_name);

    $str = str_space_before_every_capital_letter($str);

    return $str;
}

function str_function_name_to_human_text($class_name)
{
    $str = str_class_name_without_namespace($class_name);

    $str = str_space_before_every_capital_letter($str);

    $str = str_replace("_", " ", $str);

    return $str;
}

function pagination_links($total_pages, $page)
{
    $html  = "";
    $html .= '<ul class="pagination">';

    $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . url($_GET['r'], ["page" => 1]) .'" tabindex="-1"><</a>';
    $html .= '</li>';

    if ($page > 1)
    {
        $html .= '<li class="page-item">';
            $html .= '<a class="page-link" href="' . url($_GET['r'], ["page" => $page - 1]) .'" tabindex="-1">Prev</a>';
        $html .= '</li>';
    }
    else
    {
        $html .= '<li class="page-item disabled">';
            $html .= '<a class="page-link" href="#" tabindex="-1">Prev</a>';
        $html .= '</li>';
    }

    $start = $page - 1;
    $end = $page + 1;

    if ($start <= 0)
    {
        $start +=1;
        $end +=1;
    }

    if ($end > $total_pages)
    {
        $end = $total_pages;   
    }

    for ($i = $start; $i <= $end; $i++)
    {
        if ($page == $i)
        {
            $html .= '<li class="page-item disabled">';
                $html .= '<a class="page-link" href="#" tabindex="-1">' . $i . '</a>';
            $html .= '</li>';
        }
        else
        {
            $html .= '<li class="page-item">';
                $html .= '<a class="page-link" href="' . url($_GET['r'], ["page" => $i]) .'">' . $i . '</a>';
            $html .= '</li>';
        }
    }

    if ($page < $total_pages)
    {
        $html .= '<li class="page-item">';
            $html .= '<a class="page-link" href="' . url($_GET['r'], ["page" => $page + 1]) .'" tabindex="-1">Next</a>';
        $html .= '</li>';
    }
    else
    {
        $html .= '<li class="page-item disabled">';
            $html .= '<a class="page-link" href="#" tabindex="-1">Next</a>';
        $html .= '</li>';
    }

    $html .= '<li class="page-item">';
        $html .= '<a class="page-link" href="' . url($_GET['r'], ["page" => $total_pages]) .'" tabindex="-1">></a>';
    $html .= '</li>';

    $html .= '</ul>';

    return $html;
}

function sortable_link($field, $label)
{
    $order_dir = $_GET['order_dir'] ?? "ASC";

    if ($order_dir == "DESC")    
    {
        $order_dir = 'ASC';
    }
    else
    {
        $order_dir = 'DESC';
    }

    $url = url($_GET['r'], ["order_by" => $field, "order_dir" => $order_dir]);

    if (($_GET['order_by'] ?? '') == $field)
    {
        $cls = $order_dir == "DESC" ? 'fas fa-sort-alpha-down' : 'fas fa-sort-alpha-up-alt';
    }   
    else
    {
        $cls = 'fas fa-sort-alpha-down';
    }

    $label .= ' <i class="' . $cls . '"></i>';

    $html = '<a href="' . $url . '">' . $label . '</a>';

    return $html;
}

function camelToSnake($input) {
    return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
}

function deleteDir($dirPath) {
    if (!is_dir($dirPath)) {
        return false;
    }

    // Ensure path ends with a slash
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }

    // Get all files and folders, excluding . and ..
    $files = glob($dirPath . '*', GLOB_MARK);

    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }

    return rmdir($dirPath);
}

function formatToMillion($number) {
    if ($number >= 1000000) {
        // Divide by 1 million and round to 1 decimal place
        return round($number / 1000000, 1) . 'M';
    }
    return number_format($number); // Returns regular formatted number if < 1M
}

function view($path, $data = []) {
    // 1. Convert "user.profile" dot notation to "user/profile.php"
    $file = $path . '.php';

    if (!file_get_contents($file)) {
        die("View file not found: $file");
    }

    // 2. Extract data array into variables
    // ['name' => 'Alex'] becomes $name = 'Alex'
    extract($data);

    // 3. Start Output Buffering
    ob_start();
    
    include $file;
    
    // 4. Return the rendered content
    return ob_get_clean();
}