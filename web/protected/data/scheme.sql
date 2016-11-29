-- MySQL dump 10.13  Distrib 5.7.9, for Win64 (x86_64)
--
-- Host: imagens-concursos.ufrgs.br    Database: tarsius
-- ------------------------------------------------------
-- Server version	5.5.51

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `distribuido`
--

DROP TABLE IF EXISTS `distribuido`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `distribuido` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(40) DEFAULT NULL,
  `status` int(11) DEFAULT '0',
  `trabalho_id` int(11) DEFAULT NULL,
  `tempDir` varchar(40) DEFAULT NULL,
  `dataDistribuicao` int(11) DEFAULT NULL,
  `dataFechamento` int(11) DEFAULT NULL,
  `output` text,
  `exportado` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_distribuido_nome` (`nome`),
  KEY `isx_distribuido_trabalho` (`trabalho_id`),
  KEY `idx_distribuido_status` (`status`),
  KEY `idx_dist_all` (`status`,`nome`,`id`,`exportado`)
) ENGINE=InnoDB AUTO_INCREMENT=192556 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `erro`
--

DROP TABLE IF EXISTS `erro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `erro` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trabalho_id` int(11) DEFAULT NULL,
  `texto` text,
  `read` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `finalizado`
--

DROP TABLE IF EXISTS `finalizado`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `finalizado` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(200) DEFAULT NULL,
  `conteudo` text,
  `trabalho_id` int(11) DEFAULT NULL,
  `dataFechamento` int(11) DEFAULT NULL,
  `exportado` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idx_finalizado_nome` (`nome`,`trabalho_id`),
  KEY `idx_finalizado_trab_d` (`trabalho_id`),
  KEY `all` (`id`,`nome`,`trabalho_id`,`dataFechamento`)
) ENGINE=InnoDB AUTO_INCREMENT=174236 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `processo`
--

DROP TABLE IF EXISTS `processo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `processo` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `pid` int(11) DEFAULT NULL,
  `status` int(11) DEFAULT NULL,
  `trabalho_id` int(11) DEFAULT NULL,
  `workDir` text,
  `qtd` int(11) DEFAULT NULL,
  `dataInicio` int(11) DEFAULT NULL,
  `dataFim` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2472 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `trabalho`
--

DROP TABLE IF EXISTS `trabalho`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trabalho` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-11-29 15:36:55
