<? //header('Content-Type: application/json');
   function distance ($lat1, $lon1, $lat2, $lon2){
	   $R=6370000; //радиус земли
	   $lat1=deg2rad($lat1);
	   $lat2=deg2rad($lat2);
	   $lon1=deg2rad($lon1);
	   $lon2=deg2rad($lon2);
	   $L=$R*acos( sin($lat1)*sin($lat2) + cos($lat1)*cos($lat2)*cos($lon1-$lon2) );
	   return $L;
   }
   
   require_once ("base_defaults.php");
   require_once ("base.php");
   $base = new DB ($host, $user, $pass, $db);
		
   $method = $_SERVER['REQUEST_METHOD'];
   $formData = getFormData($method);
   
   function getFormData($method) {
 
    // GET или POST: данные возвращаем как есть
    if ($method === 'GET') return $_GET;
    if ($method === 'POST') return $_POST;
	
//	if ($method === "PUT"){
//		 $put_data =  fopen ("php://input", "r");
//		 return ($put_data);
//	}
	
 
    // PUT, PATCH или DELETE
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

$url = (isset($_GET['q'])) ? $_GET['q'] : '';
$url = rtrim($url, '/');
$urls = explode('/', $url);

$router=$urls[0];
$urlData = array_slice($urls, 1);
route($method, $router, $urlData, $formData, $base);
function route($method, $router, $urlData, $formData, $base){
	if ($router <> "points"){
		header('HTTP/1.0 400 Bad Request');
        echo json_encode(array(
        'error' => 'Bad Request'
        ));
    }
	else if ($method === 'GET' && count($urlData) === 0){
		$query="select * from `objects`";
		$result = $base -> executeQuery ($query);
		if ($result) {
            $i=0;
			while($line=mysql_fetch_array($result)){
			     $data[$i]= array ("id" => $line["id"], "name" => $line["name"], "point" => $line["point"]);
				 $i++;
			}
		    echo json_encode ($data);
		}
		else{
			echo json_encode(array(
            'error' => 'Empty table'
            ));	
		}
	}
	else if ($method === 'GET' && count($urlData) === 1){
		if ($urlData[0]==="round"){
			if (!$formData['center'] || !preg_match('/[0-9]*\.[0-9]+\,[0-9]*\.[0-9]+/', $formData["center"])){
               echo json_encode(array(
                  'error' => 'No or bad data: center'
               ));					
			}
			else if (!$formData['radius'] || !is_numeric($formData['radius'])){
               echo json_encode(array(
                  'error' => 'No or bad data: radius'
               ));					
			}
			else if (!$formData['points'] || !is_numeric($formData['points'])){
               echo json_encode(array(
                  'error' => 'No or bad data: points'
               ));					
			}
            else{
				$result=$base -> executeQuery("select * from `objects`");
				if (mysql_num_rows($result)>0){
					$data=array();$i=0;
					$coords1=explode(",", $formData["center"]);
					while ($line=mysql_fetch_array($result)){
					  $data[$i]= array ("id" => $line["id"], "name" => $line["name"], "point" => $line["point"], "distance" => 0);
					  $coords2=explode(",", $line["point"]);
					  $data[$i]["distance"] = distance ((double)$coords1[0], (double)$coords1[1], (double)$coords2[0], (double)$coords2[1]);					  
					  $i++;
					}
					$meters=array();
			        foreach ($data as $key => $row){
					    $meters[$key]=$row['distance'];
				    }
				    array_multisort($meters, SORT_ASC, $data);
				    if ((double)$data[0]['distance']>(double)$formData['radius']){
				        echo json_encode(array(
                           'error' => 'No points in radius'
                        ));
				    }
					else{
						$data1=array();
						$n1=count($data);
						$n2=(double)$formData['points'];
						$counter=0;
						while($counter<$n1 && count($data1)<$n2 && (double)$data[$counter]['distance']<(double)$formData['radius']){
                            
							$data1[$counter]=$data[$counter];
							$counter++;
						}
						echo json_encode ($data1);

					}

				}			
				else{
                   echo json_encode(array(
                      'error' => 'Empty table'
                   ));						
				}
				
			}			
		}
		else {
            echo json_encode(array(
               'error' => 'Bad Request'
            ));			
		}
	}
	
	else if ($method === 'DELETE' )
	{  
		if ( count($urlData)<>1 || !is_numeric($urlData[0])){
			echo json_encode(array(
                'error' => 'Bad id'
            ));	
		}
		else{
			$query="delete from objects where `id`=".$urlData[0];
			$base -> executeQuery ($query);
			if (mysql_affected_rows()>0){
				echo json_encode(array(
                    'ok' => 'deleted',
					'id' => $urlData[0]
                ));	
			}
			else {
			   echo json_encode(array(
               'error' => 'Not existing object'
               ));	
			}
		}
	}
	else if ($method === 'POST' && empty($urlData)){
		if ($formData["name"]==="" || !$formData["name"]){
			echo json_encode(array(
                'error' => 'Empty form:name'
            ));	
		}
		else if ($formData["coords"]==="" || !$formData["coords"]){
				echo json_encode(array(
                'error' => 'Empty form:coords'
            ));	
		}
		else if (!preg_match('/[0-9]*\.[0-9]+\,[0-9]*\.[0-9]+/', $formData["coords"])){
			echo json_encode(array(
                'error' => 'Wrong coords'
            ));	
		}
		else{
			$query="insert into `objects` (`name`,`point`) values ('".$formData["name"]."', '".$formData["coords"]."')";
			$base -> executeQuery ($query);
			if (mysql_affected_rows()>0){
				echo json_encode(array(
                    'ok' => 'created',
					'data' => json_encode($formData)
                ));	
				
			}
			else{
				echo json_encode(array(
                'error' => 'Can not write'
                ));	
			}
		}
	}
	else if ($method === 'POST' && count($urlData) === 1){
		if ( !is_numeric($urlData[0])){
			echo json_encode(array(
                'error' => 'Bad id'
            ));	
		}
        else if(($formData["name"]==="" || !$formData["name"]) && ($formData["coords"]==="" || !$formData["coords"])){
			    echo json_encode(array(
                    'error' => 'Empty form'
                ));					
			
		}
        else if	($formData["coords"] && $formData["coords"]<>"" && !preg_match('/[0-9]*\.[0-9]+\,[0-9]*\.[0-9]+/', $formData["coords"])	){
			    echo json_encode(array(
                    'error' => 'Wrong coords'
                ));				
		}
		else {
			$result=$base -> executeQuery("select * from `objects` where `id`=".$urlData[0]);
			if (mysql_num_rows($result)){
			    $query = "update `objects` set";
			    $first=false;
			    if ($formData["name"] && $formData["name"]<>"") {$query.=" `name`='".$formData["name"]."'"; $first=true;}
		     	if ($formData["coords"] && $formData["coords"]<>"") {
				    if ($first) $query.=",";
				    $query.=" `point`='".$formData["coords"]."'";
			    }
			    $query.=" where `id`=".$urlData[0];
			    $base -> executeQuery ($query);
			    if (mysql_affected_rows()>0){
				    echo json_encode(array(
                        'ok' => 'changed',
					    'id' => $urlData[0],
					    'data' => json_encode($formData)
                    ));	
			    }
			    else{
				    echo json_encode(array(
                        'error' => 'Can not write or no changes'
                    ));	
			    }
			}
			else {
				  echo json_encode(array(
                        'error' => 'Not existing object'
                  ));	
			}
		}
	}
}

?>
