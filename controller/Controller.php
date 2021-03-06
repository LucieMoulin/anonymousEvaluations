<?php
/**
 * ETML
 * Autrice : Lucie Moulin
 * Date : 06.05.2021
 * Description : Base de contrôleur
 */

/**
 * Contrôleur de base, créer des contrôleurs en étendant cette classe
 */
abstract class Controller {
    
    /**
     * Actions possibles pour ce contrôleur (listes des méthodes du contrôleur)
     *
     * @var Array 
     */
    public $actions;

    /**
     * Erreurs possibles pour ce contrôleur
     *
     * @var Array 
     */
    protected $errors = array(
        "unknownAction" => "Action inconnue",
        "invalidParameters" => "Paramètres invalides",
        "notAllowed" => "Droits insuffisants"
    );

    /**
     * Affiche une erreur
     *
     * @param string $error
     * @return string
     */
    protected function displayError($error){
        if(array_key_exists($error, $this->errors)){
            //Récupération du texte de l'erreur
            $errorText = $this->errors[$error];

            //Utilisation du template d'erreur
            ob_start();
            include('./view/errorTemplate.php');
            return ob_get_clean();
        }
    }

    /**
     * Fonction de check si la personne connectée a la permission demandée
     *
     * @param string $permission Permission demandée
     * @return bool
     */
    protected function isAllowed($permission){
        if (!isset($_SESSION['idRole']) || !is_numeric($_SESSION['idRole'])) {
            return false;
        }
        
        $permissions = PermissionRepository::findRolePermissions($_SESSION['idRole']);

        foreach($permissions as $rolePerm){
            if($rolePerm['perCode'] == $permission){
                return true;
            }
        }
        return false;
    }

    /**
     * Méthode principale du contrôleur, appel des actions
     *
     * @param string $action
     * @param Array $parameters
     * @return string retourne le html d'affichage du résultat de l'action
     */
    public function executeAction($action, $parameters = array()){
        if(in_array($action, $this->actions)){
            try {
                return call_user_func_array(array($this, $action), $parameters);
            } catch (\Throwable $th) {
                return $this->displayError("invalidParameters");
            }
        } else {
            return $this->displayError("unknownAction");
        }
    }
}