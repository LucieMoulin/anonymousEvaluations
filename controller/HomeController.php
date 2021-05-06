<?php

/**
 * ETML
 * Autrice : Lucie Moulin
 * Date : 06.05.2021
 * Description : Contrôleur d'accueil
 */

/**
 * Contrôleur des fonctions de la page d'accueil
 */
class HomeController extends Controller {

    /**
     * Actions possibles pour ce contrôleur (listes des méthodes du contrôleur)
     *
     * @var Array 
     */
    public $actions = array(
        "display"
    );

    /**
     * Constructeur
     */
    function __construct (){
        //Ajout des erreurs personnalisées de ce contrôleur
        $this->errors["unknownAction"] = "Action inconnue pour le contrôleur d'accueil.";
    }

    /**
     * Affichage de la page d'accueil
     *
     * @return string
     */
    protected function display(){
        return "<p>Hello World!</p>";
    }
}