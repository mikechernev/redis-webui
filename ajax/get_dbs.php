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

    $redis = new \Redis();
    $redis->pconnect('localhost');

    $dbs = array_flip(preg_grep("/db\d+/", array_keys($redis->info())));

    $selected_db = (isset($_COOKIE['selected_db']) && !empty($_COOKIE['selected_db'])) ? $_COOKIE['selected_db'] : 'db0';
    $dbs[$selected_db] = 'selected';
    $response['success'] = true;
    $response['result'] = $dbs;
    echo json_encode($response);
    exit;
}
