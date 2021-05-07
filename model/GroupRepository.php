<?php
/**
 * ETML
 * Autrice : Lucie Moulin
 * Date: 06.05.2021
 * Description : Repository pour les groupes
 */

class GroupRepository implements Repository {
    /**
     * Récupère tous les groupes
     *
     * @return Array
     */
    public static function findAll(){
        return executeQuery(
            "SELECT
                idGroup, groName
                FROM t_group
                ORDER BY idGroup ASC;"
        );
    }

    /**
     * Récupère tous les identifiants
     *
     * @return Array
     */
    public static function findAllIds(){
        $result = executeQuery(
            "SELECT
                idGroup
                FROM t_group
                ORDER BY idGroup ASC;"
        );

        for($i = 0; $i < count($result); $i++) {
            $result[$i] = $result[$i]['idGroup'];
        }

        return $result;
    }

    /**
     * Récupère un groupe par son id
     *
     * @param int $id
     * @return Array
     */
    public static function findOne($id){
        return executeQuery(
            "SELECT
                idGroup, groName
                FROM t_group
                WHERE idGroup = :idGroup
                LIMIT 1;",
            array(array("idGroup", $id))
        );
    }

    /**
     * Récupère un groupe créés par un utilisateur
     *
     * @param string $login
     * @return Array
     */
    public static function findOwned($login){
        return executeQuery(
            "SELECT
                idGroup, groName, fkUser
                FROM t_group 
                WHERE fkUser = (SELECT idUser FROM t_user WHERE useLogin = :useLogin);",
            array(array("useLogin", $login))
        );
    }

    /**
     * Récupère les identifiants des utilisateurs faisant partie du groupe
     *
     * @param string $login
     * @return Array
     */
    public static function getMembers($id){
        return executeQuery(
            "SELECT
                fkUser
                FROM t_r_groupUser
                WHERE fkGroup = :idGroup;",
            array(array("idGroup", $id))
        );
    }

    /**
     * Insère ou modifie un groupe
     *
     * @param Array $array
     * @return bool
     */
    public static function insertEditOne($array){
        //TODO implémenter ajout de groupe
        return false;
    }
}