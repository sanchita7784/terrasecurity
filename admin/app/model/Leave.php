<?php
namespace App\Model;

use Exception;

require_once './app/model/Location.php';

class Company extends BaseModel
{    
    public Array $validations = [
        'employee_id' => ['required'],        
        'date' => ['required', 'combo_unique' => "employee_id"],        
        'type' => ['required'],
    ];

    public function beforeDelete($id)
    {
        $record = $this->find(["status"], ["id" > $id]);
        $record = $record[0];

        if ($record['status'] !== 0)
        {
            throw new Exception("Approved or Rejected Leaves can not be deleted");
        }

        return true;
    }
}
