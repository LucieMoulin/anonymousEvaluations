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
        if(is_numeric($id)){
            return executeQuery(
                "SELECT
                    idRole, rolName, rolDescription
                    FROM t_role
                    WHERE idRole = :idRole
                    LIMIT 1;",
                array(array("idRole", $id))
            );
        } else {
            return TestsRepository::findAll();
        }
    }

    /**
     * Insère ou modifie un rôle => la plateforme ne gère pas les rôles
     *
     * @param Array $array
     * @return bool
     */
    public static function insertEditOne($array){
        return false;
    }
}