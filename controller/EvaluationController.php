<?php

/**
 * ETML
 * Autrice : Lucie Moulin
 * Date : 06.05.2021
 * Description : Contrôleur de gestion des évaluations
 */

/**
 * Contrôleur de gestion des évaluations
 */
class EvaluationController extends Controller {

    /**
     * Actions possibles pour ce contrôleur (listes des méthodes du contrôleur)
     *
     * @var Array 
     */
    public $actions = array(
        "create",
        "creationSubmitted",
        "details",
        "changeState",
        "list"
    );

    /**
     * Constructeur
     */
    function __construct (){
        //Ajout des erreurs personnalisées de ce contrôleur
        $this->errors["unknownAction"] = "Action inconnue pour le contrôleur des évaluations.";
        $this->errors['insertionError'] = 'Erreur lors de l\'insertion de l\'évaluation.';
        $this->errors['uploadError'] = 'Erreur lors de l\'upload du fichier.';
        $this->errors['fileExists'] = 'Un fichier du même nom existe déjà.';
        $this->errors['fileTooLarge'] = 'Le fichier sélectionné est trop gros. Taille MAX : 50 Mb';
        $this->errors['fileFormatUnaccepted'] = 'Le fichier sélectionné n\'est pas dans les formats acceptés : ';//TODO ajouter liste formats acceptés
        $this->errors['emptyFields'] = 'Merci de remplir tous les champs';
        $this->errors['invalidGroup'] = 'Le numéro du groupe est invalide';
        $this->errors['invalidEvaluation'] = 'Cette évaluation est invalide';
        $this->errors['invalidState'] = 'L\'état demandé n\'existe pas';
    }

    /**
     * Génère un tableau aléatoire d'identifiants anonymes
     *
     * @param integer $amount nombre d'identifiants
     * @return Array
     */
    private static function generateAnonymousIds($amount) {
        //Récupération des identifiants
        $config = json_decode(file_get_contents(ANONYMOUS_CONFIG));

        if(isset($config->anonymousIds)){            
            $availableIndexes = array();

            //Récupération des index des identifiants disponibles
            foreach($config->anonymousIds as $key => $anonymousId){
                if(isset($anonymousId->id) && isset($anonymousId->active) && $anonymousId->active) {
                    $availableIndexes[] = $key;
                }
            }

            //S'il y a assez d'identifiants disponibles, génération du nombre d'identifiants demandé
            if(count($availableIndexes) >= $amount){
                $genratedIds = array();
                $count = count($availableIndexes) - 1;
                for($i = 0; $i < $amount; $i++){
                    //Sélection aléatoire d'un index disponible
                    $randIndex = mt_rand(0,$count);
                    $index = $availableIndexes[$randIndex];

                    //Retirer cet index de la liste des index disponibles
                    unset($availableIndexes[$randIndex]);
                    $count--;
                    $availableIndexes = array_values($availableIndexes);
                    
                    //Ajout de l'identifiant dans la liste des générés
                    $genratedIds[] = $config->anonymousIds[$index];
                }

                return $genratedIds;
            } else {
                //TODO éventuellement gérer le cas ou on demande plus d'identifiants qu'il n'y en a dans la configuration
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * Fonction de recherche d'un login dans un tableau de participants
     *
     * @param string $login
     * @param Array $array
     * @return bool
     */
    private static function in_participants_array($login, $array) {
        foreach ($array as $participant) {
            if(isset($participant['useLogin']) && $participant['useLogin'] == $login) {
                return true;
            }
        }
    
        return false;
    }

    /**
     * Fonction de vérification que l'état fourni est bien un id d'état
     *
     * @param int $state
     * @return bool
     */
    private static function is_state($id) {
        $states = EvaluationRepository::findAllStates();
        foreach ($states as $state) {
            if(isset($state['idState']) && $state['idState'] == $id) {
                return true;
            }
        }    
        return false;
    }

    /**
     * Récupère la liste d'affichage des évaluations d'une personne avec un état spécifique
     *
     * @param int $idUser
     * @param int $idState
     * @param Array $name
     * @param int $counter
     * @return string
     */
    private static function getOwnEvaluationsListWithState($idUser, $idState, $name, $counter){
        //Récupération des évaluations
        $evaluations = EvaluationRepository::findOwnWithState($idUser, $idState);
        if(count($evaluations) > 0){
            $showOwner = false;
            $title = 'Mes évaluations '.$name[1];

            //Affichage de la liste des évaluations            
            ob_start();
            include('./view/listEvals.php');
            return ob_get_clean();
        } else {
            return '<h2 class="mt-4 text-center">Aucune évaluation '.$name[0].'</h2>';
        }
    }

    /**
     * Affichage du formulaire de création d'évaluation
     *
     * @return string
     */
    protected function create(){
        if($this->isAllowed('CREATE_EVAL')){
            $groups = GroupRepository::findOwned($_SESSION['connectedUser']);

            ob_start();
            include('./view/createEvaluation.php');
            return ob_get_clean();
        } else {
            return $this->displayError('notAllowed');
        }
    }

    /**
     *  Gestion de l'envoi du formulaire de création ou de modification d'évaluation
     *
     * @return string
     */
    protected function creationSubmitted(){
        //TODO vérification des droits pour la modification
        //TODO implémentation de la modification
        if($this->isAllowed('CREATE_EVAL')){
            $fileName;
            $filePath = null;
            if(isset($_FILES['instructions']) && $_FILES['instructions']['name'] != NULL){
                $fileName = time().'-'.basename($_FILES['instructions']['name']);
                $filePath = getcwd().UPLOAD_DIR.$fileName;
                $fileType = strtolower(pathinfo($filePath,PATHINFO_EXTENSION));

                //Vérification que le fichier n'existe pas déjà
                if (file_exists($filePath)) {
                    return $this->displayError('fileExists').$this->create();
                }

                //Vérification de la taille du fichier
                if ($_FILES['instructions']['size'] > 50000000) {
                    return $this->displayError('fileTooLarge').$this->create();
                }

                //Vérification du format du fichier
                if(false) {//TODO effectuer liste des extensions à accepter
                    return $this->displayError('fileFormatUnaccepted').$this->create();
                }
            }

            //Vérification du formulaire
            if(!isset($_POST['moduleNumber']) || !isset($_POST['group']) || !isset($_POST['date']) || !isset($_POST['length']) || $_POST['moduleNumber'] == NULL || $_POST['group'] == NULL || $_POST['date'] == NULL || $_POST['length'] == NULL) {
                return $this->displayError('emptyFields').$this->create();
            }

            //Vérification du groupe
            if(!in_array($_POST['group'],GroupRepository::findAllIds())){
                return $this->displayError('invalidGroup').$this->create();
            }

            //Sauvegarde et upload
            try {
                $username = UserRepository::findWithLogin($_SESSION['connectedUser']);
                $evaluation = array(
                    'idEvaluation' => NULL,//TODO récupération id lors de la modification
                    'evaModuleNumber' => $_POST['moduleNumber'],
                    'evaDate' => $_POST['date'],
                    'evaLength' => $_POST['length'],
                    'evaInstructions' => isset($fileName) ? $fileName : NULL,
                    'fkUser' => $username[0]['idUser'],
                    'fkGroup' => $_POST['group']
                );

                //Tentative d'upload du fichier
                if ($filePath == NULL || move_uploaded_file($_FILES['instructions']['tmp_name'], $filePath)) {
                    //Génération des identifiants anonymes 
                    //TODO si modification, ne pas générer ces identifiants
                    $groupMembersIds = GroupRepository::getMembers($_POST['group']);             
                    $anonymousIds = EvaluationController::generateAnonymousIds(count($groupMembersIds));
                    $evaluation['anonymousIds'] = array();
                    for($i = 0; $i < count($groupMembersIds); $i++) {
                        $evaluation['anonymousIds'][$groupMembersIds[$i]] = $anonymousIds[$i]->id;
                    }

                    //Sauvegarde en base de données
                    EvaluationRepository::insertEditOne($evaluation);
                    $successText = 'Évaluation ajoutée et upload du fichier réussi';
                } else {
                    return $this->displayError('uploadError');
                }
                ob_start();
                include('./view/successTemplate.php');
                return ob_get_clean().$this->create();//TODO rediriger vers détails éval
            } catch (\Throwable $th) {
                return $this->displayError('insertionError');
            }
        } else {
            return $this->displayError('notAllowed');
        }
    }

    /**
     * Affichage des détails d'une évaluation
     *
     * @param int $id
     * @return string
     */
    protected function details($id){
        //Récupération du propriétaire de l'évaluation et des participants, vérification des droits
        $owner = EvaluationRepository::getOwner($id);
        $participants = EvaluationRepository::getParticipants($id);
        if($this->isAllowed('SEE_EVAL_ALL') ||
            ($this->isAllowed('SEE_EVAL_OWN') && isset($owner[0]['useLogin']) && isset($_SESSION['connectedUser']) && $owner[0]['useLogin'] == $_SESSION['connectedUser']) ||
            ($this->isAllowed('SEE_EVAL') && isset($_SESSION['connectedUser']) && EvaluationController::in_participants_array($_SESSION['connectedUser'], $participants))) {

            //Récupération de l'évaluation
            $evaluation = EvaluationRepository::findOne($id);
            if(isset($evaluation[0])){
                $evaluation = $evaluation[0];

                //Vérification des droits de modification d'état
                $displayState = $this->isAllowed('EDIT_STATE_ALL') || ($this->isAllowed('EDIT_STATE_OWN') && isset($owner[0]['useLogin']) && isset($_SESSION['connectedUser']) && $owner[0]['useLogin'] == $_SESSION['connectedUser']);

                //Affichage de la vue de détails d'une évaluation
                ob_start();
                include('./view/evaluationDetails.php');
                return ob_get_clean();
            } else {
                return $this->displayError('invalidEvaluation');
            }
        } else {
            return $this->displayError('notAllowed');
        }
    }

    /**
     * Change l'état d'une évaluation
     *
     * @param int $id
     * @param int $state
     * @return string
     */
    protected function changeState($id, $state){
        //Vérification des droits
        $owner = EvaluationRepository::getOwner($id);
        if($this->isAllowed('EDIT_STATE_ALL') || ($this->isAllowed('EDIT_STATE_OWN') && isset($owner[0]['useLogin']) && isset($_SESSION['connectedUser']) && $owner[0]['useLogin'] == $_SESSION['connectedUser'])) {

            if(EvaluationController::is_state($state)){
                EvaluationRepository::changeState($id, $state);
    
                //Affichage d'un message de succès et de la vue de détails d'une évaluation
                $successText = "État modifié avec succès";
                ob_start();
                include('./view/successTemplate.php');
                return ob_get_clean().$this->details($id);
            } else {
                return $this->displayError('invalidState');
            }
        } else {
            return $this->displayError('notAllowed');
        }
    }

    /**
     * Affichage de la liste des évaluations
     *
     * @return string
     */
    protected function list(){
        if($this->isAllowed('SEE_EVAL_ALL')) {
            //Récupération de toutes les évaluations et leur propriétaire
            $evaluations = EvaluationRepository::findAll();
            foreach ($evaluations as $key => $eval) {
                $owner = EvaluationRepository::getOwner($eval['idEvaluation']);
                $evaluations[$key]['owner'] = $owner[0]['useFirstName'].' '.$owner[0]['useLastName'];
            }
            $showOwner = true;
            $title = "Toutes les évaluations";

            //Affichage de la liste des évaluations            
            ob_start();
            include('./view/listEvals.php');
            return ob_get_clean();
        } else if ($this->isAllowed('SEE_EVAL_OWN')) {
            $idUser = UserRepository::findWithLogin($_SESSION['connectedUser']);
            if(isset($idUser[0]['idUser'])){
                $idUser = $idUser[0]['idUser'];

                //Récupération et préparation de la vue des évaluations
                $display = EvaluationController::getOwnEvaluationsListWithState($idUser, STATE_WAITING, array('en attente', 'en attente'), 4);
                $display .= EvaluationController::getOwnEvaluationsListWithState($idUser, STATE_ACTIVE, array('activée', 'activées'), 3);
                $display .= EvaluationController::getOwnEvaluationsListWithState($idUser, STATE_CLOSED, array('clôturée', 'clôturées'), 2);
                $display .= EvaluationController::getOwnEvaluationsListWithState($idUser, STATE_FINISHED, array('terminée', 'terminées'), 1);
                
                return $display;
            }
        } else if ($this->isAllowed('SEE_EVAL')) {
            
        } else {
            return $this->displayError('notAllowed');
        }

    }
}