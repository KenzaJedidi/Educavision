-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : dim. 01 mars 2026 à 06:51
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `educavision`
--

-- --------------------------------------------------------

--
-- Structure de la table `answer`
--

CREATE TABLE `answer` (
  `id` int(11) NOT NULL,
  `texte` varchar(255) NOT NULL,
  `correct` tinyint(4) NOT NULL,
  `question_id` int(11) NOT NULL,
  `position` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `answer`
--

INSERT INTO `answer` (`id`, `texte`, `correct`, `question_id`, `position`) VALUES
(1, '2', 0, 1, 1),
(2, '3', 0, 1, 1),
(3, '4', 1, 1, 1),
(16, 'shyh', 1, 5, 1),
(17, 'ghaaalt', 0, 5, 1),
(18, 'ghalet', 0, 5, 1),
(19, 'shuuh', 0, 5, 1),
(20, 'rzg', 1, 6, 1),
(21, 'en', 0, 6, 1),
(22, 'rzb', 0, 6, 1),
(23, 'zgrb', 0, 6, 1),
(24, 'af(\'\'\'\'\'\'\'\'\'\'\'\'\'\'', 1, 7, 1),
(25, '\"-hg\"(-h(', 0, 7, 1),
(26, 'akg\',og,', 0, 7, 1),
(27, 'ezfzf', 0, 7, 1),
(28, 'gvjbkjb', 1, 8, 1),
(29, 'lnkn', 0, 8, 1),
(30, 'hbkjkn', 0, 8, 1),
(31, 'jbkln', 0, 8, 1),
(54, 'Un conteneur pour stocker une valeur', 1, 15, 1),
(55, 'Une fonction mathématique', 0, 15, 2),
(56, 'Un type de commentaire', 0, 15, 3),
(57, 'Un opérateur logique', 0, 15, 4),
(58, 'Une structure permettant de répéter un bloc de code', 1, 16, 1),
(59, 'Une méthode de classe', 0, 16, 2),
(60, 'Un type de tableau', 0, 16, 3),
(61, 'Une variable temporaire', 0, 16, 4),
(62, 'Créer une boucle infinie', 0, 17, 1),
(63, 'Déclarer une variable', 0, 17, 2),
(64, 'Exécuter du code selon une condition', 1, 17, 3),
(65, 'Importer une librairie', 0, 17, 4),
(66, 'Un type de commentaire', 0, 18, 1),
(67, 'Une structure de données contenant plusieurs valeurs', 1, 18, 2),
(68, 'Une fonction prédéfinie', 0, 18, 3),
(69, 'Une variable locale', 0, 18, 4),
(70, 'Une boucle infinie', 0, 19, 1),
(71, 'Un type de classe', 0, 19, 2),
(72, 'Un bloc de code réutilisable effectuant une tâche', 1, 19, 3),
(73, 'Un symbole mathématique', 0, 19, 4),
(74, 'Un opérateur logique', 0, 20, 1),
(75, 'Un conteneur pour stocker une valeur', 1, 20, 2),
(76, 'Un type de commentaire', 0, 20, 3),
(77, 'Une fonction mathématique', 0, 20, 4),
(78, 'Une structure permettant de répéter un bloc de code', 1, 21, 1),
(79, 'Une variable temporaire', 0, 21, 2),
(80, 'Un type de tableau', 0, 21, 3),
(81, 'Une méthode de classe', 0, 21, 4),
(82, 'Importer une librairie', 0, 22, 1),
(83, 'Créer une boucle infinie', 0, 22, 2),
(84, 'Déclarer une variable', 0, 22, 3),
(85, 'Exécuter du code selon une condition', 1, 22, 4),
(86, 'Une fonction prédéfinie', 0, 23, 1),
(87, 'Une variable locale', 0, 23, 2),
(88, 'Un type de commentaire', 0, 23, 3),
(89, 'Une structure de données contenant plusieurs valeurs', 1, 23, 4),
(90, 'Un symbole mathématique', 0, 24, 1),
(91, 'Un bloc de code réutilisable effectuant une tâche', 1, 24, 2),
(92, 'Un type de classe', 0, 24, 3),
(93, 'Une boucle infinie', 0, 24, 4),
(94, 'Une fonction mathématique', 0, 25, 1),
(95, 'Un opérateur logique', 0, 25, 2),
(96, 'Un type de commentaire', 0, 25, 3),
(97, 'Un conteneur pour stocker une valeur', 1, 25, 4),
(98, 'Une méthode de classe', 0, 26, 1),
(99, 'Un type de tableau', 0, 26, 2),
(100, 'Une variable temporaire', 0, 26, 3),
(101, 'Une structure permettant de répéter un bloc de code', 1, 26, 4),
(102, 'Créer une boucle infinie', 0, 27, 1),
(103, 'Déclarer une variable', 0, 27, 2),
(104, 'Importer une librairie', 0, 27, 3),
(105, 'Exécuter du code selon une condition', 1, 27, 4),
(106, 'Une fonction prédéfinie', 0, 28, 1),
(107, 'Une structure de données contenant plusieurs valeurs', 1, 28, 2),
(108, 'Une variable locale', 0, 28, 3),
(109, 'Un type de commentaire', 0, 28, 4),
(110, 'Une boucle infinie', 0, 29, 1),
(111, 'Un bloc de code réutilisable effectuant une tâche', 1, 29, 2),
(112, 'Un symbole mathématique', 0, 29, 3),
(113, 'Un type de classe', 0, 29, 4),
(114, 'Un conteneur pour stocker une valeur', 1, 30, 1),
(115, 'Un type de commentaire', 0, 30, 2),
(116, 'Un opérateur logique', 0, 30, 3),
(117, 'Une fonction mathématique', 0, 30, 4),
(118, 'Une structure permettant de répéter un bloc de code', 1, 31, 1),
(119, 'Une variable temporaire', 0, 31, 2),
(120, 'Un type de tableau', 0, 31, 3),
(121, 'Une méthode de classe', 0, 31, 4),
(122, 'Exécuter du code selon une condition', 1, 32, 1),
(123, 'Déclarer une variable', 0, 32, 2),
(124, 'Créer une boucle infinie', 0, 32, 3),
(125, 'Importer une librairie', 0, 32, 4),
(126, 'Une variable locale', 0, 33, 1),
(127, 'Une structure de données contenant plusieurs valeurs', 1, 33, 2),
(128, 'Un type de commentaire', 0, 33, 3),
(129, 'Une fonction prédéfinie', 0, 33, 4),
(130, 'Un type de classe', 0, 34, 1),
(131, 'Une boucle infinie', 0, 34, 2),
(132, 'Un bloc de code réutilisable effectuant une tâche', 1, 34, 3),
(133, 'Un symbole mathématique', 0, 34, 4),
(134, 'Un opérateur logique', 0, 35, 1),
(135, 'Un type de commentaire', 0, 35, 2),
(136, 'Une fonction mathématique', 0, 35, 3),
(137, 'Un conteneur pour stocker une valeur', 1, 35, 4),
(138, 'Un type de tableau', 0, 36, 1),
(139, 'Une structure permettant de répéter un bloc de code', 1, 36, 2),
(140, 'Une méthode de classe', 0, 36, 3),
(141, 'Une variable temporaire', 0, 36, 4),
(142, 'Déclarer une variable', 0, 37, 1),
(143, 'Exécuter du code selon une condition', 1, 37, 2),
(144, 'Importer une librairie', 0, 37, 3),
(145, 'Créer une boucle infinie', 0, 37, 4),
(146, 'Une structure de données contenant plusieurs valeurs', 1, 38, 1),
(147, 'Une variable locale', 0, 38, 2),
(148, 'Une fonction prédéfinie', 0, 38, 3),
(149, 'Un type de commentaire', 0, 38, 4),
(150, 'Un type de classe', 0, 39, 1),
(151, 'Un symbole mathématique', 0, 39, 2),
(152, 'Une boucle infinie', 0, 39, 3),
(153, 'Un bloc de code réutilisable effectuant une tâche', 1, 39, 4),
(154, 'Formulaires\r\nInstallation\r\nÉ Télécharger l’installateur (déjà présent sur Lucien)\r\n→ commande symfony\r\nÉ Nouveau projet :\r\nsymfony new mon_projet lts\r\n→ télécharge le code nécessaire dans le répertoire\r\nmon_projet\r\nÉ Serveur de développement :\r\nphp bin / ', 1, 40, 1),
(155, 'Ceci représente une interprétation erronée du concept', 0, 40, 1),
(156, 'Cette définition ne correspond pas au sujet traité', 0, 40, 1),
(157, 'Cette affirmation est incorrecte dans ce contexte', 0, 40, 1),
(158, 'Une fonction mathématique', 0, 41, 1),
(159, 'Un type de commentaire', 0, 41, 2),
(160, 'Un conteneur pour stocker une valeur', 1, 41, 3),
(161, 'Un opérateur logique', 0, 41, 4),
(162, 'Une méthode de classe', 0, 42, 1),
(163, 'Un type de tableau', 0, 42, 2),
(164, 'Une variable temporaire', 0, 42, 3),
(165, 'Une structure permettant de répéter un bloc de code', 1, 42, 4),
(166, 'Créer une boucle infinie', 0, 43, 1),
(167, 'Exécuter du code selon une condition', 1, 43, 2),
(168, 'Déclarer une variable', 0, 43, 3),
(169, 'Importer une librairie', 0, 43, 4),
(170, 'Une structure de données contenant plusieurs valeurs', 1, 44, 1),
(171, 'Un type de commentaire', 0, 44, 2),
(172, 'Une variable locale', 0, 44, 3),
(173, 'Une fonction prédéfinie', 0, 44, 4),
(174, 'Une boucle infinie', 0, 45, 1),
(175, 'Un bloc de code réutilisable effectuant une tâche', 1, 45, 2),
(176, 'Un symbole mathématique', 0, 45, 3),
(177, 'Un type de classe', 0, 45, 4),
(178, 'Un type de commentaire', 0, 46, 1),
(179, 'Un conteneur pour stocker une valeur', 1, 46, 2),
(180, 'Une fonction mathématique', 0, 46, 3),
(181, 'Un opérateur logique', 0, 46, 4),
(182, 'Une structure permettant de répéter un bloc de code', 1, 47, 1),
(183, 'Un type de tableau', 0, 47, 2),
(184, 'Une variable temporaire', 0, 47, 3),
(185, 'Une méthode de classe', 0, 47, 4),
(186, 'Déclarer une variable', 0, 48, 1),
(187, 'Exécuter du code selon une condition', 1, 48, 2),
(188, 'Importer une librairie', 0, 48, 3),
(189, 'Créer une boucle infinie', 0, 48, 4),
(190, 'Une variable locale', 0, 49, 1),
(191, 'Un type de commentaire', 0, 49, 2),
(192, 'Une structure de données contenant plusieurs valeurs', 1, 49, 3),
(193, 'Une fonction prédéfinie', 0, 49, 4),
(194, 'Un type de classe', 0, 50, 1),
(195, 'Un symbole mathématique', 0, 50, 2),
(196, 'Une boucle infinie', 0, 50, 3),
(197, 'Un bloc de code réutilisable effectuant une tâche', 1, 50, 4),
(198, 'Ceci représente une interprétation erronée du concept', 0, 51, 1),
(199, 'Cette définition ne correspond pas au sujet traité', 0, 51, 1),
(200, 'Formulaires\r\nInstallation\r\nÉ Télécharger l’installateur (déjà présent sur Lucien)\r\n→ commande symfony\r\nÉ Nouveau projet :\r\nsymfony new mon_projet lts\r\n→ télécharge le code nécessaire dans le répertoire\r\nmon_projet\r\nÉ Serveur de développement :\r\nphp bin / ', 1, 51, 1),
(201, 'Cette affirmation est incorrecte dans ce contexte', 0, 51, 1),
(202, '•une action d’un contrôleur (fonction)', 0, 52, 1),
(203, 'Éun contrôleur (classe PHP)', 1, 52, 1),
(204, '•src/AppBundle/Entity', 0, 52, 1),
(205, 'ÉSymfony', 0, 52, 1),
(206, '•une action d’un contrôleur (fonction)', 1, 53, 1),
(207, '•contrôleurs', 0, 53, 1),
(208, '•src/AppBundle/Entity', 0, 53, 1),
(209, 'ÉSymfony', 0, 53, 1),
(210, '•manipulation des bases de données', 0, 54, 1),
(211, '•contrôleurs', 0, 54, 1),
(212, 'ÉSymfony', 0, 54, 1),
(213, '•src/AppBundle/Entity', 1, 54, 1),
(214, 'Vrai', 1, 55, 1),
(215, 'Faux', 0, 55, 1),
(216, 'Vrai', 0, 56, 1),
(217, 'Faux', 1, 56, 1),
(218, 'Vrai', 1, 57, 1),
(219, 'Faux', 0, 57, 1),
(220, 'de generation d’une page', 0, 59, 1),
(221, 'ÉLa partie serveur est coupée en trois morceaux', 0, 59, 1),
(222, 'ÉContrôleurs enPHP(orienté objets)', 0, 59, 1),
(223, 'écoute surhttp', 1, 59, 1),
(224, 'ÉDeux répertoires pour développer', 0, 60, 1),
(225, 'Émais très flexible', 1, 60, 1),
(226, '•service de gestion de la bd (mysql)', 0, 60, 1),
(227, 'Écorrespondance', 0, 60, 1),
(228, '•src/AppBundle/Controller', 0, 61, 1),
(229, 'ÉDeux répertoires pour développer', 0, 61, 1),
(230, 'Écorrespondance', 1, 61, 1),
(231, 'reste', 0, 61, 1),
(232, 'Framework PHP', 1, 62, 1),
(233, 'Principes d’architecture', 0, 62, 1),
(234, 'Configurer Twig', 0, 62, 1),
(235, 'Atelier', 0, 62, 1),
(236, 'Utiliser les migrations', 0, 63, 1),
(237, 'Principes d’architecture', 1, 63, 1),
(238, 'Configurer Twig', 0, 63, 1),
(239, 'Atelier', 0, 63, 1),
(240, 'Configurer Twig', 0, 64, 1),
(241, 'Atelier', 1, 64, 1),
(242, 'Durée', 0, 64, 1),
(243, 'Utiliser les migrations', 0, 64, 1),
(244, 'Durée', 0, 65, 1),
(245, 'Utiliser les migrations', 0, 65, 1),
(246, 'Configurer Twig', 1, 65, 1),
(247, 'Tarifs inter-entreprise', 0, 65, 1),
(248, 'Utiliser les migrations', 1, 66, 1),
(249, 'Tarifs inter-entreprise', 0, 66, 1),
(250, 'Durée', 0, 66, 1),
(251, 'Public', 0, 66, 1),
(252, 'Tarifs inter-entreprise', 0, 67, 1),
(253, 'Méthodologie basée sur l’Active Learning', 0, 67, 1),
(254, 'Public', 0, 67, 1),
(255, 'Durée', 1, 67, 1),
(256, 'Public', 1, 68, 1),
(257, 'Délais d\'accès', 0, 68, 1),
(258, 'Contacts', 0, 68, 1),
(259, 'Méthodologie basée sur l’Active Learning', 0, 68, 1),
(260, 'Créer des pages', 0, 69, 1),
(261, 'Contacts', 1, 69, 1),
(262, 'Délais d\'accès', 0, 69, 1),
(263, 'Anatomie du framework', 0, 69, 1),
(264, 'Créer des pages', 0, 70, 1),
(265, 'Affiner la gestion des routes', 0, 70, 1),
(266, 'Anatomie du framework', 0, 70, 1),
(267, 'Délais d\'accès', 1, 70, 1),
(268, 'Créer des pages', 0, 71, 1),
(269, 'Affiner la gestion des routes', 0, 71, 1),
(270, 'Anatomie du framework', 1, 71, 1),
(271, 'Atelier', 0, 71, 1),
(272, 'Vrai', 0, 72, 1),
(273, 'Faux', 1, 72, 1),
(274, 'Vrai', 1, 73, 1),
(275, 'Faux', 0, 73, 1),
(276, 'Vrai', 0, 74, 1),
(277, 'Faux', 1, 74, 1),
(278, '19 Les services', 0, 75, 1),
(279, 'Notions d’ORM', 1, 75, 1),
(280, 'Utiliser une extension', 0, 75, 1),
(281, 'Dépendances optionnelles', 0, 75, 1),
(282, '19 Les services', 0, 76, 1),
(283, 'Dépendances optionnelles', 0, 76, 1),
(284, 'Utiliser une extension', 1, 76, 1),
(285, 'Pratique', 0, 76, 1),
(286, 'Dépendances optionnelles', 0, 77, 1),
(287, 'Méthode 1', 0, 77, 1),
(288, '19 Les services', 1, 77, 1),
(289, 'Pratique', 0, 77, 1),
(290, 'Dépendances optionnelles', 1, 78, 1),
(291, 'Méthode 1', 0, 78, 1),
(292, 'Méthode 2', 0, 78, 1),
(293, 'Pratique', 0, 78, 1),
(294, 'concentre sur l’essentiel dans son application', 0, 79, 1),
(295, 'Pratique', 1, 79, 1),
(296, 'Méthode 1', 0, 79, 1),
(297, 'Méthode 2', 0, 79, 1),
(298, 'Méthode 1', 0, 80, 1),
(299, 'Pratique', 1, 80, 1),
(300, 'concentre sur l’essentiel dans son application', 0, 80, 1),
(301, 'Méthode 2', 0, 80, 1),
(302, 'Méthode 1', 1, 81, 1),
(303, 'On peut les classer en plusieurs catégories', 0, 81, 1),
(304, 'Méthode 2', 0, 81, 1),
(305, 'concentre sur l’essentiel dans son application', 0, 81, 1),
(306, 'Un opérateur logique', 0, 82, 1),
(307, 'Un type de commentaire', 0, 82, 2),
(308, 'Une fonction mathématique', 0, 82, 3),
(309, 'Un conteneur pour stocker une valeur', 1, 82, 4),
(310, 'Un type de tableau', 0, 83, 1),
(311, 'Une structure permettant de répéter un bloc de code', 1, 83, 2),
(312, 'Une variable temporaire', 0, 83, 3),
(313, 'Une méthode de classe', 0, 83, 4),
(314, 'Exécuter du code selon une condition', 1, 84, 1),
(315, 'Importer une librairie', 0, 84, 2),
(316, 'Créer une boucle infinie', 0, 84, 3),
(317, 'Déclarer une variable', 0, 84, 4),
(318, 'Un type de commentaire', 0, 85, 1),
(319, 'Une variable locale', 0, 85, 2),
(320, 'Une structure de données contenant plusieurs valeurs', 1, 85, 3),
(321, 'Une fonction prédéfinie', 0, 85, 4),
(322, 'Un symbole mathématique', 0, 86, 1),
(323, 'Une boucle infinie', 0, 86, 2),
(324, 'Un bloc de code réutilisable effectuant une tâche', 1, 86, 3),
(325, 'Un type de classe', 0, 86, 4);

-- --------------------------------------------------------

--
-- Structure de la table `candidature`
--

CREATE TABLE `candidature` (
  `id` int(11) NOT NULL,
  `offre_stage_id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `niveau_etude` varchar(100) DEFAULT NULL,
  `cv` varchar(255) DEFAULT NULL,
  `lettre_motivation` longtext DEFAULT NULL,
  `statut` varchar(50) NOT NULL,
  `date_candidature` datetime NOT NULL,
  `score_ia` int(11) DEFAULT NULL,
  `resume_ia` longtext DEFAULT NULL,
  `competences_detectees` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`competences_detectees`)),
  `note_admin` int(11) DEFAULT NULL,
  `commentaire_admin` longtext DEFAULT NULL,
  `favori` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `candidature`
--

INSERT INTO `candidature` (`id`, `offre_stage_id`, `nom`, `prenom`, `email`, `telephone`, `niveau_etude`, `cv`, `lettre_motivation`, `statut`, `date_candidature`, `score_ia`, `resume_ia`, `competences_detectees`, `note_admin`, `commentaire_admin`, `favori`) VALUES
(2, 7, 'nour', 'chiha', 'mariem.najahi@gmail.com', '99452881', 'master', '/uploads/cv/cv_698385bf9ff9b.pdf', 'dhhhhhhhhhhhhhhhhhhhhhhhhhhhhhh', 'En attente', '2026-02-04 18:45:35', NULL, NULL, NULL, NULL, NULL, 0),
(4, 7, 'kenza', 'jdidi', 'kenza.jedidi@gmail.com', '92210012', 'License', '/uploads/cv/cv_69878c839b740.pdf', 'cn,xbcvxbcvcbv c ,vxbcbc,x,cbw,xbw,bx c', 'En attente', '2026-02-07 20:03:31', NULL, NULL, NULL, NULL, NULL, 0),
(5, 10, 'raja', 'arbaoui', 'raja.arbaoui@gmail.com', '92210014', 'master', '/uploads/cv/cv_6987b0e73fe47.pdf', 'je suis à la recherche d’un stage en développement logiciel afin de mettre en pratique mes compétences techniques et d’enrichir mon expérience professionnelle.', 'Accepté', '2026-02-07 22:38:47', NULL, NULL, NULL, NULL, NULL, 0),
(6, 7, 'rayen', 'guissouma', 'rayen.guissouma@gmail.com', '22345116', 'License', '/uploads/cv/cv_6987c6662e5ad.pdf', 'je suis a la recherche de ce genre de stage', 'Accepté', '2026-02-08 00:10:30', NULL, NULL, NULL, NULL, NULL, 0),
(7, 10, 'Najahi', 'Mariem', 'mariem.najahi@esprit.tn', '99452881', 'License', '/uploads/cv/cv_6989121ab30f0.pdf', 'je cherche un stage pour développer mes compétances en télécommunications et développement', 'En attente', '2026-02-08 23:43:48', NULL, NULL, '{\"texte_extrait\":\"\",\"competences_techniques\":[],\"competences_soft\":[],\"langues\":[],\"niveau_estime\":\"Non d\\u00e9termin\\u00e9\",\"resume\":\"Impossible d\'extraire le texte du CV.\"}', NULL, NULL, 0),
(10, 13, 'Najahi', 'Malek', 'malek@gmail.com', '22345116', 'License', '/uploads/cv/cv_699788b98bff3.pdf', 'jdddddddddddddddddddddddddddddddddddddddddddddddddd', 'Refusée', '2026-02-19 23:03:37', 5, 'Score: 5/100\nAnalyse: Le niveau d\'étude du candidat (License) ne correspond pas aux exigences du stage PFE, qui cible des étudiants en dernière année de Master ou Grande École. De plus, la lettre de motivation est totalement inadaptée et non professionnelle, ce qui soulève de sérieuses questions sur le sérieux de la candidature.\nPoints à améliorer: Niveau d\'étude (License) incompatible avec le profil recherché (Master/Grande École en dernière année pour un PFE)., Lettre de motivation vide et non professionnelle, indiquant un manque flagrant d\'intérêt et de sérieux., Absence d\'informations sur l\'intérêt pour le secteur des télécommunications et les compétences techniques ou analytiques.', '{\"competences_techniques\":[\"Analyse de donn\\u00e9es\",\"Reporting\",\"Gestion de bases de donn\\u00e9es\",\"Utilisation d\'outils ATS (Applicant Tracking System)\",\"Statistiques\",\"Familiarit\\u00e9 avec l\'IA dans le recrutement\"],\"competences_soft\":[\"Rigueur\",\"Organisation\",\"Communication \\u00e9crite\",\"Esprit d\'analyse\",\"Autonomie\"],\"langues\":[\"Fran\\u00e7ais\"],\"niveau_estime\":\"Interm\\u00e9diaire\",\"resume\":\"Le profil d\\u00e9montre une expertise dans la g\\u00e9n\\u00e9ration de rapports de recrutement d\\u00e9taill\\u00e9s, incluant l\'analyse statistique et le suivi des candidatures. Il poss\\u00e8de des comp\\u00e9tences en gestion de donn\\u00e9es et en pr\\u00e9sentation d\'indicateurs cl\\u00e9s de performance, avec une familiarit\\u00e9 potentielle avec les outils d\'IA appliqu\\u00e9s au recrutement.\",\"texte_extrait\":\"Rapport de Recrutement - Statistiques G\\u00e9n\\u00e9r\\u00e9 le : 18\\/02\\/2026 \\u00e0 21:37:46 5 Total 2 Accept\\u00e9es 3 En attente 0 Refus\\u00e9es Taux d\'acceptation: 40% Top Offres par nombre de candidatures Offre Nombre de candidatures Stage d\'immersionnnnn 3 stage ouvrier 2 D\\u00e9tail des candidatures Candidat Email Offre Statut Score IA Date nour chiha mariem.najahi@gmail.com Stage d\'immersionnnnn En attente\\u2014 04\\/02\\/2026 kenza jdidi kenza.jedidi@gmail.com Stage d\'immersionnnnn En attente\\u2014 07\\/02\\/2026 raja arbaoui raja.arbaoui@gmail.com stage ouvrier Accept\\u00e9 \\u2014 07\\/02\\/2026 rayen guissouma rayen.guissouma@gmail.com Stage d\'immersionnnnn Accept\\u00e9 \\u2014 08\\/02\\/2026 Najahi Mariem mariem.najahi@esprit.tn stage ouvrier En attente\\u2014 08\\/02\\/2026 EducaVision - Rapport de Recrutement IA\"}', NULL, NULL, 0),
(11, 14, 'Najahi', 'Mariem', 'mariem.najahi@esprit.tn', '99452881', 'master', '/uploads/cv/cv_6999a0df56afb.pdf', 'je suis très motivée pour etre présente dans cette société', 'Refusée', '2026-02-21 13:11:11', 35, 'Score: 35/100\nAnalyse: Le niveau d\'étude Master de la candidate correspond bien aux exigences académiques. Cependant, la lettre de motivation est extrêmement succincte et générique, ne permettant pas d\'évaluer la curiosité, la proactivité et les compétences en communication écrite, pourtant essentielles pour ce stage d\'immersion. Des informations clés sur les soft skills et la maîtrise des outils bureautiques sont manquantes.\nPoints forts: Niveau d\'étude (Master) en adéquation avec le profil recherché (Bac+2 à Bac+5), Expression d\'une motivation (même si très succincte)\nPoints à améliorer: Lettre de motivation trop courte et générique, ne démontrant pas la curiosité, la proactivité ou l\'intérêt spécifique pour le stage d\'immersion ou Oredoo, Absence d\'informations sur les compétences clés (communication, adaptation, esprit d\'équipe) et la maîtrise des outils bureautiques, Difficulté à évaluer les compétences en communication écrite requises', '{\"competences_techniques\":[\"Gestion des utilisateurs et des acc\\u00e8s\",\"D\\u00e9veloppement d\'API (CRUD, Calendrier, G\\u00e9olocalisation)\",\"Conception et optimisation d\'algorithmes (math\\u00e9matiques, logiques, IA)\",\"Impl\\u00e9mentation de syst\\u00e8mes de notification\",\"Agr\\u00e9gation de donn\\u00e9es et reporting (statistiques)\",\"D\\u00e9veloppement de fonctionnalit\\u00e9s e-commerce (panier de commande)\",\"Mise en \\u0153uvre de fonctionnalit\\u00e9s sociales (like\\/dislike)\",\"Mod\\u00e9ration de contenu (signalement, filtrage de mots inappropri\\u00e9s)\",\"Recherche et filtrage multicrit\\u00e8res (AJAX)\",\"Tri de donn\\u00e9es\",\"Utilisation des \\u00e9v\\u00e9nements Symfony\",\"AJAX\"],\"competences_soft\":[\"Analyse et r\\u00e9solution de probl\\u00e8mes\",\"Pens\\u00e9e critique et \\u00e9valuation de la qualit\\u00e9 des impl\\u00e9mentations\",\"Compr\\u00e9hension et impl\\u00e9mentation de la logique m\\u00e9tier\",\"Souci du d\\u00e9tail et de la robustesse des solutions\"],\"langues\":[],\"niveau_estime\":\"Interm\\u00e9diaire\",\"resume\":\"Ce profil d\\u00e9montre une solide exp\\u00e9rience dans le d\\u00e9veloppement de fonctionnalit\\u00e9s web complexes, incluant la gestion des utilisateurs, les API, les algorithmes avanc\\u00e9s et les syst\\u00e8mes de notification. L\'individu met en avant sa capacit\\u00e9 \\u00e0 concevoir des solutions robustes et performantes, avec une compr\\u00e9hension approfondie des exigences techniques et m\\u00e9tier.\",\"texte_extrait\":\"Quelques Exemples pour la partie m\\u00e9tier Exemple de m\\u00e9tiers - Activer\\/d\\u00e9sactiver un attribut ayant une cons\\u00e9quence(Exp Admin bloque ou d\\u00e9bloque un user --> interdiction d\\u2019authentification- archivage-Approuver des t\\u00e2ches par admin (inscription,ou autre...) ) - API calendar (sans crud \\/avec crud) -Algorithme \\u00e0 appliquer (Traitement math\\u00e9matique simple ou \\/ complexe, traitement logique \\u2026..)Evaluation (rating)\\/ optimisation de la complexit\\u00e9 des algorithmes\\/ Algorithmes\\/scripts d\'intelligence artificielle - Notification (exemple Notifier l\\u2019administrateur avant la rupture de stock d\\u2019un produit, Notifier les participants dans le cas ou un \\u00e9v\\u00e9nement est annul\\u00e9\\u2026) - Statistiques (nbr des utilisateurs actifs, nbr des utilisateurs inscrit par mois\\/ann\\u00e9e...) - API de g\\u00e9olocalisation (dynamique) - Une liste des favoris - Panier de commande - Like dislike pour un poste, une activit\\u00e9 ou autre (avec contr\\u00f4le sur un like unique par user) - Signaler un commentaire, un user - liste de mots inappropri\\u00e9s dans un commentaire - Filtrage\\/recherche multicrit\\u00e8res - Recherche Simple \\/ Tri utilisation des services utiliser les \\u00e9v\\u00e8nements de symfony fonctionnalit\\u00e9s avec Ajax Remarques: - Le m\\u00e9tier \\u201crecherche\\u201d effectu\\u00e9 avec Datatable inclue dans le template n\\u2019est pas accept\\u00e9. - Le tri et la recherche simple sont accept\\u00e9s mais ne repr\\u00e9sentent pas un m\\u00e9tier consistant. - La recherche est consid\\u00e9r\\u00e9e comme consistante si elle est r\\u00e9alis\\u00e9e avec Ajax ou\\/ et aussi multicrit\\u00e8res.\"}', NULL, NULL, 0),
(12, 14, 'Chiha', 'Nour', 'nour@gmail.com', '27006516', 'License', '/uploads/cv/cv_6999bb327d5f1.pdf', 'j\'ai hate a vivre une expérience parfaite aec cette équipe innovative', 'Refusée', '2026-02-21 15:03:30', 25, 'Score: 25/100\nAnalyse: Le niveau d\'étude du candidat correspond aux attentes. Cependant, la lettre de motivation est très succincte et contient des erreurs grammaticales et orthographiques notables, ce qui soulève de sérieuses questions quant aux compétences en communication écrite requises. Elle ne permet pas non plus d\'évaluer la curiosité ou la proactivité du candidat.\nPoints forts: Niveau d\'étude (Licence) correspondant au profil recherché (Bac+2 à Bac+5), Manifeste un intérêt pour l\'expérience et l\'entreprise innovante\nPoints à améliorer: Lettre de motivation extrêmement courte et générique, Présence d\'erreurs grammaticales et orthographiques importantes, incompatible avec les \'Bonnes compétences en communication écrite en français\', Manque de démonstration de curiosité, proactivité ou capacité d\'adaptation, Absence d\'information sur la maîtrise des outils bureautiques (Pack Office)', '{\"competences_techniques\":[],\"competences_soft\":[],\"langues\":[],\"niveau_estime\":\"Non identifiable\",\"resume\":\"Le texte fourni est une s\\u00e9quence de num\\u00e9ros et un titre g\\u00e9n\\u00e9rique (\'Partie2\'), ne contenant aucune information descriptive sur des comp\\u00e9tences, des exp\\u00e9riences ou des qualifications. Il est impossible de dresser un r\\u00e9sum\\u00e9 du profil ou d\'identifier des comp\\u00e9tences cl\\u00e9s \\u00e0 partir de ces \\u00e9l\\u00e9ments fragment\\u00e9s.\",\"texte_extrait\":\"2- 3) 4) 5) 6) 7) Partie2: 1) 2) 3) 4)\"}', NULL, NULL, 1),
(13, 15, 'GABSI', 'Chemseddine', 'chemseddine.gabsi@esprit.tn', '22558844', 'Licence', '/uploads/cv/cv_699c281764555.pdf', 'je suis motivé pour etre parmis vous', 'En attente', '2026-02-23 11:12:39', 5, 'Score: 5/100\nAnalyse: La candidature présente un désalignement majeur avec les prérequis du stage PFE, notamment concernant le niveau d\'études. L\'absence totale d\'informations sur les compétences techniques et une lettre de motivation extrêmement générique affaiblissent considérablement le dossier.\nPoints forts: Motivation déclarée (bien que non étayée)', '{\"competences_techniques\":[\"Soins infirmiers\",\"Connaissances m\\u00e9dicales de base\"],\"competences_soft\":[\"Rigueur\",\"Apprentissage rapide\",\"Esprit d\'\\u00e9quipe\",\"Empathie\"],\"langues\":[\"Fran\\u00e7ais\"],\"niveau_estime\":\"Junior\",\"resume\":\"Le candidat a suivi une formation initiale d\'infirmier d\'une dur\\u00e9e de 3 mois, totalisant 100 heures. Il est identifi\\u00e9 comme d\\u00e9butant dans le domaine des soins infirmiers.\",\"texte_extrait\":\"Formation : Infirmier ID : 4 Niveau : debutant Dur\\u00e9e : 3 mois , 100 heures , 12 semaines Description pdjdhhsjhsjdhcjsdhcjvpppppp Pr\\u00e9requis (0) Aucun pr\\u00e9requis n\\u00e9cessaire. Document g\\u00e9n\\u00e9r\\u00e9 le 23\\/02\\/2026 \\u00e0 10:42 EducaVision - Syst\\u00e8me de gestion des formations\"}', 1, NULL, 0);

-- --------------------------------------------------------

--
-- Structure de la table `chapter`
--

CREATE TABLE `chapter` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `ordre` int(11) DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `teacher_name` varchar(255) DEFAULT NULL,
  `teacher_email` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `course_id` int(11) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `enriched_content` longtext DEFAULT NULL,
  `difficulty_level` varchar(50) DEFAULT NULL,
  `translations` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`translations`)),
  `position` int(11) DEFAULT NULL,
  `structured_outline` longtext DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `chapter`
--

INSERT INTO `chapter` (`id`, `titre`, `description`, `ordre`, `image_url`, `teacher_name`, `teacher_email`, `created_at`, `course_id`, `status`, `enriched_content`, `difficulty_level`, `translations`, `position`, `structured_outline`, `updated_at`) VALUES
(1, 'Premiers pas en programmation', 'Dans ce chapitre, nous allons découvrir les concepts fondamentaux de la programmation: variables, boucles, conditions...', 1, NULL, NULL, NULL, '2026-02-06 00:41:17', 1, 'draft', '# Premiers pas en programmation - Contenu Enrichi\n\n## 📚 Introduction\nDans ce chapitre, nous allons découvrir les concepts fondamentaux de la programmation: variables, boucles, conditions...\n\n## 🔑 Concepts Clés\n- **Fondamental**: Les bases à comprendre\n- **Pratique**: Application dans des cas réels\n- **Avancé**: Approfondissements possibles\n\n## 💡 Exemples Concrets\n1. Exemple 1: Cas d\'application simple\n2. Exemple 2: Cas plus complexe\n3. Exemple 3: Cas avancé\n\n[Important: Consultez la documentation officielle pour plus de détails]\n\n## 📝 Points à Retenir\n- Point principal 1\n- Point principal 2\n- Point principal 3\n', 'intermédiaire', '{\"en\":{\"titre\":null,\"description\":null,\"enriched_content\":null}}', NULL, '# 📋 Plan Structuré: Premiers pas en programmation\n\n## Section 1: Introduction et Contexte\n- Définition du sujet\n- Importance et applications\n  - Cas d\'usage 1.1\n  - Cas d\'usage 1.2\n\n## Section 2: Concepts Fondamentaux\n- Concept principal\n- Concepts secondaires\n  - Sous-concept 2.1\n  - Sous-concept 2.2\n  - Sous-concept 2.3\n\n## Section 3: Approfondissements Pratiques\n- Exemple pratique 1\n- Exemple pratique 2\n- Exercices et cas d\'application\n\n## Section 4: Points Avancés et Extensions\n- Développements possibles\n- Ressources complémentaires\n- Liens avec d\'autres domaines\n\n## Section 5: Conclusion et Perspectives\n- Récapitulatif\n- Points clés à retenir\n- Prochaines étapes d\'apprentissage\n', '2026-02-23 02:42:31'),
(2, 'HTML et CSS et JAVA', 'Apprenez les fondamentaux du HTML et CSS et JAVA pour créer des pages web.', 1, 'https://picsum.photos/seed/df0095159d88eaca4940869f9cc5e14a/800/400.jpg', 'rayen', 'rayen.65@school.com', '2026-02-06 00:48:09', 2, 'draft', NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'Design Principles', 'Les principes fondamentaux du design graphique.', 1, NULL, NULL, NULL, '2026-02-06 00:48:09', 4, 'draft', NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'UML', 'ararar', 2, 'https://picsum.photos/seed/df0095159d88eaca4940869f9cc5e14a/800/400.jpg', 'rayen', 'rayen.65@school.com', '2026-02-07 07:36:09', 2, 'draft', NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'Chapitre TLA', 'cccccccccccc', 6, NULL, 'kk kk', 'rayenguissouma2@gmail.com', '2026-02-22 20:40:44', 13, 'draft', NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'java pidev', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', 5, 'https://picsum.photos/seed/df0095159d88eaca4940869f9cc5e14a/800/400.jpg', 'rayen', 'rayen.65@school.com', '2026-02-22 22:40:25', 6, 'draft', NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `conversation_message`
--

CREATE TABLE `conversation_message` (
  `id` int(11) NOT NULL,
  `message_id` int(11) NOT NULL,
  `sender_name` varchar(255) NOT NULL,
  `sender_type` varchar(10) NOT NULL,
  `content` longtext NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `conversation_message`
--

INSERT INTO `conversation_message` (`id`, `message_id`, `sender_name`, `sender_type`, `content`, `created_at`) VALUES
(1, 6, 'rayen', 'student', 'eezdez', '2026-02-07 08:30:38'),
(2, 6, 'rayen (étudiant)', 'teacher', 'ok', '2026-02-07 08:30:38'),
(3, 6, 'rayen', 'student', 'ff\r\n', '2026-02-07 08:30:51'),
(4, 8, 'mariem', 'student', 'salut mr vvvvv', '2026-02-08 19:43:07'),
(5, 8, 'mariem', 'student', 'merci', '2026-02-08 19:44:18'),
(6, 9, 'ggggg', 'student', 'gggggggggg', '2026-02-08 20:39:18'),
(7, 10, 'oussema', 'student', 'rayenguissouma', '2026-02-23 10:49:59');

-- --------------------------------------------------------

--
-- Structure de la table `course`
--

CREATE TABLE `course` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `image_url` varchar(255) DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `pdf_file` varchar(255) DEFAULT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `views` int(11) NOT NULL DEFAULT 0,
  `likes` int(11) NOT NULL DEFAULT 0,
  `comments_count` int(11) NOT NULL DEFAULT 0,
  `popularity_score` decimal(10,2) NOT NULL DEFAULT 0.00,
  `wikipedia_summary` longtext DEFAULT NULL,
  `last_accessed` datetime DEFAULT NULL,
  `keywords` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `course`
--

INSERT INTO `course` (`id`, `titre`, `description`, `image_url`, `price`, `category`, `status`, `created_at`, `pdf_file`, `teacher_id`, `views`, `likes`, `comments_count`, `popularity_score`, `wikipedia_summary`, `last_accessed`, `keywords`) VALUES
(1, 'Introduction à la Programmation', 'Ce cours vous apprendra les bases de la programmation avec des exemples pratiques et des exercices.', NULL, 29.99, 'Programmation', 1, '2026-02-06 00:41:13', NULL, NULL, 4, 0, 0, 0.00, 'Ce cours sur \'Introduction à la Programmation\' couvre les aspects essentiels de Programmation. Ce cours vous apprendra les bases de la programmation avec des exemples pratiques et des. Vous apprendrez les concepts fondamentaux et les meilleures pratiques pour maîtriser ce domaine. Ce cours est conçu pour tous les niveaux d\'apprentissage.', '2026-03-01 06:37:15', NULL),
(2, 'Développement Web', 'Apprenez HTML, CSS, JavaScript et plus pour créer des sites web modernes.', NULL, 39.99, 'Web Design', 1, '2026-02-06 00:48:03', NULL, NULL, 1, 0, 0, 0.00, NULL, '2026-02-23 02:40:19', NULL),
(4, 'Design Graphique', 'Créez des designs magnifiques avec Adobe Photoshop et Illustrator.', NULL, 0.00, 'Design', 1, '2026-02-06 00:48:03', NULL, NULL, 1, 0, 0, 0.00, NULL, '2026-02-23 02:39:54', NULL),
(6, 'mariem nour', 'mariem mariem', 'programmation.jpg', 20.00, 'programmation', 1, '2026-02-08 19:34:33', NULL, NULL, 2, 0, 0, 0.00, NULL, '2026-02-23 03:25:45', NULL),
(8, 'nour', 'ssssssssssss', NULL, NULL, 'math', 1, '2026-02-08 20:27:08', NULL, NULL, 0, 0, 0, 0.00, NULL, NULL, NULL),
(9, 'nour', 'hhhhhhhhhhhhhhhhhh', NULL, NULL, 'programmation', 1, '2026-02-09 06:41:38', 'offres-stage-2026-02-08-23-55-17-69897392bdd51.pdf', 5, 3, 0, 0, 0.00, NULL, '2026-03-01 06:41:16', NULL),
(13, 'TLAAA', 'cccccccccccccggggggggggggggggggg', NULL, 2000.00, 'Design', 1, '2026-02-22 20:40:12', 'Correction-Examen-ANSP-S2-2425-699b5b9cae1ea.pdf', 9, 8, 0, 0, 1.50, 'Moustafa Tlas ou Mustafa Tlass (en arabe : مصطفى طلاس), né le 11 mai 1932 à Rastane (Syrie) et mort le 27 juin 2017 à Bobigny (France), est un militaire et homme politique syrien et ministre de la Défense de Hafez el-Assad et Bachar el-Assad pendant 32 ans.', '2026-03-01 06:37:09', 'tlaaa, cccccccccccccggggggggggggggggggg'),
(14, 'Oussema', 'dddddddddnnnnnnnnnnnnnnnnnnnn', NULL, 45.00, 'Design', 1, '2026-02-22 21:32:57', NULL, NULL, 2, 0, 0, 0.00, 'Oussema Boughanmi, né le 5 février 1990, est un handballeur tunisien jouant au poste d\'ailier gauche. En 2011, il termine avec la Tunisie à la troisième place du championnat du monde junior, où il est élu meilleur ailier gauche de la compétition. Il prend part aux Jeux olympiques d\'été de 2012 où l\'équipe nationale atteint les quarts de finale.', '2026-03-01 05:41:39', 'oussema, dddddddddnnnnnnnnnnnnnnnnnnnn'),
(15, 'cyber', 'cccccccccccccccccccccccccccccccccc', 'cyber-699b69b71e48f.jpg', NULL, 'Design', 0, '2026-02-22 21:40:21', 'fiche-module-pidev-3A-2526-697ffa2e6c4ea-699b69b835941.pdf', NULL, 25, 0, 0, 12.50, 'Cyber est un préfixe à la mode à partir de la deuxième moitié du XXe siècle. Son usage est consécutif au développement exponentiel de l\'informatique et de la robotique, plus généralement à l\'avènement du réseau Internet et de la « révolution numérique », qui en est la synthèse. Le terme réfère à une abstraction englobante et multidisciplinaire des concepts et usages relatifs aux technologies informatiques. Le poids prépondérant d’Internet et des réseaux dans les sociétés modernes induit de facto une omniprésence de la « cyber » dont les champs d’application s’avèrent démesurément vastes (multimédia, communications, robotique et informatique industrielle, intelligence artificielle, etc.). Il est tiré du mot grec kubernân signifiant « gouverner ». Ce préfixe est présent notamment dans cybernétique, cyberespace, cybertexte...', '2026-02-23 10:51:15', 'cyber, cccccccccccccccccccccccccccccccccc'),
(16, 'samfony 2', 'ddddddddddddddddddddddddddddddd', NULL, 55.00, 'Marketing', 1, '2026-02-23 04:04:36', 'prosit-3-699bc4511c24e.pdf', NULL, 5, 0, 0, 1.50, NULL, '2026-03-01 06:26:01', 'samfony, ddddddddddddddddddddddddddddddd'),
(17, 'Math', 'Ce cours a pour objectif de développer la compréhension des concepts fondamentaux des mathématiques et d’améliorer les capacités de raisonnement logique et analytique. Il aborde des notions essentielles telles que l’algèbre, l’analyse, la géométrie et les statistiques. À travers des exercices pratiques et des applications concrètes, les étudiants apprennent à modéliser des problèmes, à interpréter des données et à résoudre des situations complexes de manière rigoureuse et structurée.', 'ath-699bc4b93c5a4.jpg', NULL, 'Langues', 1, '2026-02-23 04:08:38', 'Doccumentation-Groupe2-3A63-699bc4baa78d0.pdf', NULL, 27, 0, 0, 9.50, NULL, '2026-03-01 06:18:26', 'math, cours, objectif, dvelopper, comprhension, concepts, fondamentaux, mathmatiques, damliorer, capacits'),
(18, 'Symphony', 'Ce cours présente Symfony, un framework PHP open-source pour développer des applications web modulaires et performantes. Il couvre l’installation et la configuration, l’architecture MVC, la gestion des bases de données avec Doctrine, la création de formulaires et la validation des données, ainsi que la sécurité et l’authentification. Les participants apprendront également à créer des services, gérer les dépendances, appliquer les bonnes pratiques de développement et préparer le déploiement d’une application.', NULL, 0.00, 'Développement', 1, '2026-03-01 05:46:41', 'cours-symfony-69a3c4b5ebbe9.pdf', 9, 16, 0, 0, 0.00, 'Ce cours sur \'Symphony\' couvre les aspects essentiels de Développement. Ce cours présente Symfony, un framework PHP open-source pour développer des applications web modulaires et. Vous apprendrez les concepts fondamentaux et les meilleures pratiques pour maîtriser ce domaine. Ce cours est conçu pour tous les niveaux d\'apprentissage.', '2026-03-01 06:47:04', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20260201111349', '2026-02-01 12:14:10', 266),
('DoctrineMigrations\\Version20260203000000', '2026-02-21 01:32:12', 8),
('DoctrineMigrations\\Version20260207040000', '2026-02-21 01:32:29', 7),
('DoctrineMigrations\\Version20260207231500', '2026-02-21 01:32:29', 14),
('DoctrineMigrations\\Version20260209120000', '2026-02-21 01:32:44', 34),
('DoctrineMigrations\\Version20260218150000', '2026-02-21 01:32:44', 12),
('DoctrineMigrations\\Version20260221120000', '2026-02-21 14:07:46', 343),
('DoctrineMigrations\\Version20260221131000', '2026-02-21 14:39:18', 15),
('DoctrineMigrations\\Version20260222195544', '2026-02-22 20:56:19', 114),
('DoctrineMigrations\\Version20260222201404', '2026-02-22 21:14:31', 118),
('DoctrineMigrations\\Version20260223000000', '2026-02-23 02:21:22', 109),
('DoctrineMigrations\\Version20260223150000', '2026-02-23 02:48:06', 319),
('DoctrineMigrations\\Version20260223160000', '2026-02-23 02:49:51', 26),
('DoctrineMigrations\\Version20260223170000', '2026-02-23 02:51:48', 27),
('DoctrineMigrations\\Version20260223180000', '2026-02-23 06:03:20', 99),
('DoctrineMigrations\\Version20260225120000', '2026-02-27 23:30:21', 78),
('DoctrineMigrations\\Version20260225121000', '2026-02-27 23:30:21', 8);

-- --------------------------------------------------------

--
-- Structure de la table `filiere`
--

CREATE TABLE `filiere` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `responsable` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `date_creation` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `filiere`
--

INSERT INTO `filiere` (`id`, `nom`, `description`, `responsable`, `image`, `date_creation`) VALUES
(2, 'Médecine', 'jfkjejjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjj', 'mariem', 'medecine.jpg', '2026-02-04 22:49:48');

-- --------------------------------------------------------

--
-- Structure de la table `formation`
--

CREATE TABLE `formation` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `duree` varchar(100) NOT NULL,
  `niveau` varchar(50) NOT NULL,
  `prerequis_texte` longtext DEFAULT NULL,
  `competences_acquises` longtext DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `debouches` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `formation`
--

INSERT INTO `formation` (`id`, `nom`, `description`, `duree`, `niveau`, `prerequis_texte`, `competences_acquises`, `image`, `debouches`) VALUES
(4, 'Infirmier', 'pdjdhhsjhsjdhcjsdhcjvpppppp', '3 mois , 100 heures , 12 semaines', 'debutant', 'bac', 'soft skills', NULL, 'jsgdjcvdjcgv');

-- --------------------------------------------------------

--
-- Structure de la table `message`
--

CREATE TABLE `message` (
  `id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` longtext NOT NULL,
  `student` varchar(255) DEFAULT NULL,
  `teacher` varchar(255) DEFAULT NULL,
  `is_read` int(11) NOT NULL,
  `created_at` datetime NOT NULL,
  `last_message_at` datetime DEFAULT NULL,
  `message_count` int(11) NOT NULL,
  `last_message` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `message`
--

INSERT INTO `message` (`id`, `title`, `content`, `student`, `teacher`, `is_read`, `created_at`, `last_message_at`, `message_count`, `last_message`) VALUES
(1, 'Cours : Introduction Ó la Programmation', 'aaaaaaaaaaaaaaaaaaaaaaaa', 'rayen', 'oussema - Professeur', 1, '2026-02-07 07:44:24', '2026-02-07 08:03:48', 5, 'hello'),
(2, 'Cours : DÚveloppement Web', 'Bonjour professeur, j\'ai une question sur le cours de dÚveloppement web.', 'Alice Martin', 'Prof. Dupont', 1, '2026-02-07 04:00:38', '2026-02-07 04:09:49', 2, 'Bonjour Alice, merci pour votre question. Le cours de dÚveloppement web couvre les bases de HTML, CSS et JavaScript. Pour commencer, je vous recommande de consulter les chapitres 1 Ó 3. N\'hÚsitez pas si vous avez d\'autres questions !'),
(3, 'Cours : Base de DonnÚes SQL', 'Pouvez-vous m\'aider avec le projet de design ?', 'Bob Wilson', 'Prof. Lefebvre', 1, '2026-02-07 04:00:38', '2026-02-07 04:09:53', 2, 'Bonjour Bob, bien s¹r ! Pour le projet de design, commencez par crÚer une maquette sur papier. Ensuite, utilisez les outils vus en cours : Photoshop, Illustrator ou Figma. Le chapitre sur la thÚorie des couleurs vous aidera beaucoup. Bon courage !'),
(4, 'Cours : Design Graphique', 'Je ne comprends pas le chapitre sur le SEO', 'Claire Dubois', 'Prof. Martin', 1, '2026-02-07 04:00:38', '2026-02-07 04:09:58', 2, 'Bonjour Claire, le SEO (Search Engine Optimization) peut sembler complexe au dÚbut. Concentrez-vous sur les 3 piliers : technique (balises HTML), contenu de qualitÚ et backlinks. Le chapitre 4 explique les bases. Prenez votre temps et pratiquez rÚguliÞrement.'),
(5, 'Cours : DÚveloppement Web', 'nnnnnnnnnnnnnnnnnnnnnn', 'rayen', 'Développement Web - Professeur', 1, '2026-02-07 08:04:47', '2026-02-07 08:18:45', 6, 'helooo'),
(6, 'Cours : Développement Web', 'eezdez', 'rayen', 'rayen (étudiant)', 1, '2026-02-07 08:16:30', '2026-02-08 00:13:50', 10, 'ygjjfjgjgjgj'),
(7, 'Cours : Développement Web', 'le dernier chapitre n\'est pas encore visible ', 'mariem', 'Développement Web - Professeur', 0, '2026-02-07 20:13:02', '2026-02-07 20:13:02', 2, 'le dernier chapitre n\'est pas encore visible '),
(8, 'Cours : mariem nour', 'salut mr vvvvv', 'mariem', 'mariem nour - Professeur', 0, '2026-02-08 19:42:59', '2026-02-08 19:44:18', 4, 'merci'),
(9, 'Cours : nour', 'gggggggggg', 'ggggg', 'nour - Professeur', 1, '2026-02-08 20:39:11', '2026-02-08 20:39:51', 3, 'vvvvvvvvvvvvvvvv'),
(10, 'Cours : cyber', 'rayenguissouma', 'oussema', 'cyber - Professeur', 1, '2026-02-23 10:49:52', '2026-02-23 10:50:46', 3, 'merci*\r\n');

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext NOT NULL,
  `headers` longtext NOT NULL,
  `queue_name` varchar(190) NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `messenger_messages`
--

INSERT INTO `messenger_messages` (`id`, `body`, `headers`, `queue_name`, `created_at`, `available_at`, `delivered_at`) VALUES
(1, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:2082:\\\"<!DOCTYPE html>\r\n<html lang=\\\"fr\\\">\r\n<head><meta charset=\\\"UTF-8\\\"><meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\"></head>\r\n<body style=\\\"margin:0;padding:0;background:#f5f7fb;font-family:\\\'Segoe UI\\\',Arial,sans-serif;\\\">\r\n<table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#f5f7fb;padding:40px 0;\\\">\r\n  <tr><td align=\\\"center\\\">\r\n    <table width=\\\"560\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);\\\">\r\n      <tr><td style=\\\"background:linear-gradient(135deg,#f7971e,#ffd200);padding:40px;text-align:center;\\\">\r\n        <div style=\\\"font-size:56px;margin-bottom:12px;\\\">⚠️</div>\r\n        <h1 style=\\\"color:#1a1a2e;margin:0;font-size:26px;font-weight:700;\\\">Compte suspendu</h1>\r\n        <p style=\\\"color:rgba(26,26,46,0.75);margin:8px 0 0;font-size:15px;\\\">Notification EducaVision</p>\r\n      </td></tr>\r\n      <tr><td style=\\\"padding:40px 48px;\\\">\r\n        <p style=\\\"color:#555;font-size:16px;line-height:1.7;\\\">Bonjour <strong>aaa dv</strong>,</p>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Votre compte EducaVision a été temporairement <strong>suspendu</strong> par l\\\'administrateur.</p>\r\n        <div style=\\\"background:#fefce8;border:1px solid #fde68a;border-radius:12px;padding:20px 24px;margin:24px 0;\\\">\r\n          <p style=\\\"color:#854d0e;margin:0 0 8px;font-size:15px;\\\"><strong>📅 Suspendu jusqu\\\'au :</strong> 03/03/2026 à 02:35</p>\r\n          <p style=\\\"color:#854d0e;margin:0;font-size:15px;\\\"><strong>📝 Raison :</strong> Test de suspension automatique</p>\r\n        </div>\r\n        <p style=\\\"color:#777;font-size:14px;line-height:1.6;\\\">Si vous pensez qu\\\'il s\\\'agit d\\\'une erreur, veuillez contacter notre équipe d\\\'administration.</p>\r\n      </td></tr>\r\n      <tr><td style=\\\"background:#f8f9fa;padding:24px 48px;text-align:center;border-top:1px solid #eee;\\\">\r\n        <p style=\\\"color:#aaa;font-size:12px;margin:0;\\\">© 2026 EducaVision — Tous droits réservés</p>\r\n      </td></tr>\r\n    </table>\r\n  </td></tr>\r\n</table>\r\n</body>\r\n</html>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:24:\\\"no-reply@educavision.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:11:\\\"EducaVision\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:25:\\\"rayenguissouma1@gmail.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:6:\\\"aaa dv\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:48:\\\"⚠️ Votre compte EducaVision a été suspendu\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-02-28 01:35:33', '2026-02-28 01:35:33', NULL),
(2, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:2996:\\\"<!DOCTYPE html>\r\n<html lang=\\\"fr\\\">\r\n<head>\r\n<meta charset=\\\"UTF-8\\\">\r\n<meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\">\r\n</head>\r\n<body style=\\\"margin:0;padding:0;background:#f5f7fb;font-family:\\\'Segoe UI\\\',Arial,sans-serif;\\\">\r\n<table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#f5f7fb;padding:40px 0;\\\">\r\n  <tr><td align=\\\"center\\\">\r\n    <table width=\\\"560\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);\\\">\r\n      <!-- Header -->\r\n      <tr><td style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);padding:40px;text-align:center;\\\">\r\n        <h1 style=\\\"color:#ffffff;margin:0;font-size:28px;font-weight:700;\\\">🎓 EducaVision</h1>\r\n        <p style=\\\"color:rgba(255,255,255,0.85);margin:8px 0 0;font-size:15px;\\\">Plateforme d\\\'apprentissage en ligne</p>\r\n      </td></tr>\r\n      <!-- Body -->\r\n      <tr><td style=\\\"padding:40px 48px;\\\">\r\n        <h2 style=\\\"color:#1a1a2e;font-size:22px;margin:0 0 16px;\\\">Réinitialisation du mot de passe</h2>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Bonjour <strong>iheb bellara</strong>,</p>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte. Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>\r\n        <div style=\\\"text-align:center;margin:32px 0;\\\">\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/648312f669c8b3a0f75aafa6bc894db8e123d57156e224345913e9473adf674e\\\" style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);color:#ffffff;text-decoration:none;padding:16px 40px;border-radius:50px;font-size:16px;font-weight:600;display:inline-block;box-shadow:0 4px 15px rgba(102,126,234,0.4);\\\">\r\n            🔐 Réinitialiser mon mot de passe\r\n          </a>\r\n        </div>\r\n        <div style=\\\"background:#fff8e1;border-left:4px solid #f59e0b;border-radius:8px;padding:16px 20px;margin:24px 0;\\\">\r\n          <p style=\\\"color:#92400e;margin:0;font-size:14px;\\\">⏰ Ce lien est valable <strong>24 heures</strong>. Si vous n\\\'avez pas demandé cette réinitialisation, ignorez cet email.</p>\r\n        </div>\r\n        <p style=\\\"color:#999;font-size:13px;margin-top:24px;\\\">Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/648312f669c8b3a0f75aafa6bc894db8e123d57156e224345913e9473adf674e\\\" style=\\\"color:#667eea;word-break:break-all;\\\">http://127.0.0.1:8000/reinitialiser-mot-de-passe/648312f669c8b3a0f75aafa6bc894db8e123d57156e224345913e9473adf674e</a>\r\n        </p>\r\n      </td></tr>\r\n      <!-- Footer -->\r\n      <tr><td style=\\\"background:#f8f9fa;padding:24px 48px;text-align:center;border-top:1px solid #eee;\\\">\r\n        <p style=\\\"color:#aaa;font-size:12px;margin:0;\\\">© 2026 EducaVision — Tous droits réservés</p>\r\n      </td></tr>\r\n    </table>\r\n  </td></tr>\r\n</table>\r\n</body>\r\n</html>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:24:\\\"no-reply@educavision.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:11:\\\"EducaVision\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"bellaraiheb@gmail.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:12:\\\"iheb bellara\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:58:\\\"🔐 Réinitialisation de votre mot de passe - EducaVision\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-02-28 23:22:01', '2026-02-28 23:22:01', NULL),
(3, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:2996:\\\"<!DOCTYPE html>\r\n<html lang=\\\"fr\\\">\r\n<head>\r\n<meta charset=\\\"UTF-8\\\">\r\n<meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\">\r\n</head>\r\n<body style=\\\"margin:0;padding:0;background:#f5f7fb;font-family:\\\'Segoe UI\\\',Arial,sans-serif;\\\">\r\n<table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#f5f7fb;padding:40px 0;\\\">\r\n  <tr><td align=\\\"center\\\">\r\n    <table width=\\\"560\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);\\\">\r\n      <!-- Header -->\r\n      <tr><td style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);padding:40px;text-align:center;\\\">\r\n        <h1 style=\\\"color:#ffffff;margin:0;font-size:28px;font-weight:700;\\\">🎓 EducaVision</h1>\r\n        <p style=\\\"color:rgba(255,255,255,0.85);margin:8px 0 0;font-size:15px;\\\">Plateforme d\\\'apprentissage en ligne</p>\r\n      </td></tr>\r\n      <!-- Body -->\r\n      <tr><td style=\\\"padding:40px 48px;\\\">\r\n        <h2 style=\\\"color:#1a1a2e;font-size:22px;margin:0 0 16px;\\\">Réinitialisation du mot de passe</h2>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Bonjour <strong>iheb bellara</strong>,</p>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte. Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>\r\n        <div style=\\\"text-align:center;margin:32px 0;\\\">\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/38e3d74c54995312cf819a6e7d4476ec2e7b7099011b5ac23e70d884e72db628\\\" style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);color:#ffffff;text-decoration:none;padding:16px 40px;border-radius:50px;font-size:16px;font-weight:600;display:inline-block;box-shadow:0 4px 15px rgba(102,126,234,0.4);\\\">\r\n            🔐 Réinitialiser mon mot de passe\r\n          </a>\r\n        </div>\r\n        <div style=\\\"background:#fff8e1;border-left:4px solid #f59e0b;border-radius:8px;padding:16px 20px;margin:24px 0;\\\">\r\n          <p style=\\\"color:#92400e;margin:0;font-size:14px;\\\">⏰ Ce lien est valable <strong>24 heures</strong>. Si vous n\\\'avez pas demandé cette réinitialisation, ignorez cet email.</p>\r\n        </div>\r\n        <p style=\\\"color:#999;font-size:13px;margin-top:24px;\\\">Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/38e3d74c54995312cf819a6e7d4476ec2e7b7099011b5ac23e70d884e72db628\\\" style=\\\"color:#667eea;word-break:break-all;\\\">http://127.0.0.1:8000/reinitialiser-mot-de-passe/38e3d74c54995312cf819a6e7d4476ec2e7b7099011b5ac23e70d884e72db628</a>\r\n        </p>\r\n      </td></tr>\r\n      <!-- Footer -->\r\n      <tr><td style=\\\"background:#f8f9fa;padding:24px 48px;text-align:center;border-top:1px solid #eee;\\\">\r\n        <p style=\\\"color:#aaa;font-size:12px;margin:0;\\\">© 2026 EducaVision — Tous droits réservés</p>\r\n      </td></tr>\r\n    </table>\r\n  </td></tr>\r\n</table>\r\n</body>\r\n</html>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:24:\\\"no-reply@educavision.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:11:\\\"EducaVision\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"bellaraiheb@gmail.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:12:\\\"iheb bellara\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:58:\\\"🔐 Réinitialisation de votre mot de passe - EducaVision\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-03-01 03:32:41', '2026-03-01 03:32:41', NULL),
(4, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:2996:\\\"<!DOCTYPE html>\r\n<html lang=\\\"fr\\\">\r\n<head>\r\n<meta charset=\\\"UTF-8\\\">\r\n<meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\">\r\n</head>\r\n<body style=\\\"margin:0;padding:0;background:#f5f7fb;font-family:\\\'Segoe UI\\\',Arial,sans-serif;\\\">\r\n<table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#f5f7fb;padding:40px 0;\\\">\r\n  <tr><td align=\\\"center\\\">\r\n    <table width=\\\"560\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);\\\">\r\n      <!-- Header -->\r\n      <tr><td style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);padding:40px;text-align:center;\\\">\r\n        <h1 style=\\\"color:#ffffff;margin:0;font-size:28px;font-weight:700;\\\">🎓 EducaVision</h1>\r\n        <p style=\\\"color:rgba(255,255,255,0.85);margin:8px 0 0;font-size:15px;\\\">Plateforme d\\\'apprentissage en ligne</p>\r\n      </td></tr>\r\n      <!-- Body -->\r\n      <tr><td style=\\\"padding:40px 48px;\\\">\r\n        <h2 style=\\\"color:#1a1a2e;font-size:22px;margin:0 0 16px;\\\">Réinitialisation du mot de passe</h2>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Bonjour <strong>iheb bellara</strong>,</p>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte. Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>\r\n        <div style=\\\"text-align:center;margin:32px 0;\\\">\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/deb75a7a2c42d0188d61c0264388b53cf8f7b0a4dc471bc64798b93714913d2e\\\" style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);color:#ffffff;text-decoration:none;padding:16px 40px;border-radius:50px;font-size:16px;font-weight:600;display:inline-block;box-shadow:0 4px 15px rgba(102,126,234,0.4);\\\">\r\n            🔐 Réinitialiser mon mot de passe\r\n          </a>\r\n        </div>\r\n        <div style=\\\"background:#fff8e1;border-left:4px solid #f59e0b;border-radius:8px;padding:16px 20px;margin:24px 0;\\\">\r\n          <p style=\\\"color:#92400e;margin:0;font-size:14px;\\\">⏰ Ce lien est valable <strong>24 heures</strong>. Si vous n\\\'avez pas demandé cette réinitialisation, ignorez cet email.</p>\r\n        </div>\r\n        <p style=\\\"color:#999;font-size:13px;margin-top:24px;\\\">Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/deb75a7a2c42d0188d61c0264388b53cf8f7b0a4dc471bc64798b93714913d2e\\\" style=\\\"color:#667eea;word-break:break-all;\\\">http://127.0.0.1:8000/reinitialiser-mot-de-passe/deb75a7a2c42d0188d61c0264388b53cf8f7b0a4dc471bc64798b93714913d2e</a>\r\n        </p>\r\n      </td></tr>\r\n      <!-- Footer -->\r\n      <tr><td style=\\\"background:#f8f9fa;padding:24px 48px;text-align:center;border-top:1px solid #eee;\\\">\r\n        <p style=\\\"color:#aaa;font-size:12px;margin:0;\\\">© 2026 EducaVision — Tous droits réservés</p>\r\n      </td></tr>\r\n    </table>\r\n  </td></tr>\r\n</table>\r\n</body>\r\n</html>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:24:\\\"no-reply@educavision.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:11:\\\"EducaVision\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"bellaraiheb@gmail.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:12:\\\"iheb bellara\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:58:\\\"🔐 Réinitialisation de votre mot de passe - EducaVision\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-03-01 03:35:52', '2026-03-01 03:35:52', NULL),
(5, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:2996:\\\"<!DOCTYPE html>\r\n<html lang=\\\"fr\\\">\r\n<head>\r\n<meta charset=\\\"UTF-8\\\">\r\n<meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\">\r\n</head>\r\n<body style=\\\"margin:0;padding:0;background:#f5f7fb;font-family:\\\'Segoe UI\\\',Arial,sans-serif;\\\">\r\n<table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#f5f7fb;padding:40px 0;\\\">\r\n  <tr><td align=\\\"center\\\">\r\n    <table width=\\\"560\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);\\\">\r\n      <!-- Header -->\r\n      <tr><td style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);padding:40px;text-align:center;\\\">\r\n        <h1 style=\\\"color:#ffffff;margin:0;font-size:28px;font-weight:700;\\\">🎓 EducaVision</h1>\r\n        <p style=\\\"color:rgba(255,255,255,0.85);margin:8px 0 0;font-size:15px;\\\">Plateforme d\\\'apprentissage en ligne</p>\r\n      </td></tr>\r\n      <!-- Body -->\r\n      <tr><td style=\\\"padding:40px 48px;\\\">\r\n        <h2 style=\\\"color:#1a1a2e;font-size:22px;margin:0 0 16px;\\\">Réinitialisation du mot de passe</h2>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Bonjour <strong>iheb bellara</strong>,</p>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte. Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>\r\n        <div style=\\\"text-align:center;margin:32px 0;\\\">\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/e928b1aa42ae0d4508e653b357b07c5a3964533154e05b3aaf9aaa5d8b28f08e\\\" style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);color:#ffffff;text-decoration:none;padding:16px 40px;border-radius:50px;font-size:16px;font-weight:600;display:inline-block;box-shadow:0 4px 15px rgba(102,126,234,0.4);\\\">\r\n            🔐 Réinitialiser mon mot de passe\r\n          </a>\r\n        </div>\r\n        <div style=\\\"background:#fff8e1;border-left:4px solid #f59e0b;border-radius:8px;padding:16px 20px;margin:24px 0;\\\">\r\n          <p style=\\\"color:#92400e;margin:0;font-size:14px;\\\">⏰ Ce lien est valable <strong>24 heures</strong>. Si vous n\\\'avez pas demandé cette réinitialisation, ignorez cet email.</p>\r\n        </div>\r\n        <p style=\\\"color:#999;font-size:13px;margin-top:24px;\\\">Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/e928b1aa42ae0d4508e653b357b07c5a3964533154e05b3aaf9aaa5d8b28f08e\\\" style=\\\"color:#667eea;word-break:break-all;\\\">http://127.0.0.1:8000/reinitialiser-mot-de-passe/e928b1aa42ae0d4508e653b357b07c5a3964533154e05b3aaf9aaa5d8b28f08e</a>\r\n        </p>\r\n      </td></tr>\r\n      <!-- Footer -->\r\n      <tr><td style=\\\"background:#f8f9fa;padding:24px 48px;text-align:center;border-top:1px solid #eee;\\\">\r\n        <p style=\\\"color:#aaa;font-size:12px;margin:0;\\\">© 2026 EducaVision — Tous droits réservés</p>\r\n      </td></tr>\r\n    </table>\r\n  </td></tr>\r\n</table>\r\n</body>\r\n</html>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:24:\\\"no-reply@educavision.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:11:\\\"EducaVision\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"bellaraiheb@gmail.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:12:\\\"iheb bellara\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:58:\\\"🔐 Réinitialisation de votre mot de passe - EducaVision\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-03-01 03:36:01', '2026-03-01 03:36:01', NULL),
(6, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:2996:\\\"<!DOCTYPE html>\r\n<html lang=\\\"fr\\\">\r\n<head>\r\n<meta charset=\\\"UTF-8\\\">\r\n<meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\">\r\n</head>\r\n<body style=\\\"margin:0;padding:0;background:#f5f7fb;font-family:\\\'Segoe UI\\\',Arial,sans-serif;\\\">\r\n<table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#f5f7fb;padding:40px 0;\\\">\r\n  <tr><td align=\\\"center\\\">\r\n    <table width=\\\"560\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);\\\">\r\n      <!-- Header -->\r\n      <tr><td style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);padding:40px;text-align:center;\\\">\r\n        <h1 style=\\\"color:#ffffff;margin:0;font-size:28px;font-weight:700;\\\">🎓 EducaVision</h1>\r\n        <p style=\\\"color:rgba(255,255,255,0.85);margin:8px 0 0;font-size:15px;\\\">Plateforme d\\\'apprentissage en ligne</p>\r\n      </td></tr>\r\n      <!-- Body -->\r\n      <tr><td style=\\\"padding:40px 48px;\\\">\r\n        <h2 style=\\\"color:#1a1a2e;font-size:22px;margin:0 0 16px;\\\">Réinitialisation du mot de passe</h2>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Bonjour <strong>iheb bellara</strong>,</p>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte. Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>\r\n        <div style=\\\"text-align:center;margin:32px 0;\\\">\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/ed6c15289b451c7d4d3343f2a9a4585fd74c5d7b1b0d47a54fd48a553c6eddea\\\" style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);color:#ffffff;text-decoration:none;padding:16px 40px;border-radius:50px;font-size:16px;font-weight:600;display:inline-block;box-shadow:0 4px 15px rgba(102,126,234,0.4);\\\">\r\n            🔐 Réinitialiser mon mot de passe\r\n          </a>\r\n        </div>\r\n        <div style=\\\"background:#fff8e1;border-left:4px solid #f59e0b;border-radius:8px;padding:16px 20px;margin:24px 0;\\\">\r\n          <p style=\\\"color:#92400e;margin:0;font-size:14px;\\\">⏰ Ce lien est valable <strong>24 heures</strong>. Si vous n\\\'avez pas demandé cette réinitialisation, ignorez cet email.</p>\r\n        </div>\r\n        <p style=\\\"color:#999;font-size:13px;margin-top:24px;\\\">Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/ed6c15289b451c7d4d3343f2a9a4585fd74c5d7b1b0d47a54fd48a553c6eddea\\\" style=\\\"color:#667eea;word-break:break-all;\\\">http://127.0.0.1:8000/reinitialiser-mot-de-passe/ed6c15289b451c7d4d3343f2a9a4585fd74c5d7b1b0d47a54fd48a553c6eddea</a>\r\n        </p>\r\n      </td></tr>\r\n      <!-- Footer -->\r\n      <tr><td style=\\\"background:#f8f9fa;padding:24px 48px;text-align:center;border-top:1px solid #eee;\\\">\r\n        <p style=\\\"color:#aaa;font-size:12px;margin:0;\\\">© 2026 EducaVision — Tous droits réservés</p>\r\n      </td></tr>\r\n    </table>\r\n  </td></tr>\r\n</table>\r\n</body>\r\n</html>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:28:\\\"oussema.laghasghir@esprit.tn\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:11:\\\"EducaVision\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"bellaraiheb@gmail.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:12:\\\"iheb bellara\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:58:\\\"🔐 Réinitialisation de votre mot de passe - EducaVision\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-03-01 03:47:29', '2026-03-01 03:47:29', NULL),
(7, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:2996:\\\"<!DOCTYPE html>\r\n<html lang=\\\"fr\\\">\r\n<head>\r\n<meta charset=\\\"UTF-8\\\">\r\n<meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\">\r\n</head>\r\n<body style=\\\"margin:0;padding:0;background:#f5f7fb;font-family:\\\'Segoe UI\\\',Arial,sans-serif;\\\">\r\n<table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#f5f7fb;padding:40px 0;\\\">\r\n  <tr><td align=\\\"center\\\">\r\n    <table width=\\\"560\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);\\\">\r\n      <!-- Header -->\r\n      <tr><td style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);padding:40px;text-align:center;\\\">\r\n        <h1 style=\\\"color:#ffffff;margin:0;font-size:28px;font-weight:700;\\\">🎓 EducaVision</h1>\r\n        <p style=\\\"color:rgba(255,255,255,0.85);margin:8px 0 0;font-size:15px;\\\">Plateforme d\\\'apprentissage en ligne</p>\r\n      </td></tr>\r\n      <!-- Body -->\r\n      <tr><td style=\\\"padding:40px 48px;\\\">\r\n        <h2 style=\\\"color:#1a1a2e;font-size:22px;margin:0 0 16px;\\\">Réinitialisation du mot de passe</h2>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Bonjour <strong>iheb bellara</strong>,</p>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte. Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>\r\n        <div style=\\\"text-align:center;margin:32px 0;\\\">\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/19ef321fba2e459b579fa3f5d12afc301820a6ac9d9c685fcc96af20e7b58a5c\\\" style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);color:#ffffff;text-decoration:none;padding:16px 40px;border-radius:50px;font-size:16px;font-weight:600;display:inline-block;box-shadow:0 4px 15px rgba(102,126,234,0.4);\\\">\r\n            🔐 Réinitialiser mon mot de passe\r\n          </a>\r\n        </div>\r\n        <div style=\\\"background:#fff8e1;border-left:4px solid #f59e0b;border-radius:8px;padding:16px 20px;margin:24px 0;\\\">\r\n          <p style=\\\"color:#92400e;margin:0;font-size:14px;\\\">⏰ Ce lien est valable <strong>24 heures</strong>. Si vous n\\\'avez pas demandé cette réinitialisation, ignorez cet email.</p>\r\n        </div>\r\n        <p style=\\\"color:#999;font-size:13px;margin-top:24px;\\\">Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/19ef321fba2e459b579fa3f5d12afc301820a6ac9d9c685fcc96af20e7b58a5c\\\" style=\\\"color:#667eea;word-break:break-all;\\\">http://127.0.0.1:8000/reinitialiser-mot-de-passe/19ef321fba2e459b579fa3f5d12afc301820a6ac9d9c685fcc96af20e7b58a5c</a>\r\n        </p>\r\n      </td></tr>\r\n      <!-- Footer -->\r\n      <tr><td style=\\\"background:#f8f9fa;padding:24px 48px;text-align:center;border-top:1px solid #eee;\\\">\r\n        <p style=\\\"color:#aaa;font-size:12px;margin:0;\\\">© 2026 EducaVision — Tous droits réservés</p>\r\n      </td></tr>\r\n    </table>\r\n  </td></tr>\r\n</table>\r\n</body>\r\n</html>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:28:\\\"oussema.laghasghir@esprit.tn\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:11:\\\"EducaVision\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"bellaraiheb@gmail.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:12:\\\"iheb bellara\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:58:\\\"🔐 Réinitialisation de votre mot de passe - EducaVision\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-03-01 03:47:35', '2026-03-01 03:47:35', NULL);
INSERT INTO `messenger_messages` (`id`, `body`, `headers`, `queue_name`, `created_at`, `available_at`, `delivered_at`) VALUES
(8, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:2996:\\\"<!DOCTYPE html>\r\n<html lang=\\\"fr\\\">\r\n<head>\r\n<meta charset=\\\"UTF-8\\\">\r\n<meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\">\r\n</head>\r\n<body style=\\\"margin:0;padding:0;background:#f5f7fb;font-family:\\\'Segoe UI\\\',Arial,sans-serif;\\\">\r\n<table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#f5f7fb;padding:40px 0;\\\">\r\n  <tr><td align=\\\"center\\\">\r\n    <table width=\\\"560\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);\\\">\r\n      <!-- Header -->\r\n      <tr><td style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);padding:40px;text-align:center;\\\">\r\n        <h1 style=\\\"color:#ffffff;margin:0;font-size:28px;font-weight:700;\\\">🎓 EducaVision</h1>\r\n        <p style=\\\"color:rgba(255,255,255,0.85);margin:8px 0 0;font-size:15px;\\\">Plateforme d\\\'apprentissage en ligne</p>\r\n      </td></tr>\r\n      <!-- Body -->\r\n      <tr><td style=\\\"padding:40px 48px;\\\">\r\n        <h2 style=\\\"color:#1a1a2e;font-size:22px;margin:0 0 16px;\\\">Réinitialisation du mot de passe</h2>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Bonjour <strong>iheb bellara</strong>,</p>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte. Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>\r\n        <div style=\\\"text-align:center;margin:32px 0;\\\">\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/8eb77dc862cf61e10471b6de4304e27b35f085866a59c1766d4c3d53eab8959b\\\" style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);color:#ffffff;text-decoration:none;padding:16px 40px;border-radius:50px;font-size:16px;font-weight:600;display:inline-block;box-shadow:0 4px 15px rgba(102,126,234,0.4);\\\">\r\n            🔐 Réinitialiser mon mot de passe\r\n          </a>\r\n        </div>\r\n        <div style=\\\"background:#fff8e1;border-left:4px solid #f59e0b;border-radius:8px;padding:16px 20px;margin:24px 0;\\\">\r\n          <p style=\\\"color:#92400e;margin:0;font-size:14px;\\\">⏰ Ce lien est valable <strong>24 heures</strong>. Si vous n\\\'avez pas demandé cette réinitialisation, ignorez cet email.</p>\r\n        </div>\r\n        <p style=\\\"color:#999;font-size:13px;margin-top:24px;\\\">Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/8eb77dc862cf61e10471b6de4304e27b35f085866a59c1766d4c3d53eab8959b\\\" style=\\\"color:#667eea;word-break:break-all;\\\">http://127.0.0.1:8000/reinitialiser-mot-de-passe/8eb77dc862cf61e10471b6de4304e27b35f085866a59c1766d4c3d53eab8959b</a>\r\n        </p>\r\n      </td></tr>\r\n      <!-- Footer -->\r\n      <tr><td style=\\\"background:#f8f9fa;padding:24px 48px;text-align:center;border-top:1px solid #eee;\\\">\r\n        <p style=\\\"color:#aaa;font-size:12px;margin:0;\\\">© 2026 EducaVision — Tous droits réservés</p>\r\n      </td></tr>\r\n    </table>\r\n  </td></tr>\r\n</table>\r\n</body>\r\n</html>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:28:\\\"oussema.laghasghir@esprit.tn\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:11:\\\"EducaVision\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"bellaraiheb@gmail.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:12:\\\"iheb bellara\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:58:\\\"🔐 Réinitialisation de votre mot de passe - EducaVision\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-03-01 03:51:22', '2026-03-01 03:51:22', NULL),
(9, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:2996:\\\"<!DOCTYPE html>\r\n<html lang=\\\"fr\\\">\r\n<head>\r\n<meta charset=\\\"UTF-8\\\">\r\n<meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\">\r\n</head>\r\n<body style=\\\"margin:0;padding:0;background:#f5f7fb;font-family:\\\'Segoe UI\\\',Arial,sans-serif;\\\">\r\n<table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#f5f7fb;padding:40px 0;\\\">\r\n  <tr><td align=\\\"center\\\">\r\n    <table width=\\\"560\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);\\\">\r\n      <!-- Header -->\r\n      <tr><td style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);padding:40px;text-align:center;\\\">\r\n        <h1 style=\\\"color:#ffffff;margin:0;font-size:28px;font-weight:700;\\\">🎓 EducaVision</h1>\r\n        <p style=\\\"color:rgba(255,255,255,0.85);margin:8px 0 0;font-size:15px;\\\">Plateforme d\\\'apprentissage en ligne</p>\r\n      </td></tr>\r\n      <!-- Body -->\r\n      <tr><td style=\\\"padding:40px 48px;\\\">\r\n        <h2 style=\\\"color:#1a1a2e;font-size:22px;margin:0 0 16px;\\\">Réinitialisation du mot de passe</h2>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Bonjour <strong>iheb bellara</strong>,</p>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte. Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>\r\n        <div style=\\\"text-align:center;margin:32px 0;\\\">\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/1b47cc5bfc7da4685b88306f5def4740e4f1ce74f3ff2d7e9d7d5540ef26490a\\\" style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);color:#ffffff;text-decoration:none;padding:16px 40px;border-radius:50px;font-size:16px;font-weight:600;display:inline-block;box-shadow:0 4px 15px rgba(102,126,234,0.4);\\\">\r\n            🔐 Réinitialiser mon mot de passe\r\n          </a>\r\n        </div>\r\n        <div style=\\\"background:#fff8e1;border-left:4px solid #f59e0b;border-radius:8px;padding:16px 20px;margin:24px 0;\\\">\r\n          <p style=\\\"color:#92400e;margin:0;font-size:14px;\\\">⏰ Ce lien est valable <strong>24 heures</strong>. Si vous n\\\'avez pas demandé cette réinitialisation, ignorez cet email.</p>\r\n        </div>\r\n        <p style=\\\"color:#999;font-size:13px;margin-top:24px;\\\">Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/1b47cc5bfc7da4685b88306f5def4740e4f1ce74f3ff2d7e9d7d5540ef26490a\\\" style=\\\"color:#667eea;word-break:break-all;\\\">http://127.0.0.1:8000/reinitialiser-mot-de-passe/1b47cc5bfc7da4685b88306f5def4740e4f1ce74f3ff2d7e9d7d5540ef26490a</a>\r\n        </p>\r\n      </td></tr>\r\n      <!-- Footer -->\r\n      <tr><td style=\\\"background:#f8f9fa;padding:24px 48px;text-align:center;border-top:1px solid #eee;\\\">\r\n        <p style=\\\"color:#aaa;font-size:12px;margin:0;\\\">© 2026 EducaVision — Tous droits réservés</p>\r\n      </td></tr>\r\n    </table>\r\n  </td></tr>\r\n</table>\r\n</body>\r\n</html>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:28:\\\"oussema.laghasghir@esprit.tn\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:11:\\\"EducaVision\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"bellaraiheb@gmail.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:12:\\\"iheb bellara\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:58:\\\"🔐 Réinitialisation de votre mot de passe - EducaVision\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-03-01 03:51:27', '2026-03-01 03:51:27', NULL),
(10, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:2996:\\\"<!DOCTYPE html>\r\n<html lang=\\\"fr\\\">\r\n<head>\r\n<meta charset=\\\"UTF-8\\\">\r\n<meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\">\r\n</head>\r\n<body style=\\\"margin:0;padding:0;background:#f5f7fb;font-family:\\\'Segoe UI\\\',Arial,sans-serif;\\\">\r\n<table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#f5f7fb;padding:40px 0;\\\">\r\n  <tr><td align=\\\"center\\\">\r\n    <table width=\\\"560\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);\\\">\r\n      <!-- Header -->\r\n      <tr><td style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);padding:40px;text-align:center;\\\">\r\n        <h1 style=\\\"color:#ffffff;margin:0;font-size:28px;font-weight:700;\\\">🎓 EducaVision</h1>\r\n        <p style=\\\"color:rgba(255,255,255,0.85);margin:8px 0 0;font-size:15px;\\\">Plateforme d\\\'apprentissage en ligne</p>\r\n      </td></tr>\r\n      <!-- Body -->\r\n      <tr><td style=\\\"padding:40px 48px;\\\">\r\n        <h2 style=\\\"color:#1a1a2e;font-size:22px;margin:0 0 16px;\\\">Réinitialisation du mot de passe</h2>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Bonjour <strong>iheb bellara</strong>,</p>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte. Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>\r\n        <div style=\\\"text-align:center;margin:32px 0;\\\">\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/c6015b7d43fd3dd211016c6610a9b5510028e0efe8b0757b312040d687cd44e3\\\" style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);color:#ffffff;text-decoration:none;padding:16px 40px;border-radius:50px;font-size:16px;font-weight:600;display:inline-block;box-shadow:0 4px 15px rgba(102,126,234,0.4);\\\">\r\n            🔐 Réinitialiser mon mot de passe\r\n          </a>\r\n        </div>\r\n        <div style=\\\"background:#fff8e1;border-left:4px solid #f59e0b;border-radius:8px;padding:16px 20px;margin:24px 0;\\\">\r\n          <p style=\\\"color:#92400e;margin:0;font-size:14px;\\\">⏰ Ce lien est valable <strong>24 heures</strong>. Si vous n\\\'avez pas demandé cette réinitialisation, ignorez cet email.</p>\r\n        </div>\r\n        <p style=\\\"color:#999;font-size:13px;margin-top:24px;\\\">Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/c6015b7d43fd3dd211016c6610a9b5510028e0efe8b0757b312040d687cd44e3\\\" style=\\\"color:#667eea;word-break:break-all;\\\">http://127.0.0.1:8000/reinitialiser-mot-de-passe/c6015b7d43fd3dd211016c6610a9b5510028e0efe8b0757b312040d687cd44e3</a>\r\n        </p>\r\n      </td></tr>\r\n      <!-- Footer -->\r\n      <tr><td style=\\\"background:#f8f9fa;padding:24px 48px;text-align:center;border-top:1px solid #eee;\\\">\r\n        <p style=\\\"color:#aaa;font-size:12px;margin:0;\\\">© 2026 EducaVision — Tous droits réservés</p>\r\n      </td></tr>\r\n    </table>\r\n  </td></tr>\r\n</table>\r\n</body>\r\n</html>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:28:\\\"oussema.laghasghir@esprit.tn\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:11:\\\"EducaVision\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"bellaraiheb@gmail.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:12:\\\"iheb bellara\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:58:\\\"🔐 Réinitialisation de votre mot de passe - EducaVision\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-03-01 03:52:51', '2026-03-01 03:52:51', NULL),
(11, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;s:56:\\\"Si vous recevez ceci, la configuration SMTP fonctionne !\\\";i:1;s:5:\\\"utf-8\\\";i:2;N;i:3;N;i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:28:\\\"oussema.laghasghir@esprit.tn\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:28:\\\"oussema.laghasghir@esprit.tn\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:0:\\\"\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:41:\\\"Test EducaVision - Configuration email OK\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-03-01 03:56:34', '2026-03-01 03:56:34', NULL),
(12, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:2996:\\\"<!DOCTYPE html>\r\n<html lang=\\\"fr\\\">\r\n<head>\r\n<meta charset=\\\"UTF-8\\\">\r\n<meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\">\r\n</head>\r\n<body style=\\\"margin:0;padding:0;background:#f5f7fb;font-family:\\\'Segoe UI\\\',Arial,sans-serif;\\\">\r\n<table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#f5f7fb;padding:40px 0;\\\">\r\n  <tr><td align=\\\"center\\\">\r\n    <table width=\\\"560\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);\\\">\r\n      <!-- Header -->\r\n      <tr><td style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);padding:40px;text-align:center;\\\">\r\n        <h1 style=\\\"color:#ffffff;margin:0;font-size:28px;font-weight:700;\\\">🎓 EducaVision</h1>\r\n        <p style=\\\"color:rgba(255,255,255,0.85);margin:8px 0 0;font-size:15px;\\\">Plateforme d\\\'apprentissage en ligne</p>\r\n      </td></tr>\r\n      <!-- Body -->\r\n      <tr><td style=\\\"padding:40px 48px;\\\">\r\n        <h2 style=\\\"color:#1a1a2e;font-size:22px;margin:0 0 16px;\\\">Réinitialisation du mot de passe</h2>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Bonjour <strong>iheb bellara</strong>,</p>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte. Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>\r\n        <div style=\\\"text-align:center;margin:32px 0;\\\">\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/45b30036cb8ab5ce88d3e16b49763e9d77b9c57c7316c9ea87f65584f1c861e0\\\" style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);color:#ffffff;text-decoration:none;padding:16px 40px;border-radius:50px;font-size:16px;font-weight:600;display:inline-block;box-shadow:0 4px 15px rgba(102,126,234,0.4);\\\">\r\n            🔐 Réinitialiser mon mot de passe\r\n          </a>\r\n        </div>\r\n        <div style=\\\"background:#fff8e1;border-left:4px solid #f59e0b;border-radius:8px;padding:16px 20px;margin:24px 0;\\\">\r\n          <p style=\\\"color:#92400e;margin:0;font-size:14px;\\\">⏰ Ce lien est valable <strong>24 heures</strong>. Si vous n\\\'avez pas demandé cette réinitialisation, ignorez cet email.</p>\r\n        </div>\r\n        <p style=\\\"color:#999;font-size:13px;margin-top:24px;\\\">Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/45b30036cb8ab5ce88d3e16b49763e9d77b9c57c7316c9ea87f65584f1c861e0\\\" style=\\\"color:#667eea;word-break:break-all;\\\">http://127.0.0.1:8000/reinitialiser-mot-de-passe/45b30036cb8ab5ce88d3e16b49763e9d77b9c57c7316c9ea87f65584f1c861e0</a>\r\n        </p>\r\n      </td></tr>\r\n      <!-- Footer -->\r\n      <tr><td style=\\\"background:#f8f9fa;padding:24px 48px;text-align:center;border-top:1px solid #eee;\\\">\r\n        <p style=\\\"color:#aaa;font-size:12px;margin:0;\\\">© 2026 EducaVision — Tous droits réservés</p>\r\n      </td></tr>\r\n    </table>\r\n  </td></tr>\r\n</table>\r\n</body>\r\n</html>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:28:\\\"oussema.laghasghir@esprit.tn\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:11:\\\"EducaVision\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"bellaraiheb@gmail.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:12:\\\"iheb bellara\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:58:\\\"🔐 Réinitialisation de votre mot de passe - EducaVision\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-03-01 03:57:54', '2026-03-01 03:57:54', NULL),
(13, 'O:36:\\\"Symfony\\\\Component\\\\Messenger\\\\Envelope\\\":2:{s:44:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0stamps\\\";a:1:{s:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\";a:1:{i:0;O:46:\\\"Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\\":1:{s:55:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Stamp\\\\BusNameStamp\\0busName\\\";s:21:\\\"messenger.bus.default\\\";}}}s:45:\\\"\\0Symfony\\\\Component\\\\Messenger\\\\Envelope\\0message\\\";O:51:\\\"Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\\":2:{s:60:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0message\\\";O:28:\\\"Symfony\\\\Component\\\\Mime\\\\Email\\\":6:{i:0;N;i:1;N;i:2;s:2996:\\\"<!DOCTYPE html>\r\n<html lang=\\\"fr\\\">\r\n<head>\r\n<meta charset=\\\"UTF-8\\\">\r\n<meta name=\\\"viewport\\\" content=\\\"width=device-width, initial-scale=1.0\\\">\r\n</head>\r\n<body style=\\\"margin:0;padding:0;background:#f5f7fb;font-family:\\\'Segoe UI\\\',Arial,sans-serif;\\\">\r\n<table width=\\\"100%\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#f5f7fb;padding:40px 0;\\\">\r\n  <tr><td align=\\\"center\\\">\r\n    <table width=\\\"560\\\" cellpadding=\\\"0\\\" cellspacing=\\\"0\\\" style=\\\"background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,0,0,0.08);\\\">\r\n      <!-- Header -->\r\n      <tr><td style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);padding:40px;text-align:center;\\\">\r\n        <h1 style=\\\"color:#ffffff;margin:0;font-size:28px;font-weight:700;\\\">🎓 EducaVision</h1>\r\n        <p style=\\\"color:rgba(255,255,255,0.85);margin:8px 0 0;font-size:15px;\\\">Plateforme d\\\'apprentissage en ligne</p>\r\n      </td></tr>\r\n      <!-- Body -->\r\n      <tr><td style=\\\"padding:40px 48px;\\\">\r\n        <h2 style=\\\"color:#1a1a2e;font-size:22px;margin:0 0 16px;\\\">Réinitialisation du mot de passe</h2>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Bonjour <strong>iheb bellara</strong>,</p>\r\n        <p style=\\\"color:#555;font-size:15px;line-height:1.7;\\\">Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte. Cliquez sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>\r\n        <div style=\\\"text-align:center;margin:32px 0;\\\">\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/4aa999f2dd8594eb1e1f085b848919379da306160d55fd36ad78a347667d3c86\\\" style=\\\"background:linear-gradient(135deg,#667eea,#764ba2);color:#ffffff;text-decoration:none;padding:16px 40px;border-radius:50px;font-size:16px;font-weight:600;display:inline-block;box-shadow:0 4px 15px rgba(102,126,234,0.4);\\\">\r\n            🔐 Réinitialiser mon mot de passe\r\n          </a>\r\n        </div>\r\n        <div style=\\\"background:#fff8e1;border-left:4px solid #f59e0b;border-radius:8px;padding:16px 20px;margin:24px 0;\\\">\r\n          <p style=\\\"color:#92400e;margin:0;font-size:14px;\\\">⏰ Ce lien est valable <strong>24 heures</strong>. Si vous n\\\'avez pas demandé cette réinitialisation, ignorez cet email.</p>\r\n        </div>\r\n        <p style=\\\"color:#999;font-size:13px;margin-top:24px;\\\">Si le bouton ne fonctionne pas, copiez ce lien dans votre navigateur :<br>\r\n          <a href=\\\"http://127.0.0.1:8000/reinitialiser-mot-de-passe/4aa999f2dd8594eb1e1f085b848919379da306160d55fd36ad78a347667d3c86\\\" style=\\\"color:#667eea;word-break:break-all;\\\">http://127.0.0.1:8000/reinitialiser-mot-de-passe/4aa999f2dd8594eb1e1f085b848919379da306160d55fd36ad78a347667d3c86</a>\r\n        </p>\r\n      </td></tr>\r\n      <!-- Footer -->\r\n      <tr><td style=\\\"background:#f8f9fa;padding:24px 48px;text-align:center;border-top:1px solid #eee;\\\">\r\n        <p style=\\\"color:#aaa;font-size:12px;margin:0;\\\">© 2026 EducaVision — Tous droits réservés</p>\r\n      </td></tr>\r\n    </table>\r\n  </td></tr>\r\n</table>\r\n</body>\r\n</html>\\\";i:3;s:5:\\\"utf-8\\\";i:4;a:0:{}i:5;a:2:{i:0;O:37:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\\":2:{s:46:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0headers\\\";a:3:{s:4:\\\"from\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:4:\\\"From\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:28:\\\"oussema.laghasghir@esprit.tn\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:11:\\\"EducaVision\\\";}}}}s:2:\\\"to\\\";a:1:{i:0;O:47:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:2:\\\"To\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:58:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\MailboxListHeader\\0addresses\\\";a:1:{i:0;O:30:\\\"Symfony\\\\Component\\\\Mime\\\\Address\\\":2:{s:39:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0address\\\";s:21:\\\"bellaraiheb@gmail.com\\\";s:36:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Address\\0name\\\";s:12:\\\"iheb bellara\\\";}}}}s:7:\\\"subject\\\";a:1:{i:0;O:48:\\\"Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\\":5:{s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0name\\\";s:7:\\\"Subject\\\";s:56:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lineLength\\\";i:76;s:50:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0lang\\\";N;s:53:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\AbstractHeader\\0charset\\\";s:5:\\\"utf-8\\\";s:55:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\UnstructuredHeader\\0value\\\";s:58:\\\"🔐 Réinitialisation de votre mot de passe - EducaVision\\\";}}}s:49:\\\"\\0Symfony\\\\Component\\\\Mime\\\\Header\\\\Headers\\0lineLength\\\";i:76;}i:1;N;}}s:61:\\\"\\0Symfony\\\\Component\\\\Mailer\\\\Messenger\\\\SendEmailMessage\\0envelope\\\";N;}}', '[]', 'default', '2026-03-01 03:58:01', '2026-03-01 03:58:01', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `metier`
--

CREATE TABLE `metier` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `filiere_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `offre_stage`
--

CREATE TABLE `offre_stage` (
  `id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `entreprise` varchar(255) NOT NULL,
  `lieu` varchar(255) DEFAULT NULL,
  `date_debut` datetime DEFAULT NULL,
  `date_fin` datetime DEFAULT NULL,
  `duree_jours` int(11) NOT NULL,
  `date_creation` datetime NOT NULL,
  `statut` varchar(50) NOT NULL,
  `salaire` decimal(10,2) DEFAULT NULL,
  `competences_requises` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`competences_requises`)),
  `description_ia` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `offre_stage`
--

INSERT INTO `offre_stage` (`id`, `titre`, `description`, `entreprise`, `lieu`, `date_debut`, `date_fin`, `duree_jours`, `date_creation`, `statut`, `salaire`, `competences_requises`, `description_ia`) VALUES
(7, 'Stage d\'immersionnnnn', 'il faut etre compétant et motivéééé', 'EY', 'centre urbain nord', '2026-02-03 14:30:00', '2026-02-19 12:45:00', 7, '2026-02-03 13:17:09', 'Ouvert', 0.00, NULL, NULL),
(8, 'Stage d\'immersion', 'hdshdjsvhjxvhcjsdcvxjsdv', 'ACTIA', 'centre urbain nord', '2026-02-03 14:30:00', '2026-02-04 14:30:00', 5, '2026-02-04 18:09:48', 'Ouvert', 200.00, NULL, NULL),
(9, 'Développeur web', 'jjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjjj', 'ESPRIT', 'TUNIS', '2026-02-03 14:30:00', '2026-02-04 14:30:00', 17, '2026-02-07 21:48:28', 'Fermé', 2300.00, NULL, NULL),
(10, 'stage ouvrier', 'etudiant capable de résoudre des pannes techniques', 'Tunisie télécom', 'ARIENA', '2026-02-03 14:30:00', '2026-02-04 14:30:00', 30, '2026-02-07 22:28:48', 'Ouvert', 300.00, NULL, NULL),
(11, 'stage PFE', 'esprit de groupe et compétant en développement logiciel', 'Focus', 'Ariena', '2026-02-03 14:30:00', '2026-02-19 12:45:00', 12, '2026-02-08 23:53:34', 'Ouvert', 400.00, NULL, NULL),
(12, 'Développeur Web Full Stack', 'Chez TechVision Tunisia, acteur innovant dans le secteur technologique, nous recherchons un(e) Stagiaire Développeur Web Full Stack passionné(e) pour renforcer notre équipe. C\'est une opportunité exceptionnelle de mettre en pratique vos connaissances en PHP et de vous immerger dans des projets stimulants au sein d\'un environnement dynamique et collaboratif.\n\n**Missions principales:**\n\n*   Développer et maintenir des fonctionnalités backend robustes en utilisant PHP et des bases de données MySQL.\n*   Intégrer des interfaces utilisateur', 'TechVision Tunisia', 'TUNIS', '2026-02-03 14:30:00', '2026-02-19 12:45:00', 9, '2026-02-18 22:47:13', 'Ouvert', 2300.00, NULL, NULL),
(13, 'stage PFE', 'PwC, leader mondial des services professionnels, est à la recherche d\'un(e) stagiaire PFE passionné(e) par le secteur des télécommunications. Vous intégrerez nos équipes de conseil et contribuerez à des projets stratégiques et innovants, en mettant à profit vos compétences académiques pour relever des défis concrets et à fort impact dans un environnement dynamique et international.\n\n**Missions principales:**\n\n*   Mener des recherches approfondies et des analyses de marché sur les dernières tendances et innovations dans le secteur des télécommunications (5G, IoT, Cloud, IA appliquée aux réseaux).\n*   Contribuer à l\'élaboration de stratégies de transformation digitale et d\'optimisation des infrastructures pour nos clients du secteur des télécoms.\n*   Participer à la modélisation et à l\'analyse de données pour identifier des opportunités et des leviers de performance.\n*   Rédiger des livrables, des présentations et des rapports d\'analyse destinés aux clients ou aux équipes internes.\n*   Assister les consultants seniors dans la gestion de projet et le suivi des missions.\n\n**Profil recherché:**\n\n*   Étudiant(e) en dernière année d\'une Grande École d\'Ingénieurs, de Commerce ou d\'un Master spécialisé en Télécommunications, Réseaux, Systèmes d\'Information ou équivalent.\n*   Démontrer un intérêt prononcé et de solides connaissances pour le secteur des télécommunications et ses', 'PWC', 'LAC', '2026-02-03 14:30:00', '2026-02-19 12:45:00', 11, '2026-02-18 22:51:42', 'Ouvert', 400.00, NULL, NULL),
(14, 'Stage d\'immersion dans une société', 'Rejoignez Oredoo à Ariana pour un stage d\'immersion unique, conçu pour vous plonger au cœur du fonctionnement d\'une entreprise dynamique. Ce stage vous offre une opportunité privilégiée de découvrir nos différents départements, d\'acquérir une vision globale de nos activités et de développer des compétences transversales essentielles dans un environnement professionnel stimulant.\n\n**Missions principales :**\n\n*   Découvrir et comprendre les processus clés et les interactions entre les différents services (Opérations, Marketing, Ressources Humaines, etc.).\n*   Apporter un soutien opérationnel aux équipes sur des missions variées (administratives, organisationnelles, coordination de projets simples).\n*   Participer à la collecte, l\'analyse et la synthèse d\'informations pour des projets internes ponctuels.\n*   Contribuer à la rédaction de documents, présentations ou rapports d\'activité.\n\n**Profil recherché :**\n\n*   Étudiant(e) en cours de formation supérieure (Bac+2 à Bac+5), toutes filières confondues, recherchant une première expérience professionnelle ou un stage d\'observation.\n*   Curieux(se), proactif(ve) et doté(e) d\'une excellente capacité d\'adaptation et d\'apprentissage rapide.\n*   Bonnes compétences en communication (écrite et orale) en français et esprit d\'équipe.\n*   Maîtrise des outils bureautiques (Pack Office).\n\n**Ce que nous offrons :**\n\n*   Une immersion complète et formatrice au sein d\'une entreprise innovante, vous permettant de découvrir divers métiers et processus.\n*   Un encadrement de proximité et un environnement de travail stimulant, propice à l\'apprentissage et au développement de vos compétences.\n*   L\'opportunité de développer votre réseau professionnel et de jeter les bases d\'une future carrière.', 'Oredoo', 'Ariena', '2026-02-03 14:30:00', '2026-02-19 12:45:00', 16, '2026-02-21 13:08:09', 'Ouvert', 300.00, NULL, NULL),
(15, 'stage pfe', 'Rejoignez HP, un leader mondial de la technologie, pour une opportunité unique de réaliser votre Projet de Fin d\'Études (PFE) au cœur de l\'innovation. Ce stage vous permettra de mettre en pratique vos compétences en développement au sein d\'une équipe dynamique et de contribuer à des projets concrets qui façonneront l\'avenir. Vous serez immergé(e) dans un environnement stimulant où l\'apprentissage et l\'excellence sont encouragés, vous offrant une expérience professionnelle inestimable pour le démarrage de votre carrière.\n\n**Missions principales:**\n\n*   Analyser les besoins fonctionnels et techniques pour la conception et l\'évolution de solutions logicielles.\n*   Participer activement au cycle complet de développement (conception, codage, tests, déploiement) d\'applications ou de modules spécifiques.\n*   Implémenter des fonctionnalités en utilisant des technologies de pointe et en respectant les bonnes pratiques de développement.\n*   Effectuer des tests unitaires, d\'intégration et rédiger la documentation technique nécessaire au projet.\n*   Collaborer étroitement avec les équipes techniques et les chefs de projet pour assurer la réussite de votre PFE.\n\n**Profil recherché:**\n\n*   Étudiant(e) en dernière année de cycle ingénieur ou Master en informatique, génie logiciel ou équivalent.\n*   Maîtrise d\'au moins un langage de programmation moderne (Java, Python, C#, JavaScript, etc.) et familiarité avec les frameworks associés.\n*   Connaissances solides en bases de données (SQL/NoSQL) et en outils de gestion de version (Git).\n*   Autonomie, rigueur, proactivité et excellente capacité à travailler en équipe dans un environnement agile.\n*   Bon niveau en français et en anglais (technique) à l\'écrit comme à l\'oral.\n\n**Ce que nous offrons:**\n\n*   Un accompagnement personnalisé par des experts métiers pour vous guider dans la réalisation de votre PFE et le développement de vos compétences.\n*   L\'opportunité de travailler sur un projet à forte valeur ajoutée au sein d\'une entreprise technologique de renommée mondiale.\n*   Un environnement de travail stimulant, innovant et collaboratif, propice à l\'apprentissage et à l\'épanouissement professionnel.', 'HP', 'tunis', '2026-02-02 08:00:00', '2026-02-10 08:00:00', 10, '2026-02-23 11:11:07', 'Ouvert', 200.00, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `prerequis`
--

CREATE TABLE `prerequis` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` longtext DEFAULT NULL,
  `ordre` int(11) NOT NULL,
  `formation_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Structure de la table `question`
--

CREATE TABLE `question` (
  `id` int(11) NOT NULL,
  `texte` varchar(255) NOT NULL,
  `idquiz` int(11) NOT NULL,
  `points` int(11) DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT 1,
  `difficulty` varchar(50) DEFAULT 'Moyen'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `question`
--

INSERT INTO `question` (`id`, `texte`, `idquiz`, `points`, `position`, `difficulty`) VALUES
(1, '11', 1, NULL, 1, 'Moyen'),
(5, 'akhtar shyh', 4, NULL, 1, 'Moyen'),
(6, 'aaazea', 5, 10, 1, 'Moyen'),
(7, 'aazeazd', 5, 20, 1, 'Moyen'),
(8, 'hbjk,n,kl', 6, 10, 1, 'Moyen'),
(15, 'Qu\'est-ce qu\'une variable en programmation?', 9, NULL, 1, 'Moyen'),
(16, 'Qu\'est-ce qu\'une boucle for?', 9, NULL, 2, 'Moyen'),
(17, 'Quel est le rôle d\'une condition if?', 9, NULL, 3, 'Facile'),
(18, 'Qu\'est-ce qu\'un tableau?', 9, NULL, 4, 'Difficile'),
(19, 'Qu\'est-ce qu\'une fonction?', 9, NULL, 5, 'Facile'),
(20, 'Qu\'est-ce qu\'une variable en programmation?', 10, NULL, 1, 'Difficile'),
(21, 'Qu\'est-ce qu\'une boucle for?', 10, NULL, 2, 'Facile'),
(22, 'Quel est le rôle d\'une condition if?', 10, NULL, 3, 'Moyen'),
(23, 'Qu\'est-ce qu\'un tableau?', 10, NULL, 4, 'Difficile'),
(24, 'Qu\'est-ce qu\'une fonction?', 10, NULL, 5, 'Facile'),
(25, 'Qu\'est-ce qu\'une variable en programmation?', 11, NULL, 1, 'Facile'),
(26, 'Qu\'est-ce qu\'une boucle for?', 11, NULL, 2, 'Difficile'),
(27, 'Quel est le rôle d\'une condition if?', 11, NULL, 3, 'Moyen'),
(28, 'Qu\'est-ce qu\'un tableau?', 11, NULL, 4, 'Facile'),
(29, 'Qu\'est-ce qu\'une fonction?', 11, NULL, 5, 'Moyen'),
(30, 'Qu\'est-ce qu\'une variable en programmation?', 12, NULL, 1, 'Difficile'),
(31, 'Qu\'est-ce qu\'une boucle for?', 12, NULL, 2, 'Moyen'),
(32, 'Quel est le rôle d\'une condition if?', 12, NULL, 3, 'Facile'),
(33, 'Qu\'est-ce qu\'un tableau?', 12, NULL, 4, 'Facile'),
(34, 'Qu\'est-ce qu\'une fonction?', 12, NULL, 5, 'Difficile'),
(35, 'Qu\'est-ce qu\'une variable en programmation?', 13, NULL, 1, 'Facile'),
(36, 'Qu\'est-ce qu\'une boucle for?', 13, NULL, 2, 'Difficile'),
(37, 'Quel est le rôle d\'une condition if?', 13, NULL, 3, 'Facile'),
(38, 'Qu\'est-ce qu\'un tableau?', 13, NULL, 4, 'Difficile'),
(39, 'Qu\'est-ce qu\'une fonction?', 13, NULL, 5, 'Facile'),
(40, 'Quelle affirmation est correcte concernant : Formulaires\r\nInstallation\r\nÉ Télécharger l’installateur (déjà présent ?', 14, 10, 1, 'Moyen'),
(41, 'Qu\'est-ce qu\'une variable en programmation?', 15, NULL, 1, 'Moyen'),
(42, 'Qu\'est-ce qu\'une boucle for?', 15, NULL, 2, 'Difficile'),
(43, 'Quel est le rôle d\'une condition if?', 15, NULL, 3, 'Difficile'),
(44, 'Qu\'est-ce qu\'un tableau?', 15, NULL, 4, 'Facile'),
(45, 'Qu\'est-ce qu\'une fonction?', 15, NULL, 5, 'Difficile'),
(46, 'Qu\'est-ce qu\'une variable en programmation?', 16, NULL, 1, 'Facile'),
(47, 'Qu\'est-ce qu\'une boucle for?', 16, NULL, 2, 'Difficile'),
(48, 'Quel est le rôle d\'une condition if?', 16, NULL, 3, 'Facile'),
(49, 'Qu\'est-ce qu\'un tableau?', 16, NULL, 4, 'Moyen'),
(50, 'Qu\'est-ce qu\'une fonction?', 16, NULL, 5, 'Moyen'),
(51, 'Quelle affirmation est correcte concernant : Formulaires\r\nInstallation\r\nÉ Télécharger l’installateur (déjà présent ?', 17, 10, 1, 'Moyen'),
(52, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « plusieursactions, une action pour »', 18, 10, 1, 'Moyen'),
(53, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « implémente la logique »', 18, 10, 1, 'Moyen'),
(54, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « les classes pour la base de données »', 18, 10, 1, 'Moyen'),
(55, 'D\'après le cours : ÉCrée la classesrc/AppBundle/Entity/Nom_table', 18, 5, 1, 'Moyen'),
(56, 'Le cours ne dit pas que : enrica duchi, sylvain perifel et cristina sirangelo', 18, 5, 1, 'Moyen'),
(57, 'D\'après le cours : L3 Info – Université Paris Diderot', 18, 5, 1, 'Moyen'),
(58, 'D\'après le cours, que pouvez-vous dire sur : ÉSymfony:frameworkcôté serveur basé surPHP ?', 18, 10, 1, 'Moyen'),
(59, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « //localhost:8000 »', 18, 10, 1, 'Moyen'),
(60, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « langage riche, possibilité d’inclure de la »', 18, 10, 1, 'Moyen'),
(61, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « données dans la bd,objets PHP »', 18, 10, 1, 'Moyen'),
(62, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « définition et revue du marché »', 19, 10, 1, 'Moyen'),
(63, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « centrage HTTP, orientation objet, orientation service »', 19, 10, 1, 'Moyen'),
(64, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « Installer, configurer et lancer un projet Symfony »', 19, 10, 1, 'Moyen'),
(65, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « chemin, échappement automatique, variables globales, … »', 19, 10, 1, 'Moyen'),
(66, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « créer le schéma »', 19, 10, 1, 'Moyen'),
(67, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « 3 jours (21 heures) »', 19, 10, 1, 'Moyen'),
(68, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « Développeurs PHP »', 19, 10, 1, 'Moyen'),
(69, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « commercial@dawan »', 19, 10, 1, 'Moyen'),
(70, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « Variable selon le type de financement »', 19, 10, 1, 'Moyen'),
(71, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « le kernel, les composants et les bundles »', 19, 10, 1, 'Moyen'),
(72, 'Le cours ne dit pas que : pour professionnaliser nos méthodes de travail et capitaliser sur notre savoir-faire, je décidais de créer un framework, d’abord réservé à nos', 20, 5, 1, 'Moyen'),
(73, 'D\'après le cours : La version 8 de Drupal par exemple intègre plus de 10 composants essentiels', 20, 5, 1, 'Moyen'),
(74, 'Le cours ne dit pas que : dans les mois à venir, peutêtre utiliserez-vous symfony pour développer des projets pour des clients, aurez-vous', 20, 5, 1, 'Moyen'),
(75, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « utiliser des objets à la place des requêtes »', 20, 10, 1, 'Moyen'),
(76, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « l’exemple de Sluggable »', 20, 10, 1, 'Moyen'),
(77, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « fonctions avancées 389 »', 20, 10, 1, 'Moyen'),
(78, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « les appels de méthodes (calls) »', 20, 10, 1, 'Moyen'),
(79, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « utiliser les convertisseurs existants »', 20, 10, 1, 'Moyen'),
(80, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « utiliser un ConsoleBundle »', 20, 10, 1, 'Moyen'),
(81, 'D\'après le cours, dans quelle section trouve-t-on l\'information suivante ? « envoyer les fichiers sur le serveur par FTP »', 20, 10, 1, 'Moyen'),
(82, 'Qu\'est-ce qu\'une variable en programmation?', 21, NULL, 1, 'Moyen'),
(83, 'Qu\'est-ce qu\'une boucle for?', 21, NULL, 2, 'Facile'),
(84, 'Quel est le rôle d\'une condition if?', 21, NULL, 3, 'Difficile'),
(85, 'Qu\'est-ce qu\'un tableau?', 21, NULL, 4, 'Difficile'),
(86, 'Qu\'est-ce qu\'une fonction?', 21, NULL, 5, 'Moyen');

-- --------------------------------------------------------

--
-- Structure de la table `quiz`
--

CREATE TABLE `quiz` (
  `idquiz` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `visible` tinyint(4) NOT NULL,
  `description` longtext DEFAULT NULL,
  `datecreation` datetime NOT NULL,
  `duree` int(11) DEFAULT NULL,
  `chapter_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'draft',
  `difficulty_level` varchar(50) DEFAULT NULL,
  `time_limit` int(11) NOT NULL DEFAULT 0,
  `number_of_questions` int(11) NOT NULL DEFAULT 0,
  `attempts` int(11) NOT NULL DEFAULT 0,
  `updated_at` datetime DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `quiz`
--

INSERT INTO `quiz` (`idquiz`, `titre`, `visible`, `description`, `datecreation`, `duree`, `chapter_id`, `status`, `difficulty_level`, `time_limit`, `number_of_questions`, `attempts`, `updated_at`, `metadata`) VALUES
(1, 'test', 1, 'test1', '2026-02-07 01:07:34', NULL, NULL, 'published', NULL, 0, 0, 0, NULL, NULL),
(4, 'educ', 1, 'educ', '2026-02-07 03:10:13', NULL, NULL, 'published', NULL, 0, 0, 0, NULL, NULL),
(5, 'reseau', 1, 'efz', '2026-02-07 03:46:23', 1, NULL, 'published', NULL, 0, 0, 0, NULL, NULL),
(6, 'rayen', 1, 'b jn', '2026-02-07 04:49:29', 1, NULL, 'published', NULL, 0, 0, 0, NULL, NULL),
(9, 'Quiz: Design Principles', 1, 'Quiz généré automatiquement pour le chapitre: Design Principles', '2026-02-23 03:21:24', NULL, 5, 'published', 'Moyen', 300, 5, 0, NULL, NULL),
(10, 'Quiz: java pidev', 1, 'Quiz généré automatiquement pour le chapitre: java pidev', '2026-02-23 03:25:03', NULL, 9, 'published', 'Moyen', 100, 5, 0, NULL, NULL),
(11, 'Quiz: HTML et CSS et JAVA', 1, 'Quiz généré automatiquement pour le chapitre: HTML et CSS et JAVA', '2026-02-23 03:29:41', NULL, 2, 'published', 'Moyen', 300, 5, 0, NULL, NULL),
(12, 'Quiz: Chapitre TLA', 1, 'Quiz généré automatiquement pour le chapitre: Chapitre TLA', '2026-02-23 03:43:52', NULL, 8, 'published', 'Moyen', 300, 5, 0, NULL, NULL),
(13, 'Quiz: Design Principles', 1, 'Quiz généré automatiquement pour le chapitre: Design Principles', '2026-02-23 03:46:12', NULL, 5, 'published', 'Facile', 300, 5, 0, NULL, NULL),
(14, 'symfony', 1, 'sqfsqc', '2026-02-23 10:58:35', 30, NULL, 'draft', NULL, 0, 0, 0, NULL, NULL),
(15, 'Quiz: java pidev', 1, 'Quiz généré automatiquement pour le chapitre: java pidev', '2026-02-23 11:03:27', NULL, 9, 'published', 'Difficile', 120, 5, 0, NULL, NULL),
(16, 'Quiz: HTML et CSS et JAVA', 1, 'Quiz généré automatiquement pour le chapitre: HTML et CSS et JAVA', '2026-03-01 00:35:32', NULL, 2, 'published', 'Moyen', 300, 5, 0, NULL, NULL),
(17, 'sympho', 1, 'test', '2026-03-01 01:23:56', 30, NULL, 'draft', NULL, 0, 0, 0, NULL, NULL),
(18, 'fsdf', 1, 'sdfvds', '2026-03-01 02:40:37', 30, NULL, 'draft', NULL, 0, 0, 0, NULL, NULL),
(19, 'gfjhh', 1, 'htgfh', '2026-03-01 03:10:08', 30, NULL, 'draft', NULL, 0, 0, 0, NULL, NULL),
(20, 'th', 1, 'httrh', '2026-03-01 03:42:28', 30, NULL, 'draft', NULL, 0, 0, 0, NULL, NULL),
(21, 'Quiz: HTML et CSS et JAVA', 1, 'Quiz généré automatiquement pour le chapitre: HTML et CSS et JAVA', '2026-03-01 04:08:25', NULL, 2, 'published', 'Moyen', 300, 5, 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `reclamation`
--

CREATE TABLE `reclamation` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(180) NOT NULL,
  `role` varchar(20) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` longtext NOT NULL,
  `status` varchar(30) NOT NULL,
  `date_reclamation` datetime NOT NULL,
  `resume_auto` longtext DEFAULT NULL,
  `sentiment_auto` varchar(20) DEFAULT NULL,
  `temps_resolution_auto` int(11) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reclamation`
--

INSERT INTO `reclamation` (`id`, `nom`, `prenom`, `email`, `role`, `titre`, `description`, `status`, `date_reclamation`, `resume_auto`, `sentiment_auto`, `temps_resolution_auto`, `category`) VALUES
(10, 'Najahi', 'Mariem', 'mariem.najahi@esprit.tn', 'etudiant', 'note', 'je suis très décue de ma note', 'traiter', '2026-02-21 12:42:10', 'je suis très décue...', 'neutre', 24, NULL),
(11, 'Chiha', 'Amira', 'amira@gmail.com', 'etudiant', 'professeur', 'j\'ai un problème de contact avec le professeur je suis fachée', 'traiter', '2026-02-21 13:34:35', 'j\'ai un problème de contact avec...', 'négatif', 24, NULL),
(12, 'Chiha', 'Ridha', 'ridha@gmail.com', 'etudiant', 'note', 'J’ai bien pris connaissance de la note obtenue, cependant je pense ne pas avoir pu consulter correctement la correction ou le détail de l’évaluation. Cela me laisse un sentiment de déception, car j’ai fourni beaucoup d’efforts et je m’attendais à un meilleur résultat. J’aimerais sincèrement pouvoir comprendre les points à améliorer afin de progresser pour la suite.', 'en cours de traitement', '2026-02-21 13:48:55', 'J’aimerais sincèrement pouvoir comprendre les points à améliorer afin de progresser pour...', 'positif', 24, NULL),
(13, 'GABSI', 'Chemseddine', 'chemseddine.gabsi@esprit.tn', 'etudiant', 'note examan', 'J’ai bien pris connaissance de la note obtenue, cependant je pense ne pas avoir pu consulter correctement la correction ou le détail de l’évaluation. Cela me laisse un sentiment de déception, car j’ai fourni beaucoup d’efforts et je m’attendais à un meilleur résultat. J’aimerais sincèrement pouvoir comprendre les points à améliorer afin de progresser pour la suite.', 'en cours de traitement', '2026-02-23 10:21:48', 'J’aimerais sincèrement pouvoir comprendre les points à améliorer afin de progresser pour...', 'positif', 24, 'Note/Évaluation'),
(14, 'GABSI', 'Chemseddine', 'chemseddine.gabsi@esprit.tn', 'professeur', 'note', 'J’ai bien pris connaissance de la note obtenue, cependant je pense ne pas avoir pu consulter correctement la correction ou le détail de l’évaluation. Cela me laisse un sentiment de déception, car j’ai fourni beaucoup d’efforts et je m’attendais à un meilleur résultat. J’aimerais sincèrement pouvoir comprendre les points à améliorer afin de progresser pour la suite.', 'traiter', '2026-02-23 10:25:51', 'J’aimerais sincèrement pouvoir comprendre les points à améliorer afin de progresser pour...', 'positif', 24, 'Note/Évaluation'),
(15, 'fze', 'fezf', 'bellaraiheb@gmail.com', 'etudiant', 'fzefz', 'zeefzef', 'en cours de traitement', '2026-03-01 00:29:44', 'zeefzef', 'neutre', 24, 'Autre');

-- --------------------------------------------------------

--
-- Structure de la table `reponse`
--

CREATE TABLE `reponse` (
  `id` int(11) NOT NULL,
  `reclamation_id` int(11) NOT NULL,
  `contenu` longtext NOT NULL,
  `date_reponse` datetime NOT NULL,
  `rating` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `reponse`
--

INSERT INTO `reponse` (`id`, `reclamation_id`, `contenu`, `date_reponse`, `rating`) VALUES
(18, 10, 'd\'accord nous allons vous vérifier ta note très bientot', '2026-02-21 13:50:58', 2),
(19, 11, 'nous allons contacter le prof t\'inquiète pas !!', '2026-02-21 14:53:50', 5),
(20, 14, 'd\'accord', '2026-02-23 10:27:53', 3);

-- --------------------------------------------------------

--
-- Structure de la table `result`
--

CREATE TABLE `result` (
  `idresult` int(11) NOT NULL,
  `score` int(11) NOT NULL,
  `utilisateur` varchar(255) NOT NULL,
  `datepassage` datetime NOT NULL,
  `idquiz` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `result`
--

INSERT INTO `result` (`idresult`, `score`, `utilisateur`, `datepassage`, `idquiz`) VALUES
(1, 1, '::1', '2026-02-07 02:23:44', 1),
(2, 1, '::1', '2026-02-07 02:23:54', 1),
(3, 1, '::1', '2026-02-07 02:25:03', 1),
(4, 1, '::1', '2026-02-07 02:27:22', 1),
(5, 1, '::1', '2026-02-07 02:28:30', 1),
(6, 0, '::1', '2026-02-07 02:28:39', 1),
(7, 1, '::1', '2026-02-07 02:35:10', 1),
(8, 0, '::1', '2026-02-07 03:01:19', 1),
(9, 1, '::1', '2026-02-07 03:10:51', 4),
(10, 1, '::1', '2026-02-07 03:13:46', 4),
(11, 1, '::1', '2026-02-07 03:22:48', 1),
(12, 0, '::1', '2026-02-07 03:23:14', 1),
(13, 30, '::1', '2026-02-07 04:23:17', 5),
(14, 30, '::1', '2026-02-07 04:23:34', 5),
(15, 0, '::1', '2026-02-07 04:23:46', 5),
(16, 0, '::1', '2026-02-07 04:24:58', 5),
(17, 20, '::1', '2026-02-07 04:27:47', 5),
(18, 20, '::1', '2026-02-07 04:28:12', 5),
(19, 20, '::1', '2026-02-07 04:28:16', 5),
(20, 30, '::1', '2026-02-07 04:28:49', 5),
(21, 30, '::1', '2026-02-07 04:30:17', 5),
(22, 0, '::1', '2026-02-07 04:31:08', 1),
(23, 0, '::1', '2026-02-07 04:32:02', 1),
(24, 30, '::1', '2026-02-07 04:32:18', 5),
(25, 0, '::1', '2026-02-07 04:33:29', 5),
(26, 30, '::1', '2026-02-07 04:33:57', 5),
(27, 30, '::1', '2026-02-07 04:34:59', 5),
(28, 30, '::1', '2026-02-07 04:35:49', 5),
(29, 30, '::1', '2026-02-07 04:37:05', 5),
(30, 30, '::1', '2026-02-07 04:38:23', 5),
(31, 0, '::1', '2026-02-07 04:39:09', 5),
(32, 10, '::1', '2026-02-07 04:40:33', 5),
(33, 0, '::1', '2026-02-07 04:43:10', 5),
(34, 10, '::1', '2026-02-07 04:43:40', 5),
(35, 10, '::1', '2026-02-07 04:45:25', 5),
(36, 10, '::1', '2026-02-07 04:46:20', 5),
(37, 30, '::1', '2026-02-07 04:46:39', 5),
(38, 0, '::1', '2026-02-07 04:48:01', 5),
(39, 10, '::1', '2026-02-07 04:51:29', 6),
(40, 10, '::1', '2026-02-07 04:55:37', 6),
(41, 10, '::1', '2026-02-07 04:57:13', 6),
(42, 10, '::1', '2026-02-07 04:59:44', 6),
(43, 10, '::1', '2026-02-07 05:00:36', 6),
(44, 10, '::1', '2026-02-07 05:02:08', 6),
(45, 10, '::1', '2026-02-07 05:03:42', 6),
(46, 10, '::1', '2026-02-07 05:05:31', 6),
(47, 10, '::1', '2026-02-07 05:06:50', 6),
(48, 10, '::1', '2026-02-07 05:08:29', 6),
(49, 0, '::1', '2026-02-07 05:08:45', 6),
(50, 10, '::1', '2026-02-07 05:11:55', 6),
(51, 10, '::1', '2026-02-07 05:12:41', 6),
(52, 10, '::1', '2026-02-07 05:14:30', 6),
(53, 10, '::1', '2026-02-07 05:18:30', 6),
(54, 10, '::1', '2026-02-07 05:21:32', 6),
(55, 10, '::1', '2026-02-07 05:23:13', 6),
(56, 10, '127.0.0.1', '2026-02-08 20:35:09', 4),
(57, 0, '127.0.0.1', '2026-02-09 00:00:26', 6),
(58, 10, '127.0.0.1', '2026-02-23 03:02:58', 6),
(59, 10, '127.0.0.1', '2026-02-23 03:55:52', 6),
(60, 10, '127.0.0.1', '2026-02-23 10:58:51', 14),
(61, 10, '127.0.0.1', '2026-02-23 11:04:49', 6),
(62, 10, '127.0.0.1', '2026-03-01 01:24:11', 17),
(63, 15, '127.0.0.1', '2026-03-01 02:44:50', 18),
(64, 30, '127.0.0.1', '2026-03-01 02:48:41', 18),
(65, 30, '127.0.0.1', '2026-03-01 02:50:08', 18),
(66, 20, '127.0.0.1', '2026-03-01 02:51:49', 18),
(67, 30, '127.0.0.1', '2026-03-01 02:57:18', 18),
(68, 25, '127.0.0.1', '2026-03-01 02:58:42', 18),
(69, 40, 'iheb bellara', '2026-03-01 03:04:07', 18),
(70, 30, 'kk kk', '2026-03-01 03:10:28', 19),
(71, 30, 'kk kk', '2026-03-01 03:13:38', 19),
(72, 20, 'iheb bellara', '2026-03-01 03:16:25', 19),
(73, 40, 'iheb bellara', '2026-03-01 03:18:51', 19),
(74, 30, 'iheb bellara', '2026-03-01 03:27:58', 19),
(75, 30, 'iheb bellara', '2026-03-01 03:33:09', 19),
(76, 10, '127.0.0.1', '2026-03-01 03:37:29', 19),
(77, 15, '127.0.0.1', '2026-03-01 03:43:00', 20),
(78, 25, '127.0.0.1', '2026-03-01 03:49:28', 20),
(79, 35, '127.0.0.1', '2026-03-01 03:54:23', 20),
(80, 20, '127.0.0.1', '2026-03-01 04:04:57', 20),
(81, 10, '127.0.0.1', '2026-03-01 04:25:57', 21);

-- --------------------------------------------------------

--
-- Structure de la table `simulation`
--

CREATE TABLE `simulation` (
  `id` int(11) NOT NULL,
  `moyenne` decimal(5,2) NOT NULL,
  `specialites` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`specialites`)),
  `preferences` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`preferences`)),
  `date_simulation` datetime NOT NULL,
  `resultats` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`resultats`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `simulation`
--

INSERT INTO `simulation` (`id`, `moyenne`, `specialites`, `preferences`, `date_simulation`, `resultats`) VALUES
(1, 15.50, '[\"Math\\u00e9matiques\",\"Physique-Chimie\",\"SVT\"]', '[\"Scientifique\",\"Logique\"]', '2026-02-09 09:50:17', '[{\"filiere\":{\"id\":1,\"nom\":\"M\\u00e9decine\",\"description\":\"Formation aux m\\u00e9tiers de la sant\\u00e9 et de la m\\u00e9decine.\"},\"pourcentage\":85.2,\"taux_acces\":85.2,\"details\":{\"note_moyenne\":77.5,\"contribution_moyenne\":51.2,\"score_specialites\":100.0,\"contribution_specialites\":34.0,\"score_preferences\":50.0,\"contribution_preferences\":0.0,\"poids\":{\"moyenne\":66,\"specialites\":34,\"preferences\":0}}},{\"filiere\":{\"id\":2,\"nom\":\"Ing\\u00e9nierie\",\"description\":\"Formation aux m\\u00e9tiers de l\'ing\\u00e9nierie et de la conception technique.\"},\"pourcentage\":73.8,\"taux_acces\":73.8,\"details\":{\"note_moyenne\":77.5,\"contribution_moyenne\":51.2,\"score_specialites\":66.66666666666666,\"contribution_specialites\":22.7,\"score_preferences\":50.0,\"contribution_preferences\":0.0,\"poids\":{\"moyenne\":66,\"specialites\":34,\"preferences\":0}}},{\"filiere\":{\"id\":3,\"nom\":\"Informatique\",\"description\":\"Formation aux m\\u00e9tiers du d\\u00e9veloppement et des technologies num\\u00e9riques.\"},\"pourcentage\":73.8,\"taux_acces\":73.8,\"details\":{\"note_moyenne\":77.5,\"contribution_moyenne\":51.2,\"score_specialites\":66.66666666666666,\"contribution_specialites\":22.7,\"score_preferences\":100.0,\"contribution_preferences\":0.0,\"poids\":{\"moyenne\":66,\"specialites\":34,\"preferences\":0}}},{\"filiere\":{\"id\":4,\"nom\":\"Commerce\",\"description\":\"Formation aux m\\u00e9tiers du management, marketing et gestion d\'entreprise.\"},\"pourcentage\":62.5,\"taux_acces\":62.5,\"details\":{\"note_moyenne\":77.5,\"contribution_moyenne\":51.2,\"score_specialites\":33.33333333333333,\"contribution_specialites\":11.3,\"score_preferences\":0.0,\"contribution_preferences\":0.0,\"poids\":{\"moyenne\":66,\"specialites\":34,\"preferences\":0}}},{\"filiere\":{\"id\":5,\"nom\":\"Avocat\",\"description\":\"Formation aux m\\u00e9tiers du droit et de la justice.\"},\"pourcentage\":51.2,\"taux_acces\":51.2,\"details\":{\"note_moyenne\":77.5,\"contribution_moyenne\":51.2,\"score_specialites\":0.0,\"contribution_specialites\":0.0,\"score_preferences\":0.0,\"contribution_preferences\":0.0,\"poids\":{\"moyenne\":66,\"specialites\":34,\"preferences\":0}}},{\"filiere\":{\"id\":6,\"nom\":\"Architecte\",\"description\":\"Formation aux m\\u00e9tiers de l\'architecture et de l\'urbanisme.\"},\"pourcentage\":50.8,\"taux_acces\":50.8,\"details\":{\"note_moyenne\":77.5,\"contribution_moyenne\":51.2,\"score_specialites\":66.66666666666666,\"contribution_specialites\":22.7,\"score_preferences\":50.0,\"contribution_preferences\":0.0,\"poids\":{\"moyenne\":66,\"specialites\":34,\"preferences\":0}}}]'),
(2, 17.00, '[\"Math\\u00e9matiques\",\"Physique-Chimie\",\"SVT\"]', '[\"Scientifique\",\"Technique\"]', '2026-02-09 09:55:51', '[{\"filiere\":{\"id\":2,\"nom\":\"M\\u00e9decine\",\"description\":\"rien a signaler\"},\"pourcentage\":90.5,\"taux_acces\":90.5,\"details\":{\"note_moyenne\":85.0,\"contribution_moyenne\":25.5,\"score_specialites\":100.0,\"contribution_specialites\":60.0,\"score_preferences\":50.0,\"contribution_preferences\":5.0,\"poids\":{\"moyenne\":30,\"specialites\":60,\"preferences\":10}}},{\"filiere\":{\"id\":3,\"nom\":\"Ing\\u00e9nieur informatique\",\"description\":\"rien a signaler\"},\"pourcentage\":75.5,\"taux_acces\":75.5,\"details\":{\"note_moyenne\":85.0,\"contribution_moyenne\":25.5,\"score_specialites\":66.7,\"contribution_specialites\":40.0,\"score_preferences\":100.0,\"contribution_preferences\":10.0,\"poids\":{\"moyenne\":30,\"specialites\":60,\"preferences\":10}}},{\"filiere\":{\"id\":5,\"nom\":\"Communication\",\"description\":\"rien a signaler\"},\"pourcentage\":50.5,\"taux_acces\":50.5,\"details\":{\"note_moyenne\":85.0,\"contribution_moyenne\":25.5,\"score_specialites\":33.3,\"contribution_specialites\":20.0,\"score_preferences\":50.0,\"contribution_preferences\":5.0,\"poids\":{\"moyenne\":30,\"specialites\":60,\"preferences\":10}}},{\"filiere\":{\"id\":4,\"nom\":\"Avocat\",\"description\":\"rien a signaler\"},\"pourcentage\":25.5,\"taux_acces\":25.5,\"details\":{\"note_moyenne\":85.0,\"contribution_moyenne\":25.5,\"score_specialites\":0.0,\"contribution_specialites\":0.0,\"score_preferences\":0.0,\"contribution_preferences\":0.0,\"poids\":{\"moyenne\":30,\"specialites\":60,\"preferences\":10}}}]'),
(3, 16.00, '[\"Physique-Chimie\",\"SVT\",\"Philosophie\"]', '[\"Scientifique\",\"Social\",\"Technique\"]', '2026-02-09 11:55:46', '[{\"filiere\":{\"id\":2,\"nom\":\"Ing\\u00e9nierie\",\"description\":\"Formation aux m\\u00e9tiers de l\'ing\\u00e9nierie et de la conception technique.\"},\"pourcentage\":70.7,\"taux_acces\":70.7,\"details\":{\"note_moyenne\":80.0,\"contribution_moyenne\":24.0,\"score_specialites\":66.7,\"contribution_specialites\":40.0,\"score_preferences\":66.7,\"contribution_preferences\":6.7,\"poids\":{\"moyenne\":30,\"specialites\":60,\"preferences\":10}}},{\"filiere\":{\"id\":3,\"nom\":\"Informatique\",\"description\":\"Formation aux m\\u00e9tiers du d\\u00e9veloppement et des technologies num\\u00e9riques.\"},\"pourcentage\":50.7,\"taux_acces\":50.7,\"details\":{\"note_moyenne\":80.0,\"contribution_moyenne\":24.0,\"score_specialites\":33.3,\"contribution_specialites\":20.0,\"score_preferences\":66.7,\"contribution_preferences\":6.7,\"poids\":{\"moyenne\":30,\"specialites\":60,\"preferences\":10}}},{\"filiere\":{\"id\":4,\"nom\":\"Commerce\",\"description\":\"Formation aux m\\u00e9tiers du management, marketing et gestion d\'entreprise.\"},\"pourcentage\":27.3,\"taux_acces\":27.3,\"details\":{\"note_moyenne\":80.0,\"contribution_moyenne\":24.0,\"score_specialites\":0.0,\"contribution_specialites\":0.0,\"score_preferences\":33.3,\"contribution_preferences\":3.3,\"poids\":{\"moyenne\":30,\"specialites\":60,\"preferences\":10}}},{\"filiere\":{\"id\":5,\"nom\":\"Avocat\",\"description\":\"Formation aux m\\u00e9tiers du droit et de la justice.\"},\"pourcentage\":27.3,\"taux_acces\":27.3,\"details\":{\"note_moyenne\":80.0,\"contribution_moyenne\":24.0,\"score_specialites\":0.0,\"contribution_specialites\":0.0,\"score_preferences\":33.3,\"contribution_preferences\":3.3,\"poids\":{\"moyenne\":30,\"specialites\":60,\"preferences\":10}}},{\"filiere\":{\"id\":6,\"nom\":\"Architecte\",\"description\":\"Formation aux m\\u00e9tiers de l\'architecture et de l\'urbanisme.\"},\"pourcentage\":10.0,\"taux_acces\":10.0,\"details\":{\"note_moyenne\":80.0,\"contribution_moyenne\":0,\"score_specialites\":0,\"contribution_specialites\":0,\"score_preferences\":0,\"contribution_preferences\":0,\"poids\":{\"moyenne\":58,\"specialites\":34,\"preferences\":8}}},{\"filiere\":{\"id\":1,\"nom\":\"M\\u00e9decine\",\"description\":\"Formation aux m\\u00e9tiers de la sant\\u00e9 et de la m\\u00e9decine.\"},\"pourcentage\":0.0,\"taux_acces\":0.0,\"details\":{\"note_moyenne\":80.0,\"contribution_moyenne\":0,\"score_specialites\":0,\"contribution_specialites\":0,\"score_preferences\":0,\"contribution_preferences\":0,\"poids\":{\"moyenne\":58,\"specialites\":34,\"preferences\":8}}}]'),
(4, 15.50, '[\"Math\\u00e9matiques\",\"Physique-Chimie\",\"SVT\"]', '[\"Scientifique\",\"Technique\"]', '2026-02-18 17:30:06', '[{\"filiere\":{\"id\":2,\"nom\":\"M\\u00e9decine\",\"description\":\"rien a signaler\"},\"pourcentage\":88.2,\"taux_acces\":88.2,\"details\":{\"note_moyenne\":77.5,\"contribution_moyenne\":23.3,\"score_specialites\":100.0,\"contribution_specialites\":60.0,\"score_preferences\":50.0,\"contribution_preferences\":5.0,\"poids\":{\"moyenne\":30,\"specialites\":60,\"preferences\":10}}},{\"filiere\":{\"id\":3,\"nom\":\"Ing\\u00e9nieur informatique\",\"description\":\"rien a signaler\"},\"pourcentage\":73.2,\"taux_acces\":73.2,\"details\":{\"note_moyenne\":77.5,\"contribution_moyenne\":23.3,\"score_specialites\":66.7,\"contribution_specialites\":40.0,\"score_preferences\":100.0,\"contribution_preferences\":10.0,\"poids\":{\"moyenne\":30,\"specialites\":60,\"preferences\":10}}},{\"filiere\":{\"id\":5,\"nom\":\"Communication\",\"description\":\"rien a signaler\"},\"pourcentage\":48.2,\"taux_acces\":48.2,\"details\":{\"note_moyenne\":77.5,\"contribution_moyenne\":23.3,\"score_specialites\":33.3,\"contribution_specialites\":20.0,\"score_preferences\":50.0,\"contribution_preferences\":5.0,\"poids\":{\"moyenne\":30,\"specialites\":60,\"preferences\":10}}},{\"filiere\":{\"id\":4,\"nom\":\"Avocat\",\"description\":\"rien a signaler\"},\"pourcentage\":23.3,\"taux_acces\":23.3,\"details\":{\"note_moyenne\":77.5,\"contribution_moyenne\":23.3,\"score_specialites\":0.0,\"contribution_specialites\":0.0,\"score_preferences\":0.0,\"contribution_preferences\":0.0,\"poids\":{\"moyenne\":30,\"specialites\":60,\"preferences\":10}}}]');

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

CREATE TABLE `utilisateurs` (
  `id` int(11) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(180) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'etudiant',
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` longtext DEFAULT NULL,
  `actif` tinyint(4) DEFAULT 1,
  `date_inscription` datetime NOT NULL,
  `date_modification` datetime NOT NULL,
  `face_id_token` varchar(255) DEFAULT NULL,
  `face_id_enrolled` tinyint(1) DEFAULT 0,
  `face_id_enrollment_date` datetime DEFAULT NULL,
  `reset_token` varchar(64) DEFAULT NULL,
  `ban_until` datetime DEFAULT NULL,
  `ban_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `role`, `telephone`, `adresse`, `actif`, `date_inscription`, `date_modification`, `face_id_token`, `face_id_enrolled`, `face_id_enrollment_date`, `reset_token`, `ban_until`, `ban_reason`) VALUES
(1, 'Admin', 'nyx', 'admin@educavision.com', '$2y$13$i9DuzWEttQgN3Jmmm8oy3uf09FhSAnzhm0tQzog0bYQezPhR53mOG', 'admin', NULL, NULL, 1, '2026-02-08 20:45:51', '2026-02-08 23:05:58', NULL, 0, NULL, NULL, NULL, NULL),
(2, 'GABSI', 'Chemseddine', 'chemseddine.gabsi@esprit.tn', '$2y$13$8HmqaH1waswGC/O6v.8CFOAu0CRYsBXXRXjUv3h0APF4/dBSHvVqW', 'etudiant', NULL, NULL, 1, '2026-02-08 22:45:47', '2026-02-08 22:45:47', NULL, 0, NULL, NULL, NULL, NULL),
(3, 'nyx', 'firas', 'firaszx232@gmail.com', '$2y$13$NjsHoi2fwuQ03ux.jgHxuuv6UxsfVi7vKuJXnTpqD.aYvnIPuKnry', 'professeur', NULL, NULL, 1, '2026-02-08 23:00:25', '2026-02-08 23:01:58', NULL, 0, NULL, NULL, NULL, NULL),
(4, 'Najahi', 'Mariem', 'mariem.najahi@esprit.tn', '$2y$13$61EhXpIP.MYaN/x3DanbJOuTGjyVWyNF3SYPgUef1D1ysZXv34aYu', 'professeur', '99452881', NULL, 1, '2026-02-08 23:42:26', '2026-02-08 23:42:26', NULL, 0, NULL, NULL, NULL, NULL),
(5, 'Najahi', 'Mariem', 'mariem.najahi@gmail.com', '$2y$13$cwH1vetNDoub0MxeTQwQDuduD8CPtRncFWMpSMmBreh4D5Hp0qNUS', 'professeur', NULL, NULL, 1, '2026-02-09 06:26:33', '2026-02-09 06:26:33', NULL, 0, NULL, NULL, NULL, NULL),
(6, 'njahi', 'mariem', 'mariem@gmail.com', '$2y$13$5zIw7w05rl0TLoeYemiLo.qvm.chpvtiRvmgzwNMz31dw4nkTzvcS', 'professeur', NULL, NULL, 1, '2026-02-17 12:15:22', '2026-02-17 12:15:22', NULL, 0, NULL, NULL, NULL, NULL),
(7, 'chiha', 'nour', 'nour@gmail.com', '$2y$13$PWGxmg4WjOv2/zWJe/MeDuguwSbJCvZH2jwRpsWNMlXnLJTjzty7W', 'professeur', NULL, NULL, 1, '2026-02-18 20:26:10', '2026-02-18 20:26:10', NULL, 0, NULL, NULL, NULL, NULL),
(8, 'Najahi', 'malek', 'malek@gmail.com', '$2y$13$wm1fNx/TasjIdWUJ6he3k.Ji2RLbBvku.JvAKxGKAoXO3M2UTMXEy', 'etudiant', NULL, NULL, 1, '2026-02-19 21:06:44', '2026-02-19 21:06:44', NULL, 0, NULL, NULL, NULL, NULL),
(9, 'kk', 'kk', 'rayenguissouma2@gmail.com', '$2y$13$x2ueDyAK1qCv2cIsBfAhoeAFdlbhWQ68ybHczgsS/NGikLQN0rnxO', 'professeur', NULL, NULL, 1, '2026-02-22 20:36:46', '2026-02-22 20:36:46', 'eyJoYXNoIjp7InNpbXBsZV9oYXNoIjoiYTI2OTA2YmFmNGEwMDhkN2U4NmJkMGZiMDNiOGI5OWJiMzU2YTcyOWZhNjNjMDNkMjg4NmJlM2FmMTE5MzVmOCIsInNpemUiOjM3OTI1LCJjaGVja3N1bSI6ImFkYjAwNDE2MmFlODRmYjRjYzcxMTIyYzQ1YTQ3MTlkIn0sImVtYWlsIjoicmF5ZW5ndWlzc291bWEyQGdtYWlsLmNvbSIsImNyZWF', 1, '2026-02-23 10:14:44', NULL, NULL, NULL),
(10, 'dv', 'aaa', 'rayenguissouma1@gmail.com', '$2y$13$iYJfxGztth6WUGbXgYpZ6.PksVrbxYreV945ZcBjPVytr3tRybWme', 'etudiant', NULL, NULL, 1, '2026-02-23 03:23:45', '2026-02-28 02:35:31', NULL, 0, NULL, NULL, '2026-03-03 02:35:31', 'Test de suspension automatique'),
(11, 'habib', 'habib', 'habib@gmail.com', '$2y$13$aRkCQKeS7qUsA6.8JXBKCumULQTsueEkoAUvoDtME5axLC7xNX31u', 'etudiant', NULL, NULL, 1, '2026-03-01 00:10:59', '2026-03-01 00:10:59', NULL, 0, NULL, NULL, NULL, NULL),
(12, 'bellara', 'iheb', 'bellaraiheb@gmail.com', '$2y$13$QVwkkuh3NuhkN.trAoB01eAEGWQMDSeDKLhw1eBflvQ/2VQz.Khv2', 'etudiant', NULL, NULL, 1, '2026-03-01 00:21:54', '2026-03-01 00:21:54', NULL, 0, NULL, NULL, NULL, NULL);

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `answer`
--
ALTER TABLE `answer`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_DADD4A251E27F6BF` (`question_id`);

--
-- Index pour la table `candidature`
--
ALTER TABLE `candidature`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_E33BD3B8195A2A28` (`offre_stage_id`);

--
-- Index pour la table `chapter`
--
ALTER TABLE `chapter`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_F981B52E591CC992` (`course_id`);

--
-- Index pour la table `conversation_message`
--
ALTER TABLE `conversation_message`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_2DEB3E75537A1329` (`message_id`);

--
-- Index pour la table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_169E6FB941807E1D` (`teacher_id`);

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `filiere`
--
ALTER TABLE `filiere`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `formation`
--
ALTER TABLE `formation`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `message`
--
ALTER TABLE `message`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0E3BD61CE16BA31DBBF396750` (`queue_name`,`available_at`,`delivered_at`,`id`);

--
-- Index pour la table `metier`
--
ALTER TABLE `metier`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_51A00D8C180AA129` (`filiere_id`);

--
-- Index pour la table `offre_stage`
--
ALTER TABLE `offre_stage`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `prerequis`
--
ALTER TABLE `prerequis`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_CAE3EB095200282E` (`formation_id`);

--
-- Index pour la table `question`
--
ALTER TABLE `question`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_B6F7494E77DD0B32` (`idquiz`);

--
-- Index pour la table `quiz`
--
ALTER TABLE `quiz`
  ADD PRIMARY KEY (`idquiz`),
  ADD KEY `IDX_A412FA92579F4768` (`chapter_id`);

--
-- Index pour la table `reclamation`
--
ALTER TABLE `reclamation`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `reponse`
--
ALTER TABLE `reponse`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_5FB6DEC72D6BA2D9` (`reclamation_id`);

--
-- Index pour la table `result`
--
ALTER TABLE `result`
  ADD PRIMARY KEY (`idresult`),
  ADD KEY `IDX_136AC11377DD0B32` (`idquiz`);

--
-- Index pour la table `simulation`
--
ALTER TABLE `simulation`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_497B315EE7927C74` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `answer`
--
ALTER TABLE `answer`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=326;

--
-- AUTO_INCREMENT pour la table `candidature`
--
ALTER TABLE `candidature`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `chapter`
--
ALTER TABLE `chapter`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `conversation_message`
--
ALTER TABLE `conversation_message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `course`
--
ALTER TABLE `course`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `filiere`
--
ALTER TABLE `filiere`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `formation`
--
ALTER TABLE `formation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `message`
--
ALTER TABLE `message`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `metier`
--
ALTER TABLE `metier`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `offre_stage`
--
ALTER TABLE `offre_stage`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `prerequis`
--
ALTER TABLE `prerequis`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `question`
--
ALTER TABLE `question`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=87;

--
-- AUTO_INCREMENT pour la table `quiz`
--
ALTER TABLE `quiz`
  MODIFY `idquiz` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT pour la table `reclamation`
--
ALTER TABLE `reclamation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `reponse`
--
ALTER TABLE `reponse`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `result`
--
ALTER TABLE `result`
  MODIFY `idresult` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT pour la table `simulation`
--
ALTER TABLE `simulation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `utilisateurs`
--
ALTER TABLE `utilisateurs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `answer`
--
ALTER TABLE `answer`
  ADD CONSTRAINT `FK_DADD4A251E27F6BF` FOREIGN KEY (`question_id`) REFERENCES `question` (`id`);

--
-- Contraintes pour la table `candidature`
--
ALTER TABLE `candidature`
  ADD CONSTRAINT `FK_E33BD3B89D486E7D` FOREIGN KEY (`offre_stage_id`) REFERENCES `offre_stage` (`id`);

--
-- Contraintes pour la table `chapter`
--
ALTER TABLE `chapter`
  ADD CONSTRAINT `FK_F981B52E591CC992` FOREIGN KEY (`course_id`) REFERENCES `course` (`id`);

--
-- Contraintes pour la table `conversation_message`
--
ALTER TABLE `conversation_message`
  ADD CONSTRAINT `FK_2DEB3E75537A1329` FOREIGN KEY (`message_id`) REFERENCES `message` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `FK_169E6FB941807E1D` FOREIGN KEY (`teacher_id`) REFERENCES `utilisateurs` (`id`);

--
-- Contraintes pour la table `metier`
--
ALTER TABLE `metier`
  ADD CONSTRAINT `FK_51A00D8C180AA129` FOREIGN KEY (`filiere_id`) REFERENCES `filiere` (`id`);

--
-- Contraintes pour la table `prerequis`
--
ALTER TABLE `prerequis`
  ADD CONSTRAINT `FK_CAE3EB095200282E` FOREIGN KEY (`formation_id`) REFERENCES `formation` (`id`);

--
-- Contraintes pour la table `question`
--
ALTER TABLE `question`
  ADD CONSTRAINT `FK_B6F7494E77DD0B32` FOREIGN KEY (`idquiz`) REFERENCES `quiz` (`idquiz`);

--
-- Contraintes pour la table `quiz`
--
ALTER TABLE `quiz`
  ADD CONSTRAINT `FK_A412FA92579F4768` FOREIGN KEY (`chapter_id`) REFERENCES `chapter` (`id`);

--
-- Contraintes pour la table `reponse`
--
ALTER TABLE `reponse`
  ADD CONSTRAINT `reponse_ibfk_1` FOREIGN KEY (`reclamation_id`) REFERENCES `reclamation` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `result`
--
ALTER TABLE `result`
  ADD CONSTRAINT `FK_136AC11377DD0B32` FOREIGN KEY (`idquiz`) REFERENCES `quiz` (`idquiz`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
