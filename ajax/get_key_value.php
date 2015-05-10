<?php

if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])
    && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
    && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
) {
    header('Content-Type: application/json');

    $response = [
        'success' => false,
    ];

    if (!class_exists('\Redis')) {
        $response['error'] = 'The Redis PHP extention is required!';
        echo json_encode($response);
        exit;
    }

    $key = $_POST['key'];
    if ($key != '0' && empty($key)) {
        $response['error'] = 'Redis key is required!';
        echo json_encode($response);
        exit;
    }

    $redis = new \Redis();
    $redis->pconnect('localhost');
    $selected_db = (isset($_COOKIE['selected_db']) && !empty($_COOKIE['selected_db'])) ? $_COOKIE['selected_db'] : 'db0';
    $redis->select(trim($selected_db, 'db'));

    switch ($redis->type($key)) {
    case Redis::REDIS_STRING:
        $response['result'] = $redis->get($key);
        break;

    case Redis::REDIS_SET:
        $response['result'] = $redis->sMembers($key);
        break;

    case Redis::REDIS_LIST:
        $response['result'] = $redis->lRange($key, 0, -1);
        break;

    case Redis::REDIS_ZSET:
        $response['result'] = $redis->zRange($key, 0, -1);
        break;

    case Redis::REDIS_HASH:
        $response['result'] = $redis->hGetAll($key);
        break;

    default:
        $response['result'] = false;
        break;
    }

    $response['success'] = true;
    echo json_encode($response);
    exit;
}
