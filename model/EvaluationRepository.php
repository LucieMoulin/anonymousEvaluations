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
     * Récupère une évaluation par son id
     *
     * @param int $id
     * @return Array
     */
    public static function findOne($id) {
        return executeQuery(
            "SELECT
                idEvaluation, evaModuleNumber, evaDate, evaLength, evaInstructions, fkUser, fkGroup, fkState
                FROM t_evaluation
                WHERE idEvaluation = :idEvaluation
                ORDER BY evaDate DESC;",
            array(array("idEvaluation", $id))
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
        }

        if(!isset($array['evaInstructions'])){
            $array['evaInstructions'] = null;
        }

        if(!isset($array['fkState'])){
            $array['fkState'] = STATE_WAITING;
        }

        if(isset($array['evaModuleNumber']) && isset($array['evaDate']) &&isset($array['evaLength']) &&isset($array['fkUser']) &&isset($array['fkGroup'])){
            //Insertion de l'évaluation
            $result = executeCommand(
                "INSERT
                    INTO t_evaluation (idEvaluation, evaModuleNumber, evaDate, evaLength, evaInstructions, fkUser, fkGroup, fkState)
                    VALUE (:idEvaluation, :evaModuleNumber, :evaDate, :evaLength, :evaInstructions, :fkUser, :fkGroup, :fkState)
                ON DUPLICATE KEY UPDATE
                    evaModuleNumber =:evaModuleNumber, evaDate = :evaDate, evaLength = :evaLength, evaInstructions = :evaInstructions, fkGroup = :fkGroup, fkState = :fkState;",
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
            return $result;
        } else {
            return false;
        }
    }
}