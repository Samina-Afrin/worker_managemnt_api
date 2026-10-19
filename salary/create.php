<?php
include '../connection.php';
include '../auth_check.php';
require_once 'salary_helpers.php';

header('Content-Type: application/json; charset=UTF-8');

$fromDate = $_POST['from_date'] ?? '';
$toDate = $_POST['to_date'] ?? '';
$paymentStatus = $_POST['payment_status'] ?? 'Pending';

if (!validate_salary_dates($fromDate, $toDate)) {
    http_response_code(400);
    echo json_encode([
        'status' => false,
        'message' => 'A valid from_date and to_date are required, and from_date must not be after to_date.'
    ]);
    exit;
}

if (!in_array($paymentStatus, ['Paid', 'Pending'], true)) {
    http_response_code(400);
    echo json_encode([
        'status' => false,
        'message' => 'payment_status must be Paid or Pending.'
    ]);
    exit;
}

try {
    $stage = 'starting transaction';
    $cc->conn->begin_transaction();

    $stage = 'loading eligible attendance';
    $workers = get_salary_preview($cc->conn, $fromDate, $toDate);

    if (count($workers) === 0) {
        $cc->conn->rollback();
        http_response_code(409);
        echo json_encode([
            'status' => false,
            'message' => 'No eligible present days are available in this date range.'
        ]);
        exit;
    }

    $insert = $cc->conn->prepare(
        "INSERT INTO salary
         (worker_id, salary_date, daily_salary, month, year, basic_salary,
          present_days, absent_days, deduction, net_salary, payment_status)
         VALUES (?, ?, ?, ?, ?, ?, 1, 0, 0, ?, ?)"
    );

    $inserted = 0;
    foreach ($workers as $worker) {
        foreach ($worker['attendance_dates'] as $attendance) {
            $stage = 'inserting salary for worker ' . $worker['worker_id'] .
                ' on ' . $attendance['date'];
            $workerId = (int)$worker['worker_id'];
            $salaryDate = $attendance['date'];
            $dailySalary = (float)$attendance['daily_salary'];
            $month = (int)$attendance['month'];
            $year = (int)$attendance['year'];
            $monthlySalary = (float)$worker['basic_salary'];

            $insert->bind_param(
                'isdiidds',
                $workerId,
                $salaryDate,
                $dailySalary,
                $month,
                $year,
                $monthlySalary,
                $dailySalary,
                $paymentStatus
            );
            $insert->execute();
            $inserted++;
        }
    }

    $stage = 'committing salary records';
    $cc->conn->commit();
    echo json_encode([
        'status' => true,
        'data' => ['records_created' => $inserted],
        'message' => $inserted . ' daily salary record(s) saved successfully.'
    ]);
} catch (Throwable $error) {
    $cc->conn->rollback();
    error_log(
        'Daily salary creation failed while ' . ($stage ?? 'processing') .
        ': ' . $error->getMessage()
    );
    http_response_code($error instanceof mysqli_sql_exception && $error->getCode() === 1062 ? 409 : 500);
    echo json_encode([
        'status' => false,
        'message' => 'Daily salary save failed while ' . ($stage ?? 'processing') .
            ': ' . $error->getMessage(),
        'error' => $error->getMessage(),
        'error_code' => $error->getCode()
    ]);
}
?>
