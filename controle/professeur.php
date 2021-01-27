<?php

function accueil() {
    $nom = $_SESSION['profil']['nom'] ;
    require("./vue/layout/layout.tpl");
}

function selectionnerTest(){
    require("./modele/professeurBD.php");
    $tests = selectionnerTestBD();
    require("./vue/layout/layout.tpl");
    //Rajouter ajax pour que selon le test selectionné son theme s'affiche.
}

function choisirThemes(){
    $idtest = $_SESSION['profil']['id_test'];
    require("./modele/professeurBD.php");
    //$tests = selectionnerTestBD();
    //require("./vue/professeur/selectionnerTest.tpl");
    $tabthemes = choisirThemesBD();
    //var_dump($themes);
    require("./vue/layout/layout.tpl");
}

function choisirQuestions(){
    $idtest = $_SESSION['profil']['id_test'];
    require("./modele/professeurBD.php");
    //$tests = selectionnerTestBD();
    //require("./vue/professeur/selectionnerTest.tpl");
    //$tabthemes = choisirThemesBD();
    //require("./vue/professeur/choisirThemes.tpl");
    $questions = choisirQuestionsBD();
    require ("./vue/layout/layout.tpl");
}

function demarrerTest() {
    require ("./modele/professeurBD.php");
    $themesChoisis = $_SESSION['profil']['themes'];
    $questionsChoisies = $_SESSION['profil']['questions'];
    $_SESSION['profil']['questionsTerminees'] = array();
    $_SESSION['profil']['questionsAnnulees'] = array();
    demarrerTestBD();
    require("./vue/layout/layout.tpl");
}

function sessionEnCours() {
    require("./modele/professeurBD.php");
    $themesChoisis = $_SESSION['profil']['themes'];
    $questionsChoisies = $_SESSION['profil']['questions'];
    sessionEnCoursBD();
    actualiser_BD();
    require("./vue/layout/layout.tpl");
}

function finQuestion(){
    require("./modele/professeurBD.php");
    $themesChoisis = $_SESSION['profil']['themes'];
    $questionsChoisies = $_SESSION['profil']['questions'];
    finQuestion_BD();
    global $action;
    $action = "sessionEnCours";
    require("./vue/layout/layout.tpl");

}

function finSession(){
    require("./modele/professeurBD.php");
    finSession_BD();
    global $action;
    $action = "accueil";
    $nom = $_SESSION['profil']['nom'];
    require("./vue/layout/layout.tpl");

}

?>