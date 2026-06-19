<?php
namespace App\Model;


class EmployeeShift extends BaseModel
{
    public Array $validations = [
        'apply_date' => ['required'],
        'start_time' => ['required'],
        'end_time' => ['required'],
    ];

    public $date_fields = [
        "apply_date" => "d-M-Y",
        "start_time" => "h:i A",
        "end_time" => "h:i A",
    ];

    public function shiftHours(&$records)
    {
        foreach($records as $k => $record)
        {
            $diff = abs(strtotime($record['end_time']) - strtotime($record['start_time']));

            $records[$k]['hours'] = floor($diff/3600);
        }
    }
}
