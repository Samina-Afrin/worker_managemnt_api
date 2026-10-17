<?php
include '../connection.php';
include '../auth_check.php';

$res = [];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    $res = $cc->common_update('attendances', [
        'deleted_at' => date('Y-m-d H:i:s')
    ], [
        'id' => $id
    ]);
} else {
    $res['status'] = false;
    $res['message'] = 'Invalid attendance ID';
}

echo json_encode($res);
?>