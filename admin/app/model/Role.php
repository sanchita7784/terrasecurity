<?php
namespace App\Model;

use HardeepVicky\QueryBuilder\Condition;

class Role extends BaseModel
{    
    public Array $validations = [
        'name' => ['required', 'unique'],
    ];

    public function beforeDelete($id)
    {
        $userModel = new User();

        $condition = new Condition("AND");
        $condition->add("role_id", $id);
        if ($userModel->findCount($condition) > 0)
        {
            return false;
        }

        return true;
    }
}
