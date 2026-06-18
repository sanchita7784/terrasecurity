<?php
namespace App\Model;

use Exception;
class Leaves extends BaseModel
{    
    public Array $validations = [
        'employee_id' => ['required'],        
        'date' => ['required', 
            'combo_unique' => [ 
                "other_fields" => ["employee_id"]
            ]
        ],        
        'type' => ['required'],
    ];


    public $date_fields = [
        "date" => "d-M-Y", 
    ];

    const APPROVE = 1;
    const PENDING = 0;
    const REJECT = -1;

    CONST STATUS_LIST = [
        self::APPROVE => "Approved",
        self::PENDING => "Pending",
        self::REJECT => "Rejected",
    ];

    const TYPE_SHORT_LEAVE = 1;
    const TYPE_HALF_DAY = 2;
    const TYPE_FULL = 3;

    CONST TYPE_LIST = [
        self::TYPE_SHORT_LEAVE => "Short Day Leave (25 % of Day)",
        self::TYPE_HALF_DAY => "Half Day Leave (50 % of Day)",
        self::TYPE_FULL => "Full Day Leave",
    ];
    
    public function beforeDelete($id)
    {
        $record = $this->find(["status"], ["id" => $id]);
        $record = $record[0];

        if ($record['status'] != 0)
        {
            throw new Exception("Approved or Rejected Leaves can not be deleted");
        }

        return true;
    }
}
