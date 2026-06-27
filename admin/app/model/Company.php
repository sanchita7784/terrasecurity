<?php
namespace App\Model;


use HardeepVicky\QueryBuilder\Condition;
use Override;

require_once './app/model/Location.php';
require_once './app/model/LedgerAccount.php';
require_once './app/model/LedgerTransaction.php';
require_once './app/model/States.php';

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

        $LedgerAccount = new LedgerAccount;
        $record = $LedgerAccount->find([], ["company_id" => $id]);

        if ($record)
        {
            $record = $record[0];

            $LedgerTransaction = new LedgerTransaction;

            $count = $LedgerTransaction->findCount(Condition::init("AND")->add("legder_account_id", $record['id']));

            if ($count > 0)
            {
                throw new \Exception("This record linked with Ledger Account and Transactions");
            }
            else
            {
                $LedgerAccount->delete($record['id']);
            }
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
}
