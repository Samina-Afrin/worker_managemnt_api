<?php
include '../connection.php';
include '../auth_check.php';
require_once 'salary_helpers.php';

header('Content-Type: application/json; charset=UTF-8');

$fromDate = $_GET['from_date'] ?? '';
$toDate = $_GET['to_date'] ?? '';

if (!validate_salary_dates($fromDate, $toDate)) {
    http_response_code(400);
    echo json_encode([
        'status' => false,
        'data' => [],
        'message' => 'A valid from_date and to_date are required, and from_date must not be after to_date.'
    ]);
    exit;
}

try {
    $workers = get_salary_preview($cc->conn, $fromDate, $toDate);
    echo json_encode([
        'status' => true,
        'data' => $workers,
        'message' => 'Salary preview loaded'
    ]);
} catch (Throwable $error) {
    error_log('Salary preview failed: ' . $error->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'data' => [],
        'message' => 'Salary preview failed: ' . $error->getMessage(),
        'error_code' => $error->getCode()
    ]);
}
?>
