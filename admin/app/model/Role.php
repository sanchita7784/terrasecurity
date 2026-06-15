<?php
namespace App\Model;

require_once './app/model/User.php';

use Exception;

class Role extends BaseModel
{    
    public Array $validations = [
        'name' => ['required', 'unique'],
    ];

    public function create_by(&$records)
    {
        $user = new User;

        $user_id_list = array_unique(array_column($records, "created_by"));

        if (empty($user_id_list))
        {
            return true;
        }

        $q = "SELECT id,name from " . $user->getTable() . " where id in (" . implode(",", $user_id_list) . ")";

        $user_records = $user->mysql->select($q);

        foreach($records as $k => $record)
        {
            foreach($user_records as $user_record)
            {
                if ($record['created_by'] == $user_record['id'])    
                {
                    $records[$k]['created_by'] = $user_record;
                }
            }    
        }
    }
}
