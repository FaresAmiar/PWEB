<?php 
//fichier modèle - utilisateurs
// fonction de requetes de la base de données pour les utilisateurs

function verif_connexion($login,$pass,&$profil,&$typeUtilisateur) {
    $typeUtilisateur = "utilisateur";
    require('./modele/connectBD.php'); //$pdo est défini dans ce fichier

    $resultat = array();
    $sql="SELECT * FROM `professeur`  where login_prof = :login and pass_prof=:pass";
    try {
        $commande = $pdo->prepare($sql);
        $commande->bindParam(':login', $login);
        $commande->bindParam(':pass', $pass);
        $bool = $commande->execute();
        if ($bool) {
            $resultat = $commande->fetch(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }
            if(is_array($resultat)){
                $typeUtilisateur = "professeur";
                echo("Type = Prof");
                //var_dump($resultat); die('arret requete');
                /*while ($ligne = $commande->fetch()) { // ligne par ligne
                    print_r($ligne);
                }*/
            }
            else {
                $sql="SELECT * FROM `etudiant`  where login_etu = :login and pass_etu=:pass";
                $commande = $pdo->prepare($sql);
                $commande->bindParam(':login', $login);
                $commande->bindParam(':pass', $pass);
                $bool = $commande->execute();
                if($bool) {
                    $resultat = $commande->fetch(PDO::FETCH_ASSOC);
                    $typeUtilisateur = "etudiant";
                }
            }
    }
    catch (PDOException $e) {
        echo utf8_encode("Echec de select : " . $e->getMessage() . "\n");
        die(); // On arrête tout.
    }

    if (is_array($resultat)) {
		//echo("Type");
        $profil = $resultat; //premier enregistrement
		$_SESSION['profil'] = $resultat;
        $_SESSION['profil']['type'] = $typeUtilisateur;
		/*echo("<pre>");
		print_r ($_SESSION['profil']);
		echo("</pre>");*/
        return true;
    }
    else {
        return false;
    }
}


?>