<?php

/**
 * ETML
 * Autrice : Lucie Moulin
 * Date : 07.05.2021
 * Description : Contrôleur des groupes
 */

/**
 * Contrôleur des groupes
 */
class GroupController extends Controller {

    /**
     * Actions possibles pour ce contrôleur (listes des méthodes du contrôleur)
     *
     * @var Array 
     */
    public $actions = array(
        "list",
        "create",
        "edit",
        "delete",
        "formSubmitted"
    );

    /**
     * Constructeur
     */
    function __construct (){
        //Ajout des erreurs personnalisées de ce contrôleur
        $this->errors["unknownAction"] = "Action inconnue pour le contrôleur des groupes.";
        $this->errors["creationFailed"] = "La création du groupe a échoué.";
        $this->errors["editionFailed"] = "La modification du groupe a échoué.";
        $this->errors["deletionFailed"] = "La suppression du groupe a échoué.";
    }

    /**
     * Affichage de la liste des groupes
     *
     * @return string
     */
    protected function list(){
        //TODO vérification droits        
        $groups = GroupRepository::findOwned($_SESSION['connectedUser']);
        foreach ($groups as $key => $group) {
            $groups[$key]['students'] = GroupRepository::getMembersName($group['idGroup']);
        }
        ob_start();
        include('./view/listGroups.php');
        return ob_get_clean();
    }

    /**
     * Affichage du formulaire de création de groupe
     *
     * @return string
     */
    protected function create(){
        //TODO vérification droits
        unset($_SESSION['idGroup']);
        $students = RoleRepository::findUsers(ROLE_STUDENT);
        ob_start();
        include('./view/createGroup.php');
        return ob_get_clean();
    }

    /**
     * Affichage du formulaire de modification de groupe
     *
     * @return string
     */
    protected function edit($id){
        //TODO vérification droits
        $students = RoleRepository::findUsers(ROLE_STUDENT);
        $group = GroupRepository::findOne($id);
        if(isset($group[0]['idGroup'])){
            $group = $group[0];
        }
        $members = GroupRepository::getMembers($id);
        foreach ($students as $key => $student) {
            $students[$key]['isMember'] = in_array($student['idUser'], $members);
        }
        $_SESSION['idGroup'] = $id;
        ob_start();
        include('./view/createGroup.php');
        return ob_get_clean();
    }

    /**
     * Gestion de l'envoi du formulaire de création ou de modification de groupe
     *
     * @return string
     */
    protected function formSubmitted(){
        //TODO vérification droits
        if(isset($_POST['name']) && $_POST['name'] != NULL) {
            $group = array();
            if(isset($_SESSION['idGroup'])){
                $group['idGroup'] = $_SESSION['idGroup'];
            }
            $group['groName'] = $_POST['name'];
            $username = UserRepository::findWithLogin($_SESSION['connectedUser']);
            $group['fkUser'] = $username[0]['idUser'];
            if(isset($_POST['student'])){
                foreach ($_POST['student'] as $key => $value) {
                    if($value == 'on'){
                        $group['students'][] = $key;
                    }
                }
            }
            if(GroupRepository::insertEditOne($group)){
                
                $successText = isset($_SESSION['idGroup']) ? 'Modification du groupe réussie' : 'Création du groupe réussie';
                ob_start();
                include('./view/successTemplate.php');
                return ob_get_clean().$this->list();
            } else {
                return isset($_SESSION['idGroup']) ? $this->displayError('editionFailed') : $this->displayError('creationFailed');
            }
        } else {
            return isset($_SESSION['idGroup']) ? $this->displayError('editionFailed') : $this->displayError('creationFailed');
        }
    }

    /**
     * Suppression d'un groupe
     *
     * @param int $id
     * @return string
     */ 
    protected function delete(){
        //TODO vérification droits

        if(isset($_SESSION['idGroup'])){
            GroupRepository::deleteOne($_SESSION['idGroup']);
            $successText = "Groupe supprimé";
            ob_start();
            include('./view/successTemplate.php');
            return ob_get_clean().$this->list();
        } else {
            return $this->displayError('deletionFailed').$this->list();
        }
        
    }
}