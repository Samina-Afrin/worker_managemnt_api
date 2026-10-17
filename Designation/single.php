
<?php
include '../connection.php';
include '../auth_check.php';

$id = $_GET['id'] ?? '';

if ($id !== '' && is_numeric($id)) {
    $res = $cc->common_select('designations', '*', [
        'id' => $id
    ]);
} else {
    $res = [
        'status' => false,
        'message' => 'Invalid designation ID'
    ];
}

echo json_encode($res);
?>
