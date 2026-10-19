<?php
include '../connection.php';
include '../auth_check.php';

header('Content-Type: application/json; charset=UTF-8');

$workerId = $_GET['worker_id'] ?? '';
if (!is_string($workerId) || !ctype_digit($workerId) || (int)$workerId < 1) {
    http_response_code(400);
    echo json_encode([
        'status' => false,
        'data' => [],
        'message' => 'A valid worker_id is required.'
    ]);
    exit;
}

try {
    $query = $cc->conn->prepare(
        "SELECT salary.*, workers.name AS worker_name
         FROM salary
         INNER JOIN workers ON workers.id = salary.worker_id
         WHERE salary.worker_id = ? AND salary.deleted_at IS NULL
         ORDER BY salary.salary_date DESC, salary.id DESC"
    );
    $id = (int)$workerId;
    $query->bind_param('i', $id);
    $query->execute();
    $rows = $query->get_result();

    $data = [];
    while ($row = $rows->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode([
        'status' => true,
        'data' => $data,
        'message' => count($data) ? 'Employee salaries found.' : 'No salary records found for this employee.'
    ]);
} catch (Throwable $error) {
    error_log('Employee salary lookup failed: ' . $error->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'data' => [],
        'message' => 'Employee salary lookup failed: ' . $error->getMessage(),
        'error_code' => $error->getCode()
    ]);
}
?>
