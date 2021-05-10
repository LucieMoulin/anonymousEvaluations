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
     * Supprime un groupe par son id
     *
     * @param int $id
     * @return Array
     */
    public static function deleteOne($id){
        executeCommand(
            "DELETE
                FROM t_r_groupUser
                WHERE fkGroup = :idGroup;",
            array(array("idGroup", $id))
        );
        
        return executeCommand(
            "DELETE
                FROM t_group
                WHERE idGroup = :idGroup;",
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
     * Récupère le nom et prénom du propriétaire du groupe
     *
     * @param int $id
     * @return Array
     */
    public static function getOwner($id){
        return executeQuery(
            "SELECT
                idUser, useLogin, useFirstName, useLastName
                FROM t_group
                    JOIN t_user ON idUser = fkUser
                WHERE idGroup = :idGroup;",
            array(array("idGroup", $id))
        );
    }


    /**
     * Récupère la liste des identifiants des utilisateurs faisant partie du groupe
     *
     * @param string $login
     * @return Array
     */
    public static function getMembers($id){
        $result = executeQuery(
            "SELECT
                fkUser
                FROM t_r_groupUser
                WHERE fkGroup = :idGroup;",
            array(array("idGroup", $id))
        );
        foreach ($result as $key => $user) {
            $result[$key] = $user['fkUser'];
        }
        return $result;
    }

    /**
     * Récupère la liste des utilisateurs faisant partie du groupe
     *
     * @param string $login
     * @return Array
     */
    public static function getMembersName($id){
        return executeQuery(
            "SELECT
                idUser, useLogin, useFirstName, useLastName
                FROM t_r_groupUser
                    JOIN t_user ON fkUser = idUser
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
        if(!isset($array['idGroup'])){
            $array['idGroup'] = null;
        }

        if(isset($array['groName']) && isset($array['fkUser'])){
            //Insertion du groupe
            $result = executeCommand(
                "INSERT
                    INTO t_group (idGroup, groName, fkUser)
                    VALUE (:idGroup, :groName, :fkUser)
                ON DUPLICATE KEY UPDATE
                    groName =:groName;",
                array(
                    array("idGroup",$array['idGroup']),
                    array("groName",$array['groName']),
                    array("fkUser",$array['fkUser'])
                )
            );

            //Sauvegarde des membres du groupe
            if($result){
                if($array['idGroup'] == null){
                    $array['idGroup'] = getLastInsertedID("t_group");
                }
                
                executeCommand(
                    "DELETE
                        FROM t_r_groupUser
                        WHERE fkGroup = :fkGroup;",
                    array(
                        array("fkGroup",$array['idGroup'])
                    )
                );

                if(isset($array['students'])){
                    foreach ($array['students'] as $key => $idUser) {
                        executeCommand(
                            "INSERT
                                INTO t_r_groupUser (fkGroup, fkUser)
                                VALUE (:fkGroup, :fkUser);",
                            array(
                                array("fkGroup",$array['idGroup']),
                                array("fkUser",$idUser)
                            )
                        );
                    }
                }
            }            
            return $result;
        } else {
            return false;
        }return ;
    }
}