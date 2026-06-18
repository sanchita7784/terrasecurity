<?php
namespace App\Model;

use HardeepVicky\QueryBuilder\Condition;

require_once './app/model/States.php';
require_once './app/model/City.php';
require_once './app/model/Company.php';
require_once './app/model/Attendance.php';

class Location extends BaseModel
{    
    public Array $validations = [
        'name' => ['required', 'unique'],        
    ];

    public function beforeDelete($id)
    {
        $userModel = new User();

        $condition = new Condition("AND");
        $condition->add("location_id", $id);
        if ($userModel->findCount($condition) > 0)
        {
            throw new \Exception("This record linked with users");
        }

        $userModel = new Attendance();

        $condition = new Condition("AND");
        $condition->add("location_id", $id);
        if ($userModel->findCount($condition) > 0)
        {
            throw new \Exception("This record linked with attendance");
        }

        return true;
    }

    public function company_id(&$records)
    {
        $relation_model = new Company();

        $id_list = array_unique(array_column($records, "company_id"));

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
    
}
