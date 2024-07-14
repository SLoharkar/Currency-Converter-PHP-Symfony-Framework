-- MySQL dump 10.13  Distrib 8.0.34, for Win64 (x86_64)
--
-- Host: localhost    Database: mysqldb
-- ------------------------------------------------------
-- Server version	8.0.34

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `authorized_ip`
--

DROP TABLE IF EXISTS `authorized_ip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `authorized_ip` (
  `id` int NOT NULL AUTO_INCREMENT,
  `ip_address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `authorized_ip`
--

LOCK TABLES `authorized_ip` WRITE;
/*!40000 ALTER TABLE `authorized_ip` DISABLE KEYS */;
INSERT INTO `authorized_ip` VALUES (16,'192.168.1.10/24'),(17,'10.0.0.1/8'),(26,'192.168.38.1/24');
/*!40000 ALTER TABLE `authorized_ip` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `plain_password` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `roles` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  CONSTRAINT `users_chk_1` CHECK (json_valid(`roles`))
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (11,'admin','$2y$13$13LveppfFhR7OIsgcsVexeglcnyS4AuHnnX1PA3bsXSyBYUf3PTEe','admin','[\"ROLE_ADMIN\"]'),(14,'Sam','$2y$13$Kjn9Ibe9UW4NG0IMunq3Dejb.GvhY2clc0sIlfh.wXsN4WbkuwNSS','Sam','[\"ROLE_USER\"]'),(30,'Test','$2y$13$JD27nMq2ojvx2t1agPp0Ru9mq.LQEZoOc7iarnZtKca4LryJyzE3m','Test','[\"ROLE_USER\"]'),(31,'Rohit','$2y$13$vxjHHmUVgvO242wZJ66KcOCJdrz6w8zT5wCUkXQXyGOBU9fVDtTp6','Rohit','[\"ROLE_ADMIN\"]'),(32,'Ramesh','$2y$13$bJjsiufgWytKq2QPNVzmROZSe9K.TpznSUz/5a7EQzla6UcvvAdCu','Ramesh','[\"ROLE_USER\"]'),(33,'Ram','$2y$13$7V19OXQN4GnQ5sK3xurXAuHgUXNVSr.JldV2YEH9FgrUQXYApm7/W','Ram','[\"ROLE_ADMIN\"]'),(34,'Mahadev','$2y$13$.vMhHoOALkskrW/53WL2XuiRiiKsspTdwWjE6AQZGuxezR6moXrz2','Mahadev','[\"ROLE_USER\"]'),(35,'System','$2y$13$l3SWFHdyN/1kGwEmGI3YmOiqvCsUvhvqlkUeBsn5n7E8qfgDv2x3G','System','[\"ROLE_ADMIN\"]'),(36,'Username','$2y$13$qpAXdJljELMhBtATBYkB.e3.sQeaaE1NySzhI31ICabPkdZo./oES','Password','[\"ROLE_USER\"]');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2024-07-14 17:13:08
