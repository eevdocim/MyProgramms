<?php
namespace api\v1;
$link = mysqli_connect('localhost','root','','restapi');
$method = $_SERVER['REQUEST_METHOD'];
$url = $_SERVER['REQUEST_URI'];
$url = trim($url, '/');
list($met, $id) = explode('/', $url);
switch ($method) {
	case 'get':
	case 'GET':
		switch ($met) {
			case 'tasks':
				if($id == ""){
					$ArrayMore = array();
					$bd = mysqli_query($link,"SELECT * FROM `tasks`");
					$Array = mysqli_fetch_array($bd);
					do{
						$ArrayMore[] = array("id" => $Array["id"],"name" => $Array["name"],"description" => $Array["description"],"done" => $Array["done"],"date_created" => $Array["date_created"]);
					}
					while ($Array = mysqli_fetch_array($bd));
					echo json_encode($ArrayMore,JSON_UNESCAPED_UNICODE);
				}else{
					$bd = mysqli_query($link,"SELECT * FROM `tasks` WHERE `id` = '$id'");
					$Array2 = mysqli_fetch_assoc($bd);
					if($Array2 == null){
						echo json_encode("Not Found",JSON_UNESCAPED_UNICODE);
					}
					else{
						$bd = mysqli_query($link,"SELECT * FROM `binding` WHERE `id_tasks` = '$id'");
						do{
							$bdTags = mysqli_query($link,"SELECT * FROM `tags` WHERE `id` = '$Array[id_tags]'");
							do{
								if($ArrayTags['name'] != null)
									$Array2[] = array('tag' => $ArrayTags['name']);
							}
							while ($ArrayTags = mysqli_fetch_array($bdTags));
						}
						while ($Array = mysqli_fetch_array($bd));
						echo json_encode($Array2,JSON_UNESCAPED_UNICODE);
					}

				}
				break;
			case 'tags':
				if($id == ""){
					$ArrayMore = array();
					$bd = mysqli_query($link,"SELECT * FROM `tags`");
					$Array = mysqli_fetch_array($bd);
					do{
						$ArrayMore[] = array("id" => $Array["id"],"name" => $Array["name"],"date_created" => $Array["date_created"]);
					}
					while ($Array = mysqli_fetch_array($bd));
					echo json_encode($ArrayMore,JSON_UNESCAPED_UNICODE);
				}else{
					$bd = mysqli_query($link,"SELECT * FROM `tags` WHERE `id`='$id'");
					$Array2 = mysqli_fetch_assoc($bd);
					if($Array2 == null)
						echo json_encode("Not Found",JSON_UNESCAPED_UNICODE);
					else
						$bd = mysqli_query($link,"SELECT * FROM `binding` WHERE `id_tags` = '$id'");
						do{
							$bdTasks = mysqli_query($link,"SELECT * FROM `tasks` WHERE `id` = '$Array[id_tasks]'");
							do{
								if($ArrayTasks['name'] != null)
									$Array2[] = array('tasks' => $ArrayTasks['name']);
							}
							while ($ArrayTasks = mysqli_fetch_array($bdTasks));
						}
						while ($Array = mysqli_fetch_array($bd));
						echo json_encode($Array2,JSON_UNESCAPED_UNICODE);
				}
				break;
			
			default:
				echo json_encode("1");
				break;
		}
		break;
	case 'post':
	case 'POST':
		switch ($met) {
				case 'tasks':
					$formData = getFormData($method);
					$date = date("Y-m-d");
					if(mysqli_query($link,"INSERT INTO `tasks` (`id`, `name`, `description`, `done`, `date_created`) VALUES (NULL, '$formData[name]', '$formData[description]', '0', '$date')")){
						echo json_encode("0");
					}else{
						echo json_encode("1");
					}
					break;
				case 'tags':
					$formData = getFormData($method);
					$date = date("Y-m-d");
					if(mysqli_query($link,"INSERT INTO `tags` (`id`, `name`, `date_created`) VALUES (NULL, '$formData[name]', '$date')")){
						echo json_encode("0");
					}else{
						echo json_encode("1");
					}
					break;
				default:
					echo json_encode("1");
					break;
			}
		break;
	case 'patch':
	case 'PATCH':
		switch ($met) {
				case 'tasks':
					$formData = getFormData($method);
					if(isset($formData['done'])){
						if($id != ''){
							if(mysqli_query($link,"UPDATE `tasks` SET `done` = '$formData[done]' WHERE `tasks`.`id` = '$id'")){
								echo json_encode("0");
							}else{
								echo json_encode("1");
							}
						}
					}
					break;
				default:
					echo json_encode("1");
					break;
			}
			break;
	case 'delete':
	case 'DELETE':
		switch ($met) {
				case 'tasks':
					if($id != ''){
						if(mysqli_query($link,"DELETE FROM `tasks` WHERE `tasks`.`id` = '$id'")){
							echo json_encode("0");
						}else{
							echo json_encode("1");
						}
					}
					break;
				case 'tags':
					if($id != ''){
						if(mysqli_query($link,"DELETE FROM `tags` WHERE `tags`.`id` = '$id'")){
							echo json_encode("0");
						}else{
							echo json_encode("1");
						}
					}
					break;
				default:
					echo json_encode("1");
					break;
			}
		break;

	default:
		echo json_encode("1");
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