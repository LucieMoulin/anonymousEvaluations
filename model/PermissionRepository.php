<?php
/**
 * ETML
 * Autrice : Lucie Moulin
 * Date: 06.05.2021
 * Description : Repository pour les permissions
 */

class PermissionRepository implements Repository {
    /**
     * Récupère toutes les permissions
     *
     * @return Array
     */
    public static function findAll(){
        return executeQuery(
            "SELECT
                idPermission, perCode, perDescription
                FROM t_permission
                ORDER BY idPermission ASC;"
        );
    }

    /**
     * Récupère une permission par son id
     *
     * @param int $id
     * @return Array
     */
    public static function findOne($id){
        return executeQuery(
            "SELECT
                idPermission, perCode, perDescription
                FROM t_permission
                WHERE idPermission = :idPermission
                LIMIT 1;",
            array(array("idPermission", $id))
        );
    }

    /**
     * Récupère une permission par son code
     *
     * @param int $id
     * @return Array
     */
    public static function findOneByCode($code){
        return executeQuery(
            "SELECT
                idPermission, perCode
                FROM t_permission
                WHERE perCode = :perCode
                LIMIT 1;",
            array(array("perCode", $code))
        );
    }

    /**
     * Récupère toutes les permissions d'un rôle à partir de son id
     *
     * @param int $id
     * @return Array
     */
    public static function findRolePermissions($id){
        if(is_numeric($id)){
            return executeQuery(
                "SELECT
                    idPermission, perCode
                    FROM t_permission
                        JOIN t_r_rolePermission
                            ON fkPermission = idPermission
                    WHERE fkRole = :idRole;",
                array(array("idRole", $id))
            );
        } else {
            return array();
        }
    }

    /**
     * Insère ou modifie une permission => la plateforme ne permet pas cette fonctionnalité
     *
     * @param Array $array
     * @return bool
     */
    public static function insertEditOne($array){
        return false;
    }
}