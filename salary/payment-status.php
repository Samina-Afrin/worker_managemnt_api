<?php
include '../connection.php';
include '../auth_check.php';

header('Content-Type: application/json; charset=UTF-8');

$paymentStatus = $_POST['payment_status'] ?? '';
$salaryId = $_POST['salary_id'] ?? '';
$workerId = $_POST['worker_id'] ?? '';
$fromDate = $_POST['from_date'] ?? '';
$toDate = $_POST['to_date'] ?? '';

if (!in_array($paymentStatus, ['Paid', 'Pending'], true)) {
    http_response_code(400);
    echo json_encode([
        'status' => false,
        'message' => 'payment_status must be Paid or Pending.'
    ]);
    exit;
}

$isSingleRecord = is_string($salaryId) && ctype_digit($salaryId) && (int)$salaryId > 0;
$isEmployeeUpdate = is_string($workerId) && ctype_digit($workerId) && (int)$workerId > 0;
if (!$isSingleRecord && !$isEmployeeUpdate) {
    http_response_code(400);
    echo json_encode([
        'status' => false,
        'message' => 'A valid salary_id or worker_id is required.'
    ]);
    exit;
}

$isValidDate = static function ($date) {
    if ($date === '') {
        return true;
    }
    if (!is_string($date)) {
        return false;
    }
    $parsed = DateTime::createFromFormat('!Y-m-d', $date);
    return $parsed && $parsed->format('Y-m-d') === $date;
};

if (
    !$isValidDate($fromDate) ||
    !$isValidDate($toDate) ||
    ($fromDate !== '' && $toDate !== '' && $fromDate > $toDate)
) {
    http_response_code(400);
    echo json_encode([
        'status' => false,
        'message' => 'Provide a valid date range with from_date on or before to_date.'
    ]);
    exit;
}

try {
    if ($isSingleRecord) {
        $id = (int)$salaryId;
        $query = $cc->conn->prepare(
            'UPDATE salary
             SET payment_status = ?
             WHERE id = ? AND deleted_at IS NULL'
        );
        $query->bind_param('si', $paymentStatus, $id);
    } else {
        $id = (int)$workerId;
        $query = $cc->conn->prepare(
            "UPDATE salary
             SET payment_status = ?
             WHERE worker_id = ? AND deleted_at IS NULL
               AND payment_status = ?
               AND (? = '' OR salary_date >= ?)
               AND (? = '' OR salary_date < DATE_ADD(?, INTERVAL 1 DAY))"
        );
        $oppositeStatus = $paymentStatus === 'Paid' ? 'Pending' : 'Paid';
        $query->bind_param(
            'sisssss',
            $paymentStatus,
            $id,
            $oppositeStatus,
            $fromDate,
            $fromDate,
            $toDate,
            $toDate
        );
    }

    $query->execute();
    echo json_encode([
        'status' => true,
        'data' => ['updated' => $query->affected_rows],
        'message' => $query->affected_rows . ' salary record(s) updated.'
    ]);
} catch (Throwable $error) {
    error_log('Salary payment status update failed: ' . $error->getMessage());
    http_response_code(500);
    echo json_encode([
        'status' => false,
        'message' => 'Salary payment status update failed: ' . $error->getMessage(),
        'error_code' => $error->getCode()
    ]);
}
?>
