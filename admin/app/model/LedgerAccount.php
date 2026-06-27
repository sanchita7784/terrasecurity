<?php
namespace App\Model;

use HardeepVicky\QueryBuilder\Condition;
use Override;

class LedgerAccount extends BaseModel
{    
    public Array $validations = [
        'company_id' => ['required', 'unique'],        
    ];

    #[Override]
    public function findList($key = "id", $value = "name", $conditions = [], $order_by = "id", $order_dir = "ASC")
    {
        $q = "SELECT 
                LA.id, C.name 
            FROM 
	            ledger_account LA
                INNER JOIN company C on C.id = LA.company_id
            ";
        if ($conditions)
        {
            $condition = Condition::init("AND")->addList($conditions);

            $q .= " WHERE " . $condition->get();
        }

        $records = $this->mysql->select($q);

        $list = [];
        foreach($records as $record)
        {
            $list[$record[$key]] = $record["name"];
        }

        return $list;
    }
}
