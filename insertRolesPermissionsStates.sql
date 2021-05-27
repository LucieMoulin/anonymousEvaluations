-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 06 mai 2021 à 10:09
-- Version du serveur :  10.4.17-MariaDB
-- Version de PHP : 8.0.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `db_anonymousevaluations`
--

--
-- Déchargement des données de la table `t_permission`
--

INSERT INTO `t_permission` (`idPermission`, `perCode`, `perDescription`) VALUES
(1, 'SEE_EVAL', 'Permet de voir la liste des évaluations auxquelles la personne a participé ou bien peut participer, ainsi que les détails de ces évaluations'),
(2, 'SEE_EVAL_OWN', 'Permet de voir la liste des évaluations que la personne a créées, ainsi que leurs détails'),
(3, 'SEE_EVAL_ALL', 'Permet de voir toutes les évaluations et leurs détails'),
(4, 'CREATE_EVAL', 'Permet de créer une évaluation'),
(5, 'EDIT_EVAL_OWN', 'Permet de modifier toutes les évaluations que la personne a créées'),
(6, 'EDIT_EVAL_ALL', 'Permet de modifier toutes les évaluations'),
(7, 'EDIT_STATE_OWN', 'Permet de changer l’état des évaluations que la personne a créées'),
(8, 'EDIT_STATE_ALL', 'Permet de changer l’état de toutes les évaluations'),
(9, 'SEE_RETURN_OWN', 'Permet d’accéder aux retours anonymes de toutes les évaluations que la personne a créées'),
(10, 'SEE_RETURN_ALL', 'Permet d’accéder aux retours anonymes de toutes les évaluations'),
(11, 'ADD_GRADE_OWN', 'Permet d’ajouter et de modifier les notes et les commentaires des retours anonymes de toutes les évaluations que la personne a créées'),
(12, 'ADD_GRADE_ALL', 'Permet d’ajouter et de modifier les notes et les commentaires des retours anonymes de toutes les évaluations'),
(13, 'SEE_NAMES_OWN', 'Permet d’afficher la correspondance entre les identifiants anonymes et les noms des élèves participant à une évaluation, pour toutes les évaluations que la personne a créées'),
(14, 'SEE_NAMES_ALL', 'Permet d’afficher la correspondance entre les identifiants anonymes et les noms des élèves participant à une évaluation, pour toutes les évaluations'),
(15, 'RETURN', 'Permet d’effectuer et de modifier un rendu'),
(16, 'CREATE_GROUP', 'Permet de créer des groupes'),
(17, 'EDIT_GROUP_OWN', 'Permet de modifier et supprimer tous les groupes que la personne a créés'),
(18, 'EDIT_GROUP_ALL', 'Permet de modifier et supprimer tous les groupes');

--
-- Déchargement des données de la table `t_role`
--

INSERT INTO `t_role` (`idRole`, `rolName`, `rolDescription`) VALUES
(1, 'Élève', 'Participer à des évaluations'),
(2, 'Enseignant-e', 'Créer et suivre des évaluations'),
(3, 'Admin', '');

--
-- Déchargement des données de la table `t_r_rolepermission`
--

INSERT INTO `t_r_rolepermission` (`fkRole`, `fkPermission`) VALUES
(1, 1),
(1, 15),
(2, 2),
(2, 4),
(2, 5),
(2, 7),
(2, 9),
(2, 11),
(2, 13),
(2, 16),
(2, 17),
(3, 1),
(3, 2),
(3, 3),
(3, 4),
(3, 5),
(3, 6),
(3, 7),
(3, 8),
(3, 9),
(3, 10),
(3, 11),
(3, 12),
(3, 13),
(3, 14),
(3, 15),
(3, 16),
(3, 17),
(3, 18);

--
-- Déchargement des données de la table `t_state`
--

INSERT INTO `t_state` (`idState`, `staName`) VALUES
(1, 'En attente'),
(2, 'Activée'),
(3, 'Clôturée'),
(4, 'Terminée');


COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
