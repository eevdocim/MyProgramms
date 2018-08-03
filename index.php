<?php namespace api;
include 'config/connect.php';
$method = $_SERVER['REQUEST_METHOD'];
$url = $_SERVER['REQUEST_URI'];
$method = mb_strtolower($method);
$url = trim($url, '/');
list($func, $id) = explode('/', $url);
$func = mb_strtolower($func);
$data = getFormData($method);
switch ($func) {
	case 'tasks':
		include 'commands/tasks.php';
		echo tasks\Route::route($method,$id,$data);
		break;
	case 'tags':
		include 'commands/tags.php';
		echo tags\Route::route($method,$id,$data);
		break;
	default:
		echo json_encode(array('Error'=> 'Function not found'));
		break;
}
function getFormData($method) {
	if ($method === 'GET') return $_GET;
	if ($method === 'POST') return $_POST;
	$data = array();
	$exploded = explode('&', file_get_contents('php://input'));
	foreach($exploded as $pair) {
		$item = explode('=', $pair);
		if (count($item) == 2) {
			$data[urldecode($item[0])] = urldecode($item[1]);
		}
	}
	return $data;
}
?>