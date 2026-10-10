<?php
include '../connection.php';
$id=$_GET['id'];
$res=$cc->common_delete('Salary',['id'=>$id]);

echo json_encode($res);