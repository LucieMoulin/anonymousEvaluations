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
        $this->errors["deletionImpossible"] = "Ce groupe a participé à des évaluations, il est impossible de le supprimer.";
    }

    /**
     * Affichage de la liste des groupes
     *
     * @return string
     */
    protected function list(){
        //Vérification des droits
        if($this->isAllowed('EDIT_GROUP_ALL')){
            //Récupération de tous les groupes, leur propriétaire et leurs membres
            $groups = GroupRepository::findAll();
            foreach ($groups as $key => $group) {
                $groups[$key]['students'] = GroupRepository::getMembersName($group['idGroup']);
                $owner = GroupRepository::getOwner($group['idGroup']);
                $groups[$key]['owner'] = $owner[0]['useFirstName'].' '.$owner[0]['useLastName'];
            }
            $showOwner = true;
            
            //Affichage de la vue de liste des groupes
            ob_start();
            include('./view/listGroups.php');
            return ob_get_clean();
        } else if($this->isAllowed('EDIT_GROUP_OWN')){
            //Récupération des groupes de la personne connectée et leurs membres
            $groups = GroupRepository::findOwned($_SESSION['connectedUser']);
            foreach ($groups as $key => $group) {
                $groups[$key]['students'] = GroupRepository::getMembersName($group['idGroup']);
            }
            $showOwner = false;

            //Affichage de la vue de liste des groupes
            ob_start();
            include('./view/listGroups.php');
            return ob_get_clean();
        } else {
            return $this->displayError('notAllowed');
        }
    }

    /**
     * Affichage du formulaire de création de groupe
     *
     * @return string
     */
    protected function create(){
        //Vérification des droits de création de groupe
        if($this->isAllowed('CREATE_GROUP')){
            unset($_SESSION['idGroup']);

            //Récupération de tous les élèves
            $students = RoleRepository::findUsers(ROLE_STUDENT);

            //Affichage de la vue de création d'un groupe
            ob_start();
            include('./view/createGroup.php');
            return ob_get_clean();
        } else {
            return $this->displayError('notAllowed');
        }
    }

    /**
     * Affichage du formulaire de modification de groupe
     *
     * @return string
     */
    protected function edit($id){
        //Récupération du propriétaire du groupe et vérification des droits
        $owner = GroupRepository::getOwner($id);
        if($this->isAllowed('EDIT_GROUP_ALL') || ($this->isAllowed('EDIT_GROUP_OWN') && isset($owner[0]['useLogin']) && isset($_SESSION['connectedUser']) && $owner[0]['useLogin'] == $_SESSION['connectedUser'])) {

            //Récupération du groupe et de tous les élèves
            $students = RoleRepository::findUsers(ROLE_STUDENT);
            $group = GroupRepository::findOne($id);
            if(isset($group[0]['idGroup'])){
                $group = $group[0];
            }

            //Récupération des membres du groupe
            $members = GroupRepository::getMembers($id);
            foreach ($students as $key => $student) {
                $students[$key]['isMember'] = in_array($student['idUser'], $members);
            }
            $_SESSION['idGroup'] = $id;

            //Affichage de la vue de modification du groupe
            ob_start();
            include('./view/createGroup.php');
            return ob_get_clean();
        } else {
            return $this->displayError('notAllowed');
        }
    }

    /**
     * Gestion de l'envoi du formulaire de création ou de modification de groupe
     *
     * @return string
     */
    protected function formSubmitted(){
        //Récupération du propriétaire du groupe modifié et vérification des droits
        $owner = array();
        if(isset($_SESSION['idGroup'])){
            $owner = GroupRepository::getOwner($_SESSION['idGroup']);
        }
        if($this->isAllowed('CREATE_GROUP') || (isset($_SESSION['idGroup']) && ($this->isAllowed('EDIT_GROUP_ALL') || ($this->isAllowed('EDIT_GROUP_OWN') && isset($owner[0]['useLogin']) && isset($_SESSION['connectedUser']) && $owner[0]['useLogin'] == $_SESSION['connectedUser'])))) {

            //Validation basique du formulaire
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

                //Insertion dans la base de données du groupe
                if(GroupRepository::insertEditOne($group)){
                    
                    //Affichage d'un message de succès et de la liste des groupes
                    $successText = isset($_SESSION['idGroup']) ? 'Modification du groupe réussie' : 'Création du groupe réussie';
                    unset($_SESSION['idGroup']);
                    ob_start();
                    include('./view/successTemplate.php');
                    return ob_get_clean().$this->list();
                } else {
                    return isset($_SESSION['idGroup']) ? $this->displayError('editionFailed') : $this->displayError('creationFailed');
                }
            } else {
                return isset($_SESSION['idGroup']) ? $this->displayError('editionFailed') : $this->displayError('creationFailed');
            }
        } else {
            return $this->displayError('notAllowed');
        }
    }

    /**
     * Suppression d'un groupe
     *
     * @param int $id
     * @return string
     */ 
    protected function delete(){
        if(isset($_SESSION['idGroup'])){

            //Récupération du propriétaire du groupe, des évaluations de ce groupe et vérification des droits
            $owner = GroupRepository::getOwner($_SESSION['idGroup']);
            $evals = GroupRepository::findEvals($_SESSION['idGroup']);
            if($this->isAllowed('EDIT_GROUP_ALL') || ($this->isAllowed('EDIT_GROUP_OWN') && isset($owner[0]['useLogin']) && isset($_SESSION['connectedUser']) && $owner[0]['useLogin'] == $_SESSION['connectedUser'])) {
                
                //Vérification que ce groupe n'a pas d'évaluations
                if(count($evals) == 0){                    
                    GroupRepository::deleteOne($_SESSION['idGroup']);

                    //Affichage d'un message de succès et de la liste des groupes
                    $successText = "Groupe supprimé";
                    ob_start();
                    include('./view/successTemplate.php');
                    return ob_get_clean().$this->list();
                } else {
                    return $this->displayError('deletionImpossible');
                }
            } else {
                return $this->displayError('notAllowed');
            }
        } else {
            return $this->displayError('deletionFailed').$this->list();
        }
    }
}