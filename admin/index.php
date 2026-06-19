<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Auth;
require_once './vendor/autoload.php';

require_once './app/include/functions.php';
require_once './app/include/GitUtility.php';
require_once './app/include/DateUtility.php';
require_once './app/include/CsvUtility.php';
require_once './app/include/FileUtility.php';
require_once './app/include/Mysql.php';
require_once './app/include/Session.php';
require_once './app/include/Cache.php';
require_once './app/include/config.php';
require_once './app/include/Auth.php';
require_once './app/model/BaseModel.php';
require_once './app/model/Role.php';
require_once './app/model/User.php';
require_once './app/model/Permission.php';
require_once './app/include/Form.php';

$mysql = new Mysql(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);

$resource = $_GET['r'] ?? 'dashboard';

date_default_timezone_set('Asia/kolkata');

$auth = new Auth();

if (!in_array($resource, $auth->defaultPageAllow))
{
    $auth->check();

    if (isset($_GET['r']) && !$auth->isPageAllowed())
    {
        require_once "./app/resource/401.php";
        exit;
    }
}

require_once "./app/resource/$resource.php";