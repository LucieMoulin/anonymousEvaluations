<?php
    session_start();
    require_once('./constants.php');
    require_once('./model/databaseFunctions.php');
    require_once('./model/ConnectionHolder.php');
    require_once('./model/Repository.php');
    require_once('./model/RoleRepository.php');
    require_once('./model/UserRepository.php');
    require_once('./model/PermissionRepository.php');
    require_once('./model/GroupRepository.php');
    require_once('./controller/Controller.php');
    require_once('./controller/MainController.php');
    require_once('./controller/HomeController.php');
    require_once('./controller/AuthenticationController.php');
    require_once('./controller/EvaluationController.php');
    
    /**
     * ETML
     * Autrice : Lucie Moulin
     * Date : 06.05.2021
     * Description : Index et routeur du site
     */

    //Interprétation de l'URL afin de récupérer le contrôleur, l'action et les paramètres
    $elements = preg_split('/(\/|\?)/', trim($_SERVER['REQUEST_URI'], '/'));
    $rootkey = array_search(ltrim(ROOT_DIR, '/'), $elements);
    if(isset($rootkey)){
        unset($elements[$rootkey]);
        $elements = array_values($elements);
    }
    //Récupération du contrôleur
    if(isset($elements[0])){
        $elements["controller"] = $elements[0];
        unset($elements[0]);
    }
    //Récupération de l'action
    if(isset($elements[1])){
        $elements["action"] = $elements[1];
        unset($elements[1]);
    }
    //Récupération des autres paramètres
    if(isset($elements[2])){
        $elements[2] = explode('&', $elements[2]);
        $parameters = array();
        foreach($elements[2] as $element){
            $array = explode('=', $element);
            $parameters[$array[0]] = $array[1];
        }
        $elements["parameters"] = $parameters;
        unset($elements[2]);
    }

    //Construction du contrôleur principal
    $mainController = new MainController();
?>

<!DOCTYPE html>
<html lang="fr">
<head>

    <?php
        include("./header.php");
    ?>
    
    <title><?= "Site"//TODO récupération du titre de la page ?></title>
</head>
<body>
    <div class="flex-wrapper">
        <?php
            //Barre de navigation
            include("./nav.php");
        ?>

        <div class="container">
            <?php
                //Lancement du contrôleur principal
                echo($mainController->executeAction("dispatch", $elements));
            ?>
        </div>

        <?php
            //Footer
            include("./footer.php");
        ?>
    </div>
</body>
</html>