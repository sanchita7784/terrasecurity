<?php 

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

use App\Auth;
use App\Model\CronLog;
use App\Model\Employee;
use HardeepVicky\QueryBuilder\Condition;

require_once './vendor/autoload.php';

require_once './app/include/functions.php';
require_once './app/include/DateUtility.php';
require_once './app/include/CsvUtility.php';
require_once './app/include/FileUtility.php';
require_once './app/include/Mysql.php';
require_once './app/include/Cache.php';
require_once './app/include/config.php';
require_once './app/include/Auth.php';
require_once './app/model/BaseModel.php';
require_once './app/model/User.php';
require_once './app/model/CronLog.php';
require_once './app/model/Employee.php';

$mysql = new Mysql(MYSQL_HOST, MYSQL_USER, MYSQL_PASS, MYSQL_DB);

$cronLogModel = new CronLog;

$cronLogModel->insert([
    "cron_name" => "Daily",
]);

$employee = new Employee();

$condition = Condition::init("AND")->add("terminate_date", date("Y-m-d"), "<=")->add("is_terminate", 0);
$records = $employee->find([], $condition);

foreach($records as $record)
{
    $employee->id = $record['id'];
    $employee->update(["is_terminate" => 1]);
}

$sql_log_path = "storage/files/CronLog";
FileUtility::createFolder($sql_log_path);
$sql_log_file = $sql_log_path . "/sql_" . $cronLogModel->id . ".sql";
$content = implode("\n", $mysql->logs);
file_put_contents($sql_log_file, $content);

echo 1;

$output = ob_get_contents(); 

$cronLogModel->update([
    "status" => $output == "1" ? 1 : 0,
    "sql_log_file" => $sql_log_file,
    "output" => $output
]);
