<?php
if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
    && ! empty($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
) {
    header('Content-Type: application/json');

    $response = array(
    'success' => false,
    );

    if (! class_exists('\Redis')) {
        $response['error'] = 'The Redis PHP extention is required!';
        echo json_encode($response);
        exit;
    }

    $redis_pattern = $_POST['pattern'];
    $redis = new \Redis();
    $redis->pconnect('localhost');
    $selected_db = (isset($_COOKIE['selected_db']) && ! empty($_COOKIE['selected_db'])) ? $_COOKIE['selected_db'] : 'db0';
    $redis->select(trim($selected_db, 'db'));

    $response['result'] = $redis->keys("*{$redis_pattern}*");

    $response['success'] = true;
    echo json_encode($response);
    exit;
}