<?php

/**
 * ETML
 * Autrice : Lucie Moulin
 * Date : 06.05.2021
 * Description : Contrôleur principal
 */
    
class MainController extends Controller {
    /**
     * Actions possibles pour ce contrôleur (listes des méthodes du contrôleur)
     *
     * @var Array 
     */
    public $actions = array(
        "dispatch"
    );

    /**
     * Constructeur
     */
    function __construct (){
        //Ajout des erreurs personnalisées de ce contrôleur
        $this->errors["unknownAction"] = "Action inconnue pour le contrôleur principal";
        $this->errors["unknownController"] = "Contrôleur inconnu";
    }

    /**
     * Construction du contrôleur et lancement de l'action
     *
     * @param string $controller
     * @param string $action
     * @param Array $parameters
     * @return string retourne le html d'affichage du résultat de l'action
     */
    public function dispatch($controller = "home", $action = "display", $parameters = array()) {
        $selectedControler;
        switch($controller){
            case 'home':
                $selectedControler = new HomeController();
                break;
            case 'auth':
                $selectedControler = new AuthenticationController();
                break;
            case 'evaluation':
                $selectedControler = new EvaluationController();
                break;
            case 'group':
                $selectedControler = new GroupController();
                break;
            default:
                return $this->displayError("unknownController");
                break;
        }
        return $selectedControler->executeAction($action, $parameters);
    }
}

?>