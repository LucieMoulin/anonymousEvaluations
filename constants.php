<?php
/**
 * ETML
 * Autrice : Lucie Moulin
 * Date : 06.05.2021
 * Description : Fichier de constantes du site
 */

//Informations sur la base de données
defined('DB_HOST') || define('DB_HOST', 'mysql:host=localhost');
defined('DB_NAME') || define('DB_NAME', 'db_anonymousevaluations');
defined('DB_USER') || define('DB_USER', 'root');
defined('DB_PASS') || define('DB_PASS', '');

//Constantes de numéros d'états/identifiants dans la base de donnée des états
defined('STATE_WAITING') || define('STATE_WAITING', 1);
defined('STATE_ACTIVE') || define('STATE_ACTIVE', 2);
defined('STATE_CLOSED') || define('STATE_CLOSED', 3);
defined('STATE_FINISHED') || define('STATE_FINISHED', 4);

//Url du site
defined('ROOT_DIR') || define('ROOT_DIR', '/anonymousEvaluations');

//Dossier des fichiers
defined('UPLOAD_DIR') || define('UPLOAD_DIR', '\\uploads\\');

//Fichier de configuration des identifiants anonymes
defined('ANONYMOUS_CONFIG') || define('ANONYMOUS_CONFIG', './anonymous-config.json');

?>