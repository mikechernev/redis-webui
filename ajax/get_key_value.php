<?php
if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])
	&& ! empty($_SERVER['HTTP_X_REQUESTED_WITH'])
	&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{
	header('Content-Type: application/json');

	$response = array(
		'success' => FALSE,
	);

	if ( ! class_exists('\Redis'))
	{
		$response['error'] = 'The Redis PHP extention is required!';
		echo json_encode($response);
		exit;
	}

	$key = $_POST['key'];
	if ($key != '0' && empty($key))
	{
		$response['error'] = 'Redis key is required!';
		echo json_encode($response);
		exit;
	}

	$redis = new \Redis();
	$redis->pconnect('localhost');
	switch ($redis->type($key))
	{
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
			$response['result'] = FALSE;
			break;
	}

	$response['success'] = TRUE;
	echo json_encode($response);
	exit;
}