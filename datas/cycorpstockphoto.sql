-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 13, 2018 at 09:02 PM
-- Server version: 5.6.34-log
-- PHP Version: 7.1.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cycorpstockphoto`
--

-- --------------------------------------------------------

--
-- Table structure for table `droit`
--

CREATE TABLE `droit` (
  `id` int(10) UNSIGNED NOT NULL,
  `lenom` varchar(45) DEFAULT NULL,
  `laperm` smallint(5) UNSIGNED DEFAULT '2'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `droit`
--

INSERT INTO `droit` (`id`, `lenom`, `laperm`) VALUES
(1, 'Administrateur', 0),
(2, 'Moderateur', 1),
(3, 'Utilisateur', 2);

-- --------------------------------------------------------

--
-- Table structure for table `photo`
--

CREATE TABLE `photo` (
  `id` int(10) UNSIGNED NOT NULL,
  `lenom` varchar(50) DEFAULT NULL,
  `lextension` char(3) DEFAULT NULL,
  `lepoids` int(10) UNSIGNED DEFAULT NULL,
  `lalargeur` int(10) UNSIGNED DEFAULT NULL,
  `lahauteur` int(10) UNSIGNED DEFAULT NULL,
  `letitre` varchar(60) DEFAULT NULL,
  `ladesc` varchar(500) DEFAULT NULL,
  `affiche` smallint(5) UNSIGNED DEFAULT '2',
  `utilisateur_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `photo_has_rubriques`
--

CREATE TABLE `photo_has_rubriques` (
  `photo_id` int(10) UNSIGNED NOT NULL,
  `rubriques_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rubriques`
--

CREATE TABLE `rubriques` (
  `id` int(10) UNSIGNED NOT NULL,
  `lintitule` varchar(60) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `rubriques`
--

INSERT INTO `rubriques` (`id`, `lintitule`) VALUES
(1, 'Animaux'),
(2, 'Architectures'),
(3, 'Artistiques'),
(4, 'Personnes'),
(5, 'Paysages'),
(6, 'Sports'),
(7, 'Technologies'),
(8, 'Transports'),
(9, 'Divers');

-- --------------------------------------------------------

--
-- Table structure for table `utilisateur`
--

CREATE TABLE `utilisateur` (
  `id` int(10) UNSIGNED NOT NULL,
  `lelogin` varchar(100) DEFAULT NULL,
  `lepass` varchar(45) DEFAULT NULL,
  `lemail` varchar(150) DEFAULT NULL,
  `lenom` varchar(80) DEFAULT NULL,
  `valide` tinyint(3) UNSIGNED NOT NULL DEFAULT '1',
  `droit_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `utilisateur`
--

INSERT INTO `utilisateur` (`id`, `lelogin`, `lepass`, `lemail`, `lenom`, `valide`, `droit_id`) VALUES
(3, 'Admin', 'admin', 'sallartistee@yahoo.fr', 'Super Admin', 1, 1),
(4, 'eric', 'salla', 'sallartiste@gmail.com', 'Eric Salla', 1, 2),
(5, 'util1', 'util1', 'utili@yahoo.nz', 'Utilisateur One', 1, 3),
(6, 'Util2', 'util2', 'utilisateur@wanadoo.uk', 'Utilisateur Two', 1, 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `droit`
--
ALTER TABLE `droit`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lenom_UNIQUE` (`lenom`);

--
-- Indexes for table `photo`
--
ALTER TABLE `photo`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lenom_p_UNIQUE` (`lenom`),
  ADD KEY `fk_photo_utilisateur1_idx` (`utilisateur_id`);

--
-- Indexes for table `photo_has_rubriques`
--
ALTER TABLE `photo_has_rubriques`
  ADD KEY `fk_photo_has_rubriques_rubriques1_idx` (`rubriques_id`),
  ADD KEY `fk_photo_has_rubriques_photo1_idx` (`photo_id`);

--
-- Indexes for table `rubriques`
--
ALTER TABLE `rubriques`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `lelogin_UNIQUE` (`lelogin`),
  ADD KEY `fk_utilisateur_droit_idx` (`droit_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `droit`
--
ALTER TABLE `droit`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- AUTO_INCREMENT for table `photo`
--
ALTER TABLE `photo`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;
--
-- AUTO_INCREMENT for table `rubriques`
--
ALTER TABLE `rubriques`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `utilisateur`
--
ALTER TABLE `utilisateur`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `photo`
--
ALTER TABLE `photo`
  ADD CONSTRAINT `fk_photo_utilisateur1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateur` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `photo_has_rubriques`
--
ALTER TABLE `photo_has_rubriques`
  ADD CONSTRAINT `fk_photo_has_rubriques_photo1` FOREIGN KEY (`photo_id`) REFERENCES `photo` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_photo_has_rubriques_rubriques1` FOREIGN KEY (`rubriques_id`) REFERENCES `rubriques` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `utilisateur`
--
ALTER TABLE `utilisateur`
  ADD CONSTRAINT `fk_utilisateur_droit` FOREIGN KEY (`droit_id`) REFERENCES `droit` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
