<?php
/**
 * ETML
 * Autrice : Lucie Moulin
 * Date: 06.05.2021
 * Description : Interface de définition des fonctions de bases d'un repository
 */

interface Repository {
    /**
     * Récupère toutes les entités
     *
     * @return Array
     */
    public static function findAll();

    /**
     * Récupère une entité par son id
     *
     * @param int $id
     * @return Array
     */
    public static function findOne($id);

    /**
     * Insère ou modifie une entité
     *
     * @param Array $array
     * @return bool
     */
    public static function insertEditOne($array);
}