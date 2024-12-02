/*
SQLyog Community v13.1.7 (64 bit)
MySQL - 10.4.32-MariaDB : Database - library_zafra
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`library_zafra` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `library_zafra`;

/*Table structure for table `authors` */

DROP TABLE IF EXISTS `authors`;

CREATE TABLE `authors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `book_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `book_id` (`book_id`),
  CONSTRAINT `authors_ibfk_1` FOREIGN KEY (`book_id`) REFERENCES `books` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `authors` */

insert  into `authors`(`id`,`name`,`book_id`) values 
(6,'verse',8),
(7,'hoyo',9),
(8,'lav',10);

/*Table structure for table `books` */

DROP TABLE IF EXISTS `books`;

CREATE TABLE `books` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `title` (`title`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `books` */

insert  into `books`(`id`,`title`) values 
(9,'mmm'),
(10,'valbook'),
(8,'zonezero');

/*Table structure for table `user_tokens` */

DROP TABLE IF EXISTS `user_tokens`;

CREATE TABLE `user_tokens` (
  `token_id` int(11) NOT NULL AUTO_INCREMENT,
  `userid` int(11) NOT NULL,
  `token` varchar(512) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`token_id`),
  UNIQUE KEY `token` (`token`),
  KEY `userid` (`userid`),
  CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`userid`) REFERENCES `users` (`userid`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `user_tokens` */

insert  into `user_tokens`(`token_id`,`userid`,`token`,`created_at`) values 
(1,1,'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeV96YWZyYS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeV96YWZyYS5jb20iLCJpYXQiOjE3MzAwNTQ0MDIsImV4cCI6MTczMDA1ODAwMiwiZGF0YSI6eyJ1c2VyaWQiOjF9fQ.0M0O5KMN_GrQ1dtz0qYS0jKTXNUFIGO1_yryjbrTVtk','2024-10-28 02:40:02'),
(3,1,'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeV96YWZyYS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeV96YWZyYS5jb20iLCJpYXQiOjE3MzAwNTUyODEsImV4cCI6MTczMDA1ODg4MSwiZGF0YSI6eyJ1c2VyaWQiOjF9fQ.fN2rwvN-ds_C3mXVcivNdAPg9m8Bm2WFqXoUXolEUCs','2024-10-28 02:54:41'),
(4,1,'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeV96YWZyYS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeV96YWZyYS5jb20iLCJpYXQiOjE3MzAwNTUzOTMsImV4cCI6MTczMDA1ODk5MywiZGF0YSI6eyJ1c2VyaWQiOjF9fQ.5olmsjR9f_Wjwr3K7xl8zW36VPv1XDgh3_yzzWvJ85s','2024-10-28 02:56:33'),
(5,1,'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeV96YWZyYS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeV96YWZyYS5jb20iLCJpYXQiOjE3MzAwNTU1MzIsImV4cCI6MTczMDA1OTEzMiwiZGF0YSI6eyJ1c2VyaWQiOjF9fQ.CgHcMKTQfWTkayBfHuHFYrMtlGRUX71nDOy5uYWUQ78','2024-10-28 02:58:52'),
(7,1,'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeV96YWZyYS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeV96YWZyYS5jb20iLCJpYXQiOjE3MzAwNTU3MDEsImV4cCI6MTczMDA1OTMwMSwiZGF0YSI6eyJ1c2VyaWQiOjF9fQ.kGlZueOjHoPIwqP-jAW9ZeUC2Bu3RLKLpL9qi7AFBuI','2024-10-28 03:01:41'),
(8,1,'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeV96YWZyYS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeV96YWZyYS5jb20iLCJpYXQiOjE3MzAwNTU3MTMsImV4cCI6MTczMDA1OTMxMywiZGF0YSI6eyJ1c2VyaWQiOjF9fQ.2i3aqxHf6QnIH3tv2oi44T4m_92VhbTnS7dSy1lTVhg','2024-10-28 03:01:53'),
(9,1,'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeV96YWZyYS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeV96YWZyYS5jb20iLCJpYXQiOjE3MzAwNTU3MzAsImV4cCI6MTczMDA1OTMzMCwiZGF0YSI6eyJ1c2VyaWQiOjF9fQ.Qcoyjlz6D1C7BbsIuZI_B_Lwtb7UrxK5QG_bDtYnF4Y','2024-10-28 03:02:10'),
(28,2,'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeV96YWZyYS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeV96YWZyYS5jb20iLCJpYXQiOjE3MzAwNTc2MjQsImRhdGEiOnsidXNlcmlkIjoyfX0.VKOpiwricugvH0U9uGVortoMwpbAPAoUiZySVrolr6M','2024-10-28 03:33:44'),
(32,3,'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vbGlicmFyeV96YWZyYS5vcmciLCJhdWQiOiJodHRwOi8vbGlicmFyeV96YWZyYS5jb20iLCJpYXQiOjE3MzMxMzA3NjgsImRhdGEiOnsidXNlcmlkIjozfX0.xoL0Y4-TgjVZD-NhK0xWeKUsoi3LqUy4RvrwO1uZJ_4','2024-12-02 17:12:48');

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  PRIMARY KEY (`userid`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

/*Data for the table `users` */

insert  into `users`(`userid`,`username`,`password`) values 
(1,'zafra','$2y$10$y/5vG1WTkEToGrRG7Xu0DuOmycGomzA7MzvRYEiUTTo5sTVIhdmRG'),
(2,'val','$2y$10$l4kH0NQ.rDU896qr/xPUUOZbTGZxpaOIXwPKhh4qX.7/FVJ0Yz8D2'),
(3,'lav','$2y$10$.qqlGKMMHGLg9VOGd1/GkecSta5.AQuE99EwRHhwXIcrXUCGS3..i');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
