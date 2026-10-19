
<?php
include '../connection.php';
include '../auth_check.php';

$res = [];

if (isset($_POST['name']) && !empty($_POST['name'])) {
    $res = $cc->common_insert('designations', $_POST);
} else {
    $res['status'] = false;
    $res['message'] = 'Designation name is required';
}

echo json_encode($res);
?>
