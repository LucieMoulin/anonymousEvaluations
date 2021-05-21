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
        "edit",
        "creationSubmitted",
        "details",
        "changeState",
        "list",
        "return",
        "getAllReturns",
        "saveGrades"
    );

    /**
     * Liste des formats de fichiers de consigne acceptés
     *
     * @var Array
     */
    private $instructionFormatsList;

    /**
     * Liste des formats de fichiers de retours acceptés
     *
     * @var Array
     */
    private $returnFormatsList;

    /**
     * Constructeur
     */
    function __construct (){
        //Récupération de la configuration
        $config = json_decode(file_get_contents(CONFIG), true);

        $instructionFormats = "";
        $this->instructionFormatsList = array();
        $returnFormats = "";        
        $this->returnFormatsList = array();

        if(isset($config['instructionsAcceptedFormats']) && isset($config['returnAcceptedFormats'])){
            foreach ($config['instructionsAcceptedFormats'] as $format) {
                if($format['active']){
                    $this->instructionFormatsList[] = $format['format'];
                    $instructionFormats .= $format['format'].', ';
                }
            }
    
            foreach ($config['returnAcceptedFormats'] as $format) {
                if($format['active']){
                    $this->returnFormatsList[] = $format['format'];
                    $returnFormats .= $format['format'].', ';
                }
            }

            $instructionFormats = trim($instructionFormats, ', ');
            $returnFormats = trim($returnFormats, ', ');
        }

        //Ajout des erreurs personnalisées de ce contrôleur
        $this->errors['unknownAction'] = "Action inconnue pour le contrôleur des évaluations.";
        $this->errors['insertionError'] = 'Erreur lors de l\'insertion.';
        $this->errors['uploadError'] = 'Erreur lors de l\'upload du fichier.';
        $this->errors['noFile'] = 'Aucun fichier sélectionné.';
        $this->errors['fileExists'] = 'Un fichier du même nom existe déjà.';
        $this->errors['fileTooLarge'] = 'Le fichier sélectionné est trop gros. Taille MAX : 50 Mb';
        $this->errors['instructionsFormat'] = 'Le fichier sélectionné n\'est pas dans les formats acceptés pour le document de consigne : '.$instructionFormats;
        $this->errors['returnFormat'] = 'Le fichier sélectionné n\'est pas dans les formats acceptés pour le fichier de retour : '.$returnFormats;
        $this->errors['emptyFields'] = 'Merci de remplir tous les champs';
        $this->errors['invalidGroup'] = 'Le numéro du groupe est invalide';
        $this->errors['invalidEvaluation'] = 'Cette évaluation est invalide';
        $this->errors['invalidState'] = 'L\'état demandé n\'existe pas';
        $this->errors['zipError'] = 'Erreur lors de la création de l\'archive.';
        $this->errors['generationError'] = 'Erreur lors de la génération des identifiants anonymes.';
    }

    /**
     * Génère un tableau aléatoire d'identifiants anonymes
     *
     * @param integer $amount nombre d'identifiants
     * @return Array
     */
    private static function generateAnonymousIds($amount) {
        //Récupération des identifiants
        $config = json_decode(file_get_contents(CONFIG));

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
     * Récupère les détails d'un identifiant anonyme
     *
     * @param int $id
     * @return Array
     */
    private static function getAnonymousIdDetails($id){
        $config = json_decode(file_get_contents(CONFIG), true);
        $index = -1;
        foreach ($config['anonymousIds'] as $key => $anonymousId) {
            if ($anonymousId['id'] == $id) {
                $index = $key;
                break;
            }
        }

        if($index != -1){
            return $config['anonymousIds'][$index];
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
     * Récupère la liste d'affichage des évaluations avec un état spécifique auxquelles une personne participe
     *
     * @param int $idUser
     * @param int $idState
     * @param Array $title
     * @param int $counter
     * @return string
     */
    private static function getParticipatingEvaluationsListWithState($idUser, $idState, $title, $counter){
        //Récupération des évaluations terminées
        $evaluations = EvaluationRepository::findParticipatingWithState($idUser, $idState);
        if(count($evaluations) > 0){
            $showOwner = true;
            foreach ($evaluations as $key => $evaluation) {
                $owner = EvaluationRepository::getOwner($evaluation['idEvaluation']);
                $evaluations[$key]['owner'] = $owner[0]['useFirstName'].' '.$owner[0]['useLastName'];
            }
            $title = 'Évaluations '.$title[1];


            //Affichage de la liste des évaluations ouvertes
            ob_start();
            include('./view/listEvals.php');
            return ob_get_clean();
        } else {
            return '<h2 class="mt-4 text-center">Aucune évaluation '.$title[0].'</h2>';
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
            $isEdition = false;

            ob_start();
            include('./view/createEvaluation.php');
            return ob_get_clean();
        } else {
            return $this->displayError('notAllowed');
        }
    }

    /**
     * Affichage du formulaire de modification d'une évaluation
     *
     * @return string
     */
    protected function edit($id){
        //Récupération du propriétaire de l'évaluation et des participants, vérification des droits
        $owner = EvaluationRepository::getOwner($id);
        $participants = EvaluationRepository::getParticipants($id);
        if($this->isAllowed('EDIT_EVAL_ALL') ||
            ($this->isAllowed('EDIT_EVAL_OWN') && isset($owner[0]['useLogin']) && isset($_SESSION['connectedUser']) && $owner[0]['useLogin'] == $_SESSION['connectedUser'])) {
            $groups = GroupRepository::findOwned($_SESSION['connectedUser']);
            
            $evaluation = EvaluationRepository::findOne($id);
            if(isset($evaluation[0])){
                $evaluation = $evaluation[0];
                $isEdition = true;

                ob_start();
                include('./view/createEvaluation.php');
                return ob_get_clean();
            } else {
                return $this->displayError('invalidEvaluation');
            }
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
        $id = -1;
        if(isset($_POST['submit']) && is_numeric($_POST['submit'])){
            //Récupération du propriétaire de l'évaluation et des participants
            $id = $_POST['submit'];
            $owner = EvaluationRepository::getOwner($id);
            $participants = EvaluationRepository::getParticipants($id);
        }

        //Vérification des droits
        if(($this->isAllowed('CREATE_EVAL') && $id == -1) ||
            ($this->isAllowed('EDIT_EVAL_ALL') && $id != -1) ||
            ($this->isAllowed('EDIT_EVAL_OWN') && $id != -1 && isset($owner[0]['useLogin']) && isset($_SESSION['connectedUser']) && $owner[0]['useLogin'] == $_SESSION['connectedUser'])){

            $fileName;
            $filePath = null;
            if(isset($_FILES['instructions']) && $_FILES['instructions']['name'] != NULL){
                $fileName = time().'-'.basename($_FILES['instructions']['name']);
                $filePath = getcwd().UPLOAD_DIR.$fileName;
                $fileType = strtolower(pathinfo($filePath,PATHINFO_EXTENSION));

                //Vérification que le fichier n'existe pas déjà
                if (file_exists($filePath)) {
                    return $id == -1 ? $this->displayError('fileExists').$this->create() : $this->displayError('fileExists').$this->edit($id);
                }

                //Vérification de la taille du fichier
                if ($_FILES['instructions']['size'] > 50000000) {
                    return $id == -1 ? $this->displayError('fileTooLarge').$this->create() : $this->displayError('fileTooLarge').$this->edit($id);
                }

                //Vérification du format du fichier
                if(!in_array($fileType, $this->instructionFormatsList)) {
                    return $id == -1 ? $this->displayError('instructionsFormat').$this->create() : $this->displayError('instructionsFormat').$this->edit($id);
                }
            }

            //Vérification du formulaire
            if(!isset($_POST['moduleNumber']) || ($id == -1 && !isset($_POST['group'])) || !isset($_POST['date']) || !isset($_POST['length']) || $_POST['moduleNumber'] == NULL || ($id == -1 && $_POST['group'] == NULL) || $_POST['date'] == NULL || $_POST['length'] == NULL) {
                return $id == -1 ? $this->displayError('emptyFields').$this->create() : $this->displayError('emptyFields').$this->edit($id);
            }

            //Vérification du groupe
            if($id == -1 && !in_array($_POST['group'],GroupRepository::findAllIds())){
                return $this->displayError('invalidGroup').$this->create();
            }

            //Sauvegarde et upload
            try {
                $username = UserRepository::findWithLogin($_SESSION['connectedUser']);
                if(!(isset($_POST['removeFile']) && $_POST['removeFile'] == 'on')){
                    $currentFile = $id != -1 ? EvaluationRepository::findOne($id)[0]['evaInstructions'] : NULL;
                }
                $evaluation = array(
                    'idEvaluation' => $id == -1 ? null : $id,
                    'evaModuleNumber' => $_POST['moduleNumber'],
                    'evaDate' => $_POST['date'],
                    'evaLength' => $_POST['length'],
                    'evaInstructions' => isset($fileName) ? $fileName : (isset($currentFile) && $currentFile != NULL ? $currentFile : NULL),
                    'fkUser' => $username[0]['idUser'],
                    'fkGroup' => $id == -1 ? $_POST['group'] : NULL 
                );

                //Tentative d'upload du fichier
                if ($filePath == NULL || move_uploaded_file($_FILES['instructions']['tmp_name'], $filePath)) {
                    if($id == -1){
                        //Génération des identifiants anonymes
                        $groupMembersIds = GroupRepository::getMembers($_POST['group']);             
                        $anonymousIds = EvaluationController::generateAnonymousIds(count($groupMembersIds));
                        if($anonymousIds){
                            $evaluation['anonymousIds'] = array();
                            for($i = 0; $i < count($groupMembersIds); $i++) {
                                $evaluation['anonymousIds'][$groupMembersIds[$i]] = $anonymousIds[$i]->id;
                            }
                        } else {
                            return $this->displayError('generationError');
                        }
                        
                    }

                    //Sauvegarde en base de données
                    if($id == -1){
                        $successText = 'Évaluation ajoutée';
                    } else {                        
                        $successText = 'Évaluation modifiée';
                    }
                    $id = EvaluationRepository::insertEditOne($evaluation);
                } else {
                    return $this->displayError('uploadError');
                }
                ob_start();
                include('./view/successTemplate.php');
                return ob_get_clean().$this->details($id);
            } catch (\Throwable $th) {
                echo $th;
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
     * @param bool $showNames
     * @return string
     */
    protected function details($id, $showNames = false){
        //Récupération de l'évaluation
        $evaluation = EvaluationRepository::findOne($id);

        //Récupération du propriétaire de l'évaluation et des participants, vérification des droits
        $owner = EvaluationRepository::getOwner($id);
        $participants = EvaluationRepository::getParticipants($id);
        if($this->isAllowed('SEE_EVAL_ALL') ||
            ($this->isAllowed('SEE_EVAL_OWN') && isset($owner[0]['useLogin']) && isset($_SESSION['connectedUser']) && $owner[0]['useLogin'] == $_SESSION['connectedUser']) ||
            ($this->isAllowed('SEE_EVAL') && isset($_SESSION['connectedUser']) && EvaluationController::in_participants_array($_SESSION['connectedUser'], $participants) && isset($evaluation[0]['fkState']) && ($evaluation[0]['fkState'] == STATE_ACTIVE || $evaluation[0]['fkState'] == STATE_FINISHED))) {

            if(isset($evaluation[0])){
                $evaluation = $evaluation[0];
                $evaluation['owner'] = $owner[0]['useFirstName'].' '.$owner[0]['useLastName'];
                
                if(EvaluationController::in_participants_array($_SESSION['connectedUser'], $participants)){
                    $participant = EvaluationRepository::getParticipant($_SESSION['connectedUser'], $id);
                    $evaluation['anonymousId'] = EvaluationController::getAnonymousIdDetails($participant[0]['useAnonymousId']);
                    $evaluation['anonymousReturn'] = $participant[0]['useReturn'];
                    $evaluation['evaGrade'] = $participant[0]['useGrade'];
                    $evaluation['evaComment'] = $participant[0]['useComment'];
                }

                //Vérification des droits d'affichage des fonctionnalités de la page, configuration de la page
                $displayState = $this->isAllowed('EDIT_STATE_ALL') || ($this->isAllowed('EDIT_STATE_OWN') && isset($owner[0]['useLogin']) && isset($_SESSION['connectedUser']) && $owner[0]['useLogin'] == $_SESSION['connectedUser']);
                $displayEditButton = $this->isAllowed('EDIT_EVAL_ALL') || ($this->isAllowed('EDIT_EVAL_OWN') && isset($owner[0]['useLogin']) && isset($_SESSION['connectedUser']) && $owner[0]['useLogin'] == $_SESSION['connectedUser']);
                $displayId = $this->isAllowed('RETURN') && EvaluationController::in_participants_array($_SESSION['connectedUser'], $participants) && isset($evaluation['anonymousId']['id']) && $evaluation['anonymousId']['id'] != null;
                $displayResult = $this->isAllowed('RETURN') && EvaluationController::in_participants_array($_SESSION['connectedUser'], $participants) && isset($evaluation['evaGrade']) && $evaluation['evaGrade'] != null;
                $displayReturn = $this->isAllowed('RETURN') && EvaluationController::in_participants_array($_SESSION['connectedUser'], $participants);
                $displayReturnForm = $this->isAllowed('RETURN') && $evaluation['fkState'] == STATE_ACTIVE && EvaluationController::in_participants_array($_SESSION['connectedUser'], $participants) && $evaluation['fkState'] == STATE_ACTIVE;
                $displayReturns = ($evaluation['fkState'] == STATE_CLOSED || $evaluation['fkState'] == STATE_FINISHED) && ($this->isAllowed('SEE_RETURN_ALL') || ($this->isAllowed('SEE_RETURN_OWN') && isset($owner[0]['useLogin']) && isset($_SESSION['connectedUser']) && $owner[0]['useLogin'] == $_SESSION['connectedUser']));
                $displayGradesForm = $evaluation['fkState'] == STATE_CLOSED && ($this->isAllowed('ADD_GRADE_ALL') || ($this->isAllowed('ADD_GRADE_OWN') && isset($owner[0]['useLogin']) && isset($_SESSION['connectedUser']) && $owner[0]['useLogin'] == $_SESSION['connectedUser']));
                $showNames = $showNames && $evaluation['fkState'] == STATE_FINISHED && ($this->isAllowed('SEE_NAMES_ALL') || ($this->isAllowed('SEE_NAMES_OWN') && isset($owner[0]['useLogin']) && isset($_SESSION['connectedUser']) && $owner[0]['useLogin'] == $_SESSION['connectedUser']));

                //Récupère les retours et compte le nombre d'élèves ayant rendu
                $returns = EvaluationRepository::getReturns($id);
                $counter = 0;
                foreach($returns as $key => $return){
                    $returns[$key]['anonymousId'] = EvaluationController::getAnonymousIdDetails($return['useAnonymousId']);
                    if($return['useReturn'] != NULL){
                        $counter++;
                    }
                }
                $displayConfirm = $displayState && $counter < count($returns);
                $displayDownloadAllButton = $displayReturns && $counter > 1;
                $toggleActive = $displayReturns && $evaluation['fkState'] == STATE_FINISHED;

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
            $idUser = UserRepository::findWithLogin($_SESSION['connectedUser']);
            if(isset($idUser[0]['idUser'])){
                $display = '';

                //Récupération des évaluations actives
                $display = EvaluationController::getParticipatingEvaluationsListWithState($idUser[0]['idUser'], STATE_ACTIVE, array('ouverte', 'ouvertes'), 2);
                $display .= EvaluationController::getParticipatingEvaluationsListWithState($idUser[0]['idUser'], STATE_FINISHED, array('terminée', 'terminées'), 1);

                return $display;
            }
        } else {
            return $this->displayError('notAllowed');
        }

    }

    /**
     * Gestion de la soumission d'un retour d'évaluation
     *
     * @param int $id
     * @return string
     */
    protected function return($id){
        //Récupération de l'évaluation et des participants
        $evaluation = EvaluationRepository::findOne($id);
        $participants = EvaluationRepository::getParticipants($id);
        if(isset($evaluation[0]['fkState']) && $evaluation[0]['fkState'] == STATE_ACTIVE && $this->isAllowed('RETURN') && EvaluationController::in_participants_array($_SESSION['connectedUser'], $participants)){
            $fileName;
            $filePath = null;
            if(isset($_FILES['return']) && $_FILES['return']['name'] != NULL){

                $fileType = strtolower(pathinfo($_FILES['return']['name'],PATHINFO_EXTENSION));
                $fileName = EvaluationRepository::getParticipant($_SESSION['connectedUser'], $id)[0]['useAnonymousId'].'.'.$fileType;
                $filePath = getcwd().UPLOAD_DIR.'/'.$id.'/'.$fileName;
                if(!is_dir(getcwd().UPLOAD_DIR.'/'.$id)){
                    mkdir(getcwd().UPLOAD_DIR.'/'.$id);
                }

                //TODO upload dans un fichier protégé

                //Vérification de la taille du fichier
                if ($_FILES['return']['size'] > 50000000) {
                    return $this->displayError('fileTooLarge').$this->details($id);
                }

                //Vérification du format du fichier
                if(!in_array($fileType, $this->returnFormatsList)) {
                    return $this->displayError('returnFormat').$this->details($id);
                }

                //Sauvegarde et upload
                try {
                    $username = UserRepository::findWithLogin($_SESSION['connectedUser']);
                    
                    //Tentative d'upload du fichier
                    if ($filePath == NULL || move_uploaded_file($_FILES['return']['tmp_name'], $filePath)) {
                        //Sauvegarde en base de données
                        EvaluationRepository::return($id, $username[0]['idUser'], $fileName);
                        $successText = 'Retour ajouté et upload du fichier réussi';
                    } else {
                        return $this->displayError('uploadError');
                    }
                    ob_start();
                    include('./view/successTemplate.php');
                    return ob_get_clean().$this->details($id);
                } catch (\Throwable $th) {
                    return $this->displayError('insertionError').$this->details($id);
                }
            } else {
                return $this->displayError('noFile').$this->details($id);
            }
        } else {
            return $this->displayError('notAllowed');
        }
    }

    /**
     * Lance le téléchargement de tous les retours d'évaluation d'une évaluation
     *
     * @param int $id
     * @return string
     */
    protected function getAllReturns($id){
        //Récupération des données de l'évaluation et vérification des droits
        $evaluation = EvaluationRepository::findOne($id)[0];
        $owner = EvaluationRepository::getOwner($id);
        if(($evaluation['fkState'] == STATE_CLOSED || $evaluation['fkState'] == STATE_FINISHED) &&
            ($this->isAllowed('SEE_RETURN_ALL') ||
                ($this->isAllowed('SEE_RETURN_OWN') && isset($owner[0]['useLogin']) && isset($_SESSION['connectedUser']) && $owner[0]['useLogin'] == $_SESSION['connectedUser']))){
            
            $returns = EvaluationRepository::getReturns($id);

            $returnsZip = new ZipArchive();
            $fileName = getcwd().UPLOAD_DIR.'/'.$id.'/all.zip';

            if(file_exists($fileName)){
                $result = $returnsZip->open($fileName, ZipArchive::OVERWRITE);
            } else {
                $result = $returnsZip->open($fileName, ZipArchive::CREATE);
            }
            
            if($result === true){
                foreach ($returns as $return) {
                    if(isset($return['useReturn']) && $return['useReturn'] != null){
                        $returnsZip->addFile(getcwd().UPLOAD_DIR.'/'.$id.'/'.$return['useReturn'], $return['useReturn']);
                    }
                }
                $returnsZip->close();

                ob_start();
                header("Content-Type: application/octet-stream");
                header("Content-Disposition: attachment; filename=evaluation$id.zip");
                readfile($fileName);
                return ob_get_clean();
            } else {
                return $this->displayError('zipError');
            }            
        } else {
            return $this->displayError('notAllowed');
        }
    }

    /**
     * Gestion du résultat du formulaire de sauvegarde des notes
     *
     * @param int $id
     * @return string
     */
    protected function saveGrades($id){
        //Vérification des droits
        $owner = EvaluationRepository::getOwner($id);
        if($this->isAllowed('ADD_GRADE_ALL') || ($this->isAllowed('ADD_GRADE_OWN') && isset($owner[0]['useLogin']) && isset($_SESSION['connectedUser']) && $owner[0]['useLogin'] == $_SESSION['connectedUser'])) {

            if(isset($_POST['grades']) && count($_POST['grades']) > 0){
                foreach ($_POST['grades'] as $anonymousId => $grade) {
                    EvaluationRepository::addGrade($id, $anonymousId, $grade['grade'], $grade['comment']);                    
                }
            } else {
                return $this->displayError('insertionError');
            }

            //Affichage d'un message de succès et de la vue de détails d'une évaluation
            $successText = "Notes insérées avec succès";
            ob_start();
            include('./view/successTemplate.php');
            return ob_get_clean().$this->details($id);
        } else {
            return $this->displayError('notAllowed');
        }
    }
}