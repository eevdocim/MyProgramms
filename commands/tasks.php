<?php namespace api\tasks;
class Route{
	function get_tasks(){
		$ArrayMore = array();
		global $mysqli;
		if($bd = $mysqli->query("SELECT * FROM `tasks`")){
			$Array = mysqli_fetch_array($bd);
			do{
				$ArrayMore[] = array("id" => $Array["id"],"name" => $Array["name"],"description" => $Array["description"],"done" => $Array["done"],"date_created" => $Array["date_created"]);
			}
			while ($Array = mysqli_fetch_array($bd));
			echo json_encode($ArrayMore,JSON_UNESCAPED_UNICODE);
		}else{
			echo json_encode(array('Error' => 'Table not found'),JSON_UNESCAPED_UNICODE);
			exit;
		}
	}
	function get_task($id){
		global $mysqli;
		if($bd = $mysqli->query("SELECT * FROM `tasks` WHERE `id` = '$id'")){
			$Array2 = mysqli_fetch_assoc($bd);
			if($Array2 == null){
				echo json_encode(array('Error' => 'Task not found'),JSON_UNESCAPED_UNICODE);
			}
			else{
				if($bd = $mysqli->query("SELECT * FROM `binding` WHERE `id_tasks` = '$id'")){
					do{
						if($bdTags = $mysqli->query("SELECT * FROM `tags` WHERE `id` = '$Array[id_tags]'")){
							do{
								if($ArrayTags['name'] != null)
									$Array2['tags'][] = array($ArrayTags['id'] => $ArrayTags['name']);
							}
							while ($ArrayTags = mysqli_fetch_array($bdTags));
						}else{
							echo json_encode(array('Error' => 'Table tags not found'),JSON_UNESCAPED_UNICODE);
							exit;
						}
					}
					while ($Array = mysqli_fetch_array($bd));
					echo json_encode($Array2,JSON_UNESCAPED_UNICODE);
				}else{
					echo json_encode(array('Error' => 'Table binding not found'),JSON_UNESCAPED_UNICODE);
					exit;
				}
			}
		}else{
			echo json_encode(array('Error' => 'Table tasks not found'),JSON_UNESCAPED_UNICODE);
			exit;
		}
	}
	function create_task($data){
		global $mysqli;
		$date = date("Y-m-d");
		if($mysqli->query("INSERT INTO `tasks` (`id`, `name`, `description`, `done`, `date_created`) VALUES (NULL, '$data[name]', '$data[description]', '0', '$date')")){
			echo json_encode('Success');
		}else{
			echo json_encode(array('Error'=>'Record not created'));
		}
	}
	function update_task($id,$data){
		global $mysqli;
		if(isset($data['done'])){
			if($id != ''){
				if($mysqli->query("UPDATE `tasks` SET `done` = '$data[done]' WHERE `tasks`.`id` = '$id'")){
					echo json_encode('Success');
				}else{
					echo json_encode(array('Error'=>'Record not updated'));
				}
			}else{
				echo json_encode(array('Error'=>'id not found'));
			}
		}else{
			echo json_encode(array('Error'=>'Data not found'));
		}
	}
	function delete_task($id){
		global $mysqli;
		if($id != ''){
			if($mysqli->query("DELETE FROM `tasks` WHERE `tasks`.`id` = '$id'")){
				echo json_encode('Success');
			}else{
				echo json_encode(array('Error'=>'Record not delete'));
			}
		}else{
			echo json_encode(array('Error'=>'id not found'));
		}
	}
	function route($method,$id,$data){
		switch ($method) {
			case 'get':
				if($id == '')
					Route::get_tasks();
				else
					Route::get_task($id);
				break;
			case 'post':
				Route::create_task($data);
				break;
			case 'patch':
				Route::update_task($id,$data);
				break;
			case 'delete':
				Route::delete_task($id,$data);
				break;
			default:
				echo json_encode(array('Error' => 'Method not found'),JSON_UNESCAPED_UNICODE);
				break;
		}
	}
}
?>