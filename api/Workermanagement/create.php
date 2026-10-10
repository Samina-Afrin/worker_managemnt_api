<?php
    include '../connection.php';
    $data = json_decode(file_get_contents('php://input'), true);
    $res=[];
    if($data['name']){
        $worker['name'] = $data['name'];
        $worker['email'] = $data['email'];
        $worker['phone'] = $data['phone'];
        $worker['address'] = $data['address'];
        $worker['position'] = $data['position'];
        $worker['salary'] = $data['salary'];
        $res=$cc->common_insert('Workers', $worker);

        $user['email'] = $data['email'];
        $user["name"] = $data['name'];
        $user['username'] = $data['email'];
        $user['role'] = 'worker';
        $user['is_active'] = 1;
        $user['created_at'] = date('Y-m-d H:i:s');
        $user['password']= md5($data['password']);

        $res=$cc->common_insert('Users', $user);
        if($res['status']==true){
            $workers['user_id'] = $res['data'];
            $res = $cc->common_insert('Workers', $workers);
        }
    } else {
        $res['status'] = false;
        $res['message'] = 'data is not saved';
    }
echo json_encode($res);