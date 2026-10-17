
<?php
    include '../connection.php';
    include '../auth_check.php';

    $id = $_GET['id'];
    $res = [];

    if (isset($_POST['worker_id']) && !empty($_POST['worker_id'])) {
        $res = $cc->common_update('salary', $_POST, ['id' => $id]);
    } else {
        $res['status'] = false;
        $res['message'] = 'Salary data is not updated';
    }

    echo json_encode($res);
?>
