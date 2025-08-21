-- MySQL dump 10.13  Distrib 8.0.19, for Win64 (x86_64)
--
-- Host: localhost    Database: laravel
-- ------------------------------------------------------
-- Server version	8.4.2

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `departments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `institute_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(4) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `departments_name_unique` (`name`),
  UNIQUE KEY `departments_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` (`institute_id`, `name`, `code`, `description`, `status`, `created_at`) VALUES
('FIM', 'Bahagian Pengurusan Sumber Manusia', 'HRD', '-', 'Active', NOW()),
('FIM', 'Bahagian Akaun', 'ACC', '-', 'Active', NOW()),
('FIM', 'Bahagian Kewangan', 'FIN', '-', 'Active', NOW()),
('FIM', 'Bahagian Pembangunan', 'DEV', '-', 'Active', NOW()),
('FIM', 'Bahagian Dasar Dan Penyelidikan', 'BDP', '-', 'Active', NOW()),
('FIM', 'Bahagian Perancangan Strategik', 'BPS', '-', 'Active', NOW()),
('FIM', 'Bahagian Hubungan Antarabangsa', 'BHA', '-', 'Active', NOW()),
('FIM', 'Bahagian Khidmat Pengurusan', 'BKP', '-', 'Active', NOW()),
('FIM', 'Bahagian Biasiswa', 'BSC', '-', 'Active', NOW()),
('FIM', 'Bahagian Pengurusan Maklumat', 'BPM', '-', 'Active', NOW()),
('FIM', 'Unit Undang-Undang', 'LAW', '-', 'Active', NOW()),
('FIM', 'Unit Audit Dalam', 'AUD', '-', 'Active', NOW()),
('FIM', 'Unit Komunikasi Korporat', 'BKK', '-', 'Active', NOW()),
('FIM', 'Unit Integriti', 'INT', '-', 'Active', NOW()),
('FIM', 'Bahagian Kecemerlangan Akademik (BKA)', 'BKA', '-', 'Active', NOW()),
('FIM', 'Bahagian Kemasukan Pelajar', 'KPA', '-', 'Active', NOW()),
('FIM', 'Bahagian Hal Ehwal Pelajar (BHEP)', 'HEP', '-', 'Active', NOW()),
('FIM', 'Bahagian Penguatkuasaan dan Inspektorat (BPI)', 'BPI', '-', 'Active', NOW()),
('FIM', 'Bahagian Koordinasi TVET', 'BKT', '-', 'Active', NOW()),
('FIM', 'Bahagian Perancangan Program dan Institusi', 'BPPI', '-', 'Active', NOW()),
('FIM', 'Pusat Penyelidikan dan Inovasi', 'PPI', '-', 'Active', NOW()),
('FIM', 'Bahagian Governan dan Kecemerlangan', 'BGK', '-', 'Active', NOW()),
('FIM', 'Bahagian Kolaborasi Industri dan Komuniti', 'BKIK', '-', 'Active', NOW()),
('FIM', 'Bahagian Kurikulum', 'BKU', '-', 'Active', NOW()),
('FIM', 'Bahagian Instruksional Dan Pembelajaran Digital', 'BIPD', '-', 'Active', NOW()),
('FIM', 'Bahagian Peperiksaan & Penilaian', 'BPP', '-', 'Active', NOW()),
('FIM', 'Bahagian Kompetensi dan Peningkatan Kerjaya', 'BKPK', '-', 'Active', NOW()),
('FIM', 'Bahagian Ambilan & Pembangunan Pelajar', 'BAPP', '-', 'Active', NOW()),
('FIM', 'Pasukan Petugas ICT', 'ICT', '-', 'Active', NOW()),
('FIM', 'Bahagian Teknologi Maklumat', 'BTM', '-', 'Active', NOW());


/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-08-21 16:32:58
