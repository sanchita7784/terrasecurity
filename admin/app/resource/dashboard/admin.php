<?php

use App\Model\Attendance;
use App\Model\Company;
use App\Model\Employee;
use HardeepVicky\QueryBuilder\Condition;

require_once './app/model/Company.php';
require_once './app/model/Employee.php';

$company = new Company();
$company_count = $company->findCount(null);

$employee = new Employee();
$employee_count = $employee->findCount(null);

$attendance = new Attendance();
$present_emp_count = $attendance->findCount(Condition::init("AND")->add("in_time", date("Y-m-d"), ">"));

require_once './app/resource/layout/main/head.php'
?>
<div class="container-fluid">

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Dashboard</h4>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xl-2 col-md-4">
            <!-- card -->
            <div class="card card-h-100">
                <!-- card body -->
                <div class="card-body">
                    <div class="align-items-center">                        
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Companies</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?=  $company_count ?>"><?=  $company_count ?></span>
                        </h4>                        
                    </div>                        
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->
        <div class="col-xl-2 col-md-4">
            <!-- card -->
            <div class="card card-h-100">
                <!-- card body -->
                <div class="card-body">
                    <div class="align-items-center">                        
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Total Employee</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?=  $employee_count ?>"><?=  $employee_count ?></span>
                        </h4>                        
                    </div>                        
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->
        <div class="col-xl-2 col-md-4">
            <!-- card -->
            <div class="card card-h-100">
                <!-- card body -->
                <div class="card-body">
                    <div class="align-items-center">                        
                        <span class="text-muted mb-3 lh-1 d-block text-truncate">Present Employee</span>
                        <h4 class="mb-3">
                            <span class="counter-value" data-target="<?=  $present_emp_count ?>"><?=  $present_emp_count ?></span>
                        </h4>                        
                    </div>                        
                </div><!-- end card body -->
            </div><!-- end card -->
        </div><!-- end col -->
    </div>
</div>


<?php require_once './app/resource/layout/main/foot.php' ?>