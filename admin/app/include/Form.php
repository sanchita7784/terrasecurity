<?php
namespace App;

use App\Model\BaseModel;

class Form
{
    private $db_data, $data;
    public function __construct(private BaseModel $model)
    {
        if (isset($_GET['id']))
        {
            $records = $this->model->find([], ["id" => $_GET['id']]);

            $this->db_data = $records[0];            
        }

        if (!empty($_POST['form_data']))
        {
            $this->data = $_POST['form_data'];
        }

        if (!empty($_GET['form_data']))
        {
            $this->data = $_GET['form_data'];
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'GET' && $this->db_data)
        {
            $this->data = $this->db_data;
        }        
    }

    public function label(String $field, Array $options = [])
    {
        $html = "<label";

        if (!isset($options['class']))
        {
            $options['class'] = "form-label";
        }

        foreach($options as $attr => $value)
        {
            $html .= ' ' . $attr . '="' . $value . '" ';
        }

        $html .= ">";
        $html .= ucwords(str_function_name_to_human_text($field));
        $html .= "</label>";

        return $html;
    }

    public function input(String $field, Array $options)
    {
        $type = $options['type'] ?? "text";

        $html = "";

        switch ($type)
        {
            case "text":
            case "password":
            case "email":
            case "hidden":
                $html = $this->textField($field, $options);
                break;

            case "select":
                $html = $this->selectField($field, $options);
                break;

            case "checkbox":
                $html = $this->checkboxField($field, $options);
                break;
        }

        $html .= $this->errorHtml($field);

        return $html;
    }

    public function getInputName(String $field)
    {
        if (strpos($field, "["))
        {
            return $field;
        }
        else
        {
            return 'form_data[' . $field . ']';
        }
    }

    private function textField(String $field, Array $options)
    {
        $html = '<input name="' . $this->getInputName($field) . '"';

        if (isset($this->data[$field]))
        {
            $options['value'] = $this->data[$field];
        }

        foreach($options as $attr => $value)
        {
            $html .= " " .$attr . '="' . $value . '" ';
        }

        $html .= "/>";

        return $html;
    }

    private function selectField(String $field, Array $options)
    {
        $html = '<select name="' . $this->getInputName($field) . '"';

        $list = $options['list'] ?? [];

        unset($options['list']);


        foreach($options as $attr => $value)
        {
            $html .= " " .$attr . '="' . $value . '" ';
        }

        $html .= ">";

        $options['empty'] = $options['empty'] ?? false;

        if ($options['empty'])
        {
            if (is_bool($options['empty']))
            {
                $list = ["" => "Please Select"] + $list;
            }
            else if (is_string($options['empty']))
            {
                $list = ["" => $options['empty']] + $list;
            }
        }

        foreach($list as $key => $text)
        {
            $attr = "";
            if (isset($this->data))
            {
                if ($key == $this->data[$field])
                {
                    $attr = ' selected="selected"';
                }
            }
            
            $html .= '<option value="' . $key . '"'. $attr .'>' . $text . '</option>';
        }

        $html .= "</select>";

        return $html;
    }

    public function checkboxField(String $field, Array $options = [])
    {
        $html = '<input name="' . $this->getInputName($field) . '"';

        if (isset($this->data[$field]) && $this->data[$field])
        {
            $options['checked'] = true;
        }

        foreach($options as $attr => $value)
        {
            $html .= " " . $attr . '="' . $value . '" ';
        }

        $html .= "/>";

        return $html;
    }

    public function errorHtml(String $field)
    {
        $html = "";
        if (isset($this->model->validationErrors[$field]))
        {
            foreach($this->model->validationErrors[$field] as $msg)
            {
                $html .= '<label class="error">' . $msg . "</label>";
            }
        }

        return $html;
    }
}
