<?php

use App\Auth;
$auth = new Auth();

if ($auth->user['role']['name'] == 'user')
{
    require_once './app/resource/dashboard/user.php';
}
else
{
    require_once './app/resource/dashboard/admin.php';
}
