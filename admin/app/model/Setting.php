<?php
namespace App\Model;

use Exception;

class Setting extends BaseModel
{    
    public function findValue(array $names)
    {
        $records = $this->find();

        $list = [];

        foreach($names as $name)
        {
            foreach($records as $record)
            {
                if ($record['name'] == $name)
                {
                    $list[$name] = $record['value'];
                }
            }
        }

        foreach($names as $name)
        {
            if (!isset($list[$name]))
            {
                throw new Exception("$name not found in settings");
            }
        }

        return $list;
    }
}