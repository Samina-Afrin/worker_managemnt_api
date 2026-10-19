
<?php
include '../connection.php';
include '../auth_check.php';

$res = $cc->common_select('designations', '*');

echo json_encode($res);
?>
