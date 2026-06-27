<?php
namespace App\Model;

require_once './app/model/Income.php';

class Invoice extends BaseModel
{    
    public function beforeDelete($id)
    {
        $record = $this->find([], ["id" => $id]);
        $record = $record[0];

        if ($record['is_paid'])
        {
            throw new \Exception("Payment Done Invoices can not be deleted");
        }

        $income = new Income();
        $income_records = $income->find(["id"], ["invoice_id" => $id]);

        foreach($income_records as $income_record)
        {
            $income->delete($income_record['id']);
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

        $q = "SELECT * from " . $relation_model->getTable() . " where id in (" . implode(",", $id_list) . ")";

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

}
