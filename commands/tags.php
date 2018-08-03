<?php namespace api\tags;
class Route{
	function get_tags(){
		$ArrayMore = array();
		global $mysqli;
		if($bd = $mysqli->query("SELECT * FROM `tags`")){
			$Array = mysqli_fetch_array($bd);
			do{
				$ArrayMore[] = array("id" => $Array["id"],"name" => $Array["name"],"date_created" => $Array["date_created"]);
			}
			while ($Array = mysqli_fetch_array($bd));
			echo json_encode($ArrayMore,JSON_UNESCAPED_UNICODE);
		}else{
			echo json_encode(array('Error' => 'Table not found'),JSON_UNESCAPED_UNICODE);
			exit;
		}
	}
	function get_tag($id){
		global $mysqli;
		if($bd = $mysqli->query("SELECT * FROM `tags` WHERE `id` = '$id'")){
			$Array2 = mysqli_fetch_assoc($bd);
			if($Array2 == null){
				echo json_encode(array('Error' => 'Tags not found'),JSON_UNESCAPED_UNICODE);
			}
			else{
				if($bd = $mysqli->query("SELECT * FROM `binding` WHERE `id_tags` = '$id'")){
					do{
						if($bdTags = $mysqli->query("SELECT * FROM `tasks` WHERE `id` = '$Array[id_tasks]'")){
							do{
								if($ArrayTags['name'] != null)
									$Array2['tasks'][] = array($ArrayTags['id'] => $ArrayTags['name']);
							}
							while ($ArrayTags = mysqli_fetch_array($bdTags));
						}else{
							echo json_encode(array('Error' => 'Table tasks not found'),JSON_UNESCAPED_UNICODE);
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
			echo json_encode(array('Error' => 'Table tags not found'),JSON_UNESCAPED_UNICODE);
			exit;
		}
	}
	function create_tag($data){
		global $mysqli;
		$date = date("Y-m-d");
		if($mysqli->query("INSERT INTO `tags` (`id`, `name`, `date_created`) VALUES (NULL, '$data[name]', '$date')")){
			echo json_encode('Success');
		}else{
			echo json_encode(array('Error'=>'Record not created'));
		}
	}
	function delete_tag($id){
		global $mysqli;
		if($id != ''){
			if($mysqli->query("DELETE FROM `tags` WHERE `tags`.`id` = '$id'")){
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
					Route::get_tags();
				else
					Route::get_tag($id);
				break;
			case 'post':
				Route::create_tag($data);
				break;
			case 'delete':
				Route::delete_tag($id,$data);
				break;
			default:
				echo json_encode(array('Error' => 'Method not found'),JSON_UNESCAPED_UNICODE);
				break;
		}
	}
}
?>
