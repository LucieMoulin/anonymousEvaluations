<?php
/**
 * ETML
 * Autrice : Lucie Moulin
 * Date: 06.05.2021
 * Description : Repository pour les rôles
 */

class RoleRepository implements Repository {
    /**
     * Récupère tous les rôles
     *
     * @return Array
     */
    public static function findAll(){
        return executeQuery(
            "SELECT
                idRole, rolName, rolDescription
                FROM t_role
                ORDER BY idRole ASC;"
        );
    }

    /**
     * Récupère un rôle par son id
     *
     * @param int $id
     * @return Array
     */
    public static function findOne($id){
        return executeQuery(
            "SELECT
                idRole, rolName, rolDescription
                FROM t_role
                WHERE idRole = :idRole
                LIMIT 1;",
            array(array("idRole", $id))
        );
    }

    /**
     * Retourne les utilisateurs ayant un rôle
     *
     * @param int $id
     * @return Array
     */
    public static function findUsers($id){        
        return executeQuery(
            "SELECT
                idUser, useLogin, useLastName, useFirstName, fkRole
                FROM t_user
                WHERE fkRole = :idRole;",
            array(array("idRole", $id))
        );
    }

    /**
     * Insère ou modifie un rôle => la plateforme ne permet pas cette fonctionnalité
     *
     * @param Array $array
     * @return bool
     */
    public static function insertEditOne($array){
        return false;
    }
}