<?php

include '../connection.php';

$data = json_decode(file_get_contents('php://input'), true);

$res = [];

if ($data['name']) {

    $designation['name'] = $data['name'];

    $res = $cc->common_insert('designations', $designation);

} else {

    $res['status'] = false;
    $res['message'] = 'data is not saved';
}

echo json_encode($res);

?>