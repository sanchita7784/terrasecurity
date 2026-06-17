<?php

namespace App;

use App\Model\Permission;
use App\Model\Role;
use App\Model\User;
use Session;

class Auth
{
    public $user = [], $userModel, $allowedPermissions = [];

    public $defaultPageAllow = [
        "login",
        "register",
        "register_verify",
        "logout",
        "role/ajax_get_permission",
        "change_password"
    ];

    public $allowedPermissionForAdmin = [
        "role/set_permission"
    ];

    public function __construct()
    {
        $this->userModel = new User();

        if (isset($_SESSION['auth_user']))
        {
            $this->user = $_SESSION['auth_user'];
        }
    }

    public function find($username, $password)
    {
        $records = $this->userModel->find([], [
            "username" => $username,
        ]);

        if (empty($records))
        {
            Session::writeFlash("fail", "Wrong credantials");

            return false;
        }

        if (!$records[0]['is_active'])
        {
            Session::writeFlash("fail", "User is de-actived");

            return false;
        }

        if (password_verify($password, $records[0]['password']))
        {
            $this->userModel->role($records);
            $this->user = $records[0];
            unset($this->user['password']);

            Session::write('auth_user', $this->user);

            return true;
        }
        else
        {
            Session::writeFlash("fail", "Wrong credantials");
            return false;
        }
    }   

    public function check()
    {
        $this->user = Session::read("auth_user");
        if (empty($this->user))
        {
            redirect("login");
        }

        $records = $this->userModel->find([], ["id" => $this->user['id']]);
        $this->userModel->role($records);

        $this->user = $records[0];
    }

    public function isPageAllowed()
    {
        $this->getAllowPermission();
        
        if (in_array($_GET['r'], $this->defaultPageAllow))
        {
            return true;
        }

        $role = new Role();
        $role_records = $role->find([], ["id" => $this->user['role_id']]);
        $role_record = $role_records[0];

        $role_record['name'] = strtolower($role_record['name']);

        if ($role_record['name'] == 'superadmin' || $role_record['name'] == 'admin')
        {
            if ($_GET['r'] == 'role/set_permission')
            {
                return true;    
            }
        }

        return in_array($_GET['r'], $this->allowedPermissions);
    }

    public function getAllowPermission()
    {
        $cache = new Cache("auth");

        $role_id = $this->user['role_id'];

        $this->allowedPermissions = $cache->get("allowedPermissions.$role_id");

        if (!$this->allowedPermissions)
        {
            $permission = new Permission();
            $this->allowedPermissions = $permission->findList("id", "permission_link", ["role_id" => $this->user['role_id']]);

            $cache->put("allowedPermissions.$role_id", $this->allowedPermissions);
        }

        return $this->allowedPermissions;
    }

    public function getAllAllowPermission()
    {
        $this->getAllowPermission();

        $permissions = array_merge($this->allowedPermissions, $this->defaultPageAllow);

        if (strtolower($this->user['role']['name']) == 'superadmin')
        {
            $permissions = array_merge($permissions, $this->allowedPermissionForAdmin);
        }

        // d($permissions); exit;

        return $permissions;
    }

    public function setTimeZone($timezone)
    {
        Session::write('user_timezone', $timezone);
    }
}