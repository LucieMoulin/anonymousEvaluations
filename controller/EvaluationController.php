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
        "creationSubmitted"
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
                echo $th;
                return $this->displayError('insertionError');
            }
        } else {
            return $this->displayError('notAllowed');
        }
    }
}