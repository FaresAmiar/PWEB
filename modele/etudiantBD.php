<?php

function lire_test_actif($gpetu){ 
	require ("modele/connectBD.php") ;
	$sql="select *
		  from test 
		  where num_grpe = :gpetu
		  and bactif=1
		  limit 0,30";
	$resultat= array(); 
	
	try {
		$commande = $pdo->prepare($sql);
		$commande->bindParam(':gpetu', $gpetu);
		$bool = $commande->execute();
		if ($bool) {
			$resultat = $commande->fetchAll(PDO::FETCH_ASSOC);
			}
		}
	catch (PDOException $e) {
		$msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
		die($msg); 
	}
	
	return $resultat;
	
}


function lire_quest_test($idtest){	
	require ("modele/connectBD.php") ;
	$sql="select *
		  from question as q INNER JOIN qcm as m ON q.id_quest = m.id_quest
		  where m.id_test = :idtest
		  and bautorise=1
		  limit 0,30";
	$resultat= array();

	try {
		$commande = $pdo->prepare($sql);
		$commande->bindParam(':idtest', $idtest);
		$bool = $commande->execute();
		if ($bool) {
			$resultat = $commande->fetchAll(PDO::FETCH_ASSOC);
			}
		}
	catch (PDOException $e) {
		$msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
		die($msg); 
	}
	
	try {
        $commande = $pdo->prepare('SELECT bactif FROM test WHERE id_test = :idtest');
        $commande->bindParam(':idtest', $_SESSION['profil']['test']);
        $bool = $commande->execute();

        if ($bool) {
            $bactif = $commande->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    catch (PDOException $e) {
        $msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
        die($msg); 
    }
	
	$_SESSION['profil']['bactif'] = $bactif['0']['bactif'];
	
	return $resultat;
}

function liste_reponses_quest($idquest){
    require ("modele/connectBD.php");
    $rep = array();

    try {
        $commande = $pdo->prepare('SELECT texte_rep, id_rep FROM reponse WHERE id_quest = :idquest');
        $commande->bindParam(':idquest', $idquest);
        $bool = $commande->execute();

        if ($bool) {
            $rep = $commande->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    catch (PDOException $e) {
        $msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
        die($msg); 
    }
	
	
    return $rep;


}

function verif_bonnes_reponses($idquest,$idrep,$bmult) {
	require ("modele/connectBD.php");
    $brep = array();
    echo $idquest;

    try {
        $commande = $pdo->prepare('SELECT id_rep FROM reponse WHERE id_quest = :idquest AND bvalide = 1');
        $commande->bindParam(':idquest', $idquest);
        $bool = $commande->execute();

        if ($bool) {
            $brep = $commande->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    catch (PDOException $e) {
        $msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
        die($msg);
    }
    foreach($idrep as $r){
        try {
            $commande = $pdo->prepare('INSERT INTO resultat (id_test, id_etu, id_quest, date_res, id_rep) VALUES (:idtest,:idetu,:idquest,CURDATE(), :idrep)');
            $commande->bindParam(':idtest', $_SESSION['profil']['test']);
            $commande->bindParam(':idetu', $_SESSION['profil']['id_etu']);
            $commande->bindParam(':idquest', $idquest);
            $commande->bindParam(':idrep', $r);
            $bool = $commande->execute();
        }
        catch (PDOException $e) {
            $msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
            die($msg);
        }
    }
    if($bmult){
        $t = array();
        $i =0;
        foreach($brep as $b){
            $t[$i] = $b['id_rep'];
            ++$i;
        }
        return array_diff($t, $idrep) === array_diff($idrep, $t);
    }
    else
        return (int)$brep[0]['id_rep'] == (int)$idrep[0];
}

function ajoutbilan(){
	require ("modele/connectBD.php");
	try {
        $commande = $pdo->prepare('INSERT INTO bilan (id_test, id_etu, note_test, date_bilan) VALUES (:idtest,:idetu,:note_test,CURDATE())');
        $commande->bindParam(':idtest', $_SESSION['profil']['test']);
        $commande->bindParam(':idetu', $_SESSION['profil']['id_etu']);
        $commande->bindParam(':note_test', $_SESSION['profil']['note']);
 		$bool = $commande->execute();
    }
    catch (PDOException $e) {
        $msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
        die($msg);
    }
}

function afficherbilan(){
	require ("modele/connectBD.php");
    $bilan = array();

    try {
        $commande = $pdo->prepare('SELECT titre_test, note_test, date_bilan FROM bilan B INNER JOIN test T ON B.id_test = T.id_test WHERE id_etu = :idetu');
        $commande->bindParam(':idetu', $_SESSION['profil']['id_etu']);
        $bool = $commande->execute();

        if ($bool) {
            $bilan = $commande->fetchAll(PDO::FETCH_ASSOC);
        }
    }
    catch (PDOException $e) {
        $msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
        die($msg);
    }

    return $bilan;

}


?>