<?php

function accueil() {
    $nom = $_SESSION['profil']['nom'] ;

    require("./vue/layout/layout.tpl");
}

function testActif() {
	require ('./modele/etudiantBD.php');
	if(!isset($_SESSION['profil']['testrep']))
		$_SESSION['profil']['testrep'] = array();
	
	$gpetu = $_SESSION['profil']['num_grpe'];
	$tests =lire_test_actif($gpetu);
	require("./vue/layout/layout.tpl");		
}

function liste_quest(){	
	require ('./modele/etudiantBD.php');
	if(!isset($_SESSION['profil']['bactif']))
		$_SESSION['profil']['bactif'] = 1;
	if(!isset($_SESSION['profil']['test']))
		$_SESSION['profil']['test'] = $_GET['numtest'];
	if(!isset($_SESSION['profil']['note'])){
		$_SESSION['profil']['note'] = 0;
		$_SESSION['profil']['qrep'] = array();
	}
	
	$quest = lire_quest_test($_SESSION['profil']['test']);


	if(isset($_POST['reponse'])) {
		if (verif_bonnes_reponses($_SESSION['profil']['quest'],$_POST['reponse'],$_SESSION['profil']['bmultiple'])){
            $_SESSION['profil']['note'] += 1;
        }
        array_push($_SESSION['profil']['qrep'], $_SESSION['profil']['quest']);
        
	}

	require("./vue/layout/layout.tpl");	
}

function liste_reponses(){
    require('./modele/etudiantBD.php');
    $idquest = $_GET["numquest"];
    $_SESSION['profil']['quest'] = $idquest;
    $rep = liste_reponses_quest($idquest);
    require("./vue/layout/layout.tpl");
}

function bilan(){
	require('./modele/etudiantBD.php'); 
	if(!isset($_SESSION['profil']['fintest']))
		$_SESSION['profil']['fintest'] = false;
	if($_SESSION['profil']['fintest']){
		ajoutbilan();
		array_push($_SESSION['profil']['testrep'],$_SESSION['profil']['test']);
		unset($_SESSION['profil']['test']);
		unset($_SESSION['profil']['qrep']);
		unset($_SESSION['profil']['quest']);
		unset($_SESSION['profil']['bmultiple']);
		unset($_SESSION['profil']['note']);
		$_SESSION['profil']['fintest'] = false;
	}
	$bilan = afficherbilan();
	require("./vue/layout/layout.tpl");
}

?>