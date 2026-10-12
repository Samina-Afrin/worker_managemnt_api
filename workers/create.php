<?php
    include '../connection.php';
    include '../auth_check.php';
    
    $res=[];
    if($_POST['name']){
        $res=$cc->common_insert('workers', $_POST);
    } else {
        $res['status'] = false;
        $res['message'] = 'data is not saved';
    }
echo json_encode($res);