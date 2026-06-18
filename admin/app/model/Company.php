<?php
namespace App\Model;

use HardeepVicky\QueryBuilder\Condition;

require_once './app/model/Location.php';

class Company extends BaseModel
{    
    public Array $validations = [
        'name' => ['required', 'unique'],        
    ];

    public function beforeDelete($id)
    {
        $locationModel = new Location();

        $condition = new Condition("AND");
        $condition->add("company_id", $id);
        if ($locationModel->findCount($condition) > 0)
        {
            throw new \Exception("This record linked with locations");
        }

        return true;
    }
}
