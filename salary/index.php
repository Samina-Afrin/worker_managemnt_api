
<?php
include '../connection.php';
include '../auth_check.php';

$result = $cc->common_query(
    'SELECT salary.*, workers.name AS worker_name
     FROM salary
     JOIN workers ON workers.id = salary.worker_id
     WHERE salary.deleted_at IS NULL
     ORDER BY workers.name, salary.salary_date DESC, salary.id DESC'
);

echo json_encode($result);
?>
