
<?php
include '../connection.php';
include '../auth_check.php';

$result = $cc->common_query(
    'SELECT salary.*, workers.name AS worker_name
     FROM salary
     JOIN workers ON workers.id = salary.worker_id'
);

echo json_encode($result);
?>
