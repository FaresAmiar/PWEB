<?php 
//fichier de controle - services de gestion des utilisateurs
// les fonctions d'un fichier de contrôle sont appelées des actions

function connexion() {
	
	$_SESSION['profil'] = '';
	
	$login= isset($_POST['login'])?($_POST['login']):'';
	$pass= isset($_POST['pass'])?($_POST['pass']):'';
	$msg='';
	$url='index.php?controle=utilisateur&action=connexion';
	if  (count($_POST)==0) {
			require ("./vue/utilisateur/connexion.tpl") ;
	}
	else {
		require ("./modele/utilisateurBD.php") ;		
		$profil= array();
		if  (! verif_connexion($login,$pass,$profil,$typeUtilisateur)) {
			$msg ="erreur de saisie";
			require ("./vue/utilisateur/connexion.tpl") ;
		}
		else { 
            $url = 'index.php?controle=';
            $url.= $typeUtilisateur;
            $url.= '&action=accueil';
		header ("Location:" . $url);
        exit();
		}
	}	
}

function deconnexion (){
    session_destroy();
    require("./vue/utilisateur/connexion.tpl");
    exit;
}
?>