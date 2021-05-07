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
        $this->errors['uploadError'] = 'Erreur lors de l\'upload du fichier.';
        $this->errors['fileExists'] = 'Un fichier du même nom existe déjà.';
        $this->errors['fileTooLarge'] = 'Le fichier sélectionné est trop gros. Taille MAX : 50 Mb';
        $this->errors['fileFormatUnaccepted'] = 'Le fichier sélectionné n\'est pas dans les formats acceptés : ';//TODO ajouter liste formats acceptés
        $this->errors['emptyFields'] = 'Merci de remplir tous les champs';
        $this->errors['invalidGroup'] = 'Le numéro du groupe est invalide';
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
        if($this->isAllowed('CREATE_EVAL') && isset($_SESSION['connectedUser'])){
            $fileName;
            $filePath;
            if(isset($_FILES['instructions'])){
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
                if (move_uploaded_file($_FILES['instructions']['tmp_name'], $filePath)) {
                    EvaluationRepository::insertEditOne($evaluation);
                    $successText = 'Évaluation ajoutée et upload du fichier réussi';
                } else {
                    return $this->displayError('uploadError');
                }

                //TODO génération des identifiants anonymes

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
}