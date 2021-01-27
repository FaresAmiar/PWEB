<?php

function selectionnerTestBD(){
    require ("./modele/connectBD.php") ;
    $tests = [];
    $sql = " select * from test where id_prof = :id order by id_test ";

    try {
        $commande = $pdo->prepare($sql);
        $commande->bindParam(':id',$_SESSION['profil']['id_prof']);
        $bool = $commande->execute();
        if ($bool)
            $tests = $commande->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
    }
    catch (PDOException $e) {
        $msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
        die($msg); // On arrête tout.
    }
    return $tests;
}

function choisirThemesBD() {
    require ("./modele/connectBD.php") ;

    $idtest = $_SESSION['profil']['id_test'];
    //Update pour mettre en actif le test selectionné
    $sql = "update test set bActif = 1 where id_test = :idtest";
    $commande = $pdo->prepare($sql);
    $commande->bindParam(':idtest',$idtest);
    $commande->execute();

    //Recherche des questions du test actif
    $sql = " select distinct t.id_theme, t.titre_theme from (qcm qc inner join question q on qc.id_quest = q.id_quest) inner join theme t on t.id_theme
    = q.id_theme where id_test = :idtest";

    try {
        $commande = $pdo->prepare($sql);
        $commande->bindParam(':idtest', $idtest);
        $bool = $commande->execute();
        if ($bool)
        $tabthemes = $commande->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
    }
    catch (PDOException $e) {
        $msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
        die($msg); // On arrête tout.
    }

    $sql = "select num_grpe from test where id_test = :idtest";

    try {
        $commande = $pdo->prepare($sql);
        $commande->bindParam(':idtest', $idtest);
        $bool = $commande->execute();
        if ($bool)
        $groupe = $commande->fetch(PDO::FETCH_ASSOC); //tableau d'enregistrements
    }
    catch (PDOException $e) {
        $msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
        die($msg); // On arrête tout.
    }

    $_SESSION['profil']['groupe'] = $groupe['num_grpe'];

    return $tabthemes;
}

//Selection des question a partir des themes choisis pour un test
function choisirQuestionsBD() {
    require ("./modele/connectBD.php") ;
    $cpt = 0;
    $questions = array();
    $themesChoisis = $_SESSION['profil']['idthemes'];
    $idtest = $_SESSION['profil']['id_test'];
    foreach($themesChoisis as $themeChoisi) {
        $sql = " select * from question q inner join qcm qc on q.id_quest = qc.id_quest where qc.id_test = :idtest and q.id_theme = :idtheme  ";

        //var_dump($_SESSION['profil']);
        try {
            $commande = $pdo->prepare($sql);
            $commande->bindParam(':idtest', $idtest);
            $commande->bindParam(':idtheme', $themeChoisi);
            $bool = $commande->execute();
            if ($bool) {
                $questions[$cpt] = $commande->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
            }
        } catch (PDOException $e) {
            $msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
            die($msg); // On arrête tout.
        }
        $cpt++;
    }

    return $questions;
}

function demarrerTestBD() {

    require ("./modele/connectBD.php") ;
    $cpt = 0;
    $idthemes = $_SESSION['profil']['idthemes'];
    
    $idquestions = $_SESSION['profil']['idquestions'];
    $sql = "update qcm set bAutorise = 1, bAnnule = 0, bBloque = 1 where id_quest in (:tab)";
    
    $tab = implode(',',array_fill(0,count($idquestions),'?'));

    $commande = $pdo->prepare($sql);
    $commande->bindParam(':tab', $tab);
    $bool = $commande->execute();
    

    foreach($idthemes as $idtheme) {
        $sql = " select * from theme where id_theme = :idtheme";

        try {
            $commande = $pdo->prepare($sql);
            $commande->bindParam(':idtheme', $idtheme);
            $bool = $commande->execute();
            if ($bool) {
                $themes[$cpt] = $commande->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
            }
        } catch (PDOException $e) {
            $msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
            die($msg); // On arrête tout.
        }
        $cpt++;
    }

    $cpt = 0;

    foreach($idquestions as $idquestion) {
        $sql = " select * from question where id_quest = :idquestion  ";

        try {
            $commande = $pdo->prepare($sql);
            $commande->bindParam(':idquestion', $idquestion);
            $bool = $commande->execute();
            if ($bool) {
                $questions[$cpt] = $commande->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
            }
        } catch (PDOException $e) {
            $msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
            die($msg); // On arrête tout.
        }
        $cpt++;
        
        $sql = " select id_rep, bvalide from reponse where id_quest = :idquestion ";

        try {
            $commande = $pdo->prepare($sql);
            $commande->bindParam(':idquestion', $idquestion);
            $bool = $commande->execute();
            if ($bool) {
                $_SESSION['profil']['boolReponse'][$idquestion] = $commande->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
            }
        } catch (PDOException $e) {
            $msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
            die($msg); // On arrête tout.
        }
    }


    $_SESSION['profil']['themes'] = $themes;
    $_SESSION['profil']['questions'] = $questions;

}

function sessionEnCoursBD(){
    require("./modele/connectBD.php");

    $sql = "update qcm  
            set bBloque = 0 
            where id_quest = :idquest ";

    try {
        $commande = $pdo->prepare($sql);
        $commande->bindParam(':idquest', $_SESSION['profil']['questionChoisie']);
        $bool = $commande->execute();
    } catch (PDOException $e) {
        $msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
        die($msg); // On arrête tout.
    }


}

function finQuestion_BD() {
    require("./modele/connectBD.php");

    $sql = "update qcm
            set bBloque = 1
            where id_quest = :idquest";

    try {
        $commande = $pdo->prepare($sql);
        $commande->bindParam(':idquest', $_SESSION['profil']['finQuestion']);
        $bool = $commande->execute();
    } catch (PDOException $e) {
        $msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
        die($msg); // On arrête tout.
    }

    $sql = "select count(*) 
            from resultat
            where id_test = :idtest and date_res = CURDATE()";

    try {
        $commande = $pdo->prepare($sql);
        $commande->bindParam(':idquest', $_SESSION['profil']['finQuestion']);
        $bool = $commande->execute();
        if($bool)
            $reps = $commande->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
        die($msg); // On arrête tout.
    }

    if($reps > 0)
        array_push($_SESSION['profil']['questionsTerminees'],$_SESSION['profil']['finQuestion']);
    else 
    array_push($_SESSION['profil']['questionsAnnulees'],$_SESSION['profil']['finQuestion']);
    $_SESSION['profil']['questionChoisie'] = "";

}

function actualiser_BD() {
    require ('./modele/connectBD.php');
    $questChoisie = $_SESSION['profil']['questionChoisie'];
    $sql = "select count(*) from etudiant where num_grpe = :numgrp";

    try {
        $commande = $pdo->prepare($sql);
        $commande->bindParam(':numgrp', $_SESSION['profil']['groupe']);
        $bool = $commande->execute();
        if ($bool)
        $nbetuCo = $commande->fetch(PDO::FETCH_ASSOC); //tableau d'enregistrements
    }
    catch (PDOException $e) {
        $msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
        die($msg); // On arrête tout.
    }

    $sql = "select count(*) 
            from resultat r, etudiant e 
            where r.id_etu = e.id_etu and e.num_grpe = :numgrp and r.id_quest = :idquest and r.id_test = :idtest and r.date_res = CURDATE()";

    try {
        $commande = $pdo->prepare($sql);
        $commande->bindParam(':numgrp', $_SESSION['profil']['groupe']);
        $commande->bindParam(':idquest', $_SESSION['profil']['questionChoisie']);
        $commande->bindParam(':idtest', $_SESSION['profil']['id_test']);
        $bool = $commande->execute();
        if ($bool)
        $nbetuRep = $commande->fetch(); //tableau d'enregistrements
    }
    catch (PDOException $e) {
        $msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
        die($msg); // On arrête tout.
    }

    $_SESSION['profil']['etudiantsAttente'][$questChoisie] = $nbetuCo['count(*)'] - $nbetuRep['count(*)'];
    
    $sql = "select id_rep from reponse where id_quest = :idquest";

    try {
        $commande = $pdo->prepare($sql);
        $commande->bindParam(':idquest', $_SESSION['profil']['questionChoisie']);
        $bool = $commande->execute();
        if ($bool)
        $reponses = $commande->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
    }
    catch (PDOException $e) {
        $msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
        die($msg); // On arrête tout.
    }

    while(count($reponses) != 0) {
        $reponse = array_pop($reponses);
        $reponse = $reponse['id_rep'];
        $sql = "select count(*) from resultat where id_quest = :idquest and id_rep = :idrep and date_res = CURDATE()";
        try {
            $commande = $pdo->prepare($sql);
            $commande->bindParam(':idquest', $_SESSION['profil']['questionChoisie']);
            $commande->bindParam(':idrep', $reponse);
            $bool = $commande->execute();
            if ($bool)
                $_SESSION['profil']['reponses'][$questChoisie][$reponse] = $commande->fetchAll(PDO::FETCH_ASSOC); //tableau d'enregistrements
        }
        catch (PDOException $e) {
            $msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
            die($msg); // On arrête tout.
        }
    }
}

function finSession_BD() {

    require("./modele/connectBD.php");

    $sql = "update test
            set bActif = 0
            where id_test = :idtest";

    try {
        $commande = $pdo->prepare($sql);
        $commande->bindParam(':idtest', $_SESSION['profil']['id_test']);
        $bool = $commande->execute();
    } catch (PDOException $e) {
        $msg = utf8_encode("Echec de select : " . $e->getMessage() . "\n");
        die($msg); // On arrête tout.
    }

}

function afficherPrenomBilanBD() {
	  require("./modele/connectBD.php");
	  $testActuel= $_SESSION["profil"]['id_test'];
		$listNote=array();
	  $listPrenom=array();
	  $idetu=array();
	  $cpt2=0;
	   // $sql = " select * from question q inner join qcm qc on q.id_quest = qc.id_quest where qc.id_test = :idtest and q.id_theme = :idtheme  ";
	$reponse = $pdo->query('SELECT  etudiant.id_etu, prenom FROM bilan INNER JOIN etudiant ON bilan.id_etu=etudiant.id_etu WHERE date_bilan=CURDATE() AND bilan.id_test="'.$testActuel.'"');
		while ($don = $reponse->fetch()) {
	
	$listPrenom[$cpt2]= $don["prenom"];
	$idetu[$cpt2]=$don['id_etu'];
	
	$reponse2 = $pdo->query('SELECT bilan.note_test FROM bilan WHERE bilan.id_etu="'.$idetu[$cpt2].'" AND bilan.id_test="'.$testActuel.'"');
	
	 while ($don2 = $reponse2->fetch()) {
		$listNote[$cpt2]=$don2["note_test"];
	 }
	$cpt2++;
	

	}
	
	return $listPrenom+$listNote;
}

function afficherNoteBilanBD() {
	  require("./modele/connectBD.php");
	  $testActuel= $_SESSION["profil"]['id_test'];
		$listNote=array();
	
	  $idetu=array();
	  $cpt2 = 0;
	   // $sql = " select * from question q inner join qcm qc on q.id_quest = qc.id_quest where qc.id_test = :idtest and q.id_theme = :idtheme  ";
		$reponse = $pdo->query('SELECT  etudiant.id_etu, prenom FROM bilan INNER JOIN etudiant ON bilan.id_etu=etudiant.id_etu WHERE date_bilan=CURDATE() AND bilan.id_test="'.$testActuel.'"');
		while ($don = $reponse->fetch()) {
	
	
	$idetu[$cpt2]=$don['id_etu'];
	
	$reponse2 = $pdo->query('SELECT bilan.note_test FROM bilan WHERE bilan.id_etu="'.$idetu[$cpt2].'" AND bilan.id_test="'.$testActuel.'"');
	
	 while ($don2 = $reponse2->fetch()) {
		$listNote[$cpt2]=$don2["note_test"];
	 }
	$cpt2++;
	

	}
	ECHO "RTYUIOP";
	return $listNote;
}


function afficherRepBilanBD() {

	require("./modele/connectBD.php"); 
	//var_dump($_SESSION['profil']);
	$idetu=array();
	$listRep=array(array(array(array())));
	$cpt2=0;
	$cpt3=0;
	$cpt1=0;
	$cpt4=0;
	$testActuel= $_SESSION["profil"]['id_test'];
	
	for($i=0; $i<count($_SESSION['profil']['idquestions']); $i++) {
		$idquest=$_SESSION['profil']['idquestions'][$i];
		echo $idquest;

		$reponse = $pdo->query('SELECT  etudiant.id_etu, prenom FROM bilan INNER JOIN etudiant ON bilan.id_etu=etudiant.id_etu WHERE
		date_bilan=CURDATE() AND bilan.id_test="'.$testActuel.'"');
		
		while ($don = $reponse->fetch()) {
			$idetu[$cpt2]=$don['id_etu'];
			
			$reponse3 = $pdo->query('SELECT reponse.id_rep,reponse.bvalide FROM resultat INNER JOIN reponse ON 
			resultat.id_quest=reponse.id_quest WHERE date_res=CURDATE() AND resultat.id_test=
			"'.$testActuel.'" AND resultat.id_quest="'.$idquest.'" AND resultat.id_etu="'.$idetu[$cpt2].'"');
			
				 while ($don3 = $reponse3->fetch()) {
						$listRep[$cpt1][$cpt2][$cpt3]=$don3['bvalide'];
						echo $listRep[$cpt1][$cpt2][$cpt3];
						 $cpt3++;
				
					 
			 $reponse4 = $pdo->query('SELECT reponse.id_rep, reponse.bvalide FROM resultat INNER JOIN reponse ON 
			resultat.id_rep=reponse.id_rep WHERE date_res=CURDATE() AND resultat.id_test=
			"'.$testActuel.'" AND resultat.id_quest="'.$idquest.'" AND resultat.id_etu="'.$idetu[$cpt2].'"');
						  while ($don4 = $reponse4->fetch()) {
							  echo "yyy";
							 if ($don3['id_rep']==$don4['id_rep']) {
								 ECHO $don4['id_rep'];
								 $listRep[$cpt1][$cpt2][$cpt3][$cpt4]=$cpt3;
								 echo $listRep[$cpt1][$cpt2][$cpt3][0].'rf';
								 $cpt4++;
								// $cpt3++;
							 }  
						  }
						  $cpt4=0;
					}
					// reset($reponse3);
					$cpt3=0;
					$cpt2++;
				}
				//reset($reponse);
				$cpt2=0;
			$cpt1++;
		}
		
		
		return $listRep;
		
		
	}
	
	function afficherBonneRepBilanBD(){
		
	require("./modele/connectBD.php"); 
	//var_dump($_SESSION['profil']);
	$idetu=array();
	$listBonneRep=array(array());
	$cpt2=0;
	$cpt3=0;
	$cpt1=0;
	$cpt4=0;
	$testActuel= $_SESSION["profil"]['id_test'];
	
	for($i=0; $i<count($_SESSION['profil']['idquestions']); $i++) {
		$idquest=$_SESSION['profil']['idquestions'][$i];
		

		$reponse = $pdo->query('SELECT  etudiant.id_etu, prenom FROM bilan INNER JOIN etudiant ON bilan.id_etu=etudiant.id_etu WHERE
		date_bilan=CURDATE() AND bilan.id_test="'.$testActuel.'"');
		
		while ($don = $reponse->fetch()) {
			$idetu[$cpt2]=$don['id_etu'];
			
			$reponse3 = $pdo->query('SELECT reponse.id_rep,reponse.bvalide FROM resultat INNER JOIN reponse ON 
			resultat.id_quest=reponse.id_quest WHERE date_res=CURDATE() AND resultat.id_test=
			"'.$testActuel.'" AND resultat.id_quest="'.$idquest.'" AND resultat.id_etu="'.$idetu[$cpt2].'"');
			
				 while ($don3 = $reponse3->fetch()) {
						
						
				
					
			 $reponse4 = $pdo->query('SELECT reponse.id_rep, reponse.bvalide FROM resultat INNER JOIN reponse ON 
			resultat.id_rep=reponse.id_rep WHERE date_res=CURDATE() AND resultat.id_test=
			"'.$testActuel.'" AND resultat.id_quest="'.$idquest.'" AND resultat.id_etu="'.$idetu[$cpt2].'"');
						  while ($don4 = $reponse4->fetch()) {
							  echo "rtrtrt";
							 if ($don3['id_rep']==$don4['id_rep']) {
								 ECHO $don4['id_rep'];
								 $listBonneRep[$cpt3][$cpt4]=$cpt3;
								 echo $listBonneRep[$cpt3][$cpt4].' '.$cpt4;
								 $cpt4++;
								// $cpt3++;
							 }  
						  }
						   $cpt3++;
					}
					// reset($reponse3);
					$cpt3=0;
					$cpt2++;
				}
				//reset($reponse);
				$cpt2=0;
			$cpt1++;
		}
		
		return $listBonneRep;
		
	}
	
	function insererQuestionBD($question,$theme,$titreQuestion,$reponses,$box) {
	
	if(!empty($question)) {
	$idtest= $_SESSION['profil']['id_test'];
	echo $question;
	
	require ("./modele/connectBD.php") ;
		$newIdtheme=0;
		$req=$pdo->prepare('SELECT id_theme FROM theme WHERE id_theme=:theme');
			$req->execute(array('theme'=> $theme));
			while($reponse= $req->fetch()) {
				$newIdtheme=$reponse[0];
			}
			
			$cpt1=0;
			for($i=0; $i<count($reponses); $i++) {
				if(!empty($box[$i])){
					$cpt1++;
				}
			}
			
			if($cpt1<>1) {
				$req=$pdo-> prepare('INSERT INTO question(id_quest,id_theme,titre,texte,bmultiple) VALUES("",:newIdtheme,:titreQuestion,:question,1)');
				$req->execute(array('question'=>$question,'newIdtheme'=>$theme,'titreQuestion'=>$titreQuestion));
			}
			
			else {
				$req=$pdo-> prepare('INSERT INTO question(id_quest,id_theme,titre,texte,bmultiple) VALUES("",:newIdtheme,:titreQuestion,:question,0)');
				$req->execute(array('question'=>$question,'newIdtheme'=>$theme,'titreQuestion'=>$titreQuestion));
				
			}
			
			
			$newIdQuestion=0;
			$req=$pdo->prepare('SELECT id_quest FROM question WHERE texte=:question');
			$req->execute(array('question'=> $question));
			while($rep= $req->fetch()) {
				$newIdQuestion=$rep[0];
				echo $newIdQuestion;
			}
			

			if(count($box)>1) {
	
	for($i=0; $i<count($reponses); $i++) {
		if(!empty($reponses[$i])) {
			if(!empty($box[$i])) {
				
				$req=$pdo-> prepare('INSERT INTO reponse(id_rep,id_quest,texte_rep,bvalide)
				VALUES("",:newIdQuestion,:reponses,1)');
				$req->execute(array('reponses'=>$reponses[$i],'newIdQuestion'=>$newIdQuestion));
				
			}
		else {
		
		
			$req=$pdo-> prepare('INSERT INTO reponse(id_rep,id_quest,texte_rep,bvalide)
			VALUES("",:newIdQuestion,:reponses,0)');
			$req->execute(array('reponses'=>$reponses[$i],'newIdQuestion'=>$newIdQuestion));
			
			}
		}
	}
	
			}
			
			else {
				for($i=0; $i<count($box); $i++) {
		
		if(!empty($reponses[$i])) {
			for($u=1;$u<=count($reponses);$u++) {
			if(!empty($box[$i])&& $box[$i]==$u) {
				echo $i.'rrrrrrrr';
				
				$req=$pdo-> prepare('INSERT INTO reponse(id_rep,id_quest,texte_rep,bvalide)
				VALUES("",:newIdQuestion,:reponses,1)');
				$req->execute(array('reponses'=>$reponses[$u-1],'newIdQuestion'=>$newIdQuestion));
				
			}
			
			elseif($box[$i]<>$u) {
		
		
			$req=$pdo-> prepare('INSERT INTO reponse(id_rep,id_quest,texte_rep,bvalide)
			VALUES("",:newIdQuestion,:reponses,0)');
			$req->execute(array('reponses'=>$reponses[$u-1],'newIdQuestion'=>$newIdQuestion));
			
			}
			}
		}
	}
			}
	echo 'dyyyd'.$newIdQuestion.'dyyyyd';
	echo 'yyyyyy'.$idtest.'yyyyyy';
	$req=$pdo-> prepare('INSERT INTO qcm1(id_qcm1,id_test,id_quest,bAutorise,bBloque,bAnnule) VALUES("",:idtest,:newIdQuestion,0,0,0)');
	$req->execute(array(':idtest'=>$idtest,':newIdQuestion'=>$newIdQuestion));
	}
}


function insererThemeBD($titre,$description) {
			require ("./modele/connectBD.php") ;
			$titre1=$titre;
			$description1=$description;
			$req=$pdo->prepare('INSERT INTO theme(id_theme,titre_theme,desc_theme) VALUES("",:titre,:description)');
			$req->execute(array('titre'=> $titre1, 'description'=> $description1));
}

function recupererTitreThemeBD() {
		require ("./modele/connectBD.php") ;
		$titreT=array();
		$r=array();
		 $r= array_keys($_SESSION['profil']['idthemes']);
		 echo $r[0];
		$cpt=0;
		for($i=0;$i<=count($_SESSION['profil']['idthemes']);$i++) {
			//echo $_SESSION['profil']['idthemes'][$i];
			if(isset($_SESSION['profil']['idthemes'][$i])) {
				
			$id_theme=$_SESSION['profil']['idthemes'][$i];
			$req=$pdo->prepare('SELECT titre_theme FROM theme WHERE id_theme=:id_theme');
			$req->execute(array('id_theme'=> $id_theme));
			
			while($reponse= $req->fetch()) {
				$titreT[$cpt]=$reponse[0];
				echo "huih";
				echo $titreT[$cpt];
				$cpt++;
			}
		}
	}
	return $titreT;
}