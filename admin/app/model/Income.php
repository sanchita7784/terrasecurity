<?php
namespace App\Model;

use DateUtility;

class Income extends BaseModel
{    

    public function get_income_from_attendance($month_year, $attendance_records)
    {
        $ret_data = [];

        $empModel = new Employee();

        $emp_records = $empModel->find(["id", "type", "company_id", "agreement_salary"], ["company_id > " => 0]);
        $empModel->company_id($emp_records);

        $company_records = [];

        $emp_company_list = [];

        $emp_type_list = [];

        foreach($emp_records as $emp_record)
        {
            $emp_company_list[$emp_record['id']] = $emp_record['company_id']['id'];
            $emp_type_list[$emp_record['id']] = $emp_record['type'];

            if (!isset($company_records[$emp_record['company_id']['id']][$emp_record['type']]))
            {
                $company_records[$emp_record['company_id']['id']][$emp_record['type']] = [
                    'company' => $emp_record['company_id'],
                    "no_of_employees" => 0,
                    "declare_income" => $emp_record['agreement_salary'],
                    "one_day_salary" => 0,
                    "present_days" => 0,
                    "leaves" => 0,
                    "holidays" => 0,
                    "total_days_in_month" => DateUtility::getDate("01-" . $month_year, "t"),
                    "cal_income" => 0,
                ];
            }

            $company_records[$emp_record['company_id']['id']][$emp_record['type']]['no_of_employees'] += 1;

            
        }

        foreach($attendance_records as $attendance_record)
        {
            if (!isset($emp_company_list[$attendance_record['employee_id']]))
            {
                continue;
            }
                
            $company_id = $emp_company_list[$attendance_record['employee_id']];
            $type = $emp_type_list[$attendance_record['employee_id']];

            $company_records[$company_id][$type]['present_days'] += ($attendance_record['working_hours'] > 0 ? 1 : 0);
            $company_records[$company_id][$type]['leaves'] += $attendance_record['leaves'];
            if ($attendance_record['is_holiday'])
            {
                $company_records[$company_id][$type]['holidays'] += 1;
            }
        }

        $month = (int) DateUtility::getDate("01-" . $month_year, "m");
        $year = DateUtility::getDate("01-" . $month_year, "Y");

        $invoice = new Invoice();
        $db_records = $invoice->find([], ["month" => $month, "year" => $year]);

        $temp = $db_records;
        $db_records = [];

        foreach($temp as $arr)
        {
            $db_records[$arr['company_id']] = $arr;
        }

        foreach($company_records as $company_id => $emp_types)
        {
            foreach($emp_types as $emp_type => $attendance_record)
            {
                $one_day_salary = round($attendance_record['declare_income'] / $attendance_record['total_days_in_month'], 2);
                $company_records[$company_id][$emp_type]['one_day_salary'] = $one_day_salary;
                $company_records[$company_id][$emp_type]['cal_income'] = $one_day_salary * $attendance_record['present_days'];
                $company_records[$company_id][$emp_type]['cal_income'] += $one_day_salary * $attendance_record['leaves'];
                $company_records[$company_id][$emp_type]['cal_income'] += $one_day_salary * $attendance_record['holidays'];
                $company_records[$company_id][$emp_type]['is_paid'] = 0;
                if (isset($db_records[$company_id]))
                {
                    $company_records[$company_id][$emp_type]['is_paid'] = $db_records[$company_id]['is_paid'];
                }
            }
        }

        return $company_records;
    }
}
