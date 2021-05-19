<?php
/**
 * ETML
 * Autrice : Lucie Moulin
 * Date: 07.05.2021
 * Description : Repository pour les évaluations
 */

class EvaluationRepository implements Repository {
    /**
     * Récupère toutes les évaluations
     *
     * @return Array
     */
    public static function findAll() {
        return executeQuery(
            "SELECT
                idEvaluation, evaModuleNumber, evaDate, evaLength, evaInstructions, fkUser, fkGroup, fkState
                FROM t_evaluation
                ORDER BY evaDate DESC;"
        );
    }

    /**
     * Récupère toutes les évaluations avec un état
     *
     * @param int $id de l'état
     * @return Array
     */
    public static function findAllWithState($id) {
        return executeQuery(
            "SELECT
                idEvaluation, evaModuleNumber, evaDate, evaLength, evaInstructions, fkUser, fkGroup, fkState
                FROM t_evaluation
                WHERE fkState = :idState
                ORDER BY evaDate DESC;",
            array(array("idState", $id))
        );
    }

    /**
     * Récupère toutes les évaluations d'une personne
     *
     * @param int $idUser id de l'utilisateur-trice
     * @return Array
     */
    public static function findOwn($idUser) {
        return executeQuery(
            "SELECT
                idEvaluation, evaModuleNumber, evaDate, evaLength, evaInstructions, fkUser, fkGroup, fkState
                FROM t_evaluation
                WHERE fkUser = :idUser
                ORDER BY evaDate DESC;",
            array(array("idUser", $idUser))
        );
    }

    /**
     * Récupère toutes les évaluations d'une personne avec un état
     *
     * @param int $idUser id de l'utilisateur-trice
     * @param int $idState id de l'état
     * @return Array
     */
    public static function findOwnWithState($idUser, $idState) {
        return executeQuery(
            "SELECT
                idEvaluation, evaModuleNumber, evaDate, evaLength, evaInstructions, fkUser, fkGroup, fkState
                FROM t_evaluation
                WHERE fkUser = :idUser AND fkState = :idState
                ORDER BY evaDate DESC;",
            array(array("idUser", $idUser),array("idState", $idState))
        );
    }

    /**
     * Récupère toutes les évaluations auxquelles une personne participe avec un état
     *
     * @param int $idUser id de l'utilisateur-trice
     * @param int $idState id de l'état
     * @return Array
     */
    public static function findParticipatingWithState($idUser, $idState) {
        return executeQuery(
            "SELECT
                idEvaluation, evaModuleNumber, evaDate, evaLength, evaInstructions
                FROM t_evaluation
                    JOIN t_r_userEvaluation ON idEvaluation = fkEvaluation
                WHERE t_r_userEvaluation.fkUser = :idUser AND fkState = :idState
                ORDER BY evaDate DESC;",
            array(array("idUser", $idUser),array("idState", $idState))
        );
    }

    /**
     * Récupère une évaluation par son id
     *
     * @param int $id
     * @return Array
     */
    public static function findOne($id) {
        return executeQuery(
            "SELECT
                idEvaluation, evaModuleNumber, evaDate, evaLength, evaInstructions, t_evaluation.fkUser, fkGroup, fkState, staName, groName
                FROM t_evaluation
                    JOIN t_state ON idState = fkState
                    JOIN t_group ON idGroup = fkGroup
                WHERE idEvaluation = :idEvaluation
                LIMIT 1;",
            array(array("idEvaluation", $id))
        );
    }

    /**
     * Récupère le propriétaire d'une évaluation
     *
     * @param int $id
     * @return Array
     */
    public static function getOwner($id) {
        return executeQuery(
            "SELECT
                idUser, useLogin, useFirstName, useLastName
                FROM t_evaluation
                    JOIN t_user
                    ON fkUser = idUser
                WHERE idEvaluation = :idEvaluation;",
            array(array("idEvaluation", $id))
        );
    }

    /**
     * Récupère les participants à une évaluation
     *
     * @param int $id
     * @return Array
     */
    public static function getParticipants($id) {
        return executeQuery(
            "SELECT
                idUser, useLogin, useFirstName, useLastName
                FROM t_r_userEvaluation
                    JOIN t_user
                    ON fkUser = idUser
                WHERE fkEvaluation = :idEvaluation;",
            array(array("idEvaluation", $id))
        );
    }
    
    /**
     * Récupère les informations sur un participant à une éval
     *
     * @param string $login
     * @param int $idEvaluation
     * @return Array
     */
    public static function getParticipant($login, $idEvaluation) {
        return executeQuery(
            "SELECT
                fkUser, fkEvaluation, useAnonymousId, useReturn, useGrade, useComment
                FROM t_r_userEvaluation
                WHERE fkEvaluation = :idEvaluation AND fkUser = (SELECT idUser FROM t_user WHERE useLogin = :login)
                LIMIT 1;",
            array(array("idEvaluation", $idEvaluation),array("login", $login))
        );
    }

    /**
     * Insère ou modifie une évaluation
     *
     * @param Array $array
     * @return bool
     */
    public static function insertEditOne($array){
        if(!isset($array['idEvaluation'])){
            $array['idEvaluation'] = null;
        } else {
            //Modification d'une évaluation
            if(!isset($array['fkGroup'])){
                $result = executeQuery(
                    "SELECT fkGroup FROM t_evaluation WHERE idEvaluation = :idEvaluation;",
                    array(array("idEvaluation",$array['idEvaluation']))
                );

                if(isset($result[0]['fkGroup'])){
                    $array['fkGroup'] = $result[0]['fkGroup'];
                }
            }

            
        }

        if(!isset($array['evaInstructions'])){
            $array['evaInstructions'] = null;
        }

        if(!isset($array['fkState'])){
            $array['fkState'] = STATE_WAITING;
        }

        if(isset($array['evaModuleNumber']) && isset($array['evaDate']) && isset($array['evaLength']) && isset($array['fkUser']) && isset($array['fkGroup'])){
            //Insertion de l'évaluation
            $result = executeCommand(
                "INSERT
                    INTO t_evaluation (idEvaluation, evaModuleNumber, evaDate, evaLength, evaInstructions, fkUser, fkGroup, fkState)
                    VALUE (:idEvaluation, :evaModuleNumber, :evaDate, :evaLength, :evaInstructions, :fkUser, :fkGroup, :fkState)
                ON DUPLICATE KEY UPDATE
                    evaModuleNumber =:evaModuleNumber, evaDate = :evaDate, evaLength = :evaLength, evaInstructions = :evaInstructions;",
                array(
                    array("idEvaluation",$array['idEvaluation']),
                    array("evaModuleNumber",$array['evaModuleNumber']),
                    array("evaDate",$array['evaDate']),
                    array("evaLength",$array['evaLength']),
                    array("evaInstructions",$array['evaInstructions']),
                    array("fkUser",$array['fkUser']),
                    array("fkGroup",$array['fkGroup']),
                    array("fkState",$array['fkState'])
                )
            );

            if($result && isset($array['anonymousIds'])){
                if($array['idEvaluation'] == null){
                    $array['idEvaluation'] = getLastInsertedID("t_evaluation");
                }

                //Insertion des identifiants anonymes de chaque élève
                foreach($array['anonymousIds'] as $idUser => $anonymousId){
                    executeCommand(
                        "INSERT
                            INTO t_r_userEvaluation (fkEvaluation, fkUser, useAnonymousId)
                            VALUE (:fkEvaluation, :fkUser, :useAnonymousId);",
                        array(
                            array("fkEvaluation",$array['idEvaluation']),
                            array("fkUser",$idUser),
                            array("useAnonymousId",$anonymousId)
                        )
                    );
                }
            }
            return $array['idEvaluation'];
        } else {
            return false;
        }
    }

    /**
     * Ajoute un retour à une évaluation
     *
     * @param int $idEvaluation
     * @param int $idUser
     * @param string $return
     * @return bool
     */
    public static function return($idEvaluation, $idUser, $return){
        //Insertion de l'évaluation
        return executeCommand(
            "UPDATE
                t_r_userEvaluation
                SET useReturn = :useReturn
                WHERE fkEvaluation = :idEvaluation AND fkUser = :idUser;",
            array(
                array("idEvaluation",$idEvaluation),
                array("idUser",$idUser),
                array("useReturn",$return)
            )
        );
    }
    
    /**
     * Modifie l'état d'une évaluation
     *
     * @param int $id
     * @param int $state
     * @return Array
     */
    public static function changeState($id, $state){
        //Insertion de l'évaluation
        return executeCommand(
            "UPDATE
                t_evaluation
                SET fkState = :idState
                WHERE idEvaluation = :idEvaluation;",
            array(
                array("idEvaluation",$id),
                array("idState",$state)
            )
        );
    }
    
    /**
     * Récupère tous les états possibles
     *
     * @return Array
     */
    public static function findAllStates(){
        //Insertion de l'évaluation
        return executeQuery(
            "SELECT
                idState, staName
                FROM t_state;"
        );
    }
}