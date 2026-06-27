<?php
namespace App\Model;

use HardeepVicky\QueryBuilder\Condition;

require_once './app/model/States.php';
require_once './app/model/City.php';
require_once './app/model/Attendance.php';
require_once './app/model/Company.php';
require_once './app/model/EmployeeWorkHistory.php';

class Employee extends BaseModel
{
    public Array $validations = [
        'name' => ['required'],
        'mobile' => ['required', 'unique'],
        'address' => ['required'],
        'state_id' => ['required'], 
        'city_id' => ['required'], 
        'doj' => ['required'], 
        'salary' => ['required'], 
    ];

    public $date_fields = [
        "doj" => "d-M-Y",
        "terminate_date" => "d-M-Y",
        "rejoin_date" => "d-M-Y"
    ];

    const PAYMENT_MODE_CASH = 1;
    const PAYMENT_MODE_CHEQUE = 2;
    const PAYMENT_MODE_BANK_TRANSFER = 3;

    CONST PAYMENT_MODE_LIST = [
        self::PAYMENT_MODE_CASH => "Cash",
        self::PAYMENT_MODE_CHEQUE => "Cheque",
        self::PAYMENT_MODE_BANK_TRANSFER => "Bank Transfer", 
    ];

    const TYPE_SECURITY_GAURD = 1;
    const TYPE_LADY_SECURITY_GAURD = 2;
    const TYPE_BOUNCER = 3;
    
    const TYPE_LIST = [
        self::TYPE_SECURITY_GAURD => "Security Gaurd",
        self::TYPE_LADY_SECURITY_GAURD => "Lady Security Gaurd",
        self::TYPE_BOUNCER => "Bouncer",
    ];

    public function beforeDelete($id)
    {
        $userModel = new Attendance();

        $condition = new Condition("AND");
        $condition->add("employee_id", $id);
        if ($userModel->findCount($condition) > 0)
        {
            throw new \Exception("This record linked with Attendance");
        }

        return true;
    }

    public function state_id(&$records)
    {
        $relation_model = new States();

        $id_list = array_unique(array_column($records, "state_id"));

        if (empty($id_list))
        {
            return true;
        }

        $q = "SELECT id,name from " . $relation_model->getTable() . " where id in (" . implode(",", $id_list) . ")";

        $rel_records = $relation_model->mysql->select($q);

        foreach($records as $k => $record)
        {
            foreach($rel_records as $rel_record)
            {
                if ($record['state_id'] == $rel_record['id'])
                {
                    $records[$k]['state_id'] = $rel_record;
                }
            }
        }
    }

    public function city_id(&$records)
    {
        $relation_model = new City();

        $id_list = array_unique(array_column($records, "city_id"));

        if (empty($id_list))
        {
            return true;
        }

        $q = "SELECT id,city from " . $relation_model->getTable() . " where id in (" . implode(",", $id_list) . ")";

        $rel_records = $relation_model->mysql->select($q);

        foreach($records as $k => $record)
        {
            foreach($rel_records as $rel_record)
            {
                if ($record['city_id'] == $rel_record['id'])
                {
                    $records[$k]['city_id'] = $rel_record;
                }
            }
        }
    }


    public function company_id(&$records)
    {
        $relation_model = new Company();

        $id_list = array_unique(array_column($records, "company_id"));

        foreach($id_list as $k => $v)        
        {
            if (!$v)
            {
                unset($id_list[$k]);
            }
        }

        if (empty($id_list))
        {
            return true;
        }

        $q = "SELECT id,name from " . $relation_model->getTable() . " where id in (" . implode(",", $id_list) . ")";

        $rel_records = $relation_model->mysql->select($q);

        foreach($records as $k => $record)
        {
            foreach($rel_records as $rel_record)
            {
                if ($record['company_id'] == $rel_record['id'])
                {
                    $records[$k]['company_id'] = $rel_record;
                }
            }
        }
    }


    public function EmployeeWorkHistory(&$records)
    {
        $relation_model = new EmployeeWorkHistory();

        $id_list = array_unique(array_column($records, "id"));

        if (empty($id_list))
        {
            return true;
        }

        $q = "SELECT * from " . $relation_model->getTable() . " where employee_id in (" . implode(",", $id_list) . ")";

        $rel_records = $relation_model->mysql->select($q);
        $relation_model->dateFields($rel_records);

        foreach($records as $k => $record)
        {
            foreach($rel_records as $rel_record)
            {
                if ($record['id'] == $rel_record['employee_id'])
                {
                    $records[$k]['EmployeeWorkHistory'][] = $rel_record;
                }
            }
        }
    }
}
