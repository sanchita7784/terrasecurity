<?php
namespace App\Model;

class User extends BaseModel
{    
    public Array $validations = [
        'username' => ['required', 'unique'],
        'password' => ['required', 'confirm_password'],
        'name' => ['required'],
        'email' => ['required', 'unique'],
        'mobile' => ['required'],
    ];

    public function beforeInsert(&$data)
    {
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        return parent::beforeInsert($data);
    }

    public function beforeUpdate(&$data)
    {
        if (isset($data['password']))
        {
            $info = password_get_info($data['password']);
            

            if (!$info['algo'])
            {
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);                
            }
        }

        return parent::beforeUpdate($data);
    }

    public function role(&$records)
    {
        $role = new Role;

        $id_list = array_unique(array_column($records, "role_id"));

        if (empty($id_list))
        {
            return;    
        }

        $q = "SELECT id,name from " . $role->getTable() . " where id in (" . implode(",", $id_list) . ")";

        $role_records = $role->mysql->select($q);

        foreach($records as $k => $record)
        {
            foreach($role_records as $role_record)
            {
                if ($record['role_id'] == $role_record['id'])    
                {
                    $records[$k]['role'] = $role_record;
                }
            }    
        }
    }
}