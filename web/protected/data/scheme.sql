-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 14, 2016 at 04:34 PM
-- Server version: 5.7.16-0ubuntu0.16.04.1
-- PHP Version: 7.0.14-2+deb.sury.org~xenial+1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tarsius`
--

-- --------------------------------------------------------

--
-- Table structure for table `configuracao`
--

CREATE TABLE `configuracao` (
  `id` int(11) NOT NULL,
  `ativo` int(1) NOT NULL DEFAULT '1',
  `descricao` text NOT NULL,
  `maxProcessosAtivos` int(11) DEFAULT NULL,
  `maxAquivosProcessos` int(11) NOT NULL DEFAULT '80',
  `exportType` int(11) NOT NULL,
  `exportHost` varchar(256) DEFAULT NULL,
  `exportDatabase` varchar(64) DEFAULT NULL,
  `exportPort` varchar(64) DEFAULT NULL,
  `exportTable` varchar(64) DEFAULT NULL,
  `exportUser` varchar(64) DEFAULT NULL,
  `exportPwd` varchar(256) DEFAULT NULL,
  `exportUrl` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `distribuido`
--

CREATE TABLE `distribuido` (
  `id` int(11) NOT NULL,
  `nome` varchar(256) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `trabalho_id` int(11) DEFAULT NULL,
  `tempDir` varchar(40) DEFAULT NULL,
  `dataDistribuicao` int(11) DEFAULT NULL,
  `dataFechamento` int(11) DEFAULT NULL,
  `output` text,
  `exportado` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `erro`
--

CREATE TABLE `erro` (
  `id` int(11) NOT NULL,
  `trabalho_id` int(11) DEFAULT NULL,
  `texto` text,
  `read` int(11) DEFAULT '0',
  `trace` text
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `finalizado`
--

CREATE TABLE `finalizado` (
  `id` int(11) NOT NULL,
  `nome` varchar(200) DEFAULT NULL,
  `conteudo` text,
  `trabalho_id` int(11) DEFAULT NULL,
  `dataFechamento` int(11) DEFAULT NULL,
  `exportado` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `processo`
--

CREATE TABLE `processo` (
  `id` int(11) NOT NULL,
  `pid` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `trabalho_id` int(11) DEFAULT NULL,
  `workDir` text,
  `qtd` int(11) DEFAULT NULL,
  `dataInicio` int(11) DEFAULT NULL,
  `dataFim` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `trabalho`
--

CREATE TABLE `trabalho` (
  `id` int(11) NOT NULL,
  `nome` varchar(45) DEFAULT NULL,
  `sourceDir` text,
  `status` int(11) DEFAULT '0',
  `pid` int(11) DEFAULT NULL,
  `tempoDistribuicao` int(11) DEFAULT '10',
  `template` text,
  `distribuindo` int(11) DEFAULT '0',
  `export` text,
  `urlImagens` text,
  `command` varchar(256) NOT NULL DEFAULT 'php',
  `perfil_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `trabalho_perfil`
--

CREATE TABLE `trabalho_perfil` (
  `id` int(11) NOT NULL,
  `descricao` text NOT NULL,
  `enableDebug` int(1) NOT NULL DEFAULT '0',
  `threshold` int(4) NOT NULL DEFAULT '140',
  `minArea` int(4) NOT NULL DEFAULT '500',
  `maxArea` int(5) NOT NULL DEFAULT '4000',
  `areaTolerance` float NOT NULL DEFAULT '0.4',
  `minMatchObject` float NOT NULL DEFAULT '0.85',
  `maxExpansions` int(3) NOT NULL DEFAULT '4',
  `expasionRate` float NOT NULL DEFAULT '0.5',
  `searchArea` int(11) NOT NULL DEFAULT '10',
  `minMatchEllipse` float NOT NULL DEFAULT '0.3',
  `templateValidationTolerance` int(11) NOT NULL DEFAULT '3',
  `dynamicPointReference` int(1) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `configuracao`
--
ALTER TABLE `configuracao`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `distribuido`
--
ALTER TABLE `distribuido`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_distribuido_nome` (`nome`),
  ADD KEY `isx_distribuido_trabalho` (`trabalho_id`),
  ADD KEY `idx_distribuido_status` (`status`),
  ADD KEY `idx_dist_all` (`status`,`nome`,`id`,`exportado`);

--
-- Indexes for table `erro`
--
ALTER TABLE `erro`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `finalizado`
--
ALTER TABLE `finalizado`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_finalizado_nome` (`nome`,`trabalho_id`),
  ADD KEY `idx_finalizado_trab_d` (`trabalho_id`),
  ADD KEY `all` (`id`,`nome`,`trabalho_id`,`dataFechamento`);

--
-- Indexes for table `processo`
--
ALTER TABLE `processo`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trabalho`
--
ALTER TABLE `trabalho`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_trab_perfil` (`perfil_id`);

--
-- Indexes for table `trabalho_perfil`
--
ALTER TABLE `trabalho_perfil`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `configuracao`
--
ALTER TABLE `configuracao`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT for table `distribuido`
--
ALTER TABLE `distribuido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=404;
--
-- AUTO_INCREMENT for table `erro`
--
ALTER TABLE `erro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
--
-- AUTO_INCREMENT for table `finalizado`
--
ALTER TABLE `finalizado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=325;
--
-- AUTO_INCREMENT for table `processo`
--
ALTER TABLE `processo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;
--
-- AUTO_INCREMENT for table `trabalho`
--
ALTER TABLE `trabalho`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
--
-- AUTO_INCREMENT for table `trabalho_perfil`
--
ALTER TABLE `trabalho_perfil`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `trabalho`
--
ALTER TABLE `trabalho`
  ADD CONSTRAINT `fk_trab_perfil` FOREIGN KEY (`perfil_id`) REFERENCES `trabalho_perfil` (`id`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
