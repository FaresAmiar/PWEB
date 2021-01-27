<?php
		
    //Connexion IUT
	/*$hostname = "localhost";
	$base= "pweb19_amiar";
	$loginBD= "pweb19_amiar";
	$passBD="x10122000";*/
    

	//Connexion pc portable
	$hostname = "localhost";
    $base= "pweb19_amiar";
    $loginBD= "root";
	$passBD="";



try {
	$pdo = new PDO ("mysql:server=$hostname; dbname=$base", "$loginBD", "$passBD");
	$pdo->exec('SET NAMES utf8');
}

catch (PDOException $e) {
	die  ("Echec de connexion : " . $e->getMessage() . "\n");
}
