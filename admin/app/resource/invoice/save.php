<?php

use App\Form;
use App\Model\Company;
use App\Model\Employee;
use App\Model\Income;
use App\Model\Invoice;
use App\Model\Salary;
use Knp\Snappy\Pdf;

require_once './app/model/Income.php';
require_once './app/model/Salary.php';
require_once './app/model/Invoice.php';
require_once './app/model/Company.php';

$salary = new Salary();
$income = new Income();
$invoice = new Invoice();

$form = new Form($income);

// d($form->data); exit;

if (isset($_GET['form_data']['month_year']) && $_GET['form_data']['month_year'])
{
    $from_date = DateUtility::getDate("01-" . $_GET['form_data']['month_year'], DateUtility::DATE_FORMAT);
    $to_date = DateUtility::getDate("01-" . $_GET['form_data']['month_year'], "Y-m-t");

    $attendance_records = $salary->get_attendance($from_date, $to_date);

    $salary_data = $income->get_income_from_attendance($_GET['form_data']['month_year'], $attendance_records);

    $month = DateUtility::getDate("01-" . $_GET['form_data']['month_year'], "m");
    $year = DateUtility::getDate("01-" . $_GET['form_data']['month_year'], "Y");
}


if (isset($_POST['form_data']))
{
    // d($_POST['form_data']); exit;

    $insert_count = $update_count = $fail_count = 0;

    $company = new Company();
    $temp = $company->find();
    $company_records = [];

    foreach($temp as $arr)
    {
        $company_records[$arr['id']] = $arr;
    }

    $company->state_id($company_records);

    foreach($_POST['form_data'] as $company_id => $emp_types)
    {
        $next_id = $invoice->nextId();
        $invoice_record = [
            "company_id" => $company_id,
            "month" => $month,
            "year" => $year,
            "amount" => 0,
            "invoice_no" => date("Y") . "/" . $next_id,
            "pdf" => "",
            "income" => []
        ];

        foreach($emp_types as $emp_type => $record)
        {
            $invoice_record['amount'] += $record['cal_income'];
            $invoice_record['income'][] = $record;
        }        


        $html = view("./app/resource/invoice/pdf", [
            "records" => $emp_types,
            "company" => $company_records[$company_id],
            "invoice_no" => $invoice_record['invoice_no'],
        ]);

        // echo $html; exit;

        $path = "storage/files/Invoice/";
        FileUtility::createFolder($path);
        $file_name = "invoice_" . $company_id . "_month_" . $month . "_year_" . $year . ".pdf";
        $pdf_file = $path . $file_name;
        $invoice_record['pdf'] = $pdf_file;
        if (file_exists($pdf_file))
        {
            unlink($pdf_file);
        }

        $full_path = getcwd() . "/";

        $snappy = new Pdf(WK_HTML_TO_PDF_PATH);
        $snappy->setOption('lowquality', false);
        $snappy->setOption('disable-smart-shrinking', false);
        $snappy->setOption('dpi', 300);
        $snappy->setOption('page-size', "A4");
        $snappy->generateFromHtml($html, $full_path . $pdf_file);

        $db_record = $invoice->find([], [
            "company_id" => $company_id,
            "month" => $month,
            "year" => $year,
        ]);

        if ($db_record)
        {
            $db_record = $db_record[0];

            $invoice->id = $db_record['id'];
            $invoice->update($invoice_record);

            $update_count++;
        }
        else
        {
            $invoice->insert($invoice_record);
            $insert_count++;
        }

        foreach($invoice_record['income'] as $income_record)
        {
            $income_record['invoice_id'] = $invoice->id;
            $db_record = $income->find([], [
                "company_id" => $company_id,
                "month" => $month,
                "year" => $year,
                "emp_type" => $income_record['emp_type']
            ]);

            if ($db_record)
            {
                $db_record = $db_record[0];

                $income->id = $db_record['id'];
                $income->update($income_record);
            }
            else
            {
                $income->insert($income_record);
            }
        }
    }

    if ($insert_count || $update_count)
    {
        Session::writeFlash("success", "Invoice save successfully");
    }

    if ($fail_count > 0)
    {
        Session::writeFlash("fail", "Fail To save : $fail_count");
    }
}

require_once './app/resource/layout/main/head.php'
?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Invoice Form</h4>
            </div>
            <div class="card-body p-4">
                <form style="width : 80%">
                    <input type="hidden" name="r" value="<?= $resource ?>" />
                    <div class="row">
                        <div class="col-md-3 col-xl-2">
                            <?= $form->input("month_year", [
                                "class" => "form-control month-date-picker",
                                "required" => true
                            ]); ?>
                        </div>
                        <div class="col-md-3 col-xl-2">
                            <button type="submit" class="btn btn-primary">Search</button>
                            <a href="<?= url_without_query_params($resource) ?>" class="btn btn-secondary">Clear</a>
                        </div>
                    </div>
                </form>
            </div>

            <?php if (isset($salary_data)): ?>
            <form method="post">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>
                                    <input type="checkbox" id="parent-chk" data-sr-chkselect-children=".child-chk">
                                    #
                                </th>
                                <th>Company. Details</th>
                                <th>Cal. Salary</th>
                                <th>Payment Done</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i = 0; foreach($salary_data as $company_id => $emp_types):?>
                                <?php
                                foreach($emp_types as $emp_type => $record):
                                    $i++;
                                ?>
                                <tr>
                                    <td>
                                        <?php if (isset($record['is_paid']) && $record['is_paid'] == 0): ?>
                                            <label>
                                                <input type="checkbox" name="form_data[<?= $company_id ?>][<?= $emp_type ?>][is_check]" class="child-chk">
                                                <input type="hidden" name="form_data[<?= $company_id ?>][<?= $emp_type ?>][company_id]" value="<?= $record['company']['id'] ?>">
                                                <input type="hidden" name="form_data[<?= $company_id ?>][<?= $emp_type ?>][month]" value="<?= $month ?>">
                                                <input type="hidden" name="form_data[<?= $company_id ?>][<?= $emp_type ?>][year]" value="<?= $year ?>">
                                                <input type="hidden" name="form_data[<?= $company_id ?>][<?= $emp_type ?>][emp_type]" value="<?= $emp_type ?>">
                                                <input type="hidden" name="form_data[<?= $company_id ?>][<?= $emp_type ?>][no_of_employees]" value="<?= $record['no_of_employees'] ?>">
                                                <input type="hidden" name="form_data[<?= $company_id ?>][<?= $emp_type ?>][declare_income]" value="<?= $record['declare_income'] ?>">
                                                <input type="hidden" name="form_data[<?= $company_id ?>][<?= $emp_type ?>][one_day_salary]" value="<?= $record['one_day_salary'] ?>">
                                                <input type="hidden" name="form_data[<?= $company_id ?>][<?= $emp_type ?>][present_days]" value="<?= $record['present_days'] ?>">
                                                <input type="hidden" name="form_data[<?= $company_id ?>][<?= $emp_type ?>][cal_income]" value="<?= $record['cal_income'] ?>">
                                                <?= $i ?>
                                            </label>
                                        <?php else: ?>
                                            <?= $i ?>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <b>Company : </b> <?=  $record['company']['name'] ?>
                                        <br/><br/>
                                        <b>Emp. Type : </b> <?=  Employee::TYPE_LIST[$emp_type] ?>
                                    </td>
                                    <td>
                                        <?php if (isset($record['cal_income'])): ?>
                                            <b>Present Days : </b> <?=  $record['present_days'] ?>
                                            <br/>
                                            <b>Holidays : </b> <?=  $record['holidays'] ?>
                                            <br/>
                                            <b>Leaves : </b> <?=  $record['leaves'] ?>
                                            <br/>
                                            <b>Declare Income : </b> <?=  $record['declare_income'] ?>
                                            <br/>
                                            <b>One Day Income : </b> <?=  $record['one_day_salary'] ?>
                                            <br/>
                                            <b>Cal. Income : </b> <?=  $record['cal_income'] ?>
                                            <br/>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($record['is_paid']) && $record['is_paid']): ?>
                                            <span class="text-success">Payment Done</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
            </form>
            <?php endif; ?>
        </div>
    </div> <!-- end col -->
</div>

<script>
    $(function(){
        $(".month-date-picker").datepicker({
            format: "M-yyyy",      // Sets the output format in the text field
            startView: "months",    // The view that the picker starts on when opened
            minViewMode: "months",  // Limits the lowest view depth to month level (prevents day selection)
            autoclose: true
        });

        $("#parent-chk").chkSelectAll();
    });
</script>

<?php require_once './app/resource/layout/main/foot.php' ?>