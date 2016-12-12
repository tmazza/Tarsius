-- phpMyAdmin SQL Dump
-- version 4.5.4.1deb2ubuntu2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Dec 12, 2016 at 02:43 PM
-- Server version: 5.7.16-0ubuntu0.16.04.1
-- PHP Version: 7.0.8-0ubuntu0.16.04.3

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
  `taxaPreenchimento` double DEFAULT '0.3',
  `distribuindo` int(11) DEFAULT '0',
  `export` text,
  `urlImagens` text,
  `command` varchar(256) NOT NULL DEFAULT 'php'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

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
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `distribuido`
--
ALTER TABLE `distribuido`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
--
-- AUTO_INCREMENT for table `erro`
--
ALTER TABLE `erro`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `finalizado`
--
ALTER TABLE `finalizado`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;
--
-- AUTO_INCREMENT for table `processo`
--
ALTER TABLE `processo`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;
--
-- AUTO_INCREMENT for table `trabalho`
--
ALTER TABLE `trabalho`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
