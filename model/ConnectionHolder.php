<?php
/**
 * ETML
 * Autrice : Lucie Moulin
 * Date : 06.05.2021
 * Description : Connecteur à la base de données
 * Inspiré de https://github.com/Raynobrak/php-single-line-db-queries
 */

/**
 * Connecteur à la base de données
 */
class ConnectionHolder
{
    private static $connection;

    /**
     * Retourne la connexion
     *
     * @return PDO
     */
    public static function getConnection() {
        if(!isset(ConnectionHolder::$connection)) {
            ConnectionHolder::connect();
        }
           
        return ConnectionHolder::$connection;
    }

    /**
     * Connecte en PDO à la base de donnée sélectionnée
     *
     * @return void
     */
    private static function connect(){
        try {
            ConnectionHolder::$connection = new PDO(DB_HOST.'; dbname='.DB_NAME.';charset=UTF8', DB_USER, DB_PASS);
        } 
        catch(Exception $exception) {
            echo("ERREUR : La connexion à la base de données a échoué.");
        }
    }
}

?>