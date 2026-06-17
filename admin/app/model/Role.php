<?php
namespace App\Model;

require_once './app/model/User.php';

use Exception;

class Role extends BaseModel
{    
    public Array $validations = [
        'name' => ['required', 'unique'],
    ];

    
}
