<?php
namespace App\Model;

require_once './app/model/LedgerAccount.php';

use Override;

class LedgerTransaction extends BaseModel
{    
    #[Override]
    public function afterSave($data)
    {
        $LedgerAccount = new LedgerAccount();

        $la = $LedgerAccount->find([], ["id" => $data['legder_account_id']]);

        $la = $la[0];

        $la['balance'] += $data['amount'];

        $LedgerAccount->id = $la['id'];
        $LedgerAccount->update(["balance" => $la['balance']]);

        return parent::afterSave($data);
    }
}
