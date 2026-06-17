<?php
namespace App\Model;

class Attendance extends BaseModel
{    
    public Array $validations = [
        'location_id' => ['required'],
        'employee_id' => ['required'],
    ];

    public $date_fields = [
        "in_time" => "d-M-Y h:i A", 
        "out_time" => "d-M-Y h:i A", 
    ];
}
