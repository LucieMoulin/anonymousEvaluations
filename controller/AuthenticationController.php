<?php

/**
 * ETML
 * Autrice : Lucie Moulin
 * Date : 06.05.2021
 * Description : Contrôleur d'authentification
 */

/**
 * Contrôleur des fonctions d'authentification
 */
class AuthenticationController extends Controller {

    /**
     * Actions possibles pour ce contrôleur (listes des méthodes du contrôleur)
     *
     * @var Array 
     */
    public $actions = array(
        "login",
        "logout",
        "authenticate",
        "create",
        "formSubmitted"
    );

    /**
     * Constructeur
     */
    function __construct (){
        //Ajout des erreurs personnalisées de ce contrôleur
        $this->errors["unknownAction"] = "Action inconnue pour le contrôleur d'authentification.";
        $this->errors["authenticationFailed"] = "L'authentification a échoué, veuillez vérifier vos identifiants.";
        $this->errors["accountCreationFailed"] = "La création du compte a échoué, veuillez réessayer.";
    }

    /**
     * Fonction privée de login via LDAP
     *
     * @param string $login
     * @param string $password
     * @return Array nom et prénom
     */
    private function ldapAuth($login, $password)
    {
        //Infos serveur
        $ip = '10.228.146.36';
        $domain = '@etmlnet.local';
        $root = 'DC=etmlnet, DC=local';

        //Si pas d'identifiants donnés, erreur
        if ($login == "" || $password == "") {
            return false;
        }

        //Création du lien vers le serveur
        $link = ldap_connect($ip);

        //Si pas de lien alros serveur pas dispo
        if (!$link) {
            return false;
        }

        //Définir les options de lien
        ldap_set_option($link, LDAP_OPT_PROTOCOL_VERSION, 3);
        ldap_set_option($link, LDAP_OPT_REFERRALS, 0);

        //Essayer de se connecter avec les identifiants donnés
        $handle = @ldap_bind($link, $login.$domain, $password);

        //Retourner le résultat
        if (!$handle) {
            return false;
        } else {
            try {
                //Récupération du prénom
                $filter = "(sAMAccountName=" . $login . ")";
                $attr = array("memberof","givenname");
                $result = ldap_search($link, $root, $filter, $attr);
                $entries = ldap_get_entries($link, $result);
                $name['firstName'] = $entries[0]['givenname'][0];

                //Récupération du nom
                $attr = array("memberof","sn");
                $result = ldap_search($link, $root, $filter, $attr);
                $entries = ldap_get_entries($link, $result);
                $name['lastName'] = $entries[0]['sn'][0];
                
                return $name;
            } catch (\Throwable $th) {
                return false;
            }
            
        }
    }

    /**
     * Affichage de la page de connexion
     *
     * @return string
     */
    protected function login(){
        ob_start();
        include('./view/login.php');
        return ob_get_clean();
    }

    /**
     * Deconnexion de l'utilisateur actuel
     *
     * @return string
     */
    protected function logout(){
        $main = new MainController();
        session_destroy();
        $successText = 'Déconnexion réussie';
        ob_start();
        include('./view/successTemplate.php');
        return ob_get_clean().$main->dispatch();
    }

    /**
     * Tentative d'authentification LDAP
     *
     * @return string
     */
    protected function authenticate(){
        if(isset($_POST['login']) && isset($_POST['password'])) {
            $name = $this->ldapAuth($_POST['login'], $_POST['password']);
            $login = $_POST['login'];
    
            if($name){
                $user = UserRepository::findWithLogin($login);
                if(isset($user[0]['idUser'])){
                    $main = new MainController();
                    $_SESSION['connectedUser'] = $login;
                    $_SESSION['lastName'] = $user[0]['useLastName'];
                    $_SESSION['firstName'] = $user[0]['useFirstName'];
                    $_SESSION['idRole'] = $user[0]['fkRole'];
                    $successText = 'Connexion réussie';
                    ob_start();
                    include('./view/successTemplate.php');
                    return ob_get_clean().$main->dispatch();
                } else {
                    $_SESSION['login'] = $login;
                    $successText = 'Connexion réussie';
                    ob_start();
                    include('./view/successTemplate.php');
                    return ob_get_clean().$this->create($login, $name);
                }
            } else {
                return $this->displayError('authenticationFailed').$this->login();
            }
        } else {
            return $this->displayError('authenticationFailed').$this->login();
        }
    }

    /**
     * Affichage de la page de création de compte
     *
     * @return string
     */
    protected function create($login, $name){
        $roles = RoleRepository::findAll();
        for($i = 0; $i < count($roles); $i++){
            if($roles[$i]['rolName'] == 'Admin'){
                unset($roles[$i]);
            }
        }
        ob_start();
        include('./view/createAccount.php');
        return ob_get_clean();
    }

    /**
     * Gestion de l'envoi du formulaire de création de compte
     *
     * @return string
     */
    protected function formSubmitted(){
        if(isset($_SESSION['login']) && isset($_POST['lastName']) && isset($_POST['firstName']) && isset($_POST['role'])) {
            $user['login'] = $_SESSION['login'];
            $user['lastName'] = $_POST['lastName'];
            $user['firstName'] = $_POST['firstName'];
            $user['idRole'] = $_POST['role'];
            if(UserRepository::insertEditOne($user)){
                $_SESSION['connectedUser'] = $_SESSION['login'];
                $_SESSION['lastName'] = $user['lastName'];
                $_SESSION['firstName'] = $user['firstName'];
                $_SESSION['idRole'] = $user['idRole'];
                $successText = 'Création du compte réussie';
                ob_start();
                include('./view/successTemplate.php');
                return ob_get_clean();
            } else {
                return $this->displayError('accountCreationFailed');
            }
        } else {
            return $this->displayError('accountCreationFailed');
        }
    }
}