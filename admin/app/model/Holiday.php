<?php
namespace App\Model;

require_once './app/model/HolidayDetail.php';

use HardeepVicky\QueryBuilder\Condition;

class Holiday extends BaseModel
{
    public Array $validations = [
        'name' => ['required', 'unique'],
    ];

    public function beforeDelete($id)
    {
        $holidayDetail = new HolidayDetail();

        $records = $holidayDetail->find([], ["holiday_id" => $id]);

        foreach($records as $record)
        {
            $holidayDetail->delete($record['id']);
        }

        return true;
    }

    public function holidayDetail(&$records)
    {
        $relation_model = new HolidayDetail();

        $id_list = array_unique(array_column($records, "id"));

        if (empty($id_list))
        {
            return true;
        }

        $q = "SELECT id,holiday_id,date from " . $relation_model->getTable() . " where holiday_id in (" . implode(",", $id_list) . ")";

        $rel_records = $relation_model->mysql->select($q);
        $relation_model->dateFields($rel_records);

        foreach($records as $k => $record)
        {
            foreach($rel_records as $rel_record)
            {
                if ($record['id'] == $rel_record['holiday_id'])
                {
                    $records[$k]['holidayDetail'][] = $rel_record;
                }
            }
        }
    }
}
