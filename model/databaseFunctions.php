<?php
/**
 * ETML
 * Autrice : Lucie Moulin
 * Date : 06.05.2021
 * Description : Fonction d'accès à la base de données
 * Inspiré de https://github.com/Raynobrak/php-single-line-db-queries
 */

/**
 * Lie les paramètres à la requête
 *
 * @param  $preparedStatement
 * @param Array $args
 * @return void
 */
function bindValuesToStatement($preparedStatement, $args) {
    foreach($args as $arg){
        $preparedStatement->bindValue($arg[0], $arg[1]);
    }
}

/**
 * Exécute une commande
 *
 * @param string $sql
 * @param Array $args
 * @return Array
 */
function executeCommand($sql, $args = NULL) {
    $connection = ConnectionHolder::getConnection();
    $sql = $connection->prepare($sql);

    if($args != NULL) {
        bindValuesToStatement($sql, $args);
    }

    return $sql->execute();
}

/**
 * Exécute une requête
 *
 * @param string $query
 * @param Array $args
 * @return bool
 */
function executeQuery($query, $args = NULL) {
    $connection = ConnectionHolder::getConnection();
    $query = $connection->prepare($query);

    if($args != NULL) {
        bindValuesToStatement($query, $args);
    }

    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_ASSOC);
    $query->closeCursor();

    return $results;
}

/**
 * Récupère l'id du dernier élément inséré
 *
 * @param string $name Nom de la table
 * @return void
 */
function getLastInsertedID($name = NULL) {
    $connection = ConnectionHolder::getConnection();
    return $connection->lastInsertId($name);
}

?>