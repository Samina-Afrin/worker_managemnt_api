<?php

include '../connection.php';

$data = json_decode(file_get_contents('php://input'), true);

$res = [];

if (!empty($data['worker_id'])) {

    $salary['worker_id'] = $data['worker_id'];
    $salary['month'] = $data['month'];
    $salary['year'] = $data['year'];
    $salary['basic_salary'] = $data['basic_salary'];
    $salary['present_days'] = $data['present_days'] ?? 0;
    $salary['absent_days'] = $data['absent_days'] ?? 0;
    $salary['late_days'] = $data['late_days'] ?? 0;
    $salary['attendance_deduction'] = $data['attendance_deduction'] ?? 0;
    $salary['bonus'] = $data['bonus'] ?? 0;
    $salary['deduction'] = $data['deduction'] ?? 0;
    $salary['net_salary'] = $data['net_salary'];
    $salary['payment_status'] = $data['payment_status'] ?? 'unpaid';

    $res = $cc->common_insert('salaries', $salary);

} else {

    $res['status'] = false;
    $res['message'] = 'data is not saved';
}

echo json_encode($res);

?>