<?php
if(isset($_SERVER['HTTP_X_REQUESTED_WITH'])
	&& ! empty($_SERVER['HTTP_X_REQUESTED_WITH'])
	&& strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
{
	header('Content-Type: application/json');

	$response = array(
		'success' => FALSE,
	);
	if ( ! isset($_POST['db']) || empty($_POST['db']))
	{
		$response['error'] = 'No DB sent!';
		echo json_encode($response);
		exit;
	}
	setcookie("selected_db", $_POST['db'], time() + 60*60*24*365);
	$response['success'] = TRUE;
	echo json_encode($response);
	exit;
}