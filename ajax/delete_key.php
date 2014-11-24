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
	$redis = new \Redis();
	$redis->pconnect('localhost');

	$response['result'] = $redis->delete($key);

	$response['success'] = TRUE;
	echo json_encode($response);
	exit;
}