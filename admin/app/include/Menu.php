<?php
namespace App;
class Menu
{
    public static function get($resource)
    {
        $menu = [];

        $menu[] = Home::get();

        $menu[] = Company::get();
        $menu[] = Location::get();
        $menu[] = Employee::get();
        $menu[] = Holiday::get();
        $menu[] = Leave::get();
        $menu[] = Attendance::get();

        $menu[] = Career::get();  

        $menu[] = System::get();

        self::check_for_active($menu, $resource);

        return $menu;
    }

    private static function check_for_active(&$menus, $resource)
    {
        $auth = new Auth();
        $allowed_links = $auth->getAllAllowPermission();
        foreach($menus as $k => $menu)
        {
            if (isset($menu['links']))
            {
                foreach($menu['links'] as $k2 => $submenu)
                {
                    if (isset($submenu['links']))
                    {
                        foreach($submenu['links'] as $k3 => $child_menu)
                        {
                            if (in_array($child_menu['r'], $allowed_links))
                            {
                                if ($resource == $child_menu['r'])
                                {
                                    $menus[$k]['links'][$k2]['links'][$k3]['active'] = true;
                                }
                            }
                            else
                            {
                                unset($menus[$k]['links'][$k2]['links'][$k3]);
                            }
                        }

                        if (empty($menus[$k]['links'][$k2]['links']))
                        {
                            unset($menus[$k]['links'][$k2]);
                        }
                    }
                    else
                    {
                        if (in_array($submenu['r'], $allowed_links))
                        {
                            if ($resource == $submenu['r'])
                            {
                                $menus[$k]['links'][$k2]['active'] = true;
                            }
                        }
                        else
                        {
                            unset($menus[$k]['links'][$k2]);
                        }
                    }
                }

                if (empty($menus[$k]['links']))
                {
                    unset($menus[$k]);
                }
            }
            else
            {
                if (in_array($menu['r'], $allowed_links))
                {
                    if ($resource == $menu['r'])
                    {
                        $menus[$k]['active'] = true;
                    }
                }
                else
                {
                    unset($menus[$k]);
                }
            }
        }
    }
}

class Home
{
    public static function get()
    {
        return [
            "r" => "dashboard",
            "title" => "Dashboard",
            "icon" => "fas fa-tachometer-alt"
        ];
    }
}

class System
{
    public static function get()
    {
        $links = [
            "title" => "System",
            "icon" => "fas fa-cogs",
            "links" => []
        ];

        $links['links'][] = self::user();
        $links['links'][] = self::role();
        // $links['links'][] = [
        //     "title" => "Setting",
        //     "r" => "setting",
        //     "icon" => "fas fa-key"
        // ];

        return $links;
    }

    public static function user()
    {
        $links = [
            "title" => "User",
            "icon" => "fas fa-users",
            "links" => []
        ];

        $links['links'][] = [
            "title" => "Summary",
            "icon" => "fas fa-th-list",
            "r" => "user/summary"
        ];

        $links['links'][] = [
            "title" => "Add",
            "icon" => "fas fa-plus-circle",
            "r" => "user/save"
        ];

        return $links;
    }

    public static function role()
    {
        $links = [
            "title" => "Role",
            "icon" => "fas fa-users",
            "links" => []
        ];

        $links['links'][] = [
            "title" => "Summary",
            "icon" => "fas fa-th-list",
            "r" => "role/summary"
        ];

        $links['links'][] = [
            "title" => "Add",
            "icon" => "fas fa-plus-circle",
            "r" => "role/save"
        ];

        $links['links'][] = [
            "title" => "Set Permission",
            "icon" => " fas fa-list",
            "r" => "role/set_permission"
        ];

        return $links;
    }
}

class Career
{
    public static function get()
    {
        $links = [
            "title" => "Career",
            "icon" => "fas fa-briefcase",
            "links" => []
        ];

        $links['links'][] = [
            "title" => "Summary",
            "r" => "career/summary",
            "icon" => "fas fa-th-list"
        ];

        return $links;
    }
}


class Location
{
    public static function get()
    {
        $links = [
            "title" => "Location",
            "icon" => "fas fa-briefcase",
            "links" => []
        ];

        $links['links'][] = [
            "title" => "Summary",
            "r" => "location/summary",
            "icon" => "fas fa-th-list",
        ];

        $links['links'][] = [
            "title" => "Add",
            "r" => "location/save",
            "icon" => "fas fa-plus-circle",
        ];

        return $links;
    }
}


class Employee
{
    public static function get()
    {
        $links = [
            "title" => "Employee",
            "icon" => "fas fa-briefcase",
            "links" => []
        ];

        $links['links'][] = [
            "title" => "Summary",
            "r" => "employee/summary",
            "icon" => "fas fa-th-list",
        ];

        $links['links'][] = [
            "title" => "Add",
            "r" => "employee/save",
            "icon" => "fas fa-plus-circle",
        ];

        return $links;
    }
}

class Attendance
{
    public static function get()
    {
        $links = [
            "title" => "Attendance",
            "icon" => "fas fa-briefcase",
            "links" => []
        ];

        $links['links'][] = [
            "title" => "Summary",
            "r" => "attendance/summary",
            "icon" => "fas fa-th-list",
        ];

        $links['links'][] = [
            "title" => "Mark Attendance",
            "r" => "attendance/mark",
            "icon" => "fas fa-clock",
        ];

        return $links;
    }
}


class Company
{
    public static function get()
    {
        $links = [
            "title" => "Company",
            "icon" => "fas fa-briefcase",
            "links" => []
        ];

        $links['links'][] = [
            "title" => "Summary",
            "r" => "company/summary",
            "icon" => "fas fa-th-list",
        ];

        $links['links'][] = [
            "title" => "Add",
            "r" => "company/save",
            "icon" => "fas fa-plus-circle",
        ];

        return $links;
    }
}


class Leave
{
    public static function get()
    {
        $links = [
            "title" => "Leave",
            "icon" => "fas fa-briefcase",
            "links" => []
        ];

        $links['links'][] = [
            "title" => "Summary",
            "r" => "leave/summary",
            "icon" => "fas fa-th-list",
        ];

        $links['links'][] = [
            "title" => "Add",
            "r" => "leave/save",
            "icon" => "fas fa-plus-circle",
        ];

        return $links;
    }
}


class Holiday
{
    public static function get()
    {
        $links = [
            "title" => "Holiday",
            "icon" => "fas fa-briefcase",
            "links" => []
        ];

        $links['links'][] = [
            "title" => "Summary",
            "r" => "holiday/summary",
            "icon" => "fas fa-th-list",
        ];

        $links['links'][] = [
            "title" => "Add",
            "r" => "holiday/save",
            "icon" => "fas fa-plus-circle",
        ];

        return $links;
    }
}