-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : mar. 11 mars 2025 à 09:08
-- Version du serveur : 8.3.0
-- Version de PHP : 8.2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `baseclients`
--

-- --------------------------------------------------------

--
-- Structure de la table `actualites`
--

DROP TABLE IF EXISTS `actualites`;
CREATE TABLE IF NOT EXISTS `actualites` (
  `id_actualite` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `contenu` text COLLATE utf8mb4_unicode_ci,
  `image_actu` enum('JPEG','PNG') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_publication` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `id_auteur` int DEFAULT NULL,
  PRIMARY KEY (`id_actualite`),
  KEY `id_auteur` (`id_auteur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `dossiers`
--

DROP TABLE IF EXISTS `dossiers`;
CREATE TABLE IF NOT EXISTS `dossiers` (
  `id_dossier` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int DEFAULT NULL,
  `id_prime` int DEFAULT NULL,
  `valeur_simulation` json DEFAULT NULL,
  `etat` enum('en cours','terminé','non traité') COLLATE utf8mb4_unicode_ci DEFAULT 'non traité',
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `details` text COLLATE utf8mb4_unicode_ci,
  `pdf_recap` longblob,
  PRIMARY KEY (`id_dossier`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `id_prime` (`id_prime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `primes`
--
/*
DROP TABLE IF EXISTS `primes`;
CREATE TABLE IF NOT EXISTS `primes` (
  `id_prime` int NOT NULL AUTO_INCREMENT,
  `nom_prime` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `image_prime` enum('JPEG','PNG') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `criteres` json DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_mise_a_jour` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_prime`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
*/
-- --------------------------------------------------------

--
-- Structure de la table `rendez_vous`
--

DROP TABLE IF EXISTS `rendez_vous`;
CREATE TABLE IF NOT EXISTS `rendez_vous` (
  `id_rendez_vous` int NOT NULL AUTO_INCREMENT,
  `id_utilisateur` int DEFAULT NULL,
  `date_heure` datetime DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `statut` enum('confirmé','annulé') COLLATE utf8mb4_unicode_ci DEFAULT 'confirmé',
  PRIMARY KEY (`id_rendez_vous`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `utilisateurs`
--

DROP TABLE IF EXISTS `utilisateurs`;
CREATE TABLE IF NOT EXISTS `utilisateurs` (
  `id_utilisateur` int NOT NULL AUTO_INCREMENT,
  `prenom` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nom` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `mot_de_passe` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `notif_actu` tinyint(1) DEFAULT '0',
  `notif_dossier` tinyint(1) DEFAULT '0',
  `type_utilisateur` enum('particulier','entreprise','copropriété','SCI','artisan') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('utilisateur','administrateur') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `date_creation` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `connecte` int NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `utilisateurs`
--

INSERT INTO `utilisateurs` (`id_utilisateur`, `prenom`, `nom`, `email`, `mot_de_passe`, `notif_actu`, `notif_dossier`, `type_utilisateur`, `role`, `date_creation`, `connecte`) VALUES
(2, 'Samuel', 'GILLON', 'test@gmail.com', 'test', 0, 0, 'particulier', 'utilisateur', '2025-03-11 08:52:37', 0);

INSERT INTO `utilisateurs` (`id_utilisateur`, `prenom`, `nom`, `email`, `mot_de_passe`, `notif_actu`, `notif_dossier`, `type_utilisateur`, `role`, `date_creation`, `connecte`) VALUES
(3, 'Admin', 'TEST', 'admin@gmail.com', 'admin', 0, 0, 'particulier', 'administrateur', '2025-03-11 08:52:37', 0);

-- --------------------------------------------------------

--
-- Structure de la table `simulations`
--

DROP TABLE IF EXISTS `simulations`;
CREATE TABLE simulations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);



CREATE TABLE IF NOT EXISTS questions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question TEXT NOT NULL,
    type ENUM('bool', 'select', 'number') NOT NULL
);

DROP TABLE IF EXISTS `propositions`;
CREATE TABLE propositions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    question_id INT,
    proposition TEXT,
    FOREIGN KEY (question_id) REFERENCES questions(id)
);

DROP TABLE IF EXISTS conditions_primes;
DROP TABLE IF EXISTS `primes`;
CREATE TABLE primes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255),
    description TEXT,
    image VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS conditions_primes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    prime_id INT,
    question_id INT,
    valeur_attendue TEXT,
    FOREIGN KEY (prime_id) REFERENCES primes(id) ON DELETE CASCADE,
    FOREIGN KEY (question_id) REFERENCES questions(id) ON DELETE CASCADE
);



--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `actualites`
--
ALTER TABLE `actualites`
  ADD CONSTRAINT `actualites_ibfk_1` FOREIGN KEY (`id_auteur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE SET NULL;

--
-- Contraintes pour la table `dossiers`
--
/*
ALTER TABLE `dossiers`
  ADD CONSTRAINT `dossiers_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE,
  ADD CONSTRAINT `dossiers_ibfk_2` FOREIGN KEY (`id_prime`) REFERENCES `primes` (`id_prime`) ON DELETE SET NULL;
*/
--
-- Contraintes pour la table `rendez_vous`
--
ALTER TABLE `rendez_vous`
  ADD CONSTRAINT `rendez_vous_ibfk_1` FOREIGN KEY (`id_utilisateur`) REFERENCES `utilisateurs` (`id_utilisateur`) ON DELETE CASCADE;
COMMIT;



/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
