<?php
include '../connection.php';
include '../auth_check.php';

$res = $cc->common_query('SELECT attendances.*, workers.name,workers.phone FROM `attendances` join workers on workers.id=attendances.worker_id');

echo json_encode($res);
?>