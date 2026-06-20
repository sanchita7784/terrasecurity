<?php
namespace App\Model;


class EmployeeWorkHistory extends BaseModel
{
    public Array $validations = [
        'employee_id' => ['required'],
        'start_date' => ['required'],
        'end_date' => ['required'],
        'leave_reason' => ['required'],
    ];

    public $date_fields = [
        "start_date" => "d-M-Y",
        "end_date" => "d-M-Y",
    ];
}
