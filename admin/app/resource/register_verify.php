<?php

use App\Model\User;

$user = new User();

$user_record = $user->find([], ["uuid" => $_GET['uuid']]);

if (empty($user_record))
{
    die("wrong user");
}

$user_record = $user_record[0];

$user->id = $user_record['id'];
$user->update(["is_active" => 1]);

Session::write("success", "Account verified successfully");
redirect("login");
