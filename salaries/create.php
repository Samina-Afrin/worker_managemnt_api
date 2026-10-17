
<?php
    include '../connection.php';
    include '../auth_check.php';

    $res = [];

    if (isset($_POST['worker_id']) && !empty($_POST['worker_id'])) {
        $res = $cc->common_insert('salary', $_POST);
    } else {
        $res['status'] = false;
        $res['message'] = 'Salary data is not saved';
    }

    echo json_encode($res);
?>
