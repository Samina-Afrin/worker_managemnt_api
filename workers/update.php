<?php
    include '../connection.php';
    include '../auth_check.php';
    
    $id=$_GET['id'];
    $res=[];
    if($_POST['name']){
        $res=$cc->common_update('workers',$_POST,['id'=>$id]);
    } else {
        $res['status'] = false;
        $res['message'] = 'data is not saved';
    }
echo json_encode($res);
