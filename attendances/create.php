<?php
include '../connection.php';
include '../auth_check.php';

$res = [];

if (
    isset($_POST['worker_id'], $_POST['date'], $_POST['status']) &&
    !empty($_POST['worker_id']) &&
    !empty($_POST['date']) &&
    !empty($_POST['status'])
) {
    $res = $cc->common_insert('attendances', $_POST);
} else {
    $res['status'] = false;
    $res['message'] = 'Worker ID, date and status are required';
}

echo json_encode($res);
?>