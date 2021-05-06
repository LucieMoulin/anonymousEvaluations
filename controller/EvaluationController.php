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
        $this->errors["unknownAction"] = "Action inconnue pour le contrôleur d'accueil.";
        $this->errors['insertionError'] = 'Erreur lors de l\'insertion de l\'évaluation.';
        $this->errors['fileExists'] = 'Un fichier du même nom existe déjà.';
        $this->errors['fileTooLarge'] = 'Le fichier sélectionné est trop gros. Taille MAX : 500 kb';
        $this->errors['fileFormatUnaccepted'] = 'Le fichier sélectionné n\'est pas dans les formats acceptés : JPG, JPEG, PNG & GIF.';
    }

    /**
     * Affichage du formulaire de création d'évaluation
     *
     * @return string
     */
    protected function create(){
        if($this->isAllowed('CREATE_EVAL') && isset($_SESSION['connectedUser'])){
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
        $fileName = time().'-'.basename($_FILES['instructions']['name']);
        $filePath = getcwd().UPLOAD_DIR.$fileName;
        $fileType = strtolower(pathinfo($filePath,PATHINFO_EXTENSION));

        //Vérification que le fichier n'existe pas déjà
        if (file_exists($filePath)) {
            return $this->displayError('fileExists');
        }

        //Vérification de la taille du fichier
        if ($_FILES['testFile']['size'] > 500000) {
            return $this->displayError('fileTooLarge');
        }

        //Vérification du format du fichier
        if(false) {//TODO effectuer liste des extensions à accepter
            return $this->displayError('fileFormatUnaccepted');
        }

        //TODO Sauvegarde et upload
    }
}