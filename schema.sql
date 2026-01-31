-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: localhost    Database: study-tools-website
-- ------------------------------------------------------
-- Server version	8.0.41

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
-- Table structure for table `auth_group`
--

DROP TABLE IF EXISTS `auth_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_group` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_group`
--

LOCK TABLES `auth_group` WRITE;
/*!40000 ALTER TABLE `auth_group` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_group` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_group_permissions`
--

DROP TABLE IF EXISTS `auth_group_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_group_permissions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `group_id` int NOT NULL,
  `permission_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_group_permissions_group_id_permission_id_0cd325b0_uniq` (`group_id`,`permission_id`),
  KEY `auth_group_permissio_permission_id_84c5c92e_fk_auth_perm` (`permission_id`),
  CONSTRAINT `auth_group_permissio_permission_id_84c5c92e_fk_auth_perm` FOREIGN KEY (`permission_id`) REFERENCES `auth_permission` (`id`),
  CONSTRAINT `auth_group_permissions_group_id_b120cbf9_fk_auth_group_id` FOREIGN KEY (`group_id`) REFERENCES `auth_group` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_group_permissions`
--

LOCK TABLES `auth_group_permissions` WRITE;
/*!40000 ALTER TABLE `auth_group_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_group_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_permission`
--

DROP TABLE IF EXISTS `auth_permission`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_permission` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_type_id` int NOT NULL,
  `codename` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_permission_content_type_id_codename_01ab375a_uniq` (`content_type_id`,`codename`),
  CONSTRAINT `auth_permission_content_type_id_2f476e4b_fk_django_co` FOREIGN KEY (`content_type_id`) REFERENCES `django_content_type` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=93 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_permission`
--

LOCK TABLES `auth_permission` WRITE;
/*!40000 ALTER TABLE `auth_permission` DISABLE KEYS */;
INSERT INTO `auth_permission` VALUES (1,'Can add log entry',1,'add_logentry'),(2,'Can change log entry',1,'change_logentry'),(3,'Can delete log entry',1,'delete_logentry'),(4,'Can view log entry',1,'view_logentry'),(5,'Can add permission',2,'add_permission'),(6,'Can change permission',2,'change_permission'),(7,'Can delete permission',2,'delete_permission'),(8,'Can view permission',2,'view_permission'),(9,'Can add group',3,'add_group'),(10,'Can change group',3,'change_group'),(11,'Can delete group',3,'delete_group'),(12,'Can view group',3,'view_group'),(13,'Can add user',4,'add_user'),(14,'Can change user',4,'change_user'),(15,'Can delete user',4,'delete_user'),(16,'Can view user',4,'view_user'),(17,'Can add content type',5,'add_contenttype'),(18,'Can change content type',5,'change_contenttype'),(19,'Can delete content type',5,'delete_contenttype'),(20,'Can view content type',5,'view_contenttype'),(21,'Can add session',6,'add_session'),(22,'Can change session',6,'change_session'),(23,'Can delete session',6,'delete_session'),(24,'Can view session',6,'view_session'),(25,'Can add calendar',7,'add_calendar'),(26,'Can change calendar',7,'change_calendar'),(27,'Can delete calendar',7,'delete_calendar'),(28,'Can view calendar',7,'view_calendar'),(29,'Can add event',8,'add_event'),(30,'Can change event',8,'change_event'),(31,'Can delete event',8,'delete_event'),(32,'Can view event',8,'view_event'),(33,'Can add event reminder',9,'add_eventreminder'),(34,'Can change event reminder',9,'change_eventreminder'),(35,'Can delete event reminder',9,'delete_eventreminder'),(36,'Can view event reminder',9,'view_eventreminder'),(37,'Can add flashcard',10,'add_flashcard'),(38,'Can change flashcard',10,'change_flashcard'),(39,'Can delete flashcard',10,'delete_flashcard'),(40,'Can view flashcard',10,'view_flashcard'),(41,'Can add flashcarditem',11,'add_flashcarditem'),(42,'Can change flashcarditem',11,'change_flashcarditem'),(43,'Can delete flashcarditem',11,'delete_flashcarditem'),(44,'Can view flashcarditem',11,'view_flashcarditem'),(45,'Can add flashcardprogress',12,'add_flashcardprogress'),(46,'Can change flashcardprogress',12,'change_flashcardprogress'),(47,'Can delete flashcardprogress',12,'delete_flashcardprogress'),(48,'Can view flashcardprogress',12,'view_flashcardprogress'),(49,'Can add flashcardset',13,'add_flashcardset'),(50,'Can change flashcardset',13,'change_flashcardset'),(51,'Can delete flashcardset',13,'delete_flashcardset'),(52,'Can view flashcardset',13,'view_flashcardset'),(53,'Can add habit',14,'add_habit'),(54,'Can change habit',14,'change_habit'),(55,'Can delete habit',14,'delete_habit'),(56,'Can view habit',14,'view_habit'),(57,'Can add habitlist',15,'add_habitlist'),(58,'Can change habitlist',15,'change_habitlist'),(59,'Can delete habitlist',15,'delete_habitlist'),(60,'Can view habitlist',15,'view_habitlist'),(61,'Can add habitlistlog',16,'add_habitlistlog'),(62,'Can change habitlistlog',16,'change_habitlistlog'),(63,'Can delete habitlistlog',16,'delete_habitlistlog'),(64,'Can view habitlistlog',16,'view_habitlistlog'),(65,'Can add pomodoro',17,'add_pomodoro'),(66,'Can change pomodoro',17,'change_pomodoro'),(67,'Can delete pomodoro',17,'delete_pomodoro'),(68,'Can view pomodoro',17,'view_pomodoro'),(69,'Can add pomodorohistory',18,'add_pomodorohistory'),(70,'Can change pomodorohistory',18,'change_pomodorohistory'),(71,'Can delete pomodorohistory',18,'delete_pomodorohistory'),(72,'Can view pomodorohistory',18,'view_pomodorohistory'),(73,'Can add task',19,'add_task'),(74,'Can change task',19,'change_task'),(75,'Can delete task',19,'delete_task'),(76,'Can view task',19,'view_task'),(77,'Can add todolist',20,'add_todolist'),(78,'Can change todolist',20,'change_todolist'),(79,'Can delete todolist',20,'delete_todolist'),(80,'Can view todolist',20,'view_todolist'),(81,'Can add todolistgroup',21,'add_todolistgroup'),(82,'Can change todolistgroup',21,'change_todolistgroup'),(83,'Can delete todolistgroup',21,'delete_todolistgroup'),(84,'Can view todolistgroup',21,'view_todolistgroup'),(85,'Can add user',22,'add_user'),(86,'Can change user',22,'change_user'),(87,'Can delete user',22,'delete_user'),(88,'Can view user',22,'view_user'),(89,'Can add users avatar',23,'add_usersavatar'),(90,'Can change users avatar',23,'change_usersavatar'),(91,'Can delete users avatar',23,'delete_usersavatar'),(92,'Can view users avatar',23,'view_usersavatar');
/*!40000 ALTER TABLE `auth_permission` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_user`
--

DROP TABLE IF EXISTS `auth_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_user` (
  `id` int NOT NULL AUTO_INCREMENT,
  `password` varchar(128) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_login` datetime(6) DEFAULT NULL,
  `is_superuser` tinyint(1) NOT NULL,
  `username` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `first_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(254) COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_staff` tinyint(1) NOT NULL,
  `is_active` tinyint(1) NOT NULL,
  `date_joined` datetime(6) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_user`
--

LOCK TABLES `auth_user` WRITE;
/*!40000 ALTER TABLE `auth_user` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_user_groups`
--

DROP TABLE IF EXISTS `auth_user_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_user_groups` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `group_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_user_groups_user_id_group_id_94350c0c_uniq` (`user_id`,`group_id`),
  KEY `auth_user_groups_group_id_97559544_fk_auth_group_id` (`group_id`),
  CONSTRAINT `auth_user_groups_group_id_97559544_fk_auth_group_id` FOREIGN KEY (`group_id`) REFERENCES `auth_group` (`id`),
  CONSTRAINT `auth_user_groups_user_id_6a12ed8b_fk_auth_user_id` FOREIGN KEY (`user_id`) REFERENCES `auth_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_user_groups`
--

LOCK TABLES `auth_user_groups` WRITE;
/*!40000 ALTER TABLE `auth_user_groups` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_user_groups` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `auth_user_user_permissions`
--

DROP TABLE IF EXISTS `auth_user_user_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auth_user_user_permissions` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `permission_id` int NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `auth_user_user_permissions_user_id_permission_id_14a6b632_uniq` (`user_id`,`permission_id`),
  KEY `auth_user_user_permi_permission_id_1fbb5f2c_fk_auth_perm` (`permission_id`),
  CONSTRAINT `auth_user_user_permi_permission_id_1fbb5f2c_fk_auth_perm` FOREIGN KEY (`permission_id`) REFERENCES `auth_permission` (`id`),
  CONSTRAINT `auth_user_user_permissions_user_id_a95ead1b_fk_auth_user_id` FOREIGN KEY (`user_id`) REFERENCES `auth_user` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auth_user_user_permissions`
--

LOCK TABLES `auth_user_user_permissions` WRITE;
/*!40000 ALTER TABLE `auth_user_user_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `auth_user_user_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `calendar`
--

DROP TABLE IF EXISTS `calendar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `calendar` (
  `calendar_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'My Calendar',
  `color` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT '#4a6cf7',
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_deleted` tinyint NOT NULL DEFAULT '0',
  `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `updated_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  `user_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`calendar_id`),
  UNIQUE KEY `unique_user_calendar` (`user_id`),
  KEY `Calendar_user_id_f281c5b2_fk_User_user_id` (`user_id`),
  CONSTRAINT `Calendar_user_id_f281c5b2_fk_User_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `calendar`
--

LOCK TABLES `calendar` WRITE;
/*!40000 ALTER TABLE `calendar` DISABLE KEYS */;
INSERT INTO `calendar` VALUES ('1be324da-fc4e-11f0-aa8f-74d4dd2c201f','Calendar của SV3','#f59e0b','Calendar cá nhân',0,'2026-01-27 20:34:45.000000','2026-01-28 20:34:45.399877','5955a743-4ec7-4bc3-b74d-51fdbb29791f'),('1be3259d-fc4e-11f0-aa8f-74d4dd2c201f','Calendar của SV4','#10b981','Calendar cá nhân',0,'2026-01-07 20:34:45.000000','2026-01-28 20:34:45.399877','665c60a5-ac6b-49fa-82c1-34e97bc3b361'),('1be325e1-fc4e-11f0-aa8f-74d4dd2c201f','Calendar của SV5','#4a6cf7','Calendar cá nhân',0,'2026-01-20 20:34:45.000000','2026-01-28 20:34:45.399877','6436aa66-b2d3-4847-a8dd-c625dcbaff33'),('1be32617-fc4e-11f0-aa8f-74d4dd2c201f','Calendar của SV6','#4a6cf7','Calendar cá nhân',0,'2026-01-06 20:34:45.000000','2026-01-28 20:34:45.399877','ec8c547c-76d8-4c11-addd-a7ce2c669f16'),('1be326b1-fc4e-11f0-aa8f-74d4dd2c201f','Calendar của SV9','#f59e0b','Calendar cá nhân',0,'2026-01-21 20:34:45.000000','2026-01-28 20:34:45.399877','90007af7-f330-4cc2-82d0-3282783ecc4b'),('2376b122-da89-4727-811c-806f95e769b2','Default Calendar','#3b82f6',NULL,0,'2026-01-30 02:58:17.564602','2026-01-30 02:58:17.564605','d00a31f3-0661-4e31-b6ba-82a802e6c1e3'),('50a3d703-c99d-4ff6-be9e-c8867b31de41','My Calendar','#4a6cf7','Personal calendar',0,'2026-01-28 14:17:24.811397','2026-01-28 14:17:24.811397','303e179c-0ff3-4dd9-8c9b-d39ebeea66a3'),('6a84c511-f3b9-11f0-ba0d-74d4dd2c201f','My Calendar','#4a6cf7',NULL,0,'2026-01-17 22:30:12.000000','2026-01-17 22:30:12.997601','ADM001'),('6a84c8d9-f3b9-11f0-ba0d-74d4dd2c201f','My Calendar','#4a6cf7',NULL,0,'2026-01-17 22:30:12.000000','2026-01-17 22:30:12.997601','STU001'),('6a84c90b-f3b9-11f0-ba0d-74d4dd2c201f','My Calendar','#4a6cf7',NULL,0,'2026-01-17 22:30:12.000000','2026-01-17 22:30:12.997601','STU002'),('6a84c945-f3b9-11f0-ba0d-74d4dd2c201f','My Calendar','#4a6cf7',NULL,0,'2026-01-17 22:30:12.000000','2026-01-17 22:30:12.997601','ca2202af-5b0d-4d05-a20b-1bcb62d9a518'),('6a84c968-f3b9-11f0-ba0d-74d4dd2c201f','My Calendar','#4a6cf7',NULL,0,'2026-01-17 22:30:12.000000','2026-01-17 22:30:12.997601','60f2e760-2e9d-493d-b684-3c20a0c7d857'),('6a84c989-f3b9-11f0-ba0d-74d4dd2c201f','My Calendar','#4a6cf7',NULL,0,'2026-01-17 22:30:12.000000','2026-01-17 22:30:12.997601','8ad6fb11-4466-4019-a956-4279229a6f42'),('6a84c9a7-f3b9-11f0-ba0d-74d4dd2c201f','My Calendar','#4a6cf7',NULL,0,'2026-01-17 22:30:12.000000','2026-01-17 22:30:12.997601','de130b56-2a3e-4878-a723-0ac2be003ad9'),('8ffb4957-fc4c-11f0-aa8f-74d4dd2c201f','Calendar của SV8','#10b981','Calendar cá nhân',0,'2026-01-16 20:23:41.000000','2026-01-28 20:23:41.173319','1bd52590-db76-42c6-9aac-737c161c0f60'),('8ffb50e7-fc4c-11f0-aa8f-74d4dd2c201f','Calendar của SV10','#4a6cf7','Calendar cá nhân',0,'2025-12-31 20:23:41.000000','2026-01-28 20:23:41.173319','2ad82476-0064-4633-8e0e-7b4bd88fe052'),('8ffb5140-fc4c-11f0-aa8f-74d4dd2c201f','Calendar của SV7','#4a6cf7','Calendar cá nhân',0,'2026-01-20 20:23:41.000000','2026-01-28 20:23:41.173319','33b05e16-6843-42e9-8daa-8d4173f24ac3'),('8ffb5158-fc4c-11f0-aa8f-74d4dd2c201f','Calendar của SV2','#f59e0b','Calendar cá nhân',0,'2026-01-22 20:23:41.000000','2026-01-28 20:23:41.173319','3b75c652-2466-48a0-9bd0-5cdeec3c93fe'),('8ffb516f-fc4c-11f0-aa8f-74d4dd2c201f','Calendar của SV1','#ef4444','Calendar cá nhân',0,'2026-01-21 20:23:41.000000','2026-01-28 20:23:41.173319','3b85fa55-9fd9-4819-9ee5-a235ed0169c9');
/*!40000 ALTER TABLE `calendar` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `django_admin_log`
--

DROP TABLE IF EXISTS `django_admin_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `django_admin_log` (
  `id` int NOT NULL AUTO_INCREMENT,
  `action_time` datetime(6) NOT NULL,
  `object_id` longtext COLLATE utf8mb4_unicode_ci,
  `object_repr` varchar(200) COLLATE utf8mb4_unicode_ci NOT NULL,
  `action_flag` smallint unsigned NOT NULL,
  `change_message` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `content_type_id` int DEFAULT NULL,
  `user_id` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `django_admin_log_content_type_id_c4bce8eb_fk_django_co` (`content_type_id`),
  KEY `django_admin_log_user_id_c564eba6_fk_auth_user_id` (`user_id`),
  CONSTRAINT `django_admin_log_content_type_id_c4bce8eb_fk_django_co` FOREIGN KEY (`content_type_id`) REFERENCES `django_content_type` (`id`),
  CONSTRAINT `django_admin_log_user_id_c564eba6_fk_auth_user_id` FOREIGN KEY (`user_id`) REFERENCES `auth_user` (`id`),
  CONSTRAINT `django_admin_log_chk_1` CHECK ((`action_flag` >= 0))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `django_admin_log`
--

LOCK TABLES `django_admin_log` WRITE;
/*!40000 ALTER TABLE `django_admin_log` DISABLE KEYS */;
/*!40000 ALTER TABLE `django_admin_log` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `django_content_type`
--

DROP TABLE IF EXISTS `django_content_type`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `django_content_type` (
  `id` int NOT NULL AUTO_INCREMENT,
  `app_label` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `django_content_type_app_label_model_76bd3d3b_uniq` (`app_label`,`model`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `django_content_type`
--

LOCK TABLES `django_content_type` WRITE;
/*!40000 ALTER TABLE `django_content_type` DISABLE KEYS */;
INSERT INTO `django_content_type` VALUES (1,'admin','logentry'),(3,'auth','group'),(2,'auth','permission'),(4,'auth','user'),(5,'contenttypes','contenttype'),(7,'core','calendar'),(8,'core','event'),(9,'core','eventreminder'),(10,'core','flashcard'),(11,'core','flashcarditem'),(12,'core','flashcardprogress'),(13,'core','flashcardset'),(14,'core','habit'),(15,'core','habitlist'),(16,'core','habitlistlog'),(17,'core','pomodoro'),(18,'core','pomodorohistory'),(19,'core','task'),(20,'core','todolist'),(21,'core','todolistgroup'),(22,'core','user'),(23,'core','usersavatar'),(6,'sessions','session');
/*!40000 ALTER TABLE `django_content_type` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `django_migrations`
--

DROP TABLE IF EXISTS `django_migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `django_migrations` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `app` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `applied` datetime(6) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `django_migrations`
--

LOCK TABLES `django_migrations` WRITE;
/*!40000 ALTER TABLE `django_migrations` DISABLE KEYS */;
INSERT INTO `django_migrations` VALUES (1,'contenttypes','0001_initial','2026-01-29 19:44:10.413415'),(2,'auth','0001_initial','2026-01-29 19:44:11.157849'),(3,'admin','0001_initial','2026-01-29 19:44:11.314660'),(4,'admin','0002_logentry_remove_auto_add','2026-01-29 19:44:11.324452'),(5,'admin','0003_logentry_add_action_flag_choices','2026-01-29 19:44:11.334965'),(6,'contenttypes','0002_remove_content_type_name','2026-01-29 19:44:11.516222'),(7,'auth','0002_alter_permission_name_max_length','2026-01-29 19:44:11.611460'),(8,'auth','0003_alter_user_email_max_length','2026-01-29 19:44:11.655339'),(9,'auth','0004_alter_user_username_opts','2026-01-29 19:44:11.666634'),(10,'auth','0005_alter_user_last_login_null','2026-01-29 19:44:11.774065'),(11,'auth','0006_require_contenttypes_0002','2026-01-29 19:44:11.778129'),(12,'auth','0007_alter_validators_add_error_messages','2026-01-29 19:44:11.791192'),(13,'auth','0008_alter_user_username_max_length','2026-01-29 19:44:11.936354'),(14,'auth','0009_alter_user_last_name_max_length','2026-01-29 19:44:12.020026'),(15,'auth','0010_alter_group_name_max_length','2026-01-29 19:44:12.049240'),(16,'auth','0011_update_proxy_permissions','2026-01-29 19:44:12.060130'),(17,'auth','0012_alter_user_first_name_max_length','2026-01-29 19:44:12.153143'),(18,'core','0001_initial','2026-01-29 19:44:12.169047'),(19,'sessions','0001_initial','2026-01-29 19:44:12.209685');
/*!40000 ALTER TABLE `django_migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `django_session`
--

DROP TABLE IF EXISTS `django_session`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `django_session` (
  `session_key` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_data` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expire_date` datetime(6) NOT NULL,
  PRIMARY KEY (`session_key`),
  KEY `django_session_expire_date_a5c62663` (`expire_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `django_session`
--

LOCK TABLES `django_session` WRITE;
/*!40000 ALTER TABLE `django_session` DISABLE KEYS */;
INSERT INTO `django_session` VALUES ('58t3lhu1nxejif91udtzzj6v96hi1tr7','.eJyNjrFqwzAURX8l3Nmq8-RnyfKWrXvXgnmKnhODYwdL7lL67yVpu3e6cLgHzif2rNswJfToJLkxEhlm5wwfKRgJrTNsfbA2iBvZonoKi9wUPU7L9U2WCyqM-zz_wgc5vL7vpKk5ECps6_zg46aKCvIhRbbhLuWKHnUuUqZzLTlryfV0k4vm-ufzt8N_wgbyLnjqms6_3J9JZU3rPOWCHq6NlmLDhigkw9G1JgaKRm1UtXw-Bs_4-gb6Ok6_:1vlsI7:ldO-Rh2r8jVIW14gfjR6xaf5fzlZlvjCOZwVSr_cXfk','2026-02-13 17:28:19.360046'),('bpa5xqhzfhwlxnfrptenmdw3d35jysp9','.eJyNjrFqwzAURX8l3Nmq8-RnyfKWrXvXgnmKnhODYwdL7lL67yVpu3e6cLgHzif2rNswJfToJLkxEhlm5wwfKRgJrTNsfbA2iBvZonoKi9wUPU7L9U2WCyqM-zz_wgc5vL7vpKk5ECps6_zg46aKCvIhRbbhLuWKHnUuUqZzLTlryfV0k4vm-ufzt8N_wgbyLnjqms6_3J9JZU3rPOWCHq6NlmLDhigkw9G1JgaKRm1UtXw-Bs_4-gb6Ok6_:1vlepX:xvuRAKta5BfXyJMtrDyZ6_2GddVFSbSt-53otTpK0Mo','2026-02-13 03:05:55.329236');
/*!40000 ALTER TABLE `django_session` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event` (
  `event_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `start_at` datetime(6) NOT NULL,
  `end_at` datetime(6) DEFAULT NULL,
  `is_all_day` tinyint NOT NULL DEFAULT '0',
  `location` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `event_type` enum('event','reminder','task') COLLATE utf8mb4_unicode_ci DEFAULT 'event',
  `repeat_pattern` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `repeat_until` datetime(6) DEFAULT NULL,
  `status` enum('scheduled','cancelled','completed') COLLATE utf8mb4_unicode_ci DEFAULT 'scheduled',
  `priority` enum('low','medium','high') COLLATE utf8mb4_unicode_ci DEFAULT 'medium',
  `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `updated_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  `is_deleted` tinyint NOT NULL DEFAULT '0',
  `calendar_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `task_id` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`event_id`),
  KEY `Event_calendar_id_459eba45_fk_Calendar_calendar_id` (`calendar_id`),
  KEY `idx_event_start` (`start_at`),
  KEY `idx_event_end` (`end_at`),
  KEY `idx_event_calendar_status` (`calendar_id`,`status`,`is_deleted`),
  KEY `idx_event_daterange` (`start_at`,`end_at`,`is_deleted`),
  KEY `idx_event_upcoming` (`start_at`,`is_deleted`,`status`),
  KEY `fk_event_task` (`task_id`),
  CONSTRAINT `Event_calendar_id_459eba45_fk_Calendar_calendar_id` FOREIGN KEY (`calendar_id`) REFERENCES `calendar` (`calendar_id`),
  CONSTRAINT `fk_event_task` FOREIGN KEY (`task_id`) REFERENCES `task` (`task_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event`
--

LOCK TABLES `event` WRITE;
/*!40000 ALTER TABLE `event` DISABLE KEYS */;
INSERT INTO `event` VALUES ('02873719-dc7e-4f77-8a02-7dfa797ec4b9','Multi-Day Conference','','2026-02-04 09:00:00.000000','2026-02-04 10:00:00.000000',0,'adf','event',NULL,NULL,'scheduled','medium','2026-01-30 17:03:36.836710','2026-01-30 17:03:36.836718',0,'6a84c989-f3b9-11f0-ba0d-74d4dd2c201f',NULL),('14176e9c-7d50-4be2-bbf1-8d5aa6d33912','Làm bài tập','','2026-01-30 09:00:00.000000','2026-01-30 10:00:00.000000',0,'','event',NULL,NULL,'completed','medium','2026-01-30 17:01:27.462125','2026-01-30 17:02:13.493236',0,'6a84c989-f3b9-11f0-ba0d-74d4dd2c201f',NULL),('20a8a29c-264b-4c51-8477-06f1c0d25fc3','English','','2026-01-30 09:00:00.000000','2026-01-30 10:00:00.000000',0,'','event',NULL,NULL,'scheduled','medium','2026-01-30 16:57:26.651521','2026-01-30 16:57:26.651522',1,'6a84c989-f3b9-11f0-ba0d-74d4dd2c201f',NULL),('34b8e5f1-0717-4310-b33e-1ba5eaed65a4','1Multi-Day Conference','','2026-02-02 09:00:00.000000','2026-02-02 10:00:00.000000',0,'','event',NULL,NULL,'scheduled','medium','2026-01-30 17:08:12.669349','2026-01-30 17:08:12.669352',0,'6a84c989-f3b9-11f0-ba0d-74d4dd2c201f',NULL),('41007e2a-19b0-440c-a939-0c7fcfeee06d','Làm bài tập1','','2026-01-30 09:00:00.000000','2026-01-30 10:00:00.000000',0,'','event',NULL,NULL,'scheduled','medium','2026-01-30 16:56:46.557499','2026-01-30 17:02:27.777359',0,'6a84c989-f3b9-11f0-ba0d-74d4dd2c201f',NULL),('4da58f71-1bdd-4c38-8da4-86614f3343e2','Weekend Vacation1','Family vacation trip','2026-01-23 00:00:00.000000','2026-01-26 23:59:59.000000',1,'','event',NULL,NULL,'scheduled','medium','2026-01-18 16:49:14.494554','2026-01-30 16:58:44.631572',0,'6a84c989-f3b9-11f0-ba0d-74d4dd2c201f',NULL),('4fa11758-15f7-4add-9edc-41df72d62f24','Làm bài tập','dgad','2026-01-30 09:00:00.000000','2026-01-30 10:00:00.000000',0,'adf','event',NULL,NULL,'scheduled','medium','2026-01-30 16:56:31.152312','2026-01-30 16:56:31.152315',0,'6a84c989-f3b9-11f0-ba0d-74d4dd2c201f',NULL),('51ec73a2-1ed4-47d8-8b96-69c81333a5c9','todo','','2026-01-19 09:00:00.000000','2026-01-19 10:00:00.000000',0,'','event',NULL,NULL,'scheduled','medium','2026-01-18 16:28:16.637916','2026-01-20 21:03:01.000000',1,'6a84c989-f3b9-11f0-ba0d-74d4dd2c201f',NULL),('758ddbcf-c019-4efd-be33-b84614fe0cf0','Làm bài tập','cô ngân','2026-01-19 11:00:00.000000','2026-01-19 10:00:00.000000',0,'','task',NULL,NULL,'scheduled','medium','2026-01-18 16:20:05.204157','2026-01-18 16:37:34.909293',0,'6a84c989-f3b9-11f0-ba0d-74d4dd2c201f',NULL),('a78a5807-c67e-4e8c-8b1d-be59f95b85e9','Multi-Day Conference','','2026-02-03 09:00:00.000000','2026-02-03 10:00:00.000000',0,'','event',NULL,NULL,'scheduled','medium','2026-01-30 17:28:06.148953','2026-01-30 17:28:06.148959',0,'6a84c989-f3b9-11f0-ba0d-74d4dd2c201f',NULL),('c733b96e-7f78-4a7d-95f3-b920fbe77d12','Làm bài tập1','','2026-01-31 09:00:00.000000','2026-01-31 10:00:00.000000',0,'','event',NULL,NULL,'completed','medium','2026-01-30 17:02:44.671058','2026-01-30 17:27:55.264614',0,'6a84c989-f3b9-11f0-ba0d-74d4dd2c201f',NULL),('c9064484-fc4e-11f0-aa8f-74d4dd2c201f','Study Group Meeting','Event description for optional event','2026-01-31 20:39:35.000000','2026-01-29 23:39:35.000000',0,NULL,'event',NULL,NULL,'completed','medium','2026-01-18 20:39:35.000000','2026-01-28 20:39:35.875472',0,'8ffb516f-fc4c-11f0-aa8f-74d4dd2c201f',NULL),('c90691e7-fc4e-11f0-aa8f-74d4dd2c201f','Math Exam','Event description for regular event','2026-02-10 20:39:35.000000','2026-01-09 23:39:35.000000',0,NULL,'event',NULL,NULL,'completed','medium','2026-01-13 20:39:35.000000','2026-01-28 20:39:35.875472',0,'8ffb50e7-fc4c-11f0-aa8f-74d4dd2c201f',NULL),('c9069627-fc4e-11f0-aa8f-74d4dd2c201f','Study Group Meeting','Event description for important event','2026-01-22 20:39:35.000000','2026-02-06 21:39:35.000000',0,NULL,'event',NULL,NULL,'completed','medium','2026-01-19 20:39:35.000000','2026-01-28 20:39:35.875472',0,'8ffb5158-fc4c-11f0-aa8f-74d4dd2c201f',NULL),('c9069916-fc4e-11f0-aa8f-74d4dd2c201f','Assignment Due','Event description for optional event','2026-02-26 20:39:35.000000','2026-02-22 22:39:35.000000',0,NULL,'event',NULL,NULL,'scheduled','medium','2026-01-14 20:39:35.000000','2026-01-28 20:39:35.875472',0,'1be324da-fc4e-11f0-aa8f-74d4dd2c201f',NULL),('c906a344-fc4e-11f0-aa8f-74d4dd2c201f','Assignment Due','Event description for optional event','2026-01-03 20:39:35.000000','2026-01-20 22:39:35.000000',1,NULL,'event',NULL,NULL,'completed','medium','2026-01-21 20:39:35.000000','2026-01-28 20:39:35.875472',0,'1be3259d-fc4e-11f0-aa8f-74d4dd2c201f',NULL),('c906a55e-fc4e-11f0-aa8f-74d4dd2c201f','Project Deadline','Event description for regular event','2026-02-07 20:39:35.000000','2026-02-13 23:39:35.000000',0,NULL,'event',NULL,NULL,'completed','medium','2026-01-23 20:39:35.000000','2026-01-28 20:39:35.875472',0,'1be325e1-fc4e-11f0-aa8f-74d4dd2c201f',NULL),('c906a69f-fc4e-11f0-aa8f-74d4dd2c201f','Online Class','Event description for optional event','2026-02-03 20:39:35.000000','2026-02-17 22:39:35.000000',0,NULL,'event',NULL,NULL,'completed','medium','2026-01-11 20:39:35.000000','2026-01-28 20:39:35.875472',0,'1be32617-fc4e-11f0-aa8f-74d4dd2c201f',NULL),('c906a7c4-fc4e-11f0-aa8f-74d4dd2c201f','Project Deadline','Event description for optional event','2025-12-30 20:39:35.000000','2026-02-16 21:39:35.000000',0,NULL,'event',NULL,NULL,'scheduled','medium','2026-01-20 20:39:35.000000','2026-01-28 20:39:35.875472',0,'8ffb5140-fc4c-11f0-aa8f-74d4dd2c201f',NULL),('c906a8cc-fc4e-11f0-aa8f-74d4dd2c201f','Project Deadline','Event description for regular event','2026-02-16 20:39:35.000000','2026-02-26 22:39:35.000000',0,NULL,'event',NULL,NULL,'completed','medium','2026-01-12 20:39:35.000000','2026-01-28 20:39:35.875472',0,'8ffb4957-fc4c-11f0-aa8f-74d4dd2c201f',NULL),('c906a9d7-fc4e-11f0-aa8f-74d4dd2c201f','Library Study','Event description for important event','2026-02-09 20:39:35.000000','2026-01-24 21:39:35.000000',1,NULL,'event',NULL,NULL,'scheduled','medium','2026-01-27 20:39:35.000000','2026-01-28 20:39:35.875472',0,'1be326b1-fc4e-11f0-aa8f-74d4dd2c201f',NULL),('cc6ac5c8-c180-4940-a4c3-3d8d22958f0a','1Multi-Day Conference','Annual tech conference','2026-01-21 09:00:00.000000','2026-01-22 17:00:00.000000',0,'','event',NULL,NULL,'scheduled','high','2026-01-18 16:49:14.499131','2026-01-20 21:02:48.146394',0,'6a84c989-f3b9-11f0-ba0d-74d4dd2c201f',NULL),('e31108ac-941c-4a26-a8af-827b701319d6','English','','2026-01-19 09:00:00.000000','2026-01-19 09:00:00.000000',0,'','event',NULL,NULL,'scheduled','medium','2026-01-18 16:27:14.598632','2026-01-18 16:27:28.059838',1,'6a84c989-f3b9-11f0-ba0d-74d4dd2c201f',NULL),('e5b4809a-f862-4567-bbdb-cc7e861c40c3','Làm bài tập','','2026-01-30 09:00:00.000000','2026-01-30 10:00:00.000000',0,'','event',NULL,NULL,'scheduled','medium','2026-01-30 16:58:11.089779','2026-01-30 16:58:11.089783',0,'6a84c989-f3b9-11f0-ba0d-74d4dd2c201f',NULL),('ea1965c1-929d-4552-b522-fda89ddd0f83','Làm bài tập','','2026-01-31 09:00:00.000000','2026-01-31 10:00:00.000000',0,'','event',NULL,NULL,'scheduled','medium','2026-01-30 17:01:46.310199','2026-01-30 17:01:46.310202',0,'6a84c989-f3b9-11f0-ba0d-74d4dd2c201f',NULL),('fa96371f-0dc8-4a61-95fc-b67cfbf0e3d4','Bao cao co khue','','2026-01-29 09:00:00.000000','2026-01-29 10:00:00.000000',0,'','event',NULL,NULL,'completed','medium','2026-01-29 08:53:58.188324','2026-01-29 08:54:22.000000',1,'6a84c989-f3b9-11f0-ba0d-74d4dd2c201f',NULL);
/*!40000 ALTER TABLE `event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event_reminder`
--

DROP TABLE IF EXISTS `event_reminder`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `event_reminder` (
  `reminder_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `event_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remind_at` datetime(6) NOT NULL,
  `remind_type` enum('email','notification','both') COLLATE utf8mb4_unicode_ci DEFAULT 'notification',
  `is_sent` tinyint NOT NULL DEFAULT '0',
  `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  PRIMARY KEY (`reminder_id`),
  KEY `fk_reminder_event` (`event_id`),
  KEY `idx_reminder_time` (`remind_at`,`is_sent`),
  CONSTRAINT `fk_reminder_event` FOREIGN KEY (`event_id`) REFERENCES `event` (`event_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event_reminder`
--

LOCK TABLES `event_reminder` WRITE;
/*!40000 ALTER TABLE `event_reminder` DISABLE KEYS */;
/*!40000 ALTER TABLE `event_reminder` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flashcard`
--

DROP TABLE IF EXISTS `flashcard`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flashcard` (
  `flashcard_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `user_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`flashcard_id`),
  KEY `Flashcard_user_id_e324c8d7_fk_User_user_id` (`user_id`),
  CONSTRAINT `Flashcard_user_id_e324c8d7_fk_User_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flashcard`
--

LOCK TABLES `flashcard` WRITE;
/*!40000 ALTER TABLE `flashcard` DISABLE KEYS */;
INSERT INTO `flashcard` VALUES ('849e2323-a2f7-43dd-b8df-5ebe6972897a','2026-01-26 22:32:06.778063','8ad6fb11-4466-4019-a956-4279229a6f42'),('9009765f-54ca-49ce-b559-670403c96301','2026-01-28 08:43:05.000000','303e179c-0ff3-4dd9-8c9b-d39ebeea66a3'),('b8a13646-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-16 20:39:08.000000','3b85fa55-9fd9-4819-9ee5-a235ed0169c9'),('b8a1369f-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-13 20:39:08.000000','2ad82476-0064-4633-8e0e-7b4bd88fe052'),('b8a136b6-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-16 20:39:08.000000','3b75c652-2466-48a0-9bd0-5cdeec3c93fe'),('b8a136c8-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-14 20:39:08.000000','5955a743-4ec7-4bc3-b74d-51fdbb29791f'),('b8a136da-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-25 20:39:08.000000','665c60a5-ac6b-49fa-82c1-34e97bc3b361');
/*!40000 ALTER TABLE `flashcard` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flashcarditem`
--

DROP TABLE IF EXISTS `flashcarditem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flashcarditem` (
  `card_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `question` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `answer` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `learned` tinyint NOT NULL DEFAULT '0',
  `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `updated_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  `is_deleted` tinyint NOT NULL DEFAULT '0',
  `set_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`card_id`),
  KEY `FlashcardItem_set_id_d04c527a_fk_FlashcardSet_set_id` (`set_id`),
  CONSTRAINT `FlashcardItem_set_id_d04c527a_fk_FlashcardSet_set_id` FOREIGN KEY (`set_id`) REFERENCES `flashcardset` (`set_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flashcarditem`
--

LOCK TABLES `flashcarditem` WRITE;
/*!40000 ALTER TABLE `flashcarditem` DISABLE KEYS */;
INSERT INTO `flashcarditem` VALUES ('096e02c6-118c-4906-9584-12bcedd4d368','Hello','xin chaof',0,'2026-01-29 02:55:37.000000','2026-01-29 08:55:37.985786',0,'86d343a9-0808-4889-b4f2-39c45698da86'),('288bdc85-d765-49e1-a780-ae5c039a2d06','xin chào','hi',0,'2026-01-26 18:51:43.000000','2026-01-27 01:25:09.943524',1,'7825e524-6edb-4820-b169-00fbf0c6d801'),('3120cb4e-1a6f-42fe-8feb-69b8df6c3012','ád','ầ',1,'2026-01-29 20:11:02.780623','2026-01-30 02:38:37.919244',0,'7825e524-6edb-4820-b169-00fbf0c6d801'),('3277b0ca-ed48-4b26-8f48-44cf5ca9b934','miss','nhớ nhung',0,'2026-01-26 18:52:11.000000','2026-01-27 10:05:44.299786',1,'7825e524-6edb-4820-b169-00fbf0c6d801'),('36d3c230-a9a7-4825-9be1-c6be84b554b8','d','e',1,'2026-01-27 04:06:00.000000','2026-01-30 02:38:39.029348',0,'7825e524-6edb-4820-b169-00fbf0c6d801'),('44dcb3e5-1099-4ac0-8539-9c8b7b3e20a1','hello','xin chào',0,'2026-01-26 18:51:35.000000','2026-01-27 10:05:46.354127',1,'7825e524-6edb-4820-b169-00fbf0c6d801'),('6987df07-49e6-4ba3-95bb-5f4c4d2c94cd','project','du an',0,'2026-01-29 02:55:53.000000','2026-01-29 08:55:53.441722',0,'86d343a9-0808-4889-b4f2-39c45698da86'),('84b43018-98b2-4436-bae8-71c93e7a816d','b','c',1,'2026-01-27 04:05:55.000000','2026-01-30 02:38:39.439291',0,'7825e524-6edb-4820-b169-00fbf0c6d801'),('acae7e37-a580-4512-80ea-7b100763d977','a','b',1,'2026-01-27 04:05:51.000000','2026-01-30 02:38:39.586694',0,'7825e524-6edb-4820-b169-00fbf0c6d801'),('c226e382-fc4e-11f0-aa8f-74d4dd2c201f','Define API','Function calling itself',1,'2026-01-17 20:39:24.000000','2026-01-28 20:39:24.345826',0,'c221dc44-fc4e-11f0-aa8f-74d4dd2c201f'),('c2270e69-fc4e-11f0-aa8f-74d4dd2c201f','Define variable in programming','Object Oriented Programming',1,'2026-01-25 20:39:24.000000','2026-01-28 20:39:24.345826',0,'c22208bd-fc4e-11f0-aa8f-74d4dd2c201f'),('c2270ff7-fc4e-11f0-aa8f-74d4dd2c201f','What is a loop?','Relational database system',1,'2026-01-27 20:39:24.000000','2026-01-28 20:39:24.345826',0,'c22209e4-fc4e-11f0-aa8f-74d4dd2c201f'),('c22710b3-fc4e-11f0-aa8f-74d4dd2c201f','What is a loop?','Function calling itself',1,'2026-01-22 20:39:24.000000','2026-01-28 20:39:24.345826',0,'c2220a79-fc4e-11f0-aa8f-74d4dd2c201f'),('c2271798-fc4e-11f0-aa8f-74d4dd2c201f','Explain recursion','Container for storing data',0,'2026-01-21 20:39:24.000000','2026-01-28 20:39:24.345826',0,'c2220b01-fc4e-11f0-aa8f-74d4dd2c201f'),('cd78ef53-5e64-4c95-a7a9-1d2fde502b70','Hello','Chào',0,'2026-01-26 18:50:44.000000','2026-01-27 00:51:05.173638',1,'7825e524-6edb-4820-b169-00fbf0c6d801'),('d751914b-be74-4cd1-98df-3afdc8ad1fb1','Hello','Xinchào',0,'2026-01-27 14:59:38.000000','2026-01-30 02:50:40.763978',0,'7825e524-6edb-4820-b169-00fbf0c6d801'),('fdb83972-e142-4e04-aada-e9f02ec581dd','chào','chào cậu',0,'2026-01-26 19:25:30.000000','2026-01-27 10:05:41.921905',1,'7825e524-6edb-4820-b169-00fbf0c6d801');
/*!40000 ALTER TABLE `flashcarditem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flashcardprogress`
--

DROP TABLE IF EXISTS `flashcardprogress`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flashcardprogress` (
  `progress_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'new',
  `last_reviewed` datetime(6) DEFAULT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `card_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`progress_id`),
  UNIQUE KEY `FlashcardProgress_card_id_user_id_29cb67f3_uniq` (`card_id`,`user_id`),
  KEY `FlashcardProgress_user_id_2056d839_fk_User_user_id` (`user_id`),
  CONSTRAINT `FlashcardProgress_card_id_8ee20e2a_fk_FlashcardItem_card_id` FOREIGN KEY (`card_id`) REFERENCES `flashcarditem` (`card_id`),
  CONSTRAINT `FlashcardProgress_user_id_2056d839_fk_User_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flashcardprogress`
--

LOCK TABLES `flashcardprogress` WRITE;
/*!40000 ALTER TABLE `flashcardprogress` DISABLE KEYS */;
INSERT INTO `flashcardprogress` VALUES ('09382d5c-9ee1-476d-8d23-ef51ee2e785f','learned','2026-01-30 02:38:37.914931','2026-01-29 20:25:08.354834','3120cb4e-1a6f-42fe-8feb-69b8df6c3012','8ad6fb11-4466-4019-a956-4279229a6f42'),('44ee573a-6458-4f9c-abe8-0aa9f7ac04eb','again','2026-01-27 00:54:36.000000','2026-01-27 00:53:58.979468','288bdc85-d765-49e1-a780-ae5c039a2d06','8ad6fb11-4466-4019-a956-4279229a6f42'),('48548aef-5037-425d-a77d-53ce93a7c3d8','learned','2026-01-27 01:41:17.000000','2026-01-27 00:53:59.859974','44dcb3e5-1099-4ac0-8539-9c8b7b3e20a1','8ad6fb11-4466-4019-a956-4279229a6f42'),('870cabaa-b682-41ce-8c26-feaf22a2af81','learned','2026-01-27 01:41:16.000000','2026-01-27 00:53:57.952082','3277b0ca-ed48-4b26-8f48-44cf5ca9b934','8ad6fb11-4466-4019-a956-4279229a6f42'),('8846acba-0b86-4a5a-859d-c207fa6acb19','learned','2026-01-30 02:38:39.584535','2026-01-27 10:06:11.185084','acae7e37-a580-4512-80ea-7b100763d977','8ad6fb11-4466-4019-a956-4279229a6f42'),('925d472b-e853-4537-b8ed-d28546bf85f3','learned','2026-01-30 02:38:39.437078','2026-01-27 10:06:18.338468','84b43018-98b2-4436-bae8-71c93e7a816d','8ad6fb11-4466-4019-a956-4279229a6f42'),('96345a3d-6938-4972-8ab8-897ad3130daf','new','2026-01-30 02:50:40.761580','2026-01-29 20:25:33.177444','d751914b-be74-4cd1-98df-3afdc8ad1fb1','8ad6fb11-4466-4019-a956-4279229a6f42'),('a9490269-7655-409e-a003-e7f8118ca0d0','learned','2026-01-29 02:56:11.000000','2026-01-29 08:56:11.563078','096e02c6-118c-4906-9584-12bcedd4d368','8ad6fb11-4466-4019-a956-4279229a6f42'),('c8b9c960-e2da-44f1-9a8e-2a7e1f9e24ca','new','2026-01-29 02:56:14.000000','2026-01-29 08:56:14.849384','6987df07-49e6-4ba3-95bb-5f4c4d2c94cd','8ad6fb11-4466-4019-a956-4279229a6f42'),('c900c22a-fc4e-11f0-aa8f-74d4dd2c201f','learning','2026-01-26 20:39:35.000000','2026-01-20 20:39:35.000000','c226e382-fc4e-11f0-aa8f-74d4dd2c201f','2ad82476-0064-4633-8e0e-7b4bd88fe052'),('c900ea59-fc4e-11f0-aa8f-74d4dd2c201f','mastered','2026-01-20 20:39:35.000000','2026-01-19 20:39:35.000000','c2270e69-fc4e-11f0-aa8f-74d4dd2c201f','3b75c652-2466-48a0-9bd0-5cdeec3c93fe'),('c900ee68-fc4e-11f0-aa8f-74d4dd2c201f','reviewing','2026-01-24 20:39:35.000000','2026-01-25 20:39:35.000000','c2270ff7-fc4e-11f0-aa8f-74d4dd2c201f','3b85fa55-9fd9-4819-9ee5-a235ed0169c9'),('c900fa4f-fc4e-11f0-aa8f-74d4dd2c201f','mastered','2026-01-22 20:39:35.000000','2026-01-21 20:39:35.000000','c22710b3-fc4e-11f0-aa8f-74d4dd2c201f','5955a743-4ec7-4bc3-b74d-51fdbb29791f'),('c900fbd4-fc4e-11f0-aa8f-74d4dd2c201f','reviewing','2026-01-21 20:39:35.000000','2026-01-15 20:39:35.000000','c2271798-fc4e-11f0-aa8f-74d4dd2c201f','665c60a5-ac6b-49fa-82c1-34e97bc3b361'),('f161f369-745d-4477-bde1-403948279e4f','new','2026-01-27 01:41:10.000000','2026-01-27 01:25:45.770616','fdb83972-e142-4e04-aada-e9f02ec581dd','8ad6fb11-4466-4019-a956-4279229a6f42'),('f4883e0a-fd87-47a8-93df-bcef26061aa2','learned','2026-01-30 02:38:39.027114','2026-01-27 10:06:19.777244','36d3c230-a9a7-4825-9be1-c6be84b554b8','8ad6fb11-4466-4019-a956-4279229a6f42');
/*!40000 ALTER TABLE `flashcardprogress` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `flashcardset`
--

DROP TABLE IF EXISTS `flashcardset`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `flashcardset` (
  `set_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `is_deleted` tinyint NOT NULL DEFAULT '0',
  `flashcard_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`set_id`),
  KEY `FlashcardSet_flashcard_id_04ad91ab_fk_Flashcard_flashcard_id` (`flashcard_id`),
  CONSTRAINT `FlashcardSet_flashcard_id_04ad91ab_fk_Flashcard_flashcard_id` FOREIGN KEY (`flashcard_id`) REFERENCES `flashcard` (`flashcard_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `flashcardset`
--

LOCK TABLES `flashcardset` WRITE;
/*!40000 ALTER TABLE `flashcardset` DISABLE KEYS */;
INSERT INTO `flashcardset` VALUES ('4835b4c6-a287-46ea-a34a-ce59bb3abc0b','English','2026-01-26 23:34:27.581299',1,'849e2323-a2f7-43dd-b8df-5ebe6972897a'),('65d649dd-3c3a-45bd-8e4f-66c383e6864c','Ád','2026-01-29 20:11:36.616937',0,'849e2323-a2f7-43dd-b8df-5ebe6972897a'),('7825e524-6edb-4820-b169-00fbf0c6d801','Bài tập cô ngân','2026-01-26 18:38:25.000000',0,'849e2323-a2f7-43dd-b8df-5ebe6972897a'),('86d343a9-0808-4889-b4f2-39c45698da86','Tiếng Anh IT','2026-01-27 15:36:21.000000',0,'849e2323-a2f7-43dd-b8df-5ebe6972897a'),('c221dc44-fc4e-11f0-aa8f-74d4dd2c201f','Math Formulas','2026-01-27 20:39:24.000000',0,'b8a1369f-fc4e-11f0-aa8f-74d4dd2c201f'),('c22208bd-fc4e-11f0-aa8f-74d4dd2c201f','Math Formulas','2026-01-20 20:39:24.000000',0,'b8a136b6-fc4e-11f0-aa8f-74d4dd2c201f'),('c22209e4-fc4e-11f0-aa8f-74d4dd2c201f','Programming Terms','2026-01-17 20:39:24.000000',0,'b8a13646-fc4e-11f0-aa8f-74d4dd2c201f'),('c2220a79-fc4e-11f0-aa8f-74d4dd2c201f','Math Formulas','2026-01-12 20:39:24.000000',0,'b8a136c8-fc4e-11f0-aa8f-74d4dd2c201f'),('c2220b01-fc4e-11f0-aa8f-74d4dd2c201f','Math Formulas','2026-01-14 20:39:24.000000',0,'b8a136da-fc4e-11f0-aa8f-74d4dd2c201f');
/*!40000 ALTER TABLE `flashcardset` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `habit`
--

DROP TABLE IF EXISTS `habit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `habit` (
  `habit_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `user_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`habit_id`),
  KEY `Habit_user_id_a8bc7125_fk_User_user_id` (`user_id`),
  CONSTRAINT `Habit_user_id_a8bc7125_fk_User_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `habit`
--

LOCK TABLES `habit` WRITE;
/*!40000 ALTER TABLE `habit` DISABLE KEYS */;
INSERT INTO `habit` VALUES ('548d58db-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-27 20:36:20.000000','3b85fa55-9fd9-4819-9ee5-a235ed0169c9'),('548d5963-fc4e-11f0-aa8f-74d4dd2c201f','2025-12-30 20:36:20.000000','2ad82476-0064-4633-8e0e-7b4bd88fe052'),('548d597c-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-07 20:36:20.000000','3b75c652-2466-48a0-9bd0-5cdeec3c93fe'),('548d598b-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-06 20:36:20.000000','5955a743-4ec7-4bc3-b74d-51fdbb29791f'),('548d599c-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-13 20:36:20.000000','665c60a5-ac6b-49fa-82c1-34e97bc3b361'),('548d59ac-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-20 20:36:20.000000','6436aa66-b2d3-4847-a8dd-c625dcbaff33'),('548d59bc-fc4e-11f0-aa8f-74d4dd2c201f','2025-12-30 20:36:20.000000','ec8c547c-76d8-4c11-addd-a7ce2c669f16'),('548d59cb-fc4e-11f0-aa8f-74d4dd2c201f','2025-12-31 20:36:20.000000','33b05e16-6843-42e9-8daa-8d4173f24ac3'),('548d59d9-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-02 20:36:20.000000','1bd52590-db76-42c6-9aac-737c161c0f60'),('548d59e9-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-10 20:36:20.000000','90007af7-f330-4cc2-82d0-3282783ecc4b'),('688f65f1-e8cb-4542-a62a-dfedbeb39bc2','2026-01-30 00:24:24.000000','8ad6fb11-4466-4019-a956-4279229a6f42');
/*!40000 ALTER TABLE `habit` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `habit_list`
--

DROP TABLE IF EXISTS `habit_list`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `habit_list` (
  `habit_list_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `habit_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`habit_list_id`),
  UNIQUE KEY `unique_habit_date` (`habit_id`,`date`),
  CONSTRAINT `habit_list_ibfk_1` FOREIGN KEY (`habit_id`) REFERENCES `habit` (`habit_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `habit_list`
--

LOCK TABLES `habit_list` WRITE;
/*!40000 ALTER TABLE `habit_list` DISABLE KEYS */;
/*!40000 ALTER TABLE `habit_list` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `habitlist`
--

DROP TABLE IF EXISTS `habitlist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `habitlist` (
  `habitlist_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '#4a6cf7',
  `daily_target` int unsigned NOT NULL DEFAULT '1',
  `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `is_deleted` tinyint NOT NULL DEFAULT '0',
  `habit_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`habitlist_id`),
  KEY `HabitList_habit_id_4217570c_fk_Habit_habit_id` (`habit_id`),
  CONSTRAINT `HabitList_habit_id_4217570c_fk_Habit_habit_id` FOREIGN KEY (`habit_id`) REFERENCES `habit` (`habit_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `habitlist`
--

LOCK TABLES `habitlist` WRITE;
/*!40000 ALTER TABLE `habitlist` DISABLE KEYS */;
INSERT INTO `habitlist` VALUES ('08f8a7f6-4909-4bf0-af72-7fa757be4276','Tập Thể Dục 2','#10B981',1,'2026-01-30 01:56:07.000000',0,'688f65f1-e8cb-4542-a62a-dfedbeb39bc2'),('376ee629-286e-4232-968a-ad60e241cc6e','AD','#4a6cf7',1,'2026-01-29 20:10:52.123891',1,'688f65f1-e8cb-4542-a62a-dfedbeb39bc2'),('5812ca0a-a6f8-4d8f-a8ce-eb9bb8e6dfc4','Học IT','#4a6cf7',1,'2026-01-30 01:16:27.000000',0,'688f65f1-e8cb-4542-a62a-dfedbeb39bc2'),('59ea57cb-fc4e-11f0-aa8f-74d4dd2c201f','Reading','#4a6cf7',3,'2026-01-14 20:36:29.000000',0,'548d59d9-fc4e-11f0-aa8f-74d4dd2c201f'),('59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f','Water Intake','#4a6cf7',1,'2026-01-23 20:36:29.000000',0,'548d5963-fc4e-11f0-aa8f-74d4dd2c201f'),('59eab075-fc4e-11f0-aa8f-74d4dd2c201f','Water Intake','#4a6cf7',1,'2026-01-05 20:36:29.000000',0,'548d59cb-fc4e-11f0-aa8f-74d4dd2c201f'),('59eab110-fc4e-11f0-aa8f-74d4dd2c201f','Coding Practice','#4a6cf7',2,'2026-01-13 20:36:29.000000',0,'548d597c-fc4e-11f0-aa8f-74d4dd2c201f'),('59eab196-fc4e-11f0-aa8f-74d4dd2c201f','Exercise','#4a6cf7',1,'2026-01-07 20:36:29.000000',0,'548d58db-fc4e-11f0-aa8f-74d4dd2c201f'),('59eab21f-fc4e-11f0-aa8f-74d4dd2c201f','Daily Study','#4a6cf7',2,'2026-01-16 20:36:29.000000',0,'548d598b-fc4e-11f0-aa8f-74d4dd2c201f'),('59eab2a4-fc4e-11f0-aa8f-74d4dd2c201f','Daily Study','#4a6cf7',2,'2026-01-12 20:36:29.000000',0,'548d59ac-fc4e-11f0-aa8f-74d4dd2c201f'),('59eab328-fc4e-11f0-aa8f-74d4dd2c201f','Water Intake','#4a6cf7',3,'2026-01-15 20:36:29.000000',0,'548d599c-fc4e-11f0-aa8f-74d4dd2c201f'),('59eab3ad-fc4e-11f0-aa8f-74d4dd2c201f','Meditation','#4a6cf7',2,'2026-01-21 20:36:29.000000',0,'548d59e9-fc4e-11f0-aa8f-74d4dd2c201f'),('59eab42b-fc4e-11f0-aa8f-74d4dd2c201f','Daily Study','#4a6cf7',1,'2026-01-21 20:36:29.000000',0,'548d59bc-fc4e-11f0-aa8f-74d4dd2c201f'),('8f2bf2ed-5265-49af-a3d1-a74a2a09eac3','Học tiếng Anh','#4a6cf7',1,'2026-01-30 00:24:24.000000',0,'688f65f1-e8cb-4542-a62a-dfedbeb39bc2'),('cb66e410-b791-40ce-a316-6dc7fd3793d2','àd','#EF4444',1,'2026-01-30 16:27:25.519277',0,'688f65f1-e8cb-4542-a62a-dfedbeb39bc2');
/*!40000 ALTER TABLE `habitlist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `habitlistlog`
--

DROP TABLE IF EXISTS `habitlistlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `habitlistlog` (
  `log_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` date NOT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `is_deleted` tinyint NOT NULL DEFAULT '0',
  `habitlist_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`log_id`),
  UNIQUE KEY `HabitListLog_habitlist_id_date_c2e67c02_uniq` (`habitlist_id`,`date`),
  KEY `idx_habitlog_date` (`date`),
  CONSTRAINT `HabitListLog_habitlist_id_c7b7b46b_fk_HabitList_habitlist_id` FOREIGN KEY (`habitlist_id`) REFERENCES `habitlist` (`habitlist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `habitlistlog`
--

LOCK TABLES `habitlistlog` WRITE;
/*!40000 ALTER TABLE `habitlistlog` DISABLE KEYS */;
INSERT INTO `habitlistlog` VALUES ('4a1783ab-a546-4e5b-b158-f0456431ba3d','2026-01-30','completed',NULL,'2026-01-29 20:10:28.375071',1,'08f8a7f6-4909-4bf0-af72-7fa757be4276'),('606f2c35-e75b-4a25-8991-c57edac21b3f','2026-01-29','completed',NULL,'2026-01-30 01:56:09.000000',0,'08f8a7f6-4909-4bf0-af72-7fa757be4276'),('6b4bf043-8876-475f-bf3c-27a644a20612','2026-01-29','completed',NULL,'2026-01-30 16:27:28.768018',1,'cb66e410-b791-40ce-a316-6dc7fd3793d2'),('8ee8d19e-6e14-4d94-a0fe-09ef8953934a','2026-01-28','completed',NULL,'2026-01-30 01:56:10.000000',0,'08f8a7f6-4909-4bf0-af72-7fa757be4276'),('ad71a490-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28','missed',NULL,'2026-01-28 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71d722-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28','completed',NULL,'2026-01-28 00:00:00.000000',0,'59eab196-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71d8d2-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-27','completed',NULL,'2026-01-27 00:00:00.000000',0,'59eab110-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71d96d-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-27','completed',NULL,'2026-01-27 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71da06-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-26','completed',NULL,'2026-01-26 00:00:00.000000',0,'59eab110-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71e03f-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-26','missed',NULL,'2026-01-26 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71e0d3-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-26','completed',NULL,'2026-01-26 00:00:00.000000',0,'59eab196-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71e168-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-25','completed',NULL,'2026-01-25 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71e1f1-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-24','completed',NULL,'2026-01-24 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71e27e-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-23','completed',NULL,'2026-01-23 00:00:00.000000',0,'59eab110-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71e2fe-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-23','completed',NULL,'2026-01-23 00:00:00.000000',0,'59eab196-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71e38b-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-22','completed',NULL,'2026-01-22 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71e418-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-21','missed',NULL,'2026-01-21 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71e4a1-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-20','completed',NULL,'2026-01-20 00:00:00.000000',0,'59eab110-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71e51d-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-20','missed',NULL,'2026-01-20 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71e598-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-20','pending',NULL,'2026-01-20 00:00:00.000000',0,'59eab196-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71e61e-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-19','missed',NULL,'2026-01-19 00:00:00.000000',0,'59eab110-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71e69c-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-19','missed',NULL,'2026-01-19 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71e717-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-19','completed',NULL,'2026-01-19 00:00:00.000000',0,'59eab196-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71e7a2-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-18','completed',NULL,'2026-01-18 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71e823-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-18','missed',NULL,'2026-01-18 00:00:00.000000',0,'59eab196-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71e8a3-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-17','completed',NULL,'2026-01-17 00:00:00.000000',0,'59eab110-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71e923-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-17','completed',NULL,'2026-01-17 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71e9cc-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-17','missed',NULL,'2026-01-17 00:00:00.000000',0,'59eab196-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71ea50-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-16','completed',NULL,'2026-01-16 00:00:00.000000',0,'59eab110-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71eacc-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-16','completed',NULL,'2026-01-16 00:00:00.000000',0,'59eab196-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71eb50-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-14','completed',NULL,'2026-01-14 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71ebdf-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-14','completed',NULL,'2026-01-14 00:00:00.000000',0,'59eab196-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71ec68-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-13','completed',NULL,'2026-01-13 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71ecf2-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-11','completed',NULL,'2026-01-11 00:00:00.000000',0,'59eab110-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71ed6c-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-11','completed',NULL,'2026-01-11 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71edea-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-11','missed',NULL,'2026-01-11 00:00:00.000000',0,'59eab196-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71ee71-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-10','missed',NULL,'2026-01-10 00:00:00.000000',0,'59eab110-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71eefa-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-09','completed',NULL,'2026-01-09 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71ef7f-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-08','missed',NULL,'2026-01-08 00:00:00.000000',0,'59eab110-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71effe-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-08','completed',NULL,'2026-01-08 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71f096-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-07','completed',NULL,'2026-01-07 00:00:00.000000',0,'59eab110-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71f117-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-07','completed',NULL,'2026-01-07 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71f198-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-07','pending',NULL,'2026-01-07 00:00:00.000000',0,'59eab196-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71f22e-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-06','completed',NULL,'2026-01-06 00:00:00.000000',0,'59eab110-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71f2ad-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-06','completed',NULL,'2026-01-06 00:00:00.000000',0,'59eab196-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71f332-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-05','completed',NULL,'2026-01-05 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71f3b2-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-05','completed',NULL,'2026-01-05 00:00:00.000000',0,'59eab196-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71f451-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-04','completed',NULL,'2026-01-04 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71f4d8-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-03','completed',NULL,'2026-01-03 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71f564-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-02','missed',NULL,'2026-01-02 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71f5e1-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-02','missed',NULL,'2026-01-02 00:00:00.000000',0,'59eab196-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71f673-fc4e-11f0-aa8f-74d4dd2c201f','2025-12-31','completed',NULL,'2025-12-31 00:00:00.000000',0,'59eaaf1e-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71f6f3-fc4e-11f0-aa8f-74d4dd2c201f','2025-12-31','completed',NULL,'2025-12-31 00:00:00.000000',0,'59eab196-fc4e-11f0-aa8f-74d4dd2c201f'),('ad71f774-fc4e-11f0-aa8f-74d4dd2c201f','2025-12-30','completed',NULL,'2025-12-30 00:00:00.000000',0,'59eab110-fc4e-11f0-aa8f-74d4dd2c201f'),('b70a8f44-1a17-4427-ac6a-5d9c8d92d05e','2026-01-30','completed',NULL,'2026-01-30 16:27:27.782493',0,'cb66e410-b791-40ce-a316-6dc7fd3793d2'),('b7756670-f5d1-4e51-ae9e-84891d574b09','2026-01-30','completed',NULL,'2026-01-30 01:55:49.000000',0,'5812ca0a-a6f8-4d8f-a8ce-eb9bb8e6dfc4'),('da551132-2db1-43a5-93fd-a6233df1244e','2026-01-29','completed',NULL,'2026-01-30 01:16:31.000000',0,'5812ca0a-a6f8-4d8f-a8ce-eb9bb8e6dfc4'),('e4929bfe-e9d8-4562-83ba-881f2fd4499e','2026-01-28','completed',NULL,'2026-01-30 17:28:22.200742',0,'cb66e410-b791-40ce-a316-6dc7fd3793d2'),('e94a18a6-69c2-4465-99df-84482efa8bec','2026-01-30','completed',NULL,'2026-01-30 01:14:34.000000',0,'8f2bf2ed-5265-49af-a3d1-a74a2a09eac3'),('f20b55a5-f8a4-48a2-820d-1c6c3e2e00fd','2026-01-29','completed',NULL,'2026-01-30 01:16:43.000000',0,'8f2bf2ed-5265-49af-a3d1-a74a2a09eac3');
/*!40000 ALTER TABLE `habitlistlog` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pomodoro`
--

DROP TABLE IF EXISTS `pomodoro`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pomodoro` (
  `pomodoro_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Pomodoro Session',
  `status` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'stopped',
  `work_duration` int NOT NULL DEFAULT '25',
  `break_duration` int NOT NULL DEFAULT '5',
  `current_session` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'work',
  `sessions_completed` int NOT NULL DEFAULT '0',
  `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `updated_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  `user_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`pomodoro_id`),
  KEY `Pomodoro_user_id_a96b0dd2_fk_User_user_id` (`user_id`),
  KEY `idx_pomodoro_created` (`created_at`),
  CONSTRAINT `Pomodoro_user_id_a96b0dd2_fk_User_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pomodoro`
--

LOCK TABLES `pomodoro` WRITE;
/*!40000 ALTER TABLE `pomodoro` DISABLE KEYS */;
INSERT INTO `pomodoro` VALUES ('144c2c38-6592-4f15-a1cf-3326514580d1','Pomodoro Session','stopped',25,5,'work',0,'2026-01-28 14:17:25.668280','2026-01-28 14:17:25.668280','303e179c-0ff3-4dd9-8c9b-d39ebeea66a3'),('21ca9879-fc4e-11f0-aa8f-74d4dd2c201f','Study Session - sv1','stopped',29,11,'work',0,'2025-12-14 20:34:55.000000','2026-01-28 20:34:55.305050','3b85fa55-9fd9-4819-9ee5-a235ed0169c9'),('21ca9978-fc4e-11f0-aa8f-74d4dd2c201f','Study Session - sv10','stopped',34,9,'work',0,'2025-12-22 20:34:55.000000','2026-01-28 20:34:55.305050','2ad82476-0064-4633-8e0e-7b4bd88fe052'),('21ca99b7-fc4e-11f0-aa8f-74d4dd2c201f','Study Session - sv2','stopped',31,6,'work',0,'2025-11-30 20:34:55.000000','2026-01-28 20:34:55.305050','3b75c652-2466-48a0-9bd0-5cdeec3c93fe'),('21ca9a2e-fc4e-11f0-aa8f-74d4dd2c201f','Study Session - sv3','stopped',29,8,'work',0,'2025-12-31 20:34:55.000000','2026-01-28 20:34:55.305050','5955a743-4ec7-4bc3-b74d-51fdbb29791f'),('21ca9a5d-fc4e-11f0-aa8f-74d4dd2c201f','Study Session - sv4','stopped',27,12,'work',0,'2026-01-15 20:34:55.000000','2026-01-28 20:34:55.305050','665c60a5-ac6b-49fa-82c1-34e97bc3b361'),('21ca9a88-fc4e-11f0-aa8f-74d4dd2c201f','Study Session - sv5','stopped',32,6,'work',0,'2026-01-07 20:34:55.000000','2026-01-28 20:34:55.305050','6436aa66-b2d3-4847-a8dd-c625dcbaff33'),('21ca9ab0-fc4e-11f0-aa8f-74d4dd2c201f','Study Session - sv6','stopped',28,13,'work',0,'2026-01-19 20:34:55.000000','2026-01-28 20:34:55.305050','ec8c547c-76d8-4c11-addd-a7ce2c669f16'),('21ca9adb-fc4e-11f0-aa8f-74d4dd2c201f','Study Session - sv7','stopped',26,9,'work',0,'2025-12-28 20:34:55.000000','2026-01-28 20:34:55.305050','33b05e16-6843-42e9-8daa-8d4173f24ac3'),('21ca9b04-fc4e-11f0-aa8f-74d4dd2c201f','Study Session - sv8','stopped',29,9,'work',0,'2025-12-03 20:34:55.000000','2026-01-28 20:34:55.305050','1bd52590-db76-42c6-9aac-737c161c0f60'),('21ca9b2d-fc4e-11f0-aa8f-74d4dd2c201f','Study Session - sv9','stopped',28,6,'work',0,'2025-12-24 20:34:55.000000','2026-01-28 20:34:55.305050','90007af7-f330-4cc2-82d0-3282783ecc4b'),('65821988-291b-4c50-8ded-6f5a06cce997','Pomodoro Session','stopped',25,5,'work',3,'2026-01-29 08:44:37.911868','2026-01-29 08:48:20.000000','cff04a27-fc4e-11f0-aa8f-74d4dd2c201f'),('81bf7383-c0b8-424c-b786-d6c4efa6a2b1','Pomodoro Session','stopped',25,5,'work',9,'2026-01-22 23:20:29.960597','2026-01-30 17:28:11.308981','8ad6fb11-4466-4019-a956-4279229a6f42'),('98354433-0eef-4bfe-850d-c9ab780057ad','Default','stopped',25,5,'work',0,'2026-01-29 20:41:27.840474','2026-01-29 20:41:27.840483','d00a31f3-0661-4e31-b6ba-82a802e6c1e3');
/*!40000 ALTER TABLE `pomodoro` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `pomodorohistory`
--

DROP TABLE IF EXISTS `pomodorohistory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `pomodorohistory` (
  `history_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `start_time` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `end_time` datetime(6) DEFAULT NULL,
  `duration_minutes` int unsigned NOT NULL DEFAULT '0',
  `study_topic` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(12) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'in_progress',
  `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `is_deleted` tinyint NOT NULL DEFAULT '0',
  `pomodoro_id` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `task_id` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`history_id`),
  KEY `PomodoroHistory_pomodoro_id_bf491866_fk_Pomodoro_pomodoro_id` (`pomodoro_id`),
  KEY `fk_pomodoro_history_task` (`task_id`),
  CONSTRAINT `fk_pomodoro_history_task` FOREIGN KEY (`task_id`) REFERENCES `task` (`task_id`) ON DELETE SET NULL,
  CONSTRAINT `PomodoroHistory_pomodoro_id_bf491866_fk_Pomodoro_pomodoro_id` FOREIGN KEY (`pomodoro_id`) REFERENCES `pomodoro` (`pomodoro_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `pomodorohistory`
--

LOCK TABLES `pomodorohistory` WRITE;
/*!40000 ALTER TABLE `pomodorohistory` DISABLE KEYS */;
INSERT INTO `pomodorohistory` VALUES ('1a09572a-32af-45ef-b405-68e50bff0085','2026-01-24 17:05:30.000000','2026-01-24 17:46:16.000000',40,NULL,'completed','2026-01-24 23:05:30.133605',0,'81bf7383-c0b8-424c-b786-d6c4efa6a2b1','2026-01-24 23:46:16',NULL),('21e57708-50a2-4018-993f-37a836ea0d28','2026-01-24 17:05:30.000000','2026-01-29 19:59:34.073126',7374,NULL,'completed','2026-01-24 23:05:30.151936',0,'81bf7383-c0b8-424c-b786-d6c4efa6a2b1','2026-01-29 19:59:34',NULL),('28c77e11-cd55-4018-82e0-b00bdf2456c3','2026-01-29 20:01:31.896226','2026-01-29 20:01:33.667433',0,NULL,'completed','2026-01-29 20:01:31.896231',0,'81bf7383-c0b8-424c-b786-d6c4efa6a2b1','2026-01-29 20:01:34',NULL),('40d1e8d6-fdac-4b9a-a726-4501f36198e1','2026-01-29 02:54:49.000000','2026-01-29 02:54:50.000000',0,NULL,'completed','2026-01-29 08:54:49.005920',0,'81bf7383-c0b8-424c-b786-d6c4efa6a2b1','2026-01-29 08:54:50',NULL),('53f39dbf-434f-4594-ae5e-f9db3f9a3332','2026-01-29 02:44:40.000000','2026-01-29 02:44:45.000000',0,NULL,'completed','2026-01-29 08:44:40.981660',0,'65821988-291b-4c50-8ded-6f5a06cce997','2026-01-29 08:44:45',NULL),('72aab404-4e9f-4c68-9e01-f50c9ee6ba1f','2026-01-24 17:03:33.000000','2026-01-24 17:05:00.000000',1,'Học tiếng anh','completed','2026-01-24 23:03:33.336355',0,'81bf7383-c0b8-424c-b786-d6c4efa6a2b1','2026-01-24 23:05:00',NULL),('850e3b66-9440-4388-a7df-160c253ede28','2026-01-29 20:01:41.044485','2026-01-29 20:03:35.241087',1,'đi chs','completed','2026-01-29 20:01:41.044494',0,'81bf7383-c0b8-424c-b786-d6c4efa6a2b1','2026-01-29 20:03:35',NULL),('9016e6ae-fc4c-11f0-aa8f-74d4dd2c201f','2025-12-30 11:00:00.000000','2025-12-30 13:43:00.000000',105,'History - World War II','completed','2026-01-28 20:23:41.361407',0,NULL,'2026-01-28 20:23:41',NULL),('9016f32d-fc4c-11f0-aa8f-74d4dd2c201f','2025-12-31 11:00:00.000000','2025-12-31 13:43:00.000000',17,'Science - Physics','completed','2026-01-28 20:23:41.361407',0,NULL,'2026-01-28 20:23:41',NULL),('9016f4dc-fc4c-11f0-aa8f-74d4dd2c201f','2026-01-03 21:00:00.000000','2026-01-03 20:04:00.000000',77,'English - IELTS Preparation','completed','2026-01-28 20:23:41.361407',0,NULL,'2026-01-28 20:23:41',NULL),('9016f596-fc4c-11f0-aa8f-74d4dd2c201f','2026-01-04 13:00:00.000000','2026-01-04 08:17:00.000000',132,'Literature - Poetry Analysis','completed','2026-01-28 20:23:41.361407',0,NULL,'2026-01-28 20:23:41',NULL),('9016f629-fc4c-11f0-aa8f-74d4dd2c201f','2026-01-05 15:00:00.000000','2026-01-05 22:02:00.000000',18,'Computer Science - Algorithms','completed','2026-01-28 20:23:41.361407',0,NULL,'2026-01-28 20:23:41',NULL),('9016f6b5-fc4c-11f0-aa8f-74d4dd2c201f','2026-01-06 10:00:00.000000','2026-01-06 10:41:00.000000',91,'History - World War II','completed','2026-01-28 20:23:41.361407',0,NULL,'2026-01-28 20:23:41',NULL),('9016f73d-fc4c-11f0-aa8f-74d4dd2c201f','2026-01-07 17:00:00.000000','2026-01-07 15:00:00.000000',48,'Programming - JavaScript','completed','2026-01-28 20:23:41.361407',0,NULL,'2026-01-28 20:23:41',NULL),('9016f7c9-fc4c-11f0-aa8f-74d4dd2c201f','2026-01-09 08:00:00.000000','2026-01-09 23:08:00.000000',102,'Literature - Poetry Analysis','completed','2026-01-28 20:23:41.361407',0,NULL,'2026-01-28 20:23:41',NULL),('9016f848-fc4c-11f0-aa8f-74d4dd2c201f','2026-01-11 20:00:00.000000','2026-01-11 10:37:00.000000',39,'Computer Science - Algorithms','completed','2026-01-28 20:23:41.361407',0,NULL,'2026-01-28 20:23:41',NULL),('9016f8c4-fc4c-11f0-aa8f-74d4dd2c201f','2026-01-12 13:00:00.000000','2026-01-12 09:59:00.000000',80,'History - World War II','completed','2026-01-28 20:23:41.361407',0,NULL,'2026-01-28 20:23:41',NULL),('9016f945-fc4c-11f0-aa8f-74d4dd2c201f','2026-01-13 11:00:00.000000','2026-01-13 21:38:00.000000',119,'English - IELTS Preparation','completed','2026-01-28 20:23:41.361407',0,NULL,'2026-01-28 20:23:41',NULL),('9016f9cf-fc4c-11f0-aa8f-74d4dd2c201f','2026-01-16 14:00:00.000000','2026-01-16 17:35:00.000000',101,'Mathematics - Calculus','completed','2026-01-28 20:23:41.361407',0,NULL,'2026-01-28 20:23:41',NULL),('9016fa4d-fc4c-11f0-aa8f-74d4dd2c201f','2026-01-17 09:00:00.000000','2026-01-17 18:02:00.000000',84,'Programming - PHP/MySQL','completed','2026-01-28 20:23:41.361407',0,NULL,'2026-01-28 20:23:41',NULL),('9016fac8-fc4c-11f0-aa8f-74d4dd2c201f','2026-01-18 19:00:00.000000','2026-01-18 09:41:00.000000',106,'Programming - PHP/MySQL','completed','2026-01-28 20:23:41.361407',0,NULL,'2026-01-28 20:23:41',NULL),('9016fb45-fc4c-11f0-aa8f-74d4dd2c201f','2026-01-19 13:00:00.000000','2026-01-19 19:47:00.000000',78,'English - IELTS Preparation','completed','2026-01-28 20:23:41.361407',0,NULL,'2026-01-28 20:23:41',NULL),('9016fbc3-fc4c-11f0-aa8f-74d4dd2c201f','2026-01-20 15:00:00.000000','2026-01-20 12:50:00.000000',36,'Science - Physics','completed','2026-01-28 20:23:41.361407',0,NULL,'2026-01-28 20:23:41',NULL),('9016fc46-fc4c-11f0-aa8f-74d4dd2c201f','2026-01-22 14:00:00.000000','2026-01-22 12:46:00.000000',48,'History - World War II','completed','2026-01-28 20:23:41.361407',0,NULL,'2026-01-28 20:23:41',NULL),('9016fcc7-fc4c-11f0-aa8f-74d4dd2c201f','2026-01-23 20:00:00.000000','2026-01-23 12:13:00.000000',76,'History - World War II','completed','2026-01-28 20:23:41.361407',0,NULL,'2026-01-28 20:23:41',NULL),('9016fd44-fc4c-11f0-aa8f-74d4dd2c201f','2026-01-24 09:00:00.000000','2026-01-24 12:45:00.000000',62,'Programming - PHP/MySQL','completed','2026-01-28 20:23:41.361407',0,NULL,'2026-01-28 20:23:41',NULL),('9016fdc4-fc4c-11f0-aa8f-74d4dd2c201f','2026-01-26 08:00:00.000000','2026-01-26 16:49:00.000000',46,'Computer Science - Algorithms','completed','2026-01-28 20:23:41.361407',0,NULL,'2026-01-28 20:23:41',NULL),('9529a7ee-1f9f-480b-a9fe-6fea6acec51c','2026-01-30 17:28:09.613399','2026-01-30 17:28:11.308981',0,NULL,'completed','2026-01-30 17:28:09.613404',0,'81bf7383-c0b8-424c-b786-d6c4efa6a2b1','2026-01-30 17:28:11',NULL),('c9cb5593-6301-4c25-9b33-652e9cba0486','2026-01-29 02:54:57.000000','2026-01-29 02:55:00.000000',0,'Hoc tieng a','completed','2026-01-29 08:54:57.366091',0,'81bf7383-c0b8-424c-b786-d6c4efa6a2b1','2026-01-29 08:55:00',NULL),('cff564c6-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-22 10:39:47.000000','2026-01-22 07:39:47.000000',75,'Mobile App Development','completed','2026-01-28 20:39:47.509335',0,'21ca9b2d-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff569e3-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 05:39:47.000000','2026-01-26 06:39:47.000000',59,'Mobile App Development','completed','2026-01-28 20:39:47.509335',0,'21ca9a5d-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff56b63-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-20 07:39:47.000000','2026-01-27 06:39:47.000000',36,'Software Engineering','completed','2026-01-28 20:39:47.509335',0,'21ca9a88-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff56c9c-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-22 05:39:47.000000','2026-01-25 09:39:47.000000',44,'Web Development','completed','2026-01-28 20:39:47.509335',0,'21ca9a2e-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff56df3-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-23 11:39:47.000000','2026-01-21 05:39:47.000000',44,'Machine Learning','completed','2026-01-28 20:39:47.509335',0,'21ca9879-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff56f21-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-23 10:39:47.000000','2026-01-20 06:39:47.000000',62,'Software Engineering','completed','2026-01-28 20:39:47.509335',0,'21ca99b7-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff571e2-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-27 11:39:47.000000','2026-01-29 08:39:47.000000',51,'Data Structures','completed','2026-01-28 20:39:47.509335',0,'21ca9adb-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff57969-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-26 05:39:47.000000','2026-01-24 05:39:47.000000',40,'Database Design','completed','2026-01-28 20:39:47.509335',0,'21ca9b04-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff57ea5-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-20 09:39:47.000000','2026-01-21 10:39:47.000000',53,'Web Development','completed','2026-01-28 20:39:47.509335',0,'21ca9ab0-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff580a3-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-29 11:39:47.000000','2026-01-22 10:39:47.000000',32,'Mobile App Development','completed','2026-01-28 20:39:47.509335',0,'21ca9b2d-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff58291-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-22 06:39:47.000000','2026-01-26 05:39:47.000000',79,'Web Development','completed','2026-01-28 20:39:47.509335',0,'21ca9a5d-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff58442-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-25 09:39:47.000000','2026-01-20 10:39:47.000000',51,'Data Structures','completed','2026-01-28 20:39:47.509335',0,'21ca9a88-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff58608-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-24 08:39:47.000000','2026-01-20 07:39:47.000000',38,'Database Design','completed','2026-01-28 20:39:47.509335',0,'21ca9a2e-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff58795-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-24 07:39:47.000000','2026-01-22 06:39:47.000000',34,'Mobile App Development','completed','2026-01-28 20:39:47.509335',0,'21ca9879-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff58924-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-26 04:39:47.000000','2026-01-20 09:39:47.000000',69,'Data Structures','completed','2026-01-28 20:39:47.509335',0,'21ca99b7-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff58aca-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-23 04:39:47.000000','2026-01-24 07:39:47.000000',37,'Mobile App Development','completed','2026-01-28 20:39:47.509335',0,'21ca9adb-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff58c5b-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-24 10:39:47.000000','2026-01-24 05:39:47.000000',41,'Database Design','completed','2026-01-28 20:39:47.509335',0,'21ca9978-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff58ddf-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-23 06:39:47.000000','2026-01-20 08:39:47.000000',51,'Software Engineering','completed','2026-01-28 20:39:47.509335',0,'21ca9b04-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff58f7a-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-25 05:39:47.000000','2026-01-27 09:39:47.000000',77,'Software Engineering','completed','2026-01-28 20:39:47.509335',0,'21ca9ab0-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff590ff-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-22 07:39:47.000000','2026-01-28 07:39:47.000000',46,'Database Design','completed','2026-01-28 20:39:47.509335',0,'21ca9b2d-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff59295-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-24 08:39:47.000000','2026-01-29 08:39:47.000000',35,'Database Design','completed','2026-01-28 20:39:47.509335',0,'21ca9a88-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff59424-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-26 07:39:47.000000','2026-01-27 10:39:47.000000',31,'Data Structures','completed','2026-01-28 20:39:47.509335',0,'21ca9879-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff595ae-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-27 10:39:47.000000','2026-01-27 10:39:47.000000',86,'Software Engineering','completed','2026-01-28 20:39:47.509335',0,'21ca9adb-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff59764-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-22 08:39:47.000000','2026-01-25 08:39:47.000000',51,'Data Structures','completed','2026-01-28 20:39:47.509335',0,'21ca9978-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff598f4-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-27 09:39:47.000000','2026-01-23 06:39:47.000000',53,'Web Development','completed','2026-01-28 20:39:47.509335',0,'21ca9b04-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff59a7f-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-22 06:39:47.000000','2026-01-26 05:39:47.000000',30,'Software Engineering','completed','2026-01-28 20:39:47.509335',0,'21ca9ab0-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff59b73-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-20 04:39:47.000000','2026-01-26 09:39:47.000000',60,'Database Design','completed','2026-01-28 20:39:47.509335',0,'21ca9b2d-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff59bf4-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-26 11:39:47.000000','2026-01-25 08:39:47.000000',45,'Machine Learning','completed','2026-01-28 20:39:47.509335',0,'21ca9a5d-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff59c72-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-29 09:39:47.000000','2026-01-20 09:39:47.000000',43,'Machine Learning','completed','2026-01-28 20:39:47.509335',0,'21ca9a2e-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('cff59cf2-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-21 05:39:47.000000','2026-01-26 05:39:47.000000',69,'Machine Learning','completed','2026-01-28 20:39:47.509335',0,'21ca99b7-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:39:47',NULL),('d438d5ef-19c2-41a2-ba92-c8a4aae754b9','2026-01-29 02:44:54.000000','2026-01-29 02:46:46.000000',1,NULL,'completed','2026-01-29 08:44:54.292463',0,'65821988-291b-4c50-8ded-6f5a06cce997','2026-01-29 08:46:46',NULL),('da41aba2-de26-4f1e-b68c-d44ae97f7f54','2026-01-24 17:03:33.000000',NULL,0,'Học tiếng anh','in_progress','2026-01-24 23:03:33.318752',0,'81bf7383-c0b8-424c-b786-d6c4efa6a2b1','2026-01-24 23:03:33',NULL),('e3ad1fbf-f071-4a8f-b9b3-922242288512','2026-01-29 19:57:56.405205',NULL,0,NULL,'in_progress','2026-01-29 19:57:56.405211',0,'81bf7383-c0b8-424c-b786-d6c4efa6a2b1',NULL,NULL),('f9550b6d-a1c3-4fde-88ca-c45fe87b979a','2026-01-29 02:46:53.000000','2026-01-29 02:48:20.000000',1,NULL,'completed','2026-01-29 08:46:53.290730',0,'65821988-291b-4c50-8ded-6f5a06cce997','2026-01-29 08:48:20',NULL),('f9a2aaf3-292a-493e-bdb1-ff03a7d41d60','2026-01-24 17:46:28.000000','2026-01-24 17:46:35.000000',0,'Học tiếng anh','completed','2026-01-24 23:46:28.361506',0,'81bf7383-c0b8-424c-b786-d6c4efa6a2b1','2026-01-24 23:46:35',NULL);
/*!40000 ALTER TABLE `pomodorohistory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `task`
--

DROP TABLE IF EXISTS `task`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `task` (
  `task_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(500) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `priority` enum('low','medium','high') COLLATE utf8mb4_unicode_ci DEFAULT 'medium',
  `deadline` date DEFAULT NULL,
  `status` varchar(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `updated_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  `is_deleted` tinyint NOT NULL DEFAULT '0',
  `group_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`task_id`),
  KEY `Task_group_id_488c6987_fk_ToDoListGroup_group_id` (`group_id`),
  KEY `idx_task_deadline` (`deadline`),
  KEY `idx_task_status` (`status`),
  CONSTRAINT `Task_group_id_488c6987_fk_ToDoListGroup_group_id` FOREIGN KEY (`group_id`) REFERENCES `todolistgroup` (`group_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `task`
--

LOCK TABLES `task` WRITE;
/*!40000 ALTER TABLE `task` DISABLE KEYS */;
INSERT INTO `task` VALUES ('0341191c-567a-4d56-97a1-cc6d9de91a4c','Sang','a','medium','2026-01-18','pending','2026-01-17 15:29:21.000000','2026-01-17 15:33:01.905381',1,'0bde6d02-9100-4550-bb97-14100535dd86'),('04d2c795-8031-420d-905e-fe8cdbfde23d','Làm bài tiếng anh','','high','2026-01-29','pending','2026-01-19 22:31:15.000000','2026-01-27 10:05:18.555778',0,'4c2d49bd-91d6-4423-934f-541ef2814a96'),('073cb8eb-a921-40cb-a6f1-6fdfffe6c3c1','abc','','high','2026-01-20','overdue','2026-01-17 23:05:38.000000','2026-01-24 23:58:15.720503',0,'4c2d49bd-91d6-4423-934f-541ef2814a96'),('0c088e9b-4f80-4827-9786-e59b22608dcd','Làm bài tiếng anh','','medium','2026-01-18','pending','2026-01-17 21:36:11.000000','2026-01-17 21:36:11.519780',0,'3e813092-9fa9-4617-a2d3-2a371bc53668'),('17b02ab6-3407-43cb-8c62-54350cb20065','111','','high','2026-01-29','pending','2026-01-30 02:14:55.393108','2026-01-30 02:16:01.085474',1,'368ba1b9-2f58-47f3-807b-1876249a97ca'),('1c35f304-1d2b-4a38-b34e-5a61509e0fba','sang','','medium','2026-01-21','overdue','2026-01-17 22:49:09.000000','2026-01-24 23:58:15.720503',0,'4c2d49bd-91d6-4423-934f-541ef2814a96'),('289d8526-1686-4314-b888-0fdcedcca166','Trang123','Aaâ','high','2026-01-18','completed','2026-01-17 15:22:54.000000','2026-01-17 20:14:52.124845',0,'0bde6d02-9100-4550-bb97-14100535dd86'),('3b07b51c-fc4e-11f0-aa8f-74d4dd2c201f','Prepare for English test','Task description for medium priority task','medium','2026-02-24','completed','2026-01-16 20:35:37.000000','2026-01-28 20:35:37.649065',0,'3b0262b3-fc4e-11f0-aa8f-74d4dd2c201f'),('3b07ba8d-fc4e-11f0-aa8f-74d4dd2c201f','Prepare for English test','Task description for low priority task','medium','2026-02-04','in_progress','2026-01-14 20:35:37.000000','2026-01-28 20:35:37.649065',0,'3b026694-fc4e-11f0-aa8f-74d4dd2c201f'),('3b07bbf3-fc4e-11f0-aa8f-74d4dd2c201f','Group study session','Task description for medium priority task','medium','2026-01-30','pending','2026-01-18 20:35:37.000000','2026-01-28 20:35:37.649065',0,'3b02678e-fc4e-11f0-aa8f-74d4dd2c201f'),('3b07bcfa-fc4e-11f0-aa8f-74d4dd2c201f','Complete math homework','Task description for high priority task','high','2026-01-28','pending','2026-01-15 20:35:37.000000','2026-01-28 20:35:37.649065',0,'3b026851-fc4e-11f0-aa8f-74d4dd2c201f'),('3b07bde8-fc4e-11f0-aa8f-74d4dd2c201f','Review lecture notes','Task description for low priority task','medium','2026-02-22','pending','2026-01-26 20:35:37.000000','2026-01-28 20:35:37.649065',0,'3b0268dc-fc4e-11f0-aa8f-74d4dd2c201f'),('3b07befe-fc4e-11f0-aa8f-74d4dd2c201f','Review lecture notes','Task description for low priority task','medium','2026-02-20','pending','2026-01-28 20:35:37.000000','2026-01-28 20:35:37.649065',0,'3b02696a-fc4e-11f0-aa8f-74d4dd2c201f'),('3b07bfe7-fc4e-11f0-aa8f-74d4dd2c201f','Create study flashcards','Task description for low priority task','low','2026-02-22','pending','2026-01-27 20:35:37.000000','2026-01-28 20:35:37.649065',0,'3b0269f6-fc4e-11f0-aa8f-74d4dd2c201f'),('3b07c0fe-fc4e-11f0-aa8f-74d4dd2c201f','Complete math homework','Task description for high priority task','low','2026-02-17','completed','2026-01-17 20:35:37.000000','2026-01-28 20:35:37.649065',0,'3b026a86-fc4e-11f0-aa8f-74d4dd2c201f'),('3b07c200-fc4e-11f0-aa8f-74d4dd2c201f','Complete math homework','Task description for low priority task','medium','2026-02-10','pending','2026-01-19 20:35:37.000000','2026-01-28 20:35:37.649065',0,'3b029de4-fc4e-11f0-aa8f-74d4dd2c201f'),('3b07c3e1-fc4e-11f0-aa8f-74d4dd2c201f','Watch online tutorial','Task description for low priority task','high','2026-02-01','pending','2026-01-16 20:35:37.000000','2026-01-28 20:35:37.649065',0,'3b029eef-fc4e-11f0-aa8f-74d4dd2c201f'),('6fa0f52f-2194-4098-a4f7-bafe16c8c6d6','dsgd','dág','medium','2026-01-29','pending','2026-01-29 20:14:33.805566','2026-01-29 20:14:45.715438',1,'368ba1b9-2f58-47f3-807b-1876249a97ca'),('799c5cef-6793-4aae-bb62-5c7b1d42e100','Sang','a','medium','2026-01-18','pending','2026-01-17 15:29:21.000000','2026-01-17 15:29:21.578813',0,'0bde6d02-9100-4550-bb97-14100535dd86'),('901e0d05-fc4c-11f0-aa8f-74d4dd2c201f','Group study session','Task description for high priority task','high','2026-02-17','in_progress','2026-01-23 20:23:41.000000','2026-01-28 20:23:41.408570',0,'0814cfdf-0db5-4b86-8d79-e6e8c7914327'),('901e2d77-fc4c-11f0-aa8f-74d4dd2c201f','Submit assignment','Task description for high priority task','low','2026-02-18','pending','2026-01-27 20:23:41.000000','2026-01-28 20:23:41.408570',0,'0bde6d02-9100-4550-bb97-14100535dd86'),('901e2ee2-fc4c-11f0-aa8f-74d4dd2c201f','Group study session','Task description for medium priority task','medium','2026-02-11','in_progress','2026-01-25 20:23:41.000000','2026-01-28 20:23:41.408570',0,'1fd07a74-e1c3-4c73-b6be-30b34dbe7b78'),('901e2fc0-fc4c-11f0-aa8f-74d4dd2c201f','Prepare for English test','Task description for low priority task','medium','2026-02-06','in_progress','2026-01-20 20:23:41.000000','2026-01-28 20:23:41.408570',0,'1fde9d4a-774b-4f55-90f4-918de1fdfed9'),('901e307c-fc4c-11f0-aa8f-74d4dd2c201f','Group study session','Task description for low priority task','high','2026-02-01','pending','2026-01-23 20:23:41.000000','2026-01-28 20:23:41.408570',0,'2248ef5d-a168-43ef-9886-9642120d87d2'),('901e3143-fc4c-11f0-aa8f-74d4dd2c201f','Review lecture notes','Task description for low priority task','medium','2026-02-07','completed','2026-01-25 20:23:41.000000','2026-01-28 20:23:41.408570',0,'259b71d6-ea84-4b46-8de9-89306f57856d'),('901e31e9-fc4c-11f0-aa8f-74d4dd2c201f','Finish programming project','Task description for medium priority task','medium','2026-02-26','completed','2026-01-17 20:23:41.000000','2026-01-28 20:23:41.408570',0,'2876c300-f28e-430a-8e54-46521eaa663b'),('901e32a6-fc4c-11f0-aa8f-74d4dd2c201f','Review lecture notes','Task description for medium priority task','low','2026-01-29','in_progress','2026-01-24 20:23:41.000000','2026-01-28 20:23:41.408570',0,'2d3ec23d-ba71-4531-9c08-d375a1d5fb89'),('901e3357-fc4c-11f0-aa8f-74d4dd2c201f','Complete math homework','Task description for high priority task','low','2026-01-28','pending','2026-01-14 20:23:41.000000','2026-01-28 20:23:41.408570',0,'3aad9299-daa1-43de-8174-5e1b5c292597'),('901e33f7-fc4c-11f0-aa8f-74d4dd2c201f','Submit assignment','Task description for high priority task','high','2026-02-13','in_progress','2026-01-24 20:23:41.000000','2026-01-28 20:23:41.408570',0,'3b0f3f44-eee8-444a-b435-ab57647d2754'),('901e3493-fc4c-11f0-aa8f-74d4dd2c201f','Read chapter 5 of textbook','Task description for low priority task','medium','2026-02-03','pending','2026-01-24 20:23:41.000000','2026-01-28 20:23:41.408570',0,'3cca1a5d-235c-4e2b-ba41-f66ff8b7ce9b'),('901e3535-fc4c-11f0-aa8f-74d4dd2c201f','Create study flashcards','Task description for high priority task','medium','2026-02-11','pending','2026-01-19 20:23:41.000000','2026-01-28 20:23:41.408570',0,'3e813092-9fa9-4617-a2d3-2a371bc53668'),('901e35e2-fc4c-11f0-aa8f-74d4dd2c201f','Create study flashcards','Task description for medium priority task','low','2026-02-15','in_progress','2026-01-20 20:23:41.000000','2026-01-28 20:23:41.408570',0,'3f343e8c-52f9-4e88-8d32-6e910750a1f3'),('901e3693-fc4c-11f0-aa8f-74d4dd2c201f','Group study session','Task description for low priority task','low','2026-02-11','pending','2026-01-28 20:23:41.000000','2026-01-28 20:23:41.408570',0,'4c2d49bd-91d6-4423-934f-541ef2814a96'),('901e3760-fc4c-11f0-aa8f-74d4dd2c201f','Review lecture notes','Task description for medium priority task','medium','2026-02-01','completed','2026-01-24 20:23:41.000000','2026-01-28 20:23:41.408570',0,'67205bf5-dd75-4b8c-ad61-08dae9574a9e'),('901e382b-fc4c-11f0-aa8f-74d4dd2c201f','Watch online tutorial','Task description for low priority task','low','2026-02-02','pending','2026-01-16 20:23:41.000000','2026-01-28 20:23:41.408570',0,'68d0fcf4-6191-4abb-8d0e-83cfa1ce0f83'),('901e3938-fc4c-11f0-aa8f-74d4dd2c201f','Group study session','Task description for high priority task','high','2026-02-04','completed','2026-01-15 20:23:41.000000','2026-01-28 20:23:41.408570',0,'7160f604-d4c4-4cac-8c8e-abd508afd1bc'),('901e3a05-fc4c-11f0-aa8f-74d4dd2c201f','Read chapter 5 of textbook','Task description for low priority task','high','2026-02-26','pending','2026-01-27 20:23:41.000000','2026-01-28 20:23:41.408570',0,'7f22c0ed-12a7-4498-885e-ad1e9c85fa62'),('901e3acf-fc4c-11f0-aa8f-74d4dd2c201f','Practice coding exercises','Task description for high priority task','high','2026-02-02','completed','2026-01-18 20:23:41.000000','2026-01-28 20:23:41.408570',0,'901b5afd-fc4c-11f0-aa8f-74d4dd2c201f'),('901e3bb7-fc4c-11f0-aa8f-74d4dd2c201f','Review lecture notes','Task description for low priority task','low','2026-01-30','pending','2026-01-24 20:23:41.000000','2026-01-28 20:23:41.408570',0,'914ae070-eb68-48e2-baf6-a2bbafb9c6c9'),('901e3c8c-fc4c-11f0-aa8f-74d4dd2c201f','Submit assignment','Task description for high priority task','low','2026-02-15','in_progress','2026-01-15 20:23:41.000000','2026-01-28 20:23:41.408570',0,'91ce6909-e8e7-42e5-a117-0bf9465ea1ec'),('901e3d40-fc4c-11f0-aa8f-74d4dd2c201f','Read chapter 5 of textbook','Task description for high priority task','high','2026-01-29','completed','2026-01-20 20:23:41.000000','2026-01-28 20:23:41.408570',0,'9ec7f0c2-d4df-4514-a4b0-0362dd8f7ac5'),('901e3df0-fc4c-11f0-aa8f-74d4dd2c201f','Practice coding exercises','Task description for low priority task','medium','2026-02-09','in_progress','2026-01-18 20:23:41.000000','2026-01-28 20:23:41.408570',0,'a4e2f627-d62f-4c47-a235-c0d7c8ed0c65'),('901e3eaa-fc4c-11f0-aa8f-74d4dd2c201f','Read chapter 5 of textbook','Task description for medium priority task','low','2026-02-23','completed','2026-01-24 20:23:41.000000','2026-01-28 20:23:41.408570',0,'a7546809-e2d7-476a-861f-91640e2bf2c6'),('901e3f72-fc4c-11f0-aa8f-74d4dd2c201f','Submit assignment','Task description for medium priority task','medium','2026-02-15','pending','2026-01-24 20:23:41.000000','2026-01-28 20:23:41.408570',0,'b4c2bfe3-5f54-40de-b665-37484180747a'),('901e404a-fc4c-11f0-aa8f-74d4dd2c201f','Read chapter 5 of textbook','Task description for low priority task','medium','2026-02-09','pending','2026-01-25 20:23:41.000000','2026-01-28 20:23:41.408570',0,'bc4388b0-63f4-40b6-9613-c4be57570f03'),('901e4129-fc4c-11f0-aa8f-74d4dd2c201f','Watch online tutorial','Task description for high priority task','high','2026-02-09','pending','2026-01-22 20:23:41.000000','2026-01-28 20:23:41.408570',0,'d8c0cb16-cc95-4f4f-871b-0ee1bbb0ad46'),('901e4204-fc4c-11f0-aa8f-74d4dd2c201f','Read chapter 5 of textbook','Task description for medium priority task','low','2026-02-04','pending','2026-01-23 20:23:41.000000','2026-01-28 20:23:41.408570',0,'e2e69531-07fa-44d3-9f8a-1643c891df44'),('901e42f1-fc4c-11f0-aa8f-74d4dd2c201f','Practice coding exercises','Task description for high priority task','medium','2026-02-08','pending','2026-01-17 20:23:41.000000','2026-01-28 20:23:41.408570',0,'e48e2ca3-fc2f-404a-8df1-cb0b0e2f2787'),('901e43b5-fc4c-11f0-aa8f-74d4dd2c201f','Review lecture notes','Task description for medium priority task','low','2026-01-28','pending','2026-01-15 20:23:41.000000','2026-01-28 20:23:41.408570',0,'f013d632-8136-49a5-94b0-dde1f1b654f6'),('901e4491-fc4c-11f0-aa8f-74d4dd2c201f','Create study flashcards','Task description for medium priority task','low','2026-02-05','pending','2026-01-21 20:23:41.000000','2026-01-28 20:23:41.408570',0,'f2bd5f34-e493-43a2-96b4-e4deb48f61e6'),('901e4571-fc4c-11f0-aa8f-74d4dd2c201f','Practice coding exercises','Task description for medium priority task','low','2026-02-07','pending','2026-01-17 20:23:41.000000','2026-01-28 20:23:41.408570',0,'fa12c7b6-3b8c-4f37-95ad-fe781386e9f6'),('901e4651-fc4c-11f0-aa8f-74d4dd2c201f','Group study session','Task description for high priority task','low','2026-02-21','pending','2026-01-19 20:23:41.000000','2026-01-28 20:23:41.408570',0,'ffb4f5df-2670-46b3-9b48-25108c61a357'),('901e46fb-fc4c-11f0-aa8f-74d4dd2c201f','Watch online tutorial','Task description for low priority task','medium','2026-02-08','completed','2026-01-17 20:23:41.000000','2026-01-28 20:23:41.408570',0,'901b49ab-fc4c-11f0-aa8f-74d4dd2c201f'),('b59c65be-c506-4ffe-ac78-a77bb661385b','Trang','Aaâ','high','2026-01-18','in_progress','2026-01-17 15:22:54.000000','2026-01-17 15:22:54.462248',0,'0bde6d02-9100-4550-bb97-14100535dd86'),('b5d451f3-a0b8-4318-9e48-69ee954b468b','Làm bài tiếng anh','','medium','2026-01-16','overdue','2026-01-17 22:04:52.000000','2026-01-20 22:01:51.166662',0,'1fd07a74-e1c3-4c73-b6be-30b34dbe7b78'),('db8fcb23-aa24-4b7c-9e04-abe24eda439f','Son','asss','medium','2026-01-18','pending','2026-01-17 15:38:00.000000','2026-01-17 15:38:00.127734',0,'0bde6d02-9100-4550-bb97-14100535dd86'),('de518e2a-6366-4650-a2ca-5f632fcb92b6','Trang123','','medium','2026-01-23','overdue','2026-01-17 22:49:17.000000','2026-01-24 23:58:15.720503',0,'4c2d49bd-91d6-4423-934f-541ef2814a96'),('e0d75f3a-b220-475f-a3ed-573e06f50b8a','Bao cao co Khue','','high','2026-01-28','overdue','2026-01-29 08:52:49.000000','2026-01-29 08:53:01.097705',0,'6ea0950f-fe5a-4698-9967-79761102d943'),('e1d3d5aa-c91e-48c7-b249-6b30a4c6f268','Làm bài tiếng anh','Cô ngân','high','2026-01-19','completed','2026-01-17 21:15:40.000000','2026-01-17 21:15:44.692139',0,'914ae070-eb68-48e2-baf6-a2bbafb9c6c9'),('e7240237-8d36-4062-9cce-06b2e2465b9e','Làm bài tiếng anh','sfsf','medium','2026-01-15','overdue','2026-01-19 22:25:01.000000','2026-01-19 22:27:02.447499',0,'7f22c0ed-12a7-4498-885e-ad1e9c85fa62'),('ea06cb90-75fc-4b5d-9a62-aeebb9682b97','Làm bài tiếng anh','cô ngân','medium','2026-01-16','overdue','2026-01-17 21:16:31.000000','2026-01-17 21:19:04.066098',0,'4c2d49bd-91d6-4423-934f-541ef2814a96'),('ebc6b3ce-fc4e-11f0-aa8f-74d4dd2c201f','Create study flashcards','Task description for low priority task','high','2026-02-02','completed','2026-01-14 20:40:34.000000','2026-01-28 20:40:34.179697',0,'3b0262b3-fc4e-11f0-aa8f-74d4dd2c201f'),('ebc6b8d0-fc4e-11f0-aa8f-74d4dd2c201f','Read chapter 5 of textbook','Task description for high priority task','high','2026-01-30','pending','2026-01-14 20:40:34.000000','2026-01-28 20:40:34.179697',0,'3b026694-fc4e-11f0-aa8f-74d4dd2c201f'),('ebc6ba1b-fc4e-11f0-aa8f-74d4dd2c201f','Prepare for English test','Task description for low priority task','low','2026-01-30','in_progress','2026-01-27 20:40:34.000000','2026-01-28 20:40:34.179697',0,'3b02678e-fc4e-11f0-aa8f-74d4dd2c201f'),('ebc6bb00-fc4e-11f0-aa8f-74d4dd2c201f','Submit assignment','Task description for medium priority task','low','2026-02-23','pending','2026-01-26 20:40:34.000000','2026-01-28 20:40:34.179697',0,'3b026851-fc4e-11f0-aa8f-74d4dd2c201f'),('ebc6bc45-fc4e-11f0-aa8f-74d4dd2c201f','Finish programming project','Task description for medium priority task','low','2026-02-08','pending','2026-01-23 20:40:34.000000','2026-01-28 20:40:34.179697',0,'3b0268dc-fc4e-11f0-aa8f-74d4dd2c201f'),('ebc6bd1f-fc4e-11f0-aa8f-74d4dd2c201f','Prepare for English test','Task description for medium priority task','high','2026-02-11','pending','2026-01-19 20:40:34.000000','2026-01-28 20:40:34.179697',0,'3b02696a-fc4e-11f0-aa8f-74d4dd2c201f'),('ebc6be24-fc4e-11f0-aa8f-74d4dd2c201f','Create study flashcards','Task description for medium priority task','high','2026-02-22','in_progress','2026-01-17 20:40:34.000000','2026-01-28 20:40:34.179697',0,'3b0269f6-fc4e-11f0-aa8f-74d4dd2c201f'),('ebc6bef8-fc4e-11f0-aa8f-74d4dd2c201f','Prepare for English test','Task description for high priority task','low','2026-02-22','pending','2026-01-27 20:40:34.000000','2026-01-28 20:40:34.179697',0,'3b026a86-fc4e-11f0-aa8f-74d4dd2c201f'),('ebc6c137-fc4e-11f0-aa8f-74d4dd2c201f','Finish programming project','Task description for low priority task','medium','2026-02-10','pending','2026-01-23 20:40:34.000000','2026-01-28 20:40:34.179697',0,'3b029de4-fc4e-11f0-aa8f-74d4dd2c201f'),('ebc6c20a-fc4e-11f0-aa8f-74d4dd2c201f','Read chapter 5 of textbook','Task description for low priority task','medium','2026-02-11','pending','2026-01-20 20:40:34.000000','2026-01-28 20:40:34.179697',0,'3b029eef-fc4e-11f0-aa8f-74d4dd2c201f');
/*!40000 ALTER TABLE `task` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `todolist`
--

DROP TABLE IF EXISTS `todolist`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `todolist` (
  `todolist_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `user_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`todolist_id`),
  KEY `ToDoList_user_id_70821d86_fk_User_user_id` (`user_id`),
  CONSTRAINT `ToDoList_user_id_70821d86_fk_User_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `todolist`
--

LOCK TABLES `todolist` WRITE;
/*!40000 ALTER TABLE `todolist` DISABLE KEYS */;
INSERT INTO `todolist` VALUES ('3399dd19-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-21 20:35:25.000000','3b85fa55-9fd9-4819-9ee5-a235ed0169c9'),('3399ddb6-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-05 20:35:25.000000','2ad82476-0064-4633-8e0e-7b4bd88fe052'),('3399ddd2-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-22 20:35:25.000000','3b75c652-2466-48a0-9bd0-5cdeec3c93fe'),('3399dde3-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-09 20:35:25.000000','5955a743-4ec7-4bc3-b74d-51fdbb29791f'),('3399ddf5-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-07 20:35:25.000000','665c60a5-ac6b-49fa-82c1-34e97bc3b361'),('3399de03-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-10 20:35:25.000000','6436aa66-b2d3-4847-a8dd-c625dcbaff33'),('3399de19-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-03 20:35:25.000000','ec8c547c-76d8-4c11-addd-a7ce2c669f16'),('3399de27-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-15 20:35:25.000000','33b05e16-6843-42e9-8daa-8d4173f24ac3'),('3399de37-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-08 20:35:25.000000','1bd52590-db76-42c6-9aac-737c161c0f60'),('3399de45-fc4e-11f0-aa8f-74d4dd2c201f','2026-01-28 20:35:25.000000','90007af7-f330-4cc2-82d0-3282783ecc4b'),('60f2d6f0-54de-4a16-95df-7ae06392c660','2026-01-29 08:31:35.503894','cff04a27-fc4e-11f0-aa8f-74d4dd2c201f'),('65b21b34-119d-4b65-b91b-e2bee24c0974','2026-01-13 00:25:25.350372','8ad6fb11-4466-4019-a956-4279229a6f42'),('d0eb8343-fceb-4638-9939-f4f5fbdfd6eb','2026-01-29 08:29:50.947419','d00a31f3-0661-4e31-b6ba-82a802e6c1e3'),('eb9659cb-eaab-49a6-99d7-27f3f31b4fda','2026-01-27 23:58:05.577394','303e179c-0ff3-4dd9-8c9b-d39ebeea66a3'),('ecc41606-beb8-436f-bd74-e99d3c7f6a44','2026-01-29 08:48:35.397256','STU001');
/*!40000 ALTER TABLE `todolist` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `todolistgroup`
--

DROP TABLE IF EXISTS `todolistgroup`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `todolistgroup` (
  `group_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `is_deleted` tinyint NOT NULL DEFAULT '0',
  `todolist_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`group_id`),
  KEY `ToDoListGroup_todolist_id_02a9d3a1_fk_ToDoList_todolist_id` (`todolist_id`),
  CONSTRAINT `ToDoListGroup_todolist_id_02a9d3a1_fk_ToDoList_todolist_id` FOREIGN KEY (`todolist_id`) REFERENCES `todolist` (`todolist_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `todolistgroup`
--

LOCK TABLES `todolistgroup` WRITE;
/*!40000 ALTER TABLE `todolistgroup` DISABLE KEYS */;
INSERT INTO `todolistgroup` VALUES ('0814cfdf-0db5-4b86-8d79-e6e8c7914327','Personal','2026-01-15 21:46:39.102585',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('0bde6d02-9100-4550-bb97-14100535dd86','123','2026-01-16 20:49:07.276119',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('1fd07a74-e1c3-4c73-b6be-30b34dbe7b78','Bài 3','2026-01-17 22:04:37.821100',0,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('1fde9d4a-774b-4f55-90f4-918de1fdfed9','fdsa','2026-01-15 22:00:31.190033',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('2248ef5d-a168-43ef-9886-9642120d87d2','Sa','2026-01-15 21:59:29.950883',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('259b71d6-ea84-4b46-8de9-89306f57856d','abc','2026-01-16 20:50:17.992097',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('2876c300-f28e-430a-8e54-46521eaa663b','hgk','2026-01-14 00:39:22.386430',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('2d3ec23d-ba71-4531-9c08-d375a1d5fb89','Sang','2026-01-15 21:52:03.571014',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('368ba1b9-2f58-47f3-807b-1876249a97ca','Bai 1','2026-01-29 20:14:23.208481',0,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('3aad9299-daa1-43de-8174-5e1b5c292597','Bai2','2026-01-20 21:50:35.875831',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('3b0262b3-fc4e-11f0-aa8f-74d4dd2c201f','Health & Fitness','2026-01-20 20:35:37.000000',0,'3399de37-fc4e-11f0-aa8f-74d4dd2c201f'),('3b026694-fc4e-11f0-aa8f-74d4dd2c201f','Shopping List','2026-01-12 20:35:37.000000',0,'3399ddb6-fc4e-11f0-aa8f-74d4dd2c201f'),('3b02678e-fc4e-11f0-aa8f-74d4dd2c201f','Personal Projects','2026-01-10 20:35:37.000000',0,'3399de27-fc4e-11f0-aa8f-74d4dd2c201f'),('3b026851-fc4e-11f0-aa8f-74d4dd2c201f','Shopping List','2026-01-25 20:35:37.000000',0,'3399ddd2-fc4e-11f0-aa8f-74d4dd2c201f'),('3b0268dc-fc4e-11f0-aa8f-74d4dd2c201f','Work Tasks','2026-01-22 20:35:37.000000',0,'3399dd19-fc4e-11f0-aa8f-74d4dd2c201f'),('3b02696a-fc4e-11f0-aa8f-74d4dd2c201f','Health & Fitness','2026-01-26 20:35:37.000000',0,'3399dde3-fc4e-11f0-aa8f-74d4dd2c201f'),('3b0269f6-fc4e-11f0-aa8f-74d4dd2c201f','Personal Projects','2026-01-11 20:35:37.000000',0,'3399de03-fc4e-11f0-aa8f-74d4dd2c201f'),('3b026a86-fc4e-11f0-aa8f-74d4dd2c201f','Shopping List','2026-01-15 20:35:37.000000',0,'3399ddf5-fc4e-11f0-aa8f-74d4dd2c201f'),('3b029de4-fc4e-11f0-aa8f-74d4dd2c201f','Personal Projects','2026-01-15 20:35:37.000000',0,'3399de45-fc4e-11f0-aa8f-74d4dd2c201f'),('3b029eef-fc4e-11f0-aa8f-74d4dd2c201f','Personal Projects','2026-01-21 20:35:37.000000',0,'3399de19-fc4e-11f0-aa8f-74d4dd2c201f'),('3b0f3f44-eee8-444a-b435-ab57647d2754','Bài','2026-01-17 22:03:37.214774',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('3cca1a5d-235c-4e2b-ba41-f66ff8b7ce9b','123','2026-01-15 23:53:47.980497',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('3e813092-9fa9-4617-a2d3-2a371bc53668','Bài tập','2026-01-17 21:31:46.204097',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('3f343e8c-52f9-4e88-8d32-6e910750a1f3','111111111111111111111111','2026-01-17 00:14:38.543177',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('4c2d49bd-91d6-4423-934f-541ef2814a96','Bài tập về nhà','2026-01-17 21:15:02.373172',0,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('67205bf5-dd75-4b8c-ad61-08dae9574a9e','12','2026-01-16 20:49:56.174625',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('68d0fcf4-6191-4abb-8d0e-83cfa1ce0f83','Làm bài tiếng anh','2026-01-17 21:22:28.225186',0,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('6ea0950f-fe5a-4698-9967-79761102d943','Lam du an PHP','2026-01-29 08:52:25.558396',0,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('7160f604-d4c4-4cac-8c8e-abd508afd1bc','ád','2026-01-15 22:03:31.889615',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('7f22c0ed-12a7-4498-885e-ad1e9c85fa62','Học','2026-01-19 22:24:44.506006',0,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('901b49ab-fc4c-11f0-aa8f-74d4dd2c201f','Work Tasks','2026-01-10 20:23:41.000000',0,'eb9659cb-eaab-49a6-99d7-27f3f31b4fda'),('901b5afd-fc4c-11f0-aa8f-74d4dd2c201f','Study Tasks','2026-01-11 20:23:41.000000',0,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('914ae070-eb68-48e2-baf6-a2bbafb9c6c9','11111111111111111111111111111111111111111111111111111111','2026-01-17 00:21:31.668651',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('91ce6909-e8e7-42e5-a117-0bf9465ea1ec','fdsa','2026-01-15 22:03:21.660162',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('9ec7f0c2-d4df-4514-a4b0-0362dd8f7ac5','Bài tập','2026-01-17 21:35:58.277455',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('a4e2f627-d62f-4c47-a235-c0d7c8ed0c65','Sang','2026-01-15 21:51:45.901096',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('a7546809-e2d7-476a-861f-91640e2bf2c6','az','2026-01-17 15:18:14.569928',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('b4c2bfe3-5f54-40de-b665-37484180747a','S','2026-01-15 21:58:03.916677',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('bc4388b0-63f4-40b6-9613-c4be57570f03','fdsa','2026-01-15 22:00:32.445765',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('d8c0cb16-cc95-4f4f-871b-0ee1bbb0ad46','Bài tập 1','2026-01-17 21:37:22.935080',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('e2e69531-07fa-44d3-9f8a-1643c891df44','Bài','2026-01-17 23:05:55.819008',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('e48e2ca3-fc2f-404a-8df1-cb0b0e2f2787','fdsa','2026-01-15 22:00:29.494434',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('f013d632-8136-49a5-94b0-dde1f1b654f6','Test','2026-01-17 23:05:49.010704',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('f2bd5f34-e493-43a2-96b4-e4deb48f61e6','Sang','2026-01-15 21:58:53.969000',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('fa12c7b6-3b8c-4f37-95ad-fe781386e9f6','3','2026-01-16 07:55:17.471588',1,'65b21b34-119d-4b65-b91b-e2bee24c0974'),('ffb4f5df-2670-46b3-9b48-25108c61a357','Sang','2026-01-15 23:03:11.318317',1,'65b21b34-119d-4b65-b91b-e2bee24c0974');
/*!40000 ALTER TABLE `todolistgroup` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `user_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `username` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fullname` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6),
  `updated_at` datetime(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
  `is_deleted` tinyint NOT NULL DEFAULT '0',
  `role` varchar(36) COLLATE utf8mb4_unicode_ci DEFAULT 'free',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`),
  KEY `idx_user_created` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES ('1bd52590-db76-42c6-9aac-737c161c0f60','sv8','SV8','sv8@gmail.com','$2b$12$m9jsLFO9bAclfs9.vvaIMeCnyQ5YG2dtXjvr9ad8wAM0jM9/3huBC','2026-01-28 09:12:42.000000','2026-01-30 02:13:14.789070',1,'free'),('2ad82476-0064-4633-8e0e-7b4bd88fe052','sv10','SV10','sv10@gmail.com','$2y$10$OkKTgRAZNIqlQhBJZvfeS.KlSfJ0WjNqmVan7kI.fTr0O.4bQoZ2e','2026-01-28 09:13:40.000000','2026-01-28 23:43:24.000000',0,'free'),('303e179c-0ff3-4dd9-8c9b-d39ebeea66a3','admin lỗi','Hồ Anh Sang','Admin@gmail.com','$2y$10$699XAecVyeQ/oeWI45xtIOeGpJTQPk79GBcT0mCQh9Fe2P2L3I1kW','2026-01-27 17:58:03.000000','2026-01-29 08:29:23.922433',0,'admin'),('33b05e16-6843-42e9-8daa-8d4173f24ac3','sv7','SV7','sv7@gmail.com','$2y$10$9RaC7jJgoH1zB6HXStVtjuMNOkNLX3hLSmNXtl6RAB4fcLysuHJTy','2026-01-28 09:12:17.000000','2026-01-28 09:12:17.000000',0,'free'),('3b75c652-2466-48a0-9bd0-5cdeec3c93fe','sv2','SV2','sv2@gmail.com','$2y$10$xhnGiQyPQOyqYHpbpjOvb.1yEF2R0.RFF89mfHWropuaKUy40nIXK','2026-01-28 09:02:20.000000','2026-01-28 20:23:41.081251',0,'admin'),('3b85fa55-9fd9-4819-9ee5-a235ed0169c9','sv1','SV1','sv1@gmail.com','$2y$10$6j33Q7frOURJ1gF1eIjI4.mtkWZWtkYS3AASQr.9QlYr.0nMmFLTO','2026-01-28 09:01:53.000000','2026-01-28 20:23:41.081251',0,'admin'),('5955a743-4ec7-4bc3-b74d-51fdbb29791f','sv3','SV3','sv3@gmail.com','$2y$10$EGOGi7yUC7UNkgaR3v5lvuq1uHkGrBvWxJ3yJZhrXW0akTPE0THiS','2026-01-28 09:02:40.000000','2026-01-28 09:02:40.000000',0,'free'),('60f2e760-2e9d-493d-b684-3c20a0c7d857','test_9687','Test User','test_2424@test.com','$2y$10$ui0EwiNyyb2NvpPZC2tf4uaRcooGlatwLfQRWbUePfFxU2ZMJgvL2','2026-01-06 17:38:03.000000','2026-01-06 17:38:03.000000',0,'free'),('6436aa66-b2d3-4847-a8dd-c625dcbaff33','sv5','SV5','sv5@gmail.com','$2y$10$q6drSijfP6/quK1laAeunOFMlZvzOp8QBFILa8Z0wTUEiDJa16owG','2026-01-28 09:03:54.000000','2026-01-28 23:32:45.000000',0,'free'),('665c60a5-ac6b-49fa-82c1-34e97bc3b361','sv4','SV4','sv4@gmail.com','$2y$10$yncTttba7fNRwUyw.uzEl.kFvoQsAgmxPddXbSVRSqLK20B9wOJbS','2026-01-28 09:03:11.000000','2026-01-28 09:03:11.000000',0,'free'),('8ad6fb11-4466-4019-a956-4279229a6f42','AnhSang','Sang Hồ 1','sangoclon12@gmail.com','$2y$10$YyXZOoMOdlC/nw42PfS52eC7LzncPbij.N1U7V7ieI.laeFKuKZai','2026-01-06 17:41:04.000000','2026-01-30 02:59:01.783191',0,'free'),('90007af7-f330-4cc2-82d0-3282783ecc4b','sv9','SV9','sv9@gmail.com','$2b$12$5qS6cxyx1HmrUk.gcDpD5uDRPaeUoh8DRR93l7xWDEJRlfUgemcyy','2026-01-28 09:13:11.000000','2026-01-30 02:07:08.675236',0,'free'),('ADM001','admin1','Administrator','admin@studyhub.com','$2y$10$RXmWvlZz.EGmKbtK6TY4/urC2OtKZuyO694YR5uVZd1nrljlAaGK.','2026-01-06 00:56:57.479687','2026-01-28 08:23:26.000000',0,'admin'),('ca2202af-5b0d-4d05-a20b-1bcb62d9a518','testuser_9285','Test User','test_2432@example.com','$2y$10$pQOv3fXiA8D.VSyA7ZkCx.Yh/9ACA.9E/HGvMnXVVtcJ8hok6EVom','2026-01-06 00:57:05.293216','2026-01-06 00:57:05.293216',0,'free'),('cff04a27-fc4e-11f0-aa8f-74d4dd2c201f','newuser1','New User 1','newuser1@gmail.com','$2y$10$S7MxEkpCjlQX1chakyOtE.DDgJDZeEP43j9TiOFMfdqeJ7Wozwudm','2026-01-23 20:39:47.000000','2026-01-29 08:31:31.000000',0,'free'),('cff04a70-fc4e-11f0-aa8f-74d4dd2c201f','newuser2','New User 2','newuser2@gmail.com','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi','2026-01-26 20:39:47.000000','2026-01-28 20:39:47.476099',0,'free'),('cff04a7f-fc4e-11f0-aa8f-74d4dd2c201f','newuser3','New User 3','newuser3@gmail.com','$2y$10$Xu1DK7Jsp7ze0GUX7Z0Gt.ITJiiu6ruNjt4lVrca8uvgEfuYdSZv2','2026-01-27 20:39:47.000000','2026-01-28 23:44:18.000000',0,'free'),('d00a31f3-0661-4e31-b6ba-82a802e6c1e3','admin','Hồ Anh Sang','hoanhsang242006@gmail.com','$2y$10$wpICYFC7jPDzU9t0nfl7DeFbxBRj1oCU.AlcqYmxeNNUAfaAgPDyq','2026-01-29 02:29:46.000000','2026-01-29 08:30:08.147530',0,'admin'),('de130b56-2a3e-4878-a723-0ac2be003ad9','sang_admin','Sang Hồ','hoanhsang24@gmail.com','$2y$10$zL7apBE.nIitp1UMx0bp1OmfBBB.qN4YOKrW34xCtcZCfRTjlo1Xi','2026-01-06 23:06:25.720588','2026-01-06 23:06:25.720588',0,'free'),('ec8c547c-76d8-4c11-addd-a7ce2c669f16','sv6','SV6','sv6@gmail.com','$2y$10$xBp1bsXJ/OjLbXUrBFesBeCcqnrQFKcrXmilyCKuGhw.Gd5UWNrd.','2026-01-28 09:04:18.000000','2026-01-28 09:04:18.000000',0,'free'),('STU001','student1','Nguyen Van A','student1@studyhub.com','$2y$10$KoymNEBcNBM1rWnTDSIEEOeH7HY.AXYmhSAIX3jWqj/9EfYd8WNPC','2026-01-06 00:56:57.479687','2026-01-30 01:32:00.365216',1,'student'),('STU002','student2','Tran Thi B','student2@studyhub.com','$2y$10$XoL9uKMvuHIa4nEg1jbKv.FuMt1Rctr89FUz2UR9W976v/LOLAE0i','2026-01-06 00:56:57.479687','2026-01-28 08:19:48.000000',0,'student');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users_avatar`
--

DROP TABLE IF EXISTS `users_avatar`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users_avatar` (
  `user_id` varchar(36) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id` (`user_id`),
  UNIQUE KEY `path` (`path`),
  CONSTRAINT `UsersAvatar_user_id_f281c5b2_fk_User_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_avatar`
--

LOCK TABLES `users_avatar` WRITE;
/*!40000 ALTER TABLE `users_avatar` DISABLE KEYS */;
INSERT INTO `users_avatar` VALUES ('8ad6fb11-4466-4019-a956-4279229a6f42','assets/images/avatars/avatar_8ad6fb11-4466-4019-a956-4279229a6f42_1769718387.png'),('d00a31f3-0661-4e31-b6ba-82a802e6c1e3','assets/images/avatars/avatar_d00a31f3-0661-4e31-b6ba-82a802e6c1e3_1769740592.png');
/*!40000 ALTER TABLE `users_avatar` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-31 13:52:49
