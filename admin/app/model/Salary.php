<?php
namespace App\Model;

use DateUtility;
use HardeepVicky\QueryBuilder\QuerySelect;
use HardeepVicky\QueryBuilder\Table;

require_once './app/model/Attendance.php';
require_once './app/model/Employee.php';
require_once './app/model/EmployeeShift.php';
require_once './app/model/Leaves.php';

class Salary extends BaseModel
{
    public Array $validations = [
    ];

    public function beforeDelete($id)
    {
        $record = $this->find([], ["id" => $id]);
        $record = $record[0];

        if ($record['is_paid'])
        {
            throw new \Exception("Paid Salary can not be deleted");
        }



        return true;
    }

    public function get_attendance(String $from_date,String $to_date, array $employee_id_list = [])
    {
        global $mysql;

        $q = "SELECT
                    *
                FROM
                    attendance
                WHERE
                    out_time is not null
                    and date(in_time) >= '$from_date'
                    and date(in_time) <= '$to_date'";
        
        $temp = $mysql->select($q);

        $attendance_records = [];
        foreach($temp as $arr)
        {
            $date = DateUtility::getDate($arr['in_time'], 'Y-m-d');
            $attendance_records[$arr['employee_id']][$date][] = $arr;
        }

        $emp_id_list = array_keys($attendance_records);

        if (empty($emp_id_list))
        {
            return [];
        }

        $emp_ids = implode(",", $emp_id_list);

        $empModel = new Employee();

        $emp_records = $empModel->find([], "id in ($emp_ids)");

        $temp = $emp_records;

        $emp_records = [];

        foreach($temp as $arr)
        {
            $emp_records[$arr['id']] = $arr;
        }

        $shifts = $this->shifts_from_range($from_date, $to_date, $emp_id_list);
        
        $q = "SELECT
                    *
                FROM
                    holiday_detail
                WHERE	
                    `date` >= '$from_date'
                    and `date` <= '$to_date'";
        
        $temp = $mysql->select($q);

        $holiday_records = [];

        foreach($temp as $arr)
        {
            $holiday_records[$arr['date']] = $arr;
        }

        $q = "SELECT
                    *
                FROM
                    leaves
                WHERE	
                    `status` = 1
                    and employee_id in ($emp_ids)
                    and `date` >= '$from_date'
                    and `date` <= '$to_date'";
        
        $temp = $mysql->select($q);

        $leave_records = [];

        foreach($temp as $arr)
        {
            if ($arr['type'] == Leaves::TYPE_SHORT_LEAVE)
            {
                $arr['factor'] = 0.25;
            }
            else if ($arr['type'] == Leaves::TYPE_HALF_DAY)
            {
                $arr['factor'] = 0.5;
            }
            else if ($arr['type'] == Leaves::TYPE_FULL)
            {
                $arr['factor'] = 1;
            }

            $leave_records[$arr['employee_id']][$arr['date']] = $arr;
        }

        $ret_data = [];

        foreach($shifts as $emp_id => $shift_records)
        {
            foreach($shift_records as $date => $shift_record)
            {
                if (DateUtility::compare($emp_records[$emp_id]['doj'], $date) > 0) 
                {
                    continue;
                }

                $record = [
                    "employee_id" => $emp_id,
                    "date" => $date,
                    "shift_hours" => $shift_record['hours'],
                    "is_present" => false,
                    "is_holiday" => false,
                    "is_leave" => false,
                    "day" => "absent",
                    "working_hours" => 0,
                    "diff_hours" => 0,
                    "leaves" => 0,
                    "employee" => $emp_records[$emp_id],
                ];

                if (isset($attendance_records[$emp_id][$date]))
                {
                    $record['day'] = 'present';

                    foreach($attendance_records[$emp_id][$date] as $attendance_record)
                    {
                        $diff = abs(strtotime($attendance_record['out_time']) - strtotime($attendance_record['in_time']));

                        $record['working_hours'] += round($diff / 3600, 3);                    
                    }
                }

                $record['diff_hours'] = $record['working_hours'] - $record['shift_hours'];

                if (isset($holiday_records[$date]))
                {
                    $record['day'] = 'holiday';
                    $record['is_holiday'] = true;
                }

                if (isset($leave_records[$emp_id][$date]))
                {
                    $record['day'] = 'leave';
                    $record['is_leave'] = true;
                    $record['leaves'] = $leave_records[$emp_id][$date]['factor'];
                }

                $ret_data[] = $record;
            }
        }

        return $ret_data;
    }

    public function get_salary_from_attendance($month_year, $attendance_records)
    {
        global $mysql;

        $ret_data = [];


        $empModel = new Employee();

        $emp_records = $empModel->find(["id", "name", "mobile", "salary"]);

        foreach($attendance_records as $attendance_record)
        {
            $emp_id = $attendance_record['employee_id'];

            $shift_hours = $attendance_record['shift_hours'];

            if ( !isset($ret_data[$emp_id][$shift_hours]) )
            {
                $ret_data[$emp_id][$shift_hours] = [
                    "employee" => $attendance_record['employee'],
                    "month" => DateUtility::getDate($attendance_record['date'], 'M'),
                    "year" => DateUtility::getDate($attendance_record['date'], 'Y'),
                    'shift_hours' => $attendance_record['shift_hours'],
                    "total_days_in_month" => DateUtility::getDate($month_year, "t"),
                    "attendance_hours" => 0,
                    'holidays' => 0,
                    'leaves' => 0,
                    'present_days' => 0
                ];
                
                $ret_data[$emp_id][$shift_hours]['one_day_salary'] = round($attendance_record['employee']['salary'] / $ret_data[$emp_id][$shift_hours]['total_days_in_month'], 3);
                $ret_data[$emp_id][$shift_hours]['one_hour_salary'] = round($ret_data[$emp_id][$shift_hours]['one_day_salary'] / $attendance_record['shift_hours'], 3);
                $ret_data[$emp_id][$shift_hours]['cal_salary'] = 0;
            }

            $ret_data[$emp_id][$shift_hours]['attendance_hours'] += $attendance_record['working_hours'];
            $ret_data[$emp_id][$shift_hours]['leaves'] += $attendance_record['leaves'];
            if ($attendance_record['is_holiday'])
            {
                $ret_data[$emp_id][$shift_hours]['holidays'] += 1;
            }

            $ret_data[$emp_id][$shift_hours]['present_days'] += ($attendance_record['working_hours'] > 0 ? 1 : 0);
        }

        $month = (int) DateUtility::getDate("01-" . $month_year, "m");
        $year = DateUtility::getDate("01-" . $month_year, "Y");
        $db_records = $this->find([], ["month" => $month, "year" => $year]);

        $temp = $db_records;
        $db_records = [];

        foreach($temp as $arr)
        {
            $db_records[$arr['employee_id']][$arr['shift_hours']] = $arr;
        }

        foreach($ret_data as $emp_id => $shifts)
        {
            foreach($shifts as $shift_hours => $attendance_record)
            {
                $ret_data[$emp_id][$shift_hours]['cal_salary'] = round($attendance_record['attendance_hours'] * $attendance_record['one_hour_salary']);
                $ret_data[$emp_id][$shift_hours]['cal_salary'] += round($attendance_record["leaves"] * $attendance_record['one_day_salary']);
                $ret_data[$emp_id][$shift_hours]['cal_salary'] += round($attendance_record["holidays"] * $attendance_record['one_day_salary']);
                $ret_data[$emp_id][$shift_hours]['is_paid'] = 0;
                if (isset($db_records[$emp_id][$shift_hours]))
                {
                    $ret_data[$emp_id][$shift_hours]['is_paid'] = $db_records[$emp_id][$shift_hours]['is_paid'];
                }
            }
        }

        foreach($emp_records as $emp_record)
        {
            if (!isset($ret_data[$emp_record['id']]) ) 
            {
                $ret_data[$emp_record['id']][0]['employee'] = $emp_record;
                $ret_data[$emp_record['id']][0]['problems'][] = 'Attendance not found';
            }
        }

        return $ret_data;
    }

    public function shifts_from_range($from_date, $to_date, array $employee_id_list = [])
    {
        global $mysql;

        $empShift = new EmployeeShift();

        $employee_ids = implode(",", $employee_id_list);

        $q = "SELECT 
                * 
            FROM 
                employee_shift 
            where 
                employee_id in ($employee_ids)
                and apply_date between '$from_date' and '$to_date'
            order by
                apply_date ASC 
            ";

        $temp = $mysql->select($q);

        if (empty($temp))
        {
            return false;
        }

        $empShift->shiftHours($temp);

        $records = [];

        foreach($temp as $arr)
        {
            $records[$arr['employee_id']][] = $arr;
        }

        $ret_data = [];

        foreach($records as $emp_id => $temp_records)
        {
            $f_date = $from_date;
            while($f_date <= $to_date)
            {
                $current_record = null;
                foreach($temp_records as $temp_record)
                {
                    if ($f_date >= $temp_record['apply_date'])
                    {
                        $current_record = $temp_record;
                    }
                }

                if ($current_record)
                {
                    $ret_data[$emp_id][$f_date] = $current_record;
                }

                $f_date = DateUtility::change($f_date, 1, DateUtility::DAYS, DateUtility::DATE_FORMAT);
            }
        }

        return $ret_data;
    }
}
