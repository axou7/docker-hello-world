<?php
define( 'BDD_DRIVER', 'mysql' ) ;
define( 'BDD_SERVEUR', 'localhost' ) ;
define( 'BDD_USER', 'root' ) ;
define( 'BDD_PWD', '' ) ;
define( 'BDD', 'docker_tp' ) ;
define( 'BDD_PORT', 3306 ) ;
date_default_timezone_set('Europe/Paris');
try {
	if ( isset($_GET) && isset($_GET['rule']) ) {
		$rule = $_GET['rule'] ;
	} else {
		$rule = '' ;
	}
	$nombre = -1;
	$err = NULL;
	$status = 'ok';
	$day = date("Y-m-d");
	$filename = "./log/$day.csv";
	if (!file_exists($filename)) {
		file_put_contents ($filename,"date;heure;ordre;status;compteur;erreur\r\n");
	}
	
	$db = new PDO(BDD_DRIVER.':host='.BDD_SERVEUR.';port='.BDD_PORT.';dbname='.BDD,BDD_USER,BDD_PWD, array(1002 => 'SET NAMES utf8'));
	$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	$db->exec("SET CHARACTER SET utf8");		
	
	$req = "SELECT `nbGet` FROM compteur";
	$data = $db->prepare($req); 
	$data->execute();
	$results = $data->fetchAll(PDO::FETCH_ASSOC);
	if (!empty($results)) {
		$nombre = $results[0];
		$nombre = $nombre['nbGet'];
	} else {
		$err = 'SELECT ERROR' ;
		$status = 'ko';
	}
	
	
	if ($rule == 'Set') {
		$nombre++;

		$req = "UPDATE compteur SET `nbGet`=$nombre";
		$data = $db->prepare($req); 
		$data->execute();
		if ($data->rowCount() > 0) {
			echo json_encode(array('status' => $status, 'n' => '' . $nombre, 'err' => $err));
			file_put_contents ($filename,$day.';'.date("H:i:s").";SET;$status;$nombre;$err\r\n", FILE_APPEND);
		} else {
			$err = 'UPDATE ERROR' ;
			$status = 'ko';
		}
	} else if ($rule == 'Get') {
		echo json_encode(array('status' => $status, 'n' => $nombre, 'err' => $err));
		file_put_contents ($filename,$day.';'.date("H:i:s").";GET;$status;$nombre;$err\r\n", FILE_APPEND);
	}
} catch ( Exception $e ) {
	echo json_encode(array('status' => 'ko', 'n' => $nombre, 'err' => $e));
	file_put_contents ($filename,$day.';'.date("H:i:s").";UNK;ko;$nombre;$e\r\n", FILE_APPEND);
} 



?>