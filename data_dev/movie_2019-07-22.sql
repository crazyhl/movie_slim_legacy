# ************************************************************
# Sequel Pro SQL dump
# Version 5446
#
# https://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.7.26)
# Database: movie
# Generation Time: 2019-07-22 11:12:44 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
SET NAMES utf8mb4;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table category
# ------------------------------------------------------------

LOCK TABLES `category` WRITE;
/*!40000 ALTER TABLE `category` DISABLE KEYS */;

INSERT INTO `category` (`id`, `name`, `slug`, `parent_id`, `is_show`, `order`, `created_at`, `updated_at`)
VALUES
	(1,'电影片','电影片',0,1,0,'2019-07-21 21:13:30','2019-07-21 21:13:30'),
	(2,'连续剧','连续剧',0,1,0,'2019-07-21 21:13:52','2019-07-21 21:13:52'),
	(3,'综艺片','综艺片',0,1,0,'2019-07-21 21:14:29','2019-07-21 21:14:29'),
	(4,'动漫片','动漫片',0,1,0,'2019-07-21 21:14:42','2019-07-21 21:14:42'),
	(5,'动作片','动作片',1,1,0,'2019-07-21 21:15:04','2019-07-21 21:19:07'),
	(6,'喜剧片','喜剧片',1,1,0,'2019-07-21 21:30:10','2019-07-21 21:37:22'),
	(7,'爱情片','爱情片',1,1,0,'2019-07-21 21:37:37','2019-07-21 21:37:37'),
	(8,'科幻片','科幻片',1,1,0,'2019-07-21 21:38:41','2019-07-21 21:38:41'),
	(9,'恐怖片','恐怖片',1,1,0,'2019-07-21 21:38:54','2019-07-21 21:38:54'),
	(10,'剧情片','剧情片',1,1,0,'2019-07-21 21:39:07','2019-07-21 21:39:07'),
	(11,'战争片','战争片',1,1,0,'2019-07-21 21:39:20','2019-07-21 21:39:20'),
	(12,'国产剧','国产剧',2,1,0,'2019-07-21 21:41:44','2019-07-21 21:41:44'),
	(13,'香港剧','香港剧',2,1,0,'2019-07-21 21:42:02','2019-07-21 21:42:02'),
	(14,'韩国剧','韩国剧',2,1,0,'2019-07-21 21:42:15','2019-07-21 21:42:15'),
	(15,'欧美剧','欧美剧',2,1,0,'2019-07-21 21:42:37','2019-07-21 21:42:37'),
	(16,'福利片','福利片',0,1,0,'2019-07-21 21:42:50','2019-07-21 21:42:50'),
	(17,'伦理片','伦理片',0,1,0,'2019-07-21 21:43:02','2019-07-21 21:43:02'),
	(18,'音乐片','音乐片',0,1,0,'2019-07-21 21:43:15','2019-07-21 21:43:15'),
	(19,'台湾剧','台湾剧',2,1,0,'2019-07-21 21:43:35','2019-07-21 21:43:35'),
	(20,'日本剧','日本剧',2,1,0,'2019-07-21 21:43:50','2019-07-21 21:43:50'),
	(21,'海外剧','海外剧',2,1,0,'2019-07-21 21:44:12','2019-07-21 21:44:12');

/*!40000 ALTER TABLE `category` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table source_movie_website
# ------------------------------------------------------------

LOCK TABLES `source_movie_website` WRITE;
/*!40000 ALTER TABLE `source_movie_website` DISABLE KEYS */;

INSERT INTO `source_movie_website` (`id`, `name`, `api_url`, `status`, `created_at`, `updated_at`)
VALUES
	(1,'最大资源','http://www.zdziyuan.com/inc/api_zuidam3u8.php',1,'2019-07-21 14:38:06','2019-07-21 16:41:35');

/*!40000 ALTER TABLE `source_movie_website` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table source_website_category_local_category_relation
# ------------------------------------------------------------

LOCK TABLES `source_website_category_local_category_relation` WRITE;
/*!40000 ALTER TABLE `source_website_category_local_category_relation` DISABLE KEYS */;

INSERT INTO `source_website_category_local_category_relation` (`source_website_id`, `source_website_category_id`, `local_category_id`, `is_show`)
VALUES
	(1,1,1,1),
	(1,2,2,1),
	(1,3,3,1),
	(1,4,4,1),
	(1,5,5,1),
	(1,6,6,1),
	(1,7,7,1),
	(1,8,8,1),
	(1,9,9,1),
	(1,10,10,1),
	(1,11,11,1),
	(1,12,12,1),
	(1,13,13,1),
	(1,14,14,1),
	(1,15,15,1),
	(1,16,16,1),
	(1,17,17,1),
	(1,18,18,1),
	(1,19,19,1),
	(1,20,20,1),
	(1,21,21,1);

/*!40000 ALTER TABLE `source_website_category_local_category_relation` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
