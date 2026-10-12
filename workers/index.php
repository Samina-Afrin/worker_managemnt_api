<?php
include '../connection.php';
include '../auth_check.php';

$result=$cc->common_query('SELECT workers.*,departments.name as dept_name,designations.name as desi_name FROM `workers` JOIN departments on departments.id=workers.department_id JOIN designations on designations.id=workers.designation_id');
echo json_encode($result);