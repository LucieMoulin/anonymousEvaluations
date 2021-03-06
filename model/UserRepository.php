<?php
/**
 * ETML
 * Autrice : Lucie Moulin
 * Date: 06.05.2021
 * Description : Repository pour les utilisateurs
 */

class UserRepository implements Repository {
    /**
     * Récupère tous les utilisateurs
     *
     * @return Array
     */
    public static function findAll(){
        return executeQuery(
            "SELECT
                idUser, useLogin, useLastName, useFirstName, fkRole
                FROM t_user
                ORDER BY useLastName ASC, useFirstName ASC;"
        );
    }

    /**
     * Compte tous les utilisateurs
     *
     * @return Array
     */
    public static function count(){
        return executeQuery(
            "SELECT
                Count(idUser) as count
                FROM t_user;"
        );
    }

    /**
     * Récupère un utilisateur par son id
     *
     * @param int $id
     * @return Array
     */
    public static function findOne($id){
        return executeQuery(
            "SELECT
                idUser, useLogin, useLastName, useFirstName, fkRole
                FROM t_user
                WHERE idUser = :idUser
                LIMIT 1;",
            array(array("idUser", $id))
        );
    }

    /**
     * Récupère un utilisateur par son login
     *
     * @param string $login
     * @return Array
     */
    public static function findWithLogin($login){        
        $result = executeQuery(
            "SELECT
                idUser, useLogin, useFirstName, useLastName, fkRole
                FROM t_user
                WHERE useLogin = :useLogin
                LIMIT 1;",
            array(array("useLogin", $login))
        );
        return $result;
    }

    /**
     * Insère ou modifie un utilisateur
     *
     * @param Array $array
     * @return bool
     */
    public static function insertEditOne($array){
        if(!isset($array['idUser'])){
            $array['idUser'] = null;
        }

        if(isset($array['login']) && isset($array['lastName']) && isset($array['firstName']) && isset($array['idRole'])){            
            return executeCommand(
                "INSERT
                    INTO t_user (idUser, useLogin, useLastName, useFirstName, fkRole)
                    VALUE (:idUser, :useLogin, :useLastName, :useFirstName, :fkRole)
                ON DUPLICATE KEY UPDATE
                    useLogin = :useLogin, useLastName = :useLastName, useFirstName = :useFirstName, fkRole = :fkRole;",
                array(
                    array("idUser",$array['idUser']),
                    array("useLogin",$array['login']),
                    array("useLastName",$array['lastName']),
                    array("useFirstName",$array['firstName']),
                    array("fkRole",$array['idRole'])
                )
            );
        } else {
            return false;
        }
    }
}