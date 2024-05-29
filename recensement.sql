-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 22 mai 2024 à 17:43
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `recensement`
--

-- --------------------------------------------------------

--
-- Structure de la table `dated`
--

CREATE TABLE `dated` (
  `id` int(11) NOT NULL,
  `dateD` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `dated`
--

INSERT INTO `dated` (`id`, `dateD`) VALUES
(5, '2024-04-10'),
(10, '2024-04-13');

-- --------------------------------------------------------

--
-- Structure de la table `demande`
--

CREATE TABLE `demande` (
  `idDemande` int(11) NOT NULL,
  `description` varchar(50) DEFAULT NULL,
  `justificatifs` varchar(250) DEFAULT NULL,
  `localite` varchar(50) DEFAULT NULL,
  `priorite` int(11) DEFAULT NULL,
  `remplacement` tinyint(1) DEFAULT NULL,
  `numPoste` int(11) DEFAULT NULL,
  `idNature` int(11) NOT NULL,
  `idDemandeur` int(11) NOT NULL,
  `numUF` text DEFAULT NULL,
  `idDate` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `demande`
--

INSERT INTO `demande` (`idDemande`, `description`, `justificatifs`, `localite`, `priorite`, `remplacement`, `numPoste`, `idNature`, `idDemandeur`, `numUF`, `idDate`) VALUES
(33, 'Ecran 24', 'Alee darwaze elm ast wa rasoul khouda sher elm pas baray dakhel shodan ba shahr bayad az darwaza gozasht ', '12345678', 1, 1, 122, 16, 14, '21', 5),
(34, 'Lecteur 1D ', 'Jane b feday mahdi aj oust ke yak rouz khuahad amd wa jahan pur az adl khaad shoud.', '12345667', 1, 1, 123, 13, 16, '12', 5),
(35, 'ALEE', 'haidar', '12345678', 1, 0, 0, 17, 14, '14', 10),
(36, 'Frais Transport', 'ferrte', '12345678', 1, 1, 12345, 17, 1, '\"', 10);

-- --------------------------------------------------------

--
-- Structure de la table `demandeur`
--

CREATE TABLE `demandeur` (
  `idDemandeur` int(11) NOT NULL,
  `nom` varchar(50) DEFAULT NULL,
  `prenom` varchar(50) DEFAULT NULL,
  `idPole` int(11) NOT NULL,
  `numTel` varchar(50) DEFAULT NULL,
  `admin` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `demandeur`
--

INSERT INTO `demandeur` (`idDemandeur`, `nom`, `prenom`, `idPole`, `numTel`, `admin`) VALUES
(1, 'Tester', 'test', 22, NULL, 1),
(2, 'Tomas', 'Martin', 12, NULL, 0),
(14, 'infostage1', 'Sayed MUZAFARI', 22, '78622', 1),
(16, 'sangohan', 'Sangohan GOKU', 22, '23456', 0),
(17, 'infostage1', 'Sayed MUZAFARI', 21, '12345', 0);

-- --------------------------------------------------------

--
-- Structure de la table `naturedemande`
--

CREATE TABLE `naturedemande` (
  `idNature` int(11) NOT NULL,
  `libelle` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `naturedemande`
--

INSERT INTO `naturedemande` (`idNature`, `libelle`) VALUES
(1, 'AUTRE'),
(2, 'IMPRIMANTE ETIQUETTE'),
(3, 'DOUCHETTE'),
(4, 'SOURIS SANS FILS'),
(5, 'SOURIS ERGONOMOQUE'),
(6, 'SOURIS'),
(7, 'SCANNER'),
(8, 'PRISE RESEAU'),
(9, 'PC-PORTABLE'),
(10, 'PC-FIXE'),
(11, 'LOGICIEL'),
(12, 'LECT 2D'),
(13, 'LECT 1D'),
(14, 'IMPRIMANTE N et B'),
(15, 'IMPRIMANTE COULEUR'),
(16, 'ECRAN'),
(17, 'CHARIOT MOBILE');

-- --------------------------------------------------------

--
-- Structure de la table `pole`
--

CREATE TABLE `pole` (
  `idPole` int(11) NOT NULL,
  `libelle` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Déchargement des données de la table `pole`
--

INSERT INTO `pole` (`idPole`, `libelle`) VALUES
(1, 'AMBROISE PARE'),
(2, 'ARCHIMED'),
(3, 'BIOLOGIE & PATHOLOGIE'),
(4, 'CPA'),
(5, 'DIACORP'),
(6, 'DAF'),
(7, 'DAG'),
(8, 'DAL'),
(9, 'DAM'),
(10, 'DCS'),
(11, 'DIP'),
(12, 'DRH'),
(13, 'IMAGERIE DIAGNOST. & INTERV.'),
(14, 'NNORR'),
(15, 'ONCOLOGIE'),
(16, 'PFME'),
(17, 'PHARMACIE-STÉR-INFO. MÉD.'),
(18, 'PSYCHIATRIE GÉN. & INF.-JUV.'),
(19, 'SPÉCIALITÉS MÉD. & MÉD. GÉN.'),
(20, 'URGENCES PASTEUR'),
(21, 'test_11'),
(22, 'Test');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `dated`
--
ALTER TABLE `dated`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `demande`
--
ALTER TABLE `demande`
  ADD PRIMARY KEY (`idDemande`),
  ADD KEY `idNature` (`idNature`),
  ADD KEY `idDemandeur` (`idDemandeur`),
  ADD KEY `idDate` (`idDate`);

--
-- Index pour la table `demandeur`
--
ALTER TABLE `demandeur`
  ADD PRIMARY KEY (`idDemandeur`),
  ADD KEY `idPole` (`idPole`);

--
-- Index pour la table `naturedemande`
--
ALTER TABLE `naturedemande`
  ADD PRIMARY KEY (`idNature`);

--
-- Index pour la table `pole`
--
ALTER TABLE `pole`
  ADD PRIMARY KEY (`idPole`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `dated`
--
ALTER TABLE `dated`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `demande`
--
ALTER TABLE `demande`
  MODIFY `idDemande` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT pour la table `demandeur`
--
ALTER TABLE `demandeur`
  MODIFY `idDemandeur` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `naturedemande`
--
ALTER TABLE `naturedemande`
  MODIFY `idNature` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT pour la table `pole`
--
ALTER TABLE `pole`
  MODIFY `idPole` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `demande`
--
ALTER TABLE `demande`
  ADD CONSTRAINT `demande_ibfk_1` FOREIGN KEY (`idNature`) REFERENCES `naturedemande` (`idNature`),
  ADD CONSTRAINT `demande_ibfk_2` FOREIGN KEY (`idDemandeur`) REFERENCES `demandeur` (`idDemandeur`),
  ADD CONSTRAINT `idDate` FOREIGN KEY (`idDate`) REFERENCES `dated` (`id`);

--
-- Contraintes pour la table `demandeur`
--
ALTER TABLE `demandeur`
  ADD CONSTRAINT `demandeur_ibfk_1` FOREIGN KEY (`idPole`) REFERENCES `pole` (`idPole`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
