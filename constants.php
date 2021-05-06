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

//Url du site
defined('ROOT_DIR') || define('ROOT_DIR', '/anonymousEvaluations');

//Dossier des fichiers
defined('UPLOAD_DIR') || define('UPLOAD_DIR', '\\uploads\\');
?>