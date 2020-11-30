-- MySQL dump 10.13  Distrib 5.7.12, for Win64 (x86_64)
--
-- Host: localhost    Database: file_manager
-- ------------------------------------------------------
-- Server version	5.6.17

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
-- Table structure for table `archivo`
--

DROP TABLE IF EXISTS `archivo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `archivo` (
  `id_archivo` int(8) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(20) NOT NULL,
  `nombre` varchar(100) DEFAULT NULL,
  `es_carpeta` tinyint(1) NOT NULL,
  `id_padre` int(8) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id_archivo`),
  UNIQUE KEY `i_nombre` (`nombre`),
  KEY `i_id_padre` (`id_padre`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `archivo`
--

LOCK TABLES `archivo` WRITE;
/*!40000 ALTER TABLE `archivo` DISABLE KEYS */;
/*!40000 ALTER TABLE `archivo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `padre_hijo`
--

DROP TABLE IF EXISTS `padre_hijo`;
/*!50001 DROP VIEW IF EXISTS `padre_hijo`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `padre_hijo` AS SELECT 
 1 AS `id_abuelo`,
 1 AS `id_padre`,
 1 AS `nombre_padre`,
 1 AS `id_hijo`,
 1 AS `nombre_hijo`,
 1 AS `tipo`,
 1 AS `es_carpeta`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vista_b`
--

DROP TABLE IF EXISTS `vista_b`;
/*!50001 DROP VIEW IF EXISTS `vista_b`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE VIEW `vista_b` AS SELECT 
 1 AS `id_archivo`,
 1 AS `tipo`,
 1 AS `nombre`,
 1 AS `es_carpeta`,
 1 AS `id_padre`*/;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `padre_hijo`
--

/*!50001 DROP VIEW IF EXISTS `padre_hijo`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `padre_hijo` AS select `a`.`id_padre` AS `id_abuelo`,`b`.`id_padre` AS `id_padre`,`a`.`nombre` AS `nombre_padre`,`b`.`id_archivo` AS `id_hijo`,`b`.`nombre` AS `nombre_hijo`,`b`.`tipo` AS `tipo`,`b`.`es_carpeta` AS `es_carpeta` from (`archivo` `a` join `vista_b` `b` on((`a`.`id_archivo` = `b`.`id_padre`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_b`
--

/*!50001 DROP VIEW IF EXISTS `vista_b`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_b` AS select `archivo`.`id_archivo` AS `id_archivo`,`archivo`.`tipo` AS `tipo`,`archivo`.`nombre` AS `nombre`,`archivo`.`es_carpeta` AS `es_carpeta`,`archivo`.`id_padre` AS `id_padre` from `archivo` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-11-30 16:00:05
