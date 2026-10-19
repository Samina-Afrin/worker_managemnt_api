<?php

function validate_salary_dates($fromDate, $toDate)
{
    if (!is_string($fromDate) || !is_string($toDate)) {
        return false;
    }

    $from = DateTime::createFromFormat('!Y-m-d', $fromDate);
    $to = DateTime::createFromFormat('!Y-m-d', $toDate);

    return $from &&
        $to &&
        $from->format('Y-m-d') === $fromDate &&
        $to->format('Y-m-d') === $toDate &&
        $fromDate <= $toDate;
}

function get_salary_preview($conn, $fromDate, $toDate)
{
    $attendance = $conn->prepare(
        "SELECT DISTINCT a.worker_id, w.name AS worker_name, w.basic_salary,
                DATE_FORMAT(a.date, '%Y-%m-%d') AS work_date
         FROM attendances a
         INNER JOIN workers w ON w.id = a.worker_id
         WHERE LOWER(a.status) = 'present'
           AND a.date >= ? AND a.date < DATE_ADD(?, INTERVAL 1 DAY)
           AND a.deleted_at IS NULL
         ORDER BY w.name, a.date"
    );
    $attendance->bind_param('ss', $fromDate, $toDate);
    $attendance->execute();
    $attendanceRows = $attendance->get_result();

    $salaryRows = $conn->prepare(
        "SELECT worker_id, salary_date, month, year
         FROM salary
         WHERE (salary_date BETWEEN ? AND ? OR salary_date IS NULL)
           AND deleted_at IS NULL"
    );
    $salaryRows->bind_param('ss', $fromDate, $toDate);
    $salaryRows->execute();
    $existingRows = $salaryRows->get_result();

    $alreadyRecorded = [];
    while ($row = $existingRows->fetch_assoc()) {
        if ($row['salary_date']) {
            $alreadyRecorded[$row['worker_id'] . ':' . $row['salary_date']] = true;
        } else {
            $alreadyRecorded[
                $row['worker_id'] . ':' .
                sprintf('%04d-%02d', $row['year'], $row['month'])
            ] = true;
        }
    }

    $workers = [];
    while ($row = $attendanceRows->fetch_assoc()) {
        $workerId = (string)$row['worker_id'];
        $workDate = $row['work_date'];
        if (
            isset($alreadyRecorded[$workerId . ':' . $workDate]) ||
            isset($alreadyRecorded[$workerId . ':' . substr($workDate, 0, 7)])
        ) {
            continue;
        }

        $monthlySalary = (float)$row['basic_salary'];
        $daysInMonth = (int)date('t', strtotime($workDate));
        $dailySalary = round($monthlySalary / $daysInMonth, 2);
        $dateParts = explode('-', $workDate);
        if (!isset($workers[$workerId])) {
            $workers[$workerId] = [
                'worker_id' => $workerId,
                'worker_name' => $row['worker_name'],
                'basic_salary' => number_format($monthlySalary, 2, '.', ''),
                'present_days' => 0,
                'net_salary' => 0,
                'attendance_dates' => []
            ];
        }

        $workers[$workerId]['present_days']++;
        $workers[$workerId]['net_salary'] += $dailySalary;
        $workers[$workerId]['attendance_dates'][] = [
            'date' => $workDate,
            'daily_salary' => number_format($dailySalary, 2, '.', ''),
            'month' => (int)$dateParts[1],
            'year' => (int)$dateParts[0]
        ];
    }

    foreach ($workers as &$worker) {
        $worker['net_salary'] = number_format($worker['net_salary'], 2, '.', '');
    }
    unset($worker);

    return array_values($workers);
}

?>
