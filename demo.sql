-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : jeu. 27 mai 2021 à 16:22
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
-- Déchargement des données de la table `t_state`
--

INSERT INTO `t_state` (`idState`, `staName`) VALUES
(1, 'En attente'),
(2, 'Activée'),
(3, 'Clôturée'),
(4, 'Terminée');

--
-- Déchargement des données de la table `t_user`
--

INSERT INTO `t_user` (`idUser`, `useLogin`, `useLastName`, `useFirstName`, `fkRole`) VALUES
(1, 'lucmoulin', 'Moulin', 'Lucie', 2),
(2, 'luccharbonnier', 'Charbonnier', 'Lucas', 1),
(3, 'raffelix', 'Félix', 'Rafael', 1),
(4, 'leacherpillod', 'Cherpillod', 'Léa', 1),
(5, 'chrroulin', 'Roulin', 'Christophe', 1);

--
-- Déchargement des données de la table `t_group`
--

INSERT INTO `t_group` (`idGroup`, `groName`, `fkUser`) VALUES
(1, 'MID4', 1);

--
-- Déchargement des données de la table `t_evaluation`
--

INSERT INTO `t_evaluation` (`idEvaluation`, `evaModuleNumber`, `evaDate`, `evaLength`, `evaInstructions`, `fkUser`, `fkGroup`, `fkState`) VALUES
(1, '120', '2021-06-01', '3 périodes', NULL, 1, 1, 4),
(2, '431', '2021-05-28', '1h30', '1622103159-consignes.pdf', 1, 1, 2),
(3, '104', '2021-05-28', '1h', NULL, 1, 1, 1),
(4, '320', '2021-05-21', 'Séquence complète', NULL, 1, 1, 3);

--
-- Déchargement des données de la table `t_r_groupuser`
--

INSERT INTO `t_r_groupuser` (`fkGroup`, `fkUser`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5);

--
-- Déchargement des données de la table `t_r_userevaluation`
--

INSERT INTO `t_r_userevaluation` (`fkEvaluation`, `fkUser`, `useAnonymousId`, `useReturn`, `useGrade`, `useComment`) VALUES
(1, 1, 'iota', NULL, '4.5', 'Attention aux normes de codage'),
(1, 2, 'alpha', NULL, '6', 'Bravo pour votre travail'),
(1, 3, 'epsilon', NULL, '1', 'Aucun rendu'),
(1, 4, 'êta', NULL, '', ''),
(1, 5, 'phi', NULL, '', ''),
(2, 1, 'psi', NULL, NULL, NULL),
(2, 2, 'êta', NULL, NULL, NULL),
(2, 3, 'mu', NULL, NULL, NULL),
(2, 4, 'xi', NULL, NULL, NULL),
(2, 5, 'omicron', NULL, NULL, NULL),
(3, 1, 'nu', NULL, NULL, NULL),
(3, 2, 'lambda', NULL, NULL, NULL),
(3, 3, 'psi', NULL, NULL, NULL),
(3, 4, 'iota', NULL, NULL, NULL),
(3, 5, 'mu', NULL, NULL, NULL),
(4, 1, 'alpha', NULL, NULL, NULL),
(4, 2, 'êta', NULL, NULL, NULL),
(4, 3, 'mu', NULL, NULL, NULL),
(4, 4, 'sigma', NULL, NULL, NULL),
(4, 5, 'chi', NULL, NULL, NULL);

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
