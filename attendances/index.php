<?php
include '../connection.php';
include '../auth_check.php';

$res = $cc->common_select('attendances', '*');

echo json_encode($res);
?>