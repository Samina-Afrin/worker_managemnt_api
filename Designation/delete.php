
<?php
include '../connection.php';
include '../auth_check.php';

$res = [];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    $res = $cc->common_delete('designations', [
        'id' => $id
    ]);
} else {
    $res['status'] = false;
    $res['message'] = 'Invalid designation ID';
}

echo json_encode($res);
?>
