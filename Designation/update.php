
<?php
include '../connection.php';
include '../auth_check.php';

$res = [];

if (
    isset($_GET['id']) &&
    is_numeric($_GET['id']) &&
    isset($_POST['name']) &&
    !empty($_POST['name'])
) {
    $id = $_GET['id'];

    $res = $cc->common_update(
        'designations',
        $_POST,
        ['id' => $id]
    );
} else {
    $res['status'] = false;
    $res['message'] = 'Designation data is not updated';
}

echo json_encode($res);
?>
