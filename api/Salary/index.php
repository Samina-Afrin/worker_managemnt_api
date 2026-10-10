<?php
include '../connection.php';
$result=$cc->common_select('Salary','*');
echo json_encode($result);