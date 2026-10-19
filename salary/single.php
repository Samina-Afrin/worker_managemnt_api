
<?php
include '../connection.php';
include '../auth_check.php';

$id = $_GET['id'];

$result = $cc->common_query(
    "SELECT salary.*, workers.name AS worker_name
     FROM salary
     INNER JOIN workers ON workers.id = salary.worker_id
     WHERE salary.id = '" . $cc->conn->real_escape_string($id) . "'"
);

echo json_encode($result);
?>
