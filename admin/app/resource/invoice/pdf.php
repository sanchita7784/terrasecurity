<?php
use App\Model\Employee;

require_once 'app/resource/pdf_css.php';
?>

<div class="page" style="background-color: #FFF; color: #000;">
    <table>
        <thead style="border : 1px solid #000;">
            <tr>
                <th colspan="3" style="text-align: center; padding : 10px;">
                    <p style="font-size: 26px; line-height: 14px;">Terra Security</p>
                </th>
            </tr>         
            <tr>
                <th style="padding : 10px; width:33%">
                    <b class="summary_header_footer">Billing To</b>
                    <br/>
                    <?= $company['name'] ?>
                    <br/>
                    GST : <?= $company['gst_no'] ?>
                    <br/>
                    Address : <?= $company['name'] ?>
                    <br/>
                    State : <?= $company['state_id']['name'] ?>
                </th>
                <th style="padding : 10px; width:33%"></th>
                <th style="padding : 10px; width:33%">
                    Invoice No. : <?= $invoice_no ?>
                    <br/>
                    Invoice Date : <?= date("d-M-Y") ?>
                </th>
            </tr>
        </thead>
    </table>

    <table>
        <thead>
            <tr class="summary_header_footer center">
                <th class="ceil" style="width : 8%;">Sr. No.</th>
                <th class="ceil" style="width : 20%;">Type</th>
                <th class="ceil" style="width : 15%;">No. of Employees</th>
                <th class="ceil" style="width : 15%;">Present Days</th>
                <th class="ceil" style="width : 10%;">Amount</th>
            </tr>
        </thead>
        <tbody>
                <?php $c = 0; $total_amount = 0; 
                foreach ($records as  $emp_type => $record ): 
                    $c++; 
                    $total_amount += $record['cal_income'];
                    ?>
                <tr class="center">
                    <td class="ceil"><?= $c ?></td>
                    <td class="ceil"><?= Employee::TYPE_LIST[$emp_type]; ?></td>
                    <td class="ceil"><?= $record['no_of_employees'] ?></td>
                    <td class="ceil"><?= $record['present_days'] ?></td>
                    <td class="ceil"><?= $record['cal_income'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>       
    </table>

    <div style="float: right; text-align:right; margin-top:20px; width : 33%;">
        <span style="font-size: 20px;">
            <b>Total Amount</b> : <?= $total_amount ?>
        </span>
    </div>
</div>