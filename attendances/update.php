<?php
include '../connection.php';
include '../auth_check.php';

$res = [];

if (
    isset($_GET['id']) &&
    is_numeric($_GET['id']) &&
    isset($_POST['worker_id']) &&
    !empty($_POST['worker_id']) &&
    isset($_POST['date']) &&
    !empty($_POST['date']) &&
    isset($_POST['status']) &&
    !empty($_POST['status'])
) {
    $id = $_GET['id'];

    $res = $cc->common_update(
        'attendances',
        $_POST,
        ['id' => $id]
    );
} else {
    $res['status'] = false;
    $res['message'] = 'Attendance data is not updated';
}

echo json_encode($res);
?>