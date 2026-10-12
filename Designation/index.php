<?php
include '../connection.php';
$result=$cc->common_select('Designation','*');
echo json_encode($result);