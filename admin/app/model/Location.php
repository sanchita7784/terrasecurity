<?php
namespace App\Model;

require_once './app/model/States.php';
require_once './app/model/City.php';

class Location extends BaseModel
{    
    public Array $validations = [
        'name' => ['required', 'unique'],        
    ];

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
