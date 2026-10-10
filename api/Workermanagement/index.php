<?php
include '../connection.php';
$result=$cc->common_select('Workers','*');
echo json_encode($result);