-- MySQL dump 10.13  Distrib 5.7.24, for Linux (i686)
--
-- Host: eu-cdbr-west-01.cleardb.com    Database: heroku_c5b718d1b9da9b3
-- ------------------------------------------------------
-- Server version	5.5.62-log

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
-- Table structure for table `bill`
--

DROP TABLE IF EXISTS `bill`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bill` (
  `bill_id` tinyint(4) unsigned NOT NULL AUTO_INCREMENT,
  `house_id` tinyint(4) unsigned NOT NULL,
  `room_price` varchar(63) CHARACTER SET utf8 NOT NULL,
  `room_description` varchar(63) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`bill_id`)
) ENGINE=MyISAM AUTO_INCREMENT=21 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bill`
--

LOCK TABLES `bill` WRITE;
/*!40000 ALTER TABLE `bill` DISABLE KEYS */;
INSERT INTO `bill` VALUES (19,2,'110','First floor large double room'),(3,3,'86',''),(9,1,'65','Small Double Bedroom'),(1,1,'85','Double with en-suite'),(4,4,'89','double bedroom'),(5,5,'80','large double with en-suite toilet'),(10,1,'75','Double Bedrooms'),(6,6,'100','Room with en-suite'),(8,7,'70','doubles with en-suite'),(20,2,'110','Second floor large double'),(13,5,'75','large double'),(7,6,'90','Room without en-suite'),(16,7,'65','large double'),(15,6,'105','Room with en-suite & kitchenette'),(14,5,'65','single bedrooms'),(17,7,'60','double'),(18,2,'100','First floor double room'),(2,2,'100','Ground floor double room');
/*!40000 ALTER TABLE `bill` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `description`
--

DROP TABLE IF EXISTS `description`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `description` (
  `house_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `slug` varchar(31) COLLATE utf8_unicode_ci NOT NULL,
  `house_address` varchar(63) CHARACTER SET utf8 NOT NULL,
  `postcode` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(127) CHARACTER SET utf8 NOT NULL,
  `description` varchar(511) CHARACTER SET utf8 NOT NULL,
  `rented` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `house_order` tinyint(3) NOT NULL,
  PRIMARY KEY (`house_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `description`
--

LOCK TABLES `description` WRITE;
/*!40000 ALTER TABLE `description` DISABLE KEYS */;
INSERT INTO `description` VALUES (1,'22-kyme-st','22 Kyme Street','YO1 6HG','Fully Modernised 4-Bedroom End-Terraced House','Located inside the City Walls, this is really close to the town centre, train station and Micklegate; with nearby frequent Number 4 bus route direct to the University.',1,1),(2,'23-moss-st','23 Moss Street','YO23 1BR','4-Bedroom Terraced House \r\n\r\nbills included','Really close to train station and town centre, just off Micklegate. Frequent Number 4 bus route direct to University from top of street.',1,5),(3,'25-lamel-st','25 Lamel Street','YO10 3LL','Modernised 6-Bedroom Semi-Detached House','Located near Hull Road, this property is really close to the University and the City Centre.',1,4),(4,'52-moss-st','52 Moss Street','YO23 1BR','4-Bedroom Terraced House','Really close to train station and town centre, just off Micklegate. Frequent Number 4 bus route direct to University from top of street.',1,6),(5,'54-trafalgar-st','54 Trafalgar Street','YO23 1HT','Fully Modernised 4-Bedroom Terraced House','Located in the South Bank area of York, this property has easy access to the University of York via the Millennium Bridge by foot or bike.',1,3),(6,'74-eldon-st','74 Eldon Street','YO31 7NE','Modern 5-Bedroom House','Located in The Groves, Ideal for York St John University. Close to the town centre with views of the Minster.',1,2),(7,'88-queen-victoria-st','88 Queen Victoria Street','YO23 1HN','4-Bedroom Terraced House - 1 Room available from 5th July 2015 - 4th October 2016 - Â£260pcm + bills','Located in the South Bank area of York, this property has easy access to the University of York via the Millennium Bridge by foot or bike.',1,7);
/*!40000 ALTER TABLE `description` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `epc`
--

DROP TABLE IF EXISTS `epc`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `epc` (
  `house_id` tinyint(3) unsigned NOT NULL,
  `eer_current` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `eer_potential` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `eir_current` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `eir_potential` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`house_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `epc`
--

LOCK TABLES `epc` WRITE;
/*!40000 ALTER TABLE `epc` DISABLE KEYS */;
INSERT INTO `epc` VALUES (1,71,76,67,73),(2,70,73,66,68),(3,52,67,45,62),(4,0,0,0,0),(5,59,70,51,65),(6,77,81,75,79),(7,70,75,65,71);
/*!40000 ALTER TABLE `epc` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `feature`
--

DROP TABLE IF EXISTS `feature`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `feature` (
  `feature_id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `house_id` tinyint(3) unsigned NOT NULL,
  `feature` varchar(255) CHARACTER SET utf8 NOT NULL,
  PRIMARY KEY (`feature_id`)
) ENGINE=MyISAM AUTO_INCREMENT=72 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `feature`
--

LOCK TABLES `feature` WRITE;
/*!40000 ALTER TABLE `feature` DISABLE KEYS */;
INSERT INTO `feature` VALUES (46,6,'Balcony to rear of kitchen with stairs leading to back yard'),(12,2,'Back yard for storage of bikes'),(36,5,'Modern fully fitted kitchen with fridge/freezer, washing machine, tumble dryer and cooker'),(11,2,'Good-sized lounge off kitchen'),(63,6,'Convenient for York District Hospital'),(24,3,'Registered [abbr=House in Multiple Occupation][url=http://www.york.gov.uk/info/200480/private_landlords/373/licensing]HMO[/url][/abbr] with [url=http://www.york.gov.uk/]York City Council[/url]'),(69,4,'House fully fitted with PVCu windows and doors'),(70,4,'Backyard for storage of bikes'),(38,5,'Secure back yard with locked storage for bikes'),(34,5,'Luxury house shower room with shower, toilet and sink'),(35,5,'En-suite toilet to largest bedroom'),(59,7,'Back yard for storage of bikes'),(60,7,'Free parking on street at front of house'),(58,7,'Fully fitted kitchen with cooker, fridge/freezer, tumble dryer and washing machine'),(21,3,'Secure storage for bikes and off-road parking for 1 car'),(48,6,'Burglar alarm system'),(49,6,'Hard-wired fire alarm system throughout house'),(66,1,'House is fully centrally heated, has PVCu windows throughout and has well insulated roof and walls.'),(50,6,'Registered [abbr=House in Multiple Occupation][url=http://www.york.gov.uk/info/200480/private_landlords/373/licensing]HMO[/url][/abbr] with [url=http://www.york.gov.uk/]York City Council[/url]'),(47,6,'Garage parking for 1 car and bikes'),(23,3,'Gravelled patio garden to rear'),(22,3,'Plenty of free road-side parking'),(10,2,'Modern fully-fitted dining kitchen with fridge/freezer, washing machine, tumble dryer and cooker'),(4,1,'Four double bedrooms - one with en-suite, all with plenty of storage space and TV points'),(39,5,'Free on-street parking'),(57,7,'Lounge with dining facilities'),(68,4,'House fully centrally heated'),(30,4,'Fully fitted kitchen with cooker, fridge/freezer and washing machine and tumble drier'),(20,3,'Lounge with sofa and TV point'),(56,7,'House bathroom with shower, toilet and sink'),(65,1,'House bathroom with shower, toilet and wash basin'),(8,2,'Fully centrally heated, with new efficient condensing combi boiler and PVCu double-glazed windows'),(55,7,'PVCu double-glazed windows and doors'),(45,6,'Utility room with washer and tumble dryer'),(44,6,'Modern kitchen with fridge/freezer,cooker, dining table and chairs'),(18,3,'Extra toilet (toilet and sink only)'),(17,3,'Shower room with shower, toilet and sink'),(54,7,'Fully centrally heated'),(53,7,'One bedroom with en-suite bathroom, one bedroom with en-suite toilet'),(29,4,'Lounge/dining area leading to kitchen'),(16,3,'House bathroom with bath, shower over bath, toilet and sink'),(37,5,'Large lounge/dining area off kitchen'),(9,2,'2 luxury house shower rooms with shower, toilet and sink in each'),(28,4,'Separate house shower room with shower, toilet and basin'),(19,3,'Fully fitted kitchen with fridge/freezer, washing machine, tumble dryer, cooker, dining table and chairs'),(15,3,'PVCu double-glazed windows'),(14,3,'Fully centrally heated'),(42,6,'House bathroom with separate toilet'),(43,6,'Fully centrally heated with double-glazed windows'),(41,6,'Two bedrooms have en-suite bathrooms'),(52,7,'4 good-sized double bedrooms, all including desks and storage facilities'),(51,7,'Fully renovated. Will rent this house either to a group or will rent the rooms individually.'),(27,4,'2 of these bedrooms are extra large with sofa, kitchenette and en-suite shower room'),(33,5,'Fully centrally heated with PVCu double-glazed windows'),(3,1,'Good sized lounge/diner off fully-fitted kitchen with fridge freezer, washing machine, tumble drier and cooker'),(1,1,'Fully renovated'),(40,6,'All large double bedrooms with desks, TV points and plenty of storage space'),(13,3,'All bedrooms good size with sinks, desks, TV points and plenty of storage space'),(7,2,'All good size double bedrooms with desks, TV points and plenty of storage space'),(6,2,'Fully renovated'),(32,5,'All bedrooms good size with desks, TV points and plenty of storage space'),(31,5,'Fully renovated'),(26,4,'4 good-sized double bedrooms with storage facilities'),(25,4,'House fully renovated');
/*!40000 ALTER TABLE `feature` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `setting`
--

DROP TABLE IF EXISTS `setting`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `setting` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `setting` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `value` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting` (`setting`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `setting`
--

LOCK TABLES `setting` WRITE;
/*!40000 ALTER TABLE `setting` DISABLE KEYS */;
INSERT INTO `setting` VALUES (1,'year','2019');
/*!40000 ALTER TABLE `setting` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-10-23  1:48:37
