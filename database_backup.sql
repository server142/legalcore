/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 8.0.32 : Database - despacho
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`despacho_db` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci */ /*!80016 DEFAULT ENCRYPTION='N' */;

USE `despacho_db`;

/*Table structure for table `actuaciones` */

DROP TABLE IF EXISTS `actuaciones`;

CREATE TABLE `actuaciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `expediente_id` bigint unsigned NOT NULL,
  `fecha` date NOT NULL,
  `titulo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_vencimiento` date DEFAULT NULL,
  `es_plazo` tinyint(1) NOT NULL DEFAULT '0',
  `estado` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `actuaciones_tenant_id_foreign` (`tenant_id`),
  KEY `actuaciones_expediente_id_foreign` (`expediente_id`),
  CONSTRAINT `actuaciones_expediente_id_foreign` FOREIGN KEY (`expediente_id`) REFERENCES `expedientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `actuaciones_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `actuaciones` */

insert  into `actuaciones`(`id`,`tenant_id`,`expediente_id`,`fecha`,`titulo`,`descripcion`,`fecha_vencimiento`,`es_plazo`,`estado`,`created_at`,`updated_at`,`deleted_at`) values (1,2,1,'2026-01-15','notificación','se notifica',NULL,0,'pendiente','2026-01-15 02:30:42','2026-01-15 02:30:42',NULL),(2,2,1,'2026-01-16','Presentar Recurso de Apelación','debemos apurarnos','2026-01-19',1,'cumplido','2026-01-16 14:14:03','2026-01-16 14:14:58',NULL);

/*Table structure for table `asesorias` */

DROP TABLE IF EXISTS `asesorias`;

CREATE TABLE `asesorias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned DEFAULT NULL,
  `abogado_id` bigint unsigned NOT NULL,
  `factura_id` bigint unsigned DEFAULT NULL,
  `expediente_id` bigint unsigned DEFAULT NULL,
  `folio` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('telefonica','videoconferencia','presencial') COLLATE utf8mb4_unicode_ci NOT NULL,
  `estado` enum('agendada','realizada','cancelada','no_atendida') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'agendada',
  `nombre_prospecto` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `telefono` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `asunto` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `notas` text COLLATE utf8mb4_unicode_ci,
  `fecha_hora` datetime NOT NULL,
  `duracion_minutos` int NOT NULL DEFAULT '30',
  `motivo_cancelacion` text COLLATE utf8mb4_unicode_ci,
  `motivo_no_atencion` text COLLATE utf8mb4_unicode_ci,
  `resumen` text COLLATE utf8mb4_unicode_ci,
  `prospecto_acepto` tinyint(1) DEFAULT NULL,
  `costo` decimal(10,2) NOT NULL DEFAULT '0.00',
  `pagado` tinyint(1) NOT NULL DEFAULT '0',
  `fecha_pago` datetime DEFAULT NULL,
  `link_videoconferencia` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `asesorias_folio_unique` (`folio`),
  KEY `asesorias_cliente_id_foreign` (`cliente_id`),
  KEY `asesorias_factura_id_foreign` (`factura_id`),
  KEY `asesorias_expediente_id_foreign` (`expediente_id`),
  KEY `asesorias_tenant_id_estado_fecha_hora_index` (`tenant_id`,`estado`,`fecha_hora`),
  KEY `asesorias_abogado_id_fecha_hora_index` (`abogado_id`,`fecha_hora`),
  CONSTRAINT `asesorias_abogado_id_foreign` FOREIGN KEY (`abogado_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `asesorias_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `asesorias_expediente_id_foreign` FOREIGN KEY (`expediente_id`) REFERENCES `expedientes` (`id`) ON DELETE SET NULL,
  CONSTRAINT `asesorias_factura_id_foreign` FOREIGN KEY (`factura_id`) REFERENCES `facturas` (`id`) ON DELETE SET NULL,
  CONSTRAINT `asesorias_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `asesorias` */

/*Table structure for table `audit_logs` */

DROP TABLE IF EXISTS `audit_logs`;

CREATE TABLE `audit_logs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned NOT NULL,
  `accion` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `modulo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `metadatos` text COLLATE utf8mb4_unicode_ci,
  `ip_address` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `audit_logs_tenant_id_foreign` (`tenant_id`),
  KEY `audit_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `audit_logs_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `audit_logs` */

insert  into `audit_logs`(`id`,`tenant_id`,`user_id`,`accion`,`modulo`,`descripcion`,`metadatos`,`ip_address`,`created_at`,`updated_at`) values (1,2,3,'upload','documentos','Subió el archivo: WhatsApp Ptt 2026-01-14 at 3.57.57 PM.ogg','{\"expediente_id\":1,\"extension\":\"ogg\"}','127.0.0.1','2026-01-15 02:06:38','2026-01-15 02:06:38'),(2,2,3,'upload','documentos','Subió el archivo: XAL_DXI_471_2023_CEJUM_REASvsCarlosS_segunda_parte.pdf','{\"expediente_id\":1,\"extension\":\"pdf\"}','127.0.0.1','2026-01-15 02:06:53','2026-01-15 02:06:53'),(3,2,3,'upload','documentos','Subió el archivo: XAL-DXI-CEJUM471-2023_SolicitudAccesoExpePenal_15122025.docx','{\"expediente_id\":1,\"extension\":\"docx\"}','127.0.0.1','2026-01-15 02:06:53','2026-01-15 02:06:53'),(4,2,3,'upload','documentos','Subió el archivo: XAL-DXI-CEJUM471-2023_SolicitudAccesoExpePenal_MonoAbogadosSIN_el2_15122025.docx','{\"expediente_id\":1,\"extension\":\"docx\"}','127.0.0.1','2026-01-15 02:06:53','2026-01-15 02:06:53'),(5,2,3,'view','documentos','Consultó el archivo: XAL_DXI_471_2023_CEJUM_REASvsCarlosS_segunda_parte.pdf','{\"documento_id\":2}','127.0.0.1','2026-01-15 02:06:56','2026-01-15 02:06:56'),(6,2,3,'view','documentos','Consultó el archivo: XAL_DXI_471_2023_CEJUM_REASvsCarlosS_segunda_parte.pdf','{\"documento_id\":2}','127.0.0.1','2026-01-15 02:12:48','2026-01-15 02:12:48'),(7,2,3,'view','documentos','Consultó el archivo: XAL_DXI_471_2023_CEJUM_REASvsCarlosS_segunda_parte.pdf','{\"documento_id\":2}','127.0.0.1','2026-01-15 02:13:03','2026-01-15 02:13:03'),(8,2,3,'delete','documentos','Eliminó el archivo: XAL_DXI_471_2023_CEJUM_REASvsCarlosS_segunda_parte.pdf','{\"documento_id\":2}','127.0.0.1','2026-01-15 02:13:11','2026-01-15 02:13:11'),(9,2,3,'view','documentos','Consultó el archivo: WhatsApp Ptt 2026-01-14 at 3.57.57 PM.ogg','{\"documento_id\":1}','127.0.0.1','2026-01-15 02:13:15','2026-01-15 02:13:15'),(10,2,3,'upload','documentos','Subió el archivo: WhatsApp Video 2025-12-02 at 12.52.43 PM.mp4','{\"expediente_id\":1,\"extension\":\"mp4\"}','127.0.0.1','2026-01-15 02:13:48','2026-01-15 02:13:48'),(11,2,3,'view','documentos','Consultó el archivo: WhatsApp Video 2025-12-02 at 12.52.43 PM.mp4','{\"documento_id\":5}','127.0.0.1','2026-01-15 02:14:23','2026-01-15 02:14:23'),(12,2,3,'view','documentos','Consultó el archivo: WhatsApp Video 2025-12-02 at 12.52.43 PM.mp4','{\"documento_id\":5}','127.0.0.1','2026-01-15 02:14:23','2026-01-15 02:14:23'),(13,2,3,'view','documentos','Consultó el archivo: WhatsApp Video 2025-12-02 at 12.52.43 PM.mp4','{\"documento_id\":5}','127.0.0.1','2026-01-15 02:14:28','2026-01-15 02:14:28'),(14,2,2,'view','documentos','Consultó el archivo: WhatsApp Ptt 2026-01-14 at 3.57.57 PM.ogg','{\"documento_id\":1}','127.0.0.1','2026-01-15 03:07:41','2026-01-15 03:07:41'),(15,2,2,'view','documentos','Consultó el archivo: WhatsApp Ptt 2026-01-14 at 3.57.57 PM.ogg','{\"documento_id\":1}','127.0.0.1','2026-01-15 03:07:41','2026-01-15 03:07:41'),(16,2,2,'view','documentos','Consultó el archivo: WhatsApp Video 2025-12-02 at 12.52.43 PM.mp4','{\"documento_id\":5}','127.0.0.1','2026-01-15 03:07:59','2026-01-15 03:07:59'),(17,2,2,'view','documentos','Consultó el archivo: WhatsApp Video 2025-12-02 at 12.52.43 PM.mp4','{\"documento_id\":5}','127.0.0.1','2026-01-15 03:08:00','2026-01-15 03:08:00'),(18,2,2,'view','documentos','Consultó el archivo: WhatsApp Video 2025-12-02 at 12.52.43 PM.mp4','{\"documento_id\":5}','127.0.0.1','2026-01-15 03:08:00','2026-01-15 03:08:00'),(19,2,2,'view','documentos','Consultó el archivo: WhatsApp Video 2025-12-02 at 12.52.43 PM.mp4','{\"documento_id\":5}','127.0.0.1','2026-01-15 03:08:01','2026-01-15 03:08:01'),(20,2,2,'view','documentos','Consultó el archivo: WhatsApp Video 2025-12-02 at 12.52.43 PM.mp4','{\"documento_id\":5}','127.0.0.1','2026-01-15 03:08:05','2026-01-15 03:08:05'),(21,2,2,'view','documentos','Consultó el archivo: WhatsApp Ptt 2026-01-14 at 3.57.57 PM.ogg','{\"documento_id\":1}','127.0.0.1','2026-01-15 04:41:42','2026-01-15 04:41:42'),(22,2,2,'view','documentos','Consultó el archivo: WhatsApp Ptt 2026-01-14 at 3.57.57 PM.ogg','{\"documento_id\":1}','127.0.0.1','2026-01-15 04:41:43','2026-01-15 04:41:43'),(23,2,2,'upload','documentos','Subió el archivo: factura-2.pdf','{\"expediente_id\":1,\"extension\":\"pdf\"}','127.0.0.1','2026-01-15 16:34:04','2026-01-15 16:34:04'),(24,2,2,'view','documentos','Consultó el archivo: factura-2.pdf','{\"documento_id\":6}','127.0.0.1','2026-01-15 16:34:07','2026-01-15 16:34:07'),(25,2,3,'send_message','mensajes','Envió un mensaje a Admin Méndez','{\"mensaje_id\":1,\"receiver_id\":\"2\"}','127.0.0.1','2026-01-16 15:20:28','2026-01-16 15:20:28'),(26,2,2,'send_message','mensajes','Envió un mensaje a Lic. Juan Pérez','{\"mensaje_id\":2}','127.0.0.1','2026-01-16 16:07:43','2026-01-16 16:07:43'),(27,2,3,'send_message','mensajes','Envió un mensaje a Admin Méndez','{\"mensaje_id\":3}','127.0.0.1','2026-01-16 16:10:15','2026-01-16 16:10:15'),(28,2,2,'update_assignments','expedientes','Actualizó las asignaciones del expediente 123/2024','{\"expediente_id\":1,\"assigned_users\":[\"3\"]}','127.0.0.1','2026-01-19 03:43:31','2026-01-19 03:43:31'),(29,2,2,'change_responsible','expedientes','Cambió el abogado responsable del expediente 123/2024','{\"expediente_id\":1,\"old_responsible\":3,\"new_responsible\":3}','127.0.0.1','2026-01-19 03:44:32','2026-01-19 03:44:32'),(30,2,2,'update_assignments','expedientes','Actualizó las asignaciones del expediente 123/2024','{\"expediente_id\":1,\"assigned_users\":[3]}','127.0.0.1','2026-01-19 03:47:02','2026-01-19 03:47:02'),(31,2,2,'update_assignments','expedientes','Actualizó las asignaciones del expediente 123/2024','{\"expediente_id\":1,\"assigned_users\":[]}','127.0.0.1','2026-01-19 03:47:06','2026-01-19 03:47:06'),(32,2,2,'update_assignments','expedientes','Actualizó las asignaciones del expediente 123/2024','{\"expediente_id\":1,\"assigned_users\":[\"3\"]}','127.0.0.1','2026-01-19 03:47:08','2026-01-19 03:47:08'),(33,2,2,'update_assignments','expedientes','Actualizó las asignaciones del expediente 123/2024','{\"expediente_id\":1,\"assigned_users\":[\"3\"]}','127.0.0.1','2026-01-19 03:47:10','2026-01-19 03:47:10'),(34,2,2,'update_assignments','expedientes','Actualizó las asignaciones del expediente 123/2024','{\"expediente_id\":1,\"assigned_users\":[\"3\"]}','127.0.0.1','2026-01-19 03:47:12','2026-01-19 03:47:12'),(35,2,2,'update_assignments','expedientes','Actualizó las asignaciones del expediente 123/2024','{\"expediente_id\":1,\"assigned_users\":[]}','127.0.0.1','2026-01-19 03:47:14','2026-01-19 03:47:14'),(36,2,2,'update_assignments','expedientes','Actualizó las asignaciones del expediente 123/2024','{\"expediente_id\":1,\"assigned_users\":[]}','127.0.0.1','2026-01-19 03:47:17','2026-01-19 03:47:17'),(37,2,2,'update_assignments','expedientes','Actualizó las asignaciones del expediente 123/2024','{\"expediente_id\":1,\"assigned_users\":[\"3\"]}','127.0.0.1','2026-01-19 03:47:19','2026-01-19 03:47:19'),(38,2,2,'change_responsible','expedientes','Cambió el abogado responsable del expediente 123/2024','{\"expediente_id\":1,\"old_responsible\":3,\"new_responsible\":3}','127.0.0.1','2026-01-19 03:47:21','2026-01-19 03:47:21'),(39,2,2,'change_responsible','expedientes','Cambió el abogado responsable del expediente 123/2024','{\"expediente_id\":1,\"old_responsible\":3,\"new_responsible\":3}','127.0.0.1','2026-01-19 03:47:22','2026-01-19 03:47:22'),(40,2,2,'update_assignments','expedientes','Actualizó las asignaciones del expediente 123/2024','{\"expediente_id\":1,\"assigned_users\":[\"3\"]}','127.0.0.1','2026-01-19 03:47:23','2026-01-19 03:47:23');

/*Table structure for table `cache` */

DROP TABLE IF EXISTS `cache`;

CREATE TABLE `cache` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cache` */

/*Table structure for table `cache_locks` */

DROP TABLE IF EXISTS `cache_locks`;

CREATE TABLE `cache_locks` (
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `cache_locks` */

/*Table structure for table `clientes` */

DROP TABLE IF EXISTS `clientes`;

CREATE TABLE `clientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo` enum('persona_fisica','persona_moral') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'persona_fisica',
  `rfc` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `direccion` text COLLATE utf8mb4_unicode_ci,
  `datos_fiscales` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `clientes_tenant_id_foreign` (`tenant_id`),
  CONSTRAINT `clientes_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `clientes` */

insert  into `clientes`(`id`,`tenant_id`,`nombre`,`tipo`,`rfc`,`email`,`telefono`,`direccion`,`datos_fiscales`,`created_at`,`updated_at`,`deleted_at`) values (1,2,'Empresa Patito S.A. de C.V.','persona_moral','EPA123456ABC','contacto@patito.com',NULL,NULL,NULL,'2026-01-15 00:10:14','2026-01-15 00:10:14',NULL),(2,2,'Cliente nuevo de juan','persona_fisica',NULL,'mail@mail.com','222222','cam ant a puebla\nCentro',NULL,'2026-01-16 14:53:39','2026-01-16 14:53:39',NULL),(3,3,'asdasd','persona_fisica','asd','asd@asd.com','asasd','asd',NULL,'2026-01-17 15:50:13','2026-01-17 15:50:13',NULL);

/*Table structure for table `comentario_reacciones` */

DROP TABLE IF EXISTS `comentario_reacciones`;

CREATE TABLE `comentario_reacciones` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `comentario_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `tipo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `comentario_reacciones_comentario_id_user_id_unique` (`comentario_id`,`user_id`),
  KEY `comentario_reacciones_user_id_foreign` (`user_id`),
  CONSTRAINT `comentario_reacciones_comentario_id_foreign` FOREIGN KEY (`comentario_id`) REFERENCES `comentarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comentario_reacciones_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `comentario_reacciones` */

/*Table structure for table `comentario_reads` */

DROP TABLE IF EXISTS `comentario_reads`;

CREATE TABLE `comentario_reads` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `comentario_id` bigint unsigned NOT NULL,
  `read_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `comentario_reads_user_id_comentario_id_unique` (`user_id`,`comentario_id`),
  KEY `comentario_reads_comentario_id_foreign` (`comentario_id`),
  CONSTRAINT `comentario_reads_comentario_id_foreign` FOREIGN KEY (`comentario_id`) REFERENCES `comentarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comentario_reads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `comentario_reads` */

/*Table structure for table `comentarios` */

DROP TABLE IF EXISTS `comentarios`;

CREATE TABLE `comentarios` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `expediente_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `parent_id` bigint unsigned DEFAULT NULL,
  `tenant_id` bigint unsigned NOT NULL,
  `contenido` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `comentarios_user_id_foreign` (`user_id`),
  KEY `comentarios_tenant_id_foreign` (`tenant_id`),
  KEY `comentarios_expediente_id_created_at_index` (`expediente_id`,`created_at`),
  KEY `comentarios_parent_id_foreign` (`parent_id`),
  CONSTRAINT `comentarios_expediente_id_foreign` FOREIGN KEY (`expediente_id`) REFERENCES `expedientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comentarios_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `comentarios` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comentarios_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comentarios_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `comentarios` */

/*Table structure for table `documentos` */

DROP TABLE IF EXISTS `documentos`;

CREATE TABLE `documentos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `expediente_id` bigint unsigned DEFAULT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `path` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `size` bigint NOT NULL DEFAULT '0',
  `extension` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `version` int NOT NULL DEFAULT '1',
  `uploaded_by` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `documentos_tenant_id_foreign` (`tenant_id`),
  KEY `documentos_expediente_id_foreign` (`expediente_id`),
  KEY `documentos_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `documentos_expediente_id_foreign` FOREIGN KEY (`expediente_id`) REFERENCES `expedientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documentos_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documentos_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `documentos` */

insert  into `documentos`(`id`,`tenant_id`,`expediente_id`,`nombre`,`path`,`size`,`extension`,`tipo`,`version`,`uploaded_by`,`created_at`,`updated_at`,`deleted_at`) values (1,2,1,'WhatsApp Ptt 2026-01-14 at 3.57.57 PM.ogg','documentos/1/Uv9fz4HrD9SNEP9wcRKc28Tc6hy0nRYRZAYrEnGt.ogg',0,'ogg','video',1,3,'2026-01-15 02:06:38','2026-01-15 02:06:38',NULL),(2,2,1,'XAL_DXI_471_2023_CEJUM_REASvsCarlosS_segunda_parte.pdf','documentos/1/D0aGXP2KgiHuBXSK0XSCaGyakbZJOV6ZhbxDeGTw.pdf',0,'pdf','pdf',1,3,'2026-01-15 02:06:53','2026-01-15 02:13:11','2026-01-15 02:13:11'),(3,2,1,'XAL-DXI-CEJUM471-2023_SolicitudAccesoExpePenal_15122025.docx','documentos/1/GSU34FxkKMWZQpg7EZnqva16pXrBAgmkCbzdtSO4.docx',0,'docx','word',1,3,'2026-01-15 02:06:53','2026-01-15 02:06:53',NULL),(4,2,1,'XAL-DXI-CEJUM471-2023_SolicitudAccesoExpePenal_MonoAbogadosSIN_el2_15122025.docx','documentos/1/GqGdcYEaTRAVhpI0zTJ5fetmp0F2RwveP81YMNKq.docx',0,'docx','word',1,3,'2026-01-15 02:06:53','2026-01-15 02:06:53',NULL),(5,2,1,'WhatsApp Video 2025-12-02 at 12.52.43 PM.mp4','documentos/1/gGfi1kkzLyhoZpUw9AAfLVwYG86dheAwSUa0jr3Z.mp4',0,'mp4','video',1,3,'2026-01-15 02:13:48','2026-01-15 02:13:48',NULL),(6,2,1,'factura-2.pdf','documentos/1/m2MPjRKHhsxmvsopTgcCYaBxtNZFJI4lLIH8NnUQ.pdf',0,'pdf','pdf',1,2,'2026-01-15 16:34:04','2026-01-15 16:34:04',NULL);

/*Table structure for table `evento_user` */

DROP TABLE IF EXISTS `evento_user`;

CREATE TABLE `evento_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `evento_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `evento_user_evento_id_foreign` (`evento_id`),
  KEY `evento_user_user_id_foreign` (`user_id`),
  CONSTRAINT `evento_user_evento_id_foreign` FOREIGN KEY (`evento_id`) REFERENCES `eventos` (`id`) ON DELETE CASCADE,
  CONSTRAINT `evento_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `evento_user` */

/*Table structure for table `eventos` */

DROP TABLE IF EXISTS `eventos`;

CREATE TABLE `eventos` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `titulo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `start_time` datetime NOT NULL,
  `end_time` datetime NOT NULL,
  `tipo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'cita',
  `user_id` bigint unsigned NOT NULL,
  `expediente_id` bigint unsigned DEFAULT NULL,
  `google_event_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `eventos_tenant_id_foreign` (`tenant_id`),
  KEY `eventos_user_id_foreign` (`user_id`),
  KEY `eventos_expediente_id_foreign` (`expediente_id`),
  CONSTRAINT `eventos_expediente_id_foreign` FOREIGN KEY (`expediente_id`) REFERENCES `expedientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `eventos_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `eventos_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `eventos` */

insert  into `eventos`(`id`,`tenant_id`,`titulo`,`descripcion`,`start_time`,`end_time`,`tipo`,`user_id`,`expediente_id`,`google_event_id`,`created_at`,`updated_at`,`deleted_at`) values (1,2,'asdasd',NULL,'2026-01-15 10:00:00','2026-01-15 11:00:00','audiencia',3,NULL,NULL,'2026-01-15 01:56:50','2026-01-15 01:56:50',NULL),(2,2,'dsd',NULL,'2026-01-15 10:00:00','2026-01-15 11:00:00','audiencia',3,NULL,NULL,'2026-01-15 01:58:19','2026-01-15 01:58:19',NULL),(3,2,'audiencia','veamos','2026-01-15 02:04:00','2026-01-15 03:04:00','audiencia',3,1,NULL,'2026-01-15 02:04:48','2026-01-15 02:04:48',NULL),(4,2,'asdad',NULL,'2026-01-16 10:00:00','2026-01-16 11:00:00','audiencia',2,1,NULL,'2026-01-15 22:57:18','2026-01-15 22:57:18',NULL),(5,1,'Test Debug Event 22:04:20','Debugging google calendar sync','2026-01-26 23:04:20','2026-01-27 00:04:20','cita',1,NULL,NULL,'2026-01-26 22:04:20','2026-01-26 22:04:20',NULL),(6,1,'Test Debug Event 22:05:40','Debugging google calendar sync','2026-01-26 23:05:40','2026-01-27 00:05:40','cita',1,NULL,NULL,'2026-01-26 22:05:40','2026-01-26 22:05:40',NULL),(7,1,'Test Debug Event 00:24:41','Debugging google calendar sync','2026-01-27 01:24:41','2026-01-27 02:24:41','cita',1,NULL,NULL,'2026-01-27 00:24:41','2026-01-27 00:24:41',NULL),(8,1,'Test Debug Event 00:26:34','Debugging google calendar sync','2026-01-27 01:26:34','2026-01-27 02:26:34','cita',1,NULL,NULL,'2026-01-27 00:26:34','2026-01-27 00:26:34',NULL);

/*Table structure for table `expediente_user` */

DROP TABLE IF EXISTS `expediente_user`;

CREATE TABLE `expediente_user` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `expediente_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `expediente_user_expediente_id_foreign` (`expediente_id`),
  KEY `expediente_user_user_id_foreign` (`user_id`),
  CONSTRAINT `expediente_user_expediente_id_foreign` FOREIGN KEY (`expediente_id`) REFERENCES `expedientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expediente_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `expediente_user` */

insert  into `expediente_user`(`id`,`expediente_id`,`user_id`,`created_at`,`updated_at`) values (3,1,3,NULL,NULL);

/*Table structure for table `expedientes` */

DROP TABLE IF EXISTS `expedientes`;

CREATE TABLE `expedientes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `numero` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `titulo` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `materia` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `juzgado` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_juez` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado_procesal` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'inicial',
  `cliente_id` bigint unsigned NOT NULL,
  `abogado_responsable_id` bigint unsigned NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `fecha_inicio` date DEFAULT NULL,
  `fecha_cierre` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `expedientes_numero_unique` (`numero`),
  KEY `expedientes_tenant_id_foreign` (`tenant_id`),
  KEY `expedientes_cliente_id_foreign` (`cliente_id`),
  KEY `expedientes_abogado_responsable_id_foreign` (`abogado_responsable_id`),
  CONSTRAINT `expedientes_abogado_responsable_id_foreign` FOREIGN KEY (`abogado_responsable_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expedientes_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `expedientes_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `expedientes` */

insert  into `expedientes`(`id`,`tenant_id`,`numero`,`titulo`,`materia`,`juzgado`,`nombre_juez`,`estado_procesal`,`cliente_id`,`abogado_responsable_id`,`descripcion`,`fecha_inicio`,`fecha_cierre`,`created_at`,`updated_at`,`deleted_at`) values (1,2,'123/2024','Patito vs SAT','Fiscal','Juzgado Primero de Distrito',NULL,'Ejecución',1,3,NULL,'2026-01-15',NULL,'2026-01-15 00:10:14','2026-01-15 23:07:32',NULL);

/*Table structure for table `facturas` */

DROP TABLE IF EXISTS `facturas`;

CREATE TABLE `facturas` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `cliente_id` bigint unsigned NOT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT '0.00',
  `iva` decimal(15,2) NOT NULL DEFAULT '0.00',
  `total` decimal(15,2) NOT NULL DEFAULT '0.00',
  `moneda` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MXN',
  `estado` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pendiente',
  `fecha_emision` datetime DEFAULT NULL,
  `fecha_vencimiento` datetime DEFAULT NULL,
  `uuid_fiscal` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `conceptos` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `facturas_tenant_id_foreign` (`tenant_id`),
  KEY `facturas_cliente_id_foreign` (`cliente_id`),
  CONSTRAINT `facturas_cliente_id_foreign` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `facturas_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `facturas` */

insert  into `facturas`(`id`,`tenant_id`,`cliente_id`,`subtotal`,`iva`,`total`,`moneda`,`estado`,`fecha_emision`,`fecha_vencimiento`,`uuid_fiscal`,`conceptos`,`created_at`,`updated_at`,`deleted_at`) values (1,2,1,0.00,0.00,100.00,'MXN','pagada',NULL,NULL,NULL,NULL,'2026-01-15 02:03:51','2026-01-15 02:03:51',NULL),(2,2,1,380.17,60.83,441.00,'MXN','pagada','2026-01-15 03:09:41','2026-02-14 03:09:41',NULL,NULL,'2026-01-15 03:09:41','2026-01-15 03:09:41',NULL),(3,2,1,968.10,154.90,1123.00,'MXN','pendiente','2026-01-15 22:56:28','2026-02-14 22:56:28',NULL,NULL,'2026-01-15 22:56:28','2026-01-15 22:56:28',NULL);

/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `failed_jobs` */

/*Table structure for table `global_settings` */

DROP TABLE IF EXISTS `global_settings`;

CREATE TABLE `global_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `description` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `global_settings_key_unique` (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `global_settings` */

/*Table structure for table `job_batches` */

DROP TABLE IF EXISTS `job_batches`;

CREATE TABLE `job_batches` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `job_batches` */

/*Table structure for table `jobs` */

DROP TABLE IF EXISTS `jobs`;

CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `jobs` */

insert  into `jobs`(`id`,`queue`,`payload`,`attempts`,`reserved_at`,`available_at`,`created_at`) values (1,'default','{\"uuid\":\"bc76262b-70f2-4560-8d7f-63ee8886ea7b\",\"displayName\":\"App\\\\Notifications\\\\WelcomeLawyer\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\",\"command\":\"O:48:\\\"Illuminate\\\\Notifications\\\\SendQueuedNotifications\\\":3:{s:11:\\\"notifiables\\\";O:45:\\\"Illuminate\\\\Contracts\\\\Database\\\\ModelIdentifier\\\":5:{s:5:\\\"class\\\";s:15:\\\"App\\\\Models\\\\User\\\";s:2:\\\"id\\\";a:1:{i:0;i:8;}s:9:\\\"relations\\\";a:0:{}s:10:\\\"connection\\\";s:5:\\\"mysql\\\";s:15:\\\"collectionClass\\\";N;}s:12:\\\"notification\\\";O:31:\\\"App\\\\Notifications\\\\WelcomeLawyer\\\":2:{s:8:\\\"password\\\";s:8:\\\"password\\\";s:2:\\\"id\\\";s:36:\\\"80e1439b-f56a-4c27-a8f6-aefe48c66c5e\\\";}s:8:\\\"channels\\\";a:1:{i:0;s:4:\\\"mail\\\";}}\"},\"createdAt\":1768800595,\"delay\":null}',0,NULL,1768800595,1768800595);

/*Table structure for table `juzgados` */

DROP TABLE IF EXISTS `juzgados`;

CREATE TABLE `juzgados` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `direccion` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `juzgados_tenant_id_foreign` (`tenant_id`),
  CONSTRAINT `juzgados_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `juzgados` */

/*Table structure for table `manual_pages` */

DROP TABLE IF EXISTS `manual_pages`;

CREATE TABLE `manual_pages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `image_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `manual_pages_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `manual_pages` */

insert  into `manual_pages`(`id`,`title`,`slug`,`content`,`image_path`,`order`,`created_at`,`updated_at`) values (1,'Introducción a LegalCore','introduccion-a-legalcore','Bienvenido a **LegalCore**, la plataforma integral diseñada para la gestión eficiente de su despacho jurídico. Este sistema ha sido desarrollado pensando en las necesidades críticas de los profesionales del derecho, permitiendo un control total sobre expedientes, términos procesales, agenda y facturación.\r\n\r\nEn este manual encontrará una guía detallada de cada módulo, con ejemplos prácticos y recomendaciones para optimizar su flujo de trabajo diario.',NULL,1,'2026-01-16 14:18:12','2026-01-16 14:18:12'),(2,'Panel de Control (Dashboard)','panel-de-control-dashboard','El **Dashboard** es su centro de mando. Al iniciar sesión, visualizará de forma inmediata el estado actual de su despacho:\r\n\r\n*   **Expedientes Activos:** Número total de casos en curso.\r\n*   **Vencimientos Próximos:** Alertas sobre términos que requieren atención inmediata.\r\n*   **Indicadores Financieros:** Resumen de montos cobrados y pendientes de cobro.\r\n*   **Últimos Expedientes:** Acceso rápido a los casos consultados recientemente.\r\n*   **Términos Urgentes:** Listado prioritario de plazos legales por vencer.\r\n\r\n**Ejemplo de uso:** Al iniciar su jornada, revise la sección de \"Términos Urgentes\" para priorizar las actuaciones del día.','manual/dashboard.png',2,'2026-01-16 14:18:12','2026-01-16 14:18:12'),(3,'Gestión de Expedientes','gestion-de-expedientes','El módulo de **Expedientes** permite centralizar toda la información de sus casos judiciales.\r\n\r\n**Funcionalidades clave:**\r\n1.  **Registro de Nuevo Expediente:** Capture el número de expediente, título, materia (Civil, Penal, Laboral, etc.), juzgado y abogado responsable.\r\n2.  **Seguimiento Procesal:** Actualice el estado del caso (En proceso, Ejecución, Suspendido, Cerrado).\r\n3.  **Búsqueda Avanzada:** Localice rápidamente cualquier expediente por su número o nombre de las partes.\r\n\r\n**Ejemplo de uso:** Para registrar un nuevo caso, haga clic en \"Nuevo Expediente\", complete los datos del juzgado y asigne el cliente correspondiente. Esto creará una ficha única donde podrá consultar toda la historia del proceso.','manual/expedientes.png',3,'2026-01-16 14:18:12','2026-01-16 14:18:12'),(4,'Administración de Clientes','administracion-de-clientes','Mantenga una base de datos organizada de sus representados en el módulo de **Clientes**.\r\n\r\n**Opciones disponibles:**\r\n*   **Personas Físicas y Morales:** El sistema permite diferenciar entre individuos y empresas.\r\n*   **Datos de Contacto:** Almacene correos electrónicos, teléfonos y RFC para fines de facturación.\r\n*   **Historial de Casos:** Desde el perfil del cliente, podrá visualizar todos los expedientes asociados a él.\r\n\r\n**Ejemplo de uso:** Antes de iniciar un nuevo expediente, asegúrese de registrar al cliente. Si es una empresa, capture el RFC correctamente para facilitar la emisión de facturas posteriores.','manual/clientes.png',4,'2026-01-16 14:18:12','2026-01-16 14:18:12'),(5,'Agenda Judicial','agenda-judicial','La **Agenda** es una herramienta visual (calendario) para coordinar las actividades del despacho.\r\n\r\n*   **Audiencias:** Registre fechas de audiencias con alertas automáticas.\r\n*   **Citas:** Gestione reuniones con clientes o contrapartes.\r\n*   **Colores por Tipo:** Identifique rápidamente la naturaleza del evento (Rojo para audiencias, Naranja para términos, Azul para citas).\r\n\r\n**Ejemplo de uso:** Al recibir una notificación de audiencia, regístrela en la agenda seleccionando el expediente relacionado. Esto permitirá que todos los abogados asignados al caso estén informados.','manual/agenda.png',5,'2026-01-16 14:18:12','2026-01-16 14:18:12'),(6,'Control de Términos Procesales','control-de-terminos-procesales','El módulo de **Términos** es crítico para evitar la preclusión de derechos y asegurar la vigencia de las actuaciones legales.\r\n\r\n**¿Cómo se registra un término?**\r\nEl registro se realiza siempre vinculado a un expediente específico para mantener la trazabilidad:\r\n1.  **Desde el Expediente:** Ingrese al caso y utilice el componente \"Agregar Actuación\".\r\n2.  **Definición:** Capture el título (ej. \"Presentar Recurso de Apelación\") y la fecha de notificación.\r\n3.  **Activación de Plazo:** Marque la casilla **\"Es plazo\"**. Esto habilitará el campo \"Fecha de Vencimiento\".\r\n4.  **Fecha Fatal:** Seleccione la fecha límite. El sistema asignará automáticamente el estado \"Pendiente\".\r\n\r\n**Resultados y Alertas:**\r\n*   **Dashboard:** El término se sumará al contador de \"Vencimientos Próximos\" y aparecerá en la lista de \"Términos Urgentes\" si faltan pocos días.\r\n*   **Módulo de Control:** Podrá filtrar por estado (Pendiente, Cumplido, Vencido) y marcar actuaciones como concluidas.\r\n*   **Historial:** La ficha del expediente mostrará visualmente el cumplimiento o retraso de cada plazo.\r\n\r\n**Ejemplo de uso:** Si registra hoy un término para el 20 de enero, el sistema lo resaltará en rojo en el Dashboard y lo pondrá al inicio de su lista de prioridades hasta que sea marcado como \"Cumplido\".','manual/terminos.png',6,'2026-01-16 14:18:12','2026-01-16 14:18:12'),(7,'Facturación y Cobranza','facturacion-y-cobranza','Gestione la salud financiera de su despacho en el módulo de **Facturación**.\r\n\r\n*   **Emisión de Facturas:** Cree comprobantes detallando honorarios y gastos.\r\n*   **Control de Pagos:** Marque facturas como \"Pagadas\" o \"Pendientes\".\r\n*   **Cálculo de Impuestos:** El sistema calcula automáticamente el IVA y subtotales.\r\n\r\n**Ejemplo de uso:** Al concluir una etapa procesal, genere la factura correspondiente al cliente. Podrá descargar el reporte en PDF para enviarlo por correo electrónico.','manual/facturacion.png',7,'2026-01-16 14:18:12','2026-01-16 14:18:12'),(8,'Bitácora de Seguridad','bitacora-de-seguridad','La **Bitácora** registra cada acción relevante realizada en el sistema.\r\n\r\n*   **Transparencia:** Sepa quién creó, modificó o eliminó un registro.\r\n*   **Auditoría:** Útil para revisiones internas y control de calidad.\r\n*   **Filtros por Módulo:** Busque acciones específicas realizadas en expedientes o facturación.\r\n\r\n**Ejemplo de uso:** Si un expediente fue modificado por error, consulte la bitácora para identificar qué usuario realizó el cambio y en qué fecha exacta.','manual/bitacora.png',8,'2026-01-16 14:18:12','2026-01-16 14:18:12'),(9,'Configuración del Despacho','configuracion-del-despacho','Personalice LegalCore para que se adapte a su identidad corporativa.\r\n\r\n*   **Datos del Titular:** Nombre del despacho y dirección oficial.\r\n*   **Logotipo:** Suba el logo de su firma para que aparezca en reportes y facturas.\r\n*   **Notificaciones SMS:** Configure el envío de recordatorios automáticos a clientes y abogados.\r\n\r\n**Ejemplo de uso:** Suba su logotipo en formato PNG de alta resolución para que sus reportes de expediente tengan una presentación profesional ante sus clientes.','manual/configuracion.png',9,'2026-01-16 14:18:12','2026-01-16 14:18:12');

/*Table structure for table `materias` */

DROP TABLE IF EXISTS `materias`;

CREATE TABLE `materias` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `nombre` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `materias_tenant_id_foreign` (`tenant_id`),
  CONSTRAINT `materias_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `materias` */

insert  into `materias`(`id`,`tenant_id`,`nombre`,`created_at`,`updated_at`) values (1,1,'Civil','2026-01-15 00:10:15','2026-01-15 00:10:15'),(2,1,'Penal','2026-01-15 00:10:15','2026-01-15 00:10:15'),(3,1,'Familiar','2026-01-15 00:10:15','2026-01-15 00:10:15'),(4,1,'Mercantil','2026-01-15 00:10:15','2026-01-15 00:10:15'),(5,1,'Laboral','2026-01-15 00:10:15','2026-01-15 00:10:15'),(6,1,'Administrativo','2026-01-15 00:10:15','2026-01-15 00:10:15'),(7,1,'Fiscal','2026-01-15 00:10:15','2026-01-15 00:10:15'),(8,1,'Amparo','2026-01-15 00:10:15','2026-01-15 00:17:23'),(9,1,'Constitucional','2026-01-15 00:10:15','2026-01-15 00:10:15'),(10,1,'Agrario','2026-01-15 00:10:15','2026-01-15 00:10:15'),(11,2,'Civil','2026-01-15 00:10:15','2026-01-15 00:10:15'),(12,2,'Penal','2026-01-15 00:10:15','2026-01-15 00:10:15'),(13,2,'Familiar','2026-01-15 00:10:15','2026-01-15 00:10:15'),(14,2,'Mercantil','2026-01-15 00:10:15','2026-01-15 00:10:15'),(15,2,'Laboral','2026-01-15 00:10:15','2026-01-15 00:10:15'),(16,2,'Administrativo','2026-01-15 00:10:15','2026-01-15 00:10:15'),(17,2,'Fiscal','2026-01-15 00:10:15','2026-01-15 00:10:15'),(18,2,'Amparo','2026-01-15 00:10:15','2026-01-15 00:10:15'),(19,2,'Constitucional','2026-01-15 00:10:15','2026-01-15 00:10:15'),(20,2,'Agrario','2026-01-15 00:10:15','2026-01-15 00:10:15');

/*Table structure for table `mensajes` */

DROP TABLE IF EXISTS `mensajes`;

CREATE TABLE `mensajes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `sender_id` bigint unsigned NOT NULL,
  `receiver_id` bigint unsigned NOT NULL,
  `contenido` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `attachment_path` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `attachment_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `leido` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `mensajes_tenant_id_foreign` (`tenant_id`),
  KEY `mensajes_sender_id_foreign` (`sender_id`),
  KEY `mensajes_receiver_id_foreign` (`receiver_id`),
  CONSTRAINT `mensajes_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mensajes_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `mensajes_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `mensajes` */

insert  into `mensajes`(`id`,`tenant_id`,`sender_id`,`receiver_id`,`contenido`,`attachment_path`,`attachment_name`,`attachment_type`,`leido`,`created_at`,`updated_at`,`deleted_at`) values (1,2,3,2,'ese soy juan!',NULL,NULL,NULL,1,'2026-01-16 15:20:28','2026-01-16 15:20:53',NULL),(2,2,2,3,'ea juan!',NULL,NULL,NULL,1,'2026-01-16 16:07:43','2026-01-16 16:08:38',NULL),(3,2,3,2,'55',NULL,NULL,NULL,1,'2026-01-16 16:10:15','2026-01-17 15:03:47',NULL);

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `migrations` */

insert  into `migrations`(`id`,`migration`,`batch`) values (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_01_08_042107_create_permission_tables',1),(5,'2026_01_08_042134_create_tenants_table',1),(6,'2026_01_08_042135_add_tenant_id_to_users_table',1),(7,'2026_01_08_042136_create_clientes_table',1),(8,'2026_01_08_042137_create_expedientes_table',1),(9,'2026_01_08_042138_create_actuaciones_table',1),(10,'2026_01_08_042139_create_documentos_table',1),(11,'2026_01_08_042140_create_eventos_table',1),(12,'2026_01_08_042141_create_facturas_table',1),(13,'2026_01_08_042142_create_mensajes_table',1),(14,'2026_01_08_051552_add_tipo_to_documentos_table',1),(15,'2026_01_08_054724_create_audit_logs_table',1),(16,'2026_01_08_055755_add_nombre_juez_to_expedientes_table',1),(17,'2026_01_08_055917_create_materias_table',1),(18,'2026_01_08_062031_add_trial_fields_to_tenants_table',1),(19,'2026_01_15_001219_create_juzgados_table',2),(20,'2026_01_15_025215_add_dates_to_facturas_table',3),(21,'2026_01_15_122622_add_telefono_to_users_table',4),(22,'2026_01_15_182855_add_telefono_to_users_table',4),(23,'2026_01_15_171000_create_manual_pages_table',5),(24,'2026_01_16_150351_create_expediente_user_table',6),(25,'2026_01_16_153138_add_attachment_to_mensajes_table',7),(26,'2026_01_17_060216_create_saas_tables',8),(27,'2026_01_17_070231_create_customer_columns',9),(28,'2026_01_17_070232_create_subscriptions_table',9),(29,'2026_01_17_070233_create_subscription_items_table',9),(30,'2026_01_17_070234_add_meter_id_to_subscription_items_table',9),(31,'2026_01_17_070235_add_meter_event_name_to_subscription_items_table',9),(32,'2026_01_17_140000_add_user_limits_to_plans_table',10),(33,'2026_01_20_051328_create_payments_table',11),(34,'2026_01_20_060243_update_global_settings_table_value_to_text',12),(35,'2026_01_20_063246_make_tenant_id_nullable_in_messages_and_audit_logs',13),(36,'2026_01_20_200000_ensure_saas_columns_exist',14),(37,'2026_01_20_210000_add_storage_limits_to_saas',14),(38,'2026_01_21_074812_add_google_calendar_tokens_to_users_table',15),(39,'2026_01_21_214000_add_calendar_email_to_users_table',16),(40,'2026_01_25_235500_add_google_event_id_to_eventos_table',16),(41,'2026_01_26_171241_create_evento_user_table',17),(42,'2026_01_27_025741_create_comentarios_table',18),(43,'2026_01_27_030710_add_parent_id_to_comentarios_table',18),(44,'2026_01_27_031052_create_comentario_reacciones_table',18),(45,'2026_01_27_031725_create_asesorias_table',18),(47,'2026_01_27_134946_create_comentario_reads_table',19);

/*Table structure for table `model_has_permissions` */

DROP TABLE IF EXISTS `model_has_permissions`;

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `model_has_permissions` */

/*Table structure for table `model_has_roles` */

DROP TABLE IF EXISTS `model_has_roles`;

CREATE TABLE `model_has_roles` (
  `role_id` bigint unsigned NOT NULL,
  `model_type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `model_has_roles` */

insert  into `model_has_roles`(`role_id`,`model_type`,`model_id`) values (1,'App\\Models\\User',1),(2,'App\\Models\\User',2),(3,'App\\Models\\User',3),(5,'App\\Models\\User',4),(2,'App\\Models\\User',6),(2,'App\\Models\\User',7),(3,'App\\Models\\User',8);

/*Table structure for table `password_reset_tokens` */

DROP TABLE IF EXISTS `password_reset_tokens`;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `password_reset_tokens` */

/*Table structure for table `payments` */

DROP TABLE IF EXISTS `payments`;

CREATE TABLE `payments` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `plan_id` bigint unsigned DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(3) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'MXN',
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'completed',
  `stripe_invoice_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `payment_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `payments_tenant_id_foreign` (`tenant_id`),
  KEY `payments_plan_id_foreign` (`plan_id`),
  CONSTRAINT `payments_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE SET NULL,
  CONSTRAINT `payments_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `payments` */

insert  into `payments`(`id`,`tenant_id`,`plan_id`,`amount`,`currency`,`status`,`stripe_invoice_id`,`payment_date`,`created_at`,`updated_at`) values (1,1,3,299.99,'MXN','completed',NULL,'2025-12-19 05:22:58','2026-01-20 05:22:58','2026-01-20 05:22:58'),(2,1,3,299.99,'MXN','completed',NULL,'2025-11-17 05:22:58','2026-01-20 05:22:58','2026-01-20 05:22:58'),(3,2,1,49.99,'MXN','completed',NULL,'2026-01-14 05:22:58','2026-01-20 05:22:58','2026-01-20 05:22:58'),(4,2,6,1999.00,'MXN','completed',NULL,'2025-12-03 05:22:58','2026-01-20 05:22:58','2026-01-20 05:22:58'),(5,2,6,1999.00,'MXN','completed',NULL,'2025-11-16 05:22:58','2026-01-20 05:22:58','2026-01-20 05:22:58'),(6,3,6,1999.00,'MXN','completed',NULL,'2026-01-19 05:22:58','2026-01-20 05:22:58','2026-01-20 05:22:58'),(7,3,3,299.99,'MXN','completed',NULL,'2025-12-02 05:22:58','2026-01-20 05:22:58','2026-01-20 05:22:58'),(8,3,3,299.99,'MXN','completed',NULL,'2025-11-07 05:22:58','2026-01-20 05:22:58','2026-01-20 05:22:58'),(9,4,2,99.99,'MXN','completed',NULL,'2026-01-13 05:22:58','2026-01-20 05:22:58','2026-01-20 05:22:58'),(10,4,1,49.99,'MXN','completed',NULL,'2025-12-11 05:22:58','2026-01-20 05:22:58','2026-01-20 05:22:58');

/*Table structure for table `permissions` */

DROP TABLE IF EXISTS `permissions`;

CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `permissions` */

insert  into `permissions`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (1,'manage tenants','web','2026-01-15 00:10:13','2026-01-15 00:10:13'),(2,'view global metrics','web','2026-01-15 00:10:13','2026-01-15 00:10:13'),(3,'manage users','web','2026-01-15 00:10:13','2026-01-15 00:10:13'),(4,'view all expedientes','web','2026-01-15 00:10:13','2026-01-15 00:10:13'),(5,'manage billing','web','2026-01-15 00:10:13','2026-01-15 00:10:13'),(6,'manage own expedientes','web','2026-01-15 00:10:13','2026-01-15 00:10:13'),(7,'upload documents','web','2026-01-15 00:10:13','2026-01-15 00:10:13'),(8,'view own expedientes','web','2026-01-15 00:10:13','2026-01-15 00:10:13'),(9,'manage settings','web','2026-01-15 02:37:02','2026-01-15 02:37:02'),(10,'view agenda','web','2026-01-16 14:40:14','2026-01-16 14:40:14'),(11,'view terminos','web','2026-01-16 14:40:14','2026-01-16 14:40:14'),(12,'view all terminos','web','2026-01-16 15:10:37','2026-01-16 15:10:37');

/*Table structure for table `plans` */

DROP TABLE IF EXISTS `plans`;

CREATE TABLE `plans` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_price_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration_in_days` int NOT NULL DEFAULT '30',
  `features` json DEFAULT NULL,
  `max_admin_users` int NOT NULL DEFAULT '1',
  `max_lawyer_users` int DEFAULT NULL,
  `storage_limit_gb` int NOT NULL DEFAULT '1',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `plans_slug_unique` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `plans` */

insert  into `plans`(`id`,`name`,`slug`,`stripe_price_id`,`price`,`duration_in_days`,`features`,`max_admin_users`,`max_lawyer_users`,`storage_limit_gb`,`is_active`,`created_at`,`updated_at`) values (1,'Básico','basico',NULL,49.99,30,'[\"1 usuario administrador\", \"Hasta 50 expedientes activos\", \"Gestión de términos\", \"Calendario de audiencias\", \"5 GB de almacenamiento\", \"Soporte por email\"]',1,1,1,1,'2026-01-17 07:41:54','2026-01-17 14:46:18'),(2,'Profesional','profesional',NULL,99.99,30,'[\"Hasta 5 usuarios\", \"Expedientes ilimitados\", \"Gestión de términos avanzada\", \"Calendario compartido\", \"Mensajería interna\", \"Facturación y reportes\", \"50 GB de almacenamiento\", \"Soporte prioritario\"]',1,5,1,1,'2026-01-17 07:41:54','2026-01-17 14:46:31'),(3,'Enterprise','enterprise',NULL,299.99,30,'[\"Usuarios ilimitados\", \"Expedientes ilimitados\", \"Todas las funcionalidades\", \"API personalizado\", \"Soporte 24/7 dedicado\", \"Capacitación incluida\", \"Personalización de marca\", \"100 GB de Almacenamiento\"]',1,NULL,1,1,'2026-01-17 07:41:54','2026-01-17 14:50:30'),(4,'Trial','trial',NULL,0.00,15,'\"[\\\"Acceso completo por 15 d\\\\u00edas\\\",\\\"1 usuario administrador\\\",\\\"1 usuario abogado\\\",\\\"Gesti\\\\u00f3n de expedientes\\\",\\\"Calendario de t\\\\u00e9rminos\\\"]\"',1,1,1,1,'2026-01-17 14:11:32','2026-01-17 14:11:32'),(5,'Paquete 2','paquete-2',NULL,999.00,30,'[\"Acceso completo mensual\", \"1 usuario administrador\", \"Hasta 5 usuarios abogados\", \"Gestión de expedientes\", \"Calendario de términos\", \"Documentos ilimitados\", \"Reportes avanzados\"]',1,5,1,0,'2026-01-17 14:11:32','2026-01-17 14:52:23'),(6,'Paquete 3','paquete-3',NULL,1999.00,30,'[\"Acceso completo mensual\", \"1 usuario administrador\", \"Usuarios abogados ilimitados\", \"Gestión de expedientes\", \"Calendario de términos\", \"Documentos ilimitados\", \"Reportes avanzados\", \"Soporte prioritario\"]',1,NULL,1,0,'2026-01-17 14:11:32','2026-01-17 14:52:30');

/*Table structure for table `role_has_permissions` */

DROP TABLE IF EXISTS `role_has_permissions`;

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint unsigned NOT NULL,
  `role_id` bigint unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `role_has_permissions` */

insert  into `role_has_permissions`(`permission_id`,`role_id`) values (1,1),(2,1),(3,1),(4,1),(5,1),(6,1),(7,1),(8,1),(9,1),(10,1),(11,1),(12,1),(3,2),(4,2),(5,2),(7,2),(9,2),(10,2),(11,2),(12,2),(6,3),(7,3),(10,3),(11,3),(12,3),(7,4),(10,4),(11,4),(8,5);

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `roles` */

insert  into `roles`(`id`,`name`,`guard_name`,`created_at`,`updated_at`) values (1,'super_admin','web','2026-01-15 00:10:13','2026-01-15 00:10:13'),(2,'admin','web','2026-01-15 00:10:13','2026-01-15 00:10:13'),(3,'abogado','web','2026-01-15 00:10:13','2026-01-15 00:10:13'),(4,'asistente','web','2026-01-15 00:10:13','2026-01-15 00:10:13'),(5,'cliente','web','2026-01-15 00:10:13','2026-01-15 00:10:13');

/*Table structure for table `sessions` */

DROP TABLE IF EXISTS `sessions`;

CREATE TABLE `sessions` (
  `id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `sessions` */

insert  into `sessions`(`id`,`user_id`,`ip_address`,`user_agent`,`payload`,`last_activity`) values ('AoynhZcJI7D51U5xaMBgLh0m5MXFhotzSDvSueWc',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','YTo0OntzOjY6Il90b2tlbiI7czo0MDoiVXlIek1hSTd1NDFJUkVtcW1reHpjUFhBSmNUcFRXTDNkNlNPWjFweiI7czozOiJ1cmwiO2E6MTp7czo4OiJpbnRlbmRlZCI7czozMDoiaHR0cDovLzEyNy4wLjAuMTo4MDAwL21lbnNhamVzIjt9czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1769051786),('EcqluhwcpxFoPR9FyjojMoJdgjI4U46NhWe3NHsr',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','YToyOntzOjY6Il90b2tlbiI7czo0MDoiS1VZTWYwcThkdFJzRlVadFBOejhJZmR3TXJleWdkRUNNYmtmWW5vSiI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==',1769022122),('wRxqpmDQnak9VTyrRapCrVtj08gCRkrzVoc54xok',1,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','YTo1OntzOjY6Il90b2tlbiI7czo0MDoibndLaTI3VE1FTW42VEdReWNCdVB4VzBCaE1xNmp1Tk16eHQxZ2l0diI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjk6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wcm9maWxlIjtzOjU6InJvdXRlIjtzOjc6InByb2ZpbGUiO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToxO3M6OToidGVuYW50X2lkIjtpOjE7fQ==',1768985997),('Z4nChqI2eVDuRvRj3KYcXOjqQtViemhfqB2mx0K0',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiZ1pjRjB2SVVCMFR5SW02cFlDZG9xd014YXc2SmlVdTJyWURwRFZYTyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9sb2dpbiI7czo1OiJyb3V0ZSI7czo1OiJsb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fX0=',1769039807);

/*Table structure for table `subscription_items` */

DROP TABLE IF EXISTS `subscription_items`;

CREATE TABLE `subscription_items` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` bigint unsigned NOT NULL,
  `stripe_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_product` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_price` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `meter_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `meter_event_name` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscription_items_stripe_id_unique` (`stripe_id`),
  KEY `subscription_items_subscription_id_stripe_price_index` (`subscription_id`,`stripe_price`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `subscription_items` */

/*Table structure for table `subscriptions` */

DROP TABLE IF EXISTS `subscriptions`;

CREATE TABLE `subscriptions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tenant_id` bigint unsigned NOT NULL,
  `type` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_id` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `stripe_price` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `ends_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscriptions_stripe_id_unique` (`stripe_id`),
  KEY `subscriptions_tenant_id_stripe_status_index` (`tenant_id`,`stripe_status`),
  CONSTRAINT `subscriptions_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `subscriptions` */

/*Table structure for table `tenants` */

DROP TABLE IF EXISTS `tenants`;

CREATE TABLE `tenants` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `plan` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'trial',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `subscription_ends_at` date DEFAULT NULL,
  `slug` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domain` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `settings` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `plan_id` bigint unsigned DEFAULT NULL,
  `grace_period_ends_at` timestamp NULL DEFAULT NULL,
  `subscription_status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'trial',
  `stripe_customer_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pm_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pm_last_four` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `tenants_slug_unique` (`slug`),
  UNIQUE KEY `tenants_domain_unique` (`domain`),
  KEY `tenants_plan_id_foreign` (`plan_id`),
  KEY `tenants_stripe_customer_id_index` (`stripe_customer_id`),
  CONSTRAINT `tenants_plan_id_foreign` FOREIGN KEY (`plan_id`) REFERENCES `plans` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `tenants` */

insert  into `tenants`(`id`,`name`,`plan`,`is_active`,`subscription_ends_at`,`slug`,`domain`,`status`,`trial_ends_at`,`settings`,`created_at`,`updated_at`,`deleted_at`,`plan_id`,`grace_period_ends_at`,`subscription_status`,`stripe_customer_id`,`pm_type`,`pm_last_four`) values (1,'System Admin','trial',1,NULL,'system','system.local','active','2026-02-04 21:42:56',NULL,'2026-01-15 00:10:13','2026-01-20 21:42:56',NULL,NULL,NULL,'trial',NULL,NULL,NULL),(2,'Despacho Jurídico Méndez','profesional',1,'2026-02-17','mendez-legal',NULL,'active','2026-03-16 00:10:14','{\"direccion\":\"Cam Ant A Naolinco 910\",\"titular\":\"JUAN ANOTONIO PEREZ\",\"titulares_adjuntos\":\"PEPE,LUIS,PACO\",\"datos_generales\":\"LSO GENERALES\",\"logo_path\":\"logos\\/6rD5O4ShobvzzrgtRnYTN1NJh4JQzdFlhgwTF0JO.jpg\",\"sms_enabled\":true,\"sms_days_before\":3,\"sms_recipients\":\"\"}','2026-01-15 00:10:14','2026-01-17 14:05:10',NULL,NULL,NULL,'trial',NULL,NULL,NULL),(3,'Despacho S.A.','trial',1,NULL,'despacho-sa-j17b8e',NULL,'active','2026-02-01 15:24:52',NULL,'2026-01-17 15:24:52','2026-01-17 15:24:52',NULL,4,NULL,'trial',NULL,NULL,NULL),(4,'despachoruis','basico',1,'2026-02-18','despachoruis-YwPuz7',NULL,'active',NULL,NULL,'2026-01-19 05:28:17','2026-01-19 06:37:38',NULL,1,'2026-01-22 05:36:56','active',NULL,NULL,NULL);

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `calendar_email` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `telefono` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tenant_id` bigint unsigned DEFAULT NULL,
  `role` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'abogado',
  `status` varchar(191) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'active',
  `stripe_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pm_type` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pm_last_four` varchar(4) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `trial_ends_at` timestamp NULL DEFAULT NULL,
  `google_calendar_id` varchar(191) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `google_access_token` text COLLATE utf8mb4_unicode_ci,
  `google_refresh_token` text COLLATE utf8mb4_unicode_ci,
  `google_token_expires_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_tenant_id_foreign` (`tenant_id`),
  KEY `users_stripe_id_index` (`stripe_id`),
  CONSTRAINT `users_tenant_id_foreign` FOREIGN KEY (`tenant_id`) REFERENCES `tenants` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`email`,`calendar_email`,`telefono`,`email_verified_at`,`password`,`remember_token`,`created_at`,`updated_at`,`tenant_id`,`role`,`status`,`stripe_id`,`pm_type`,`pm_last_four`,`trial_ends_at`,`google_calendar_id`,`google_access_token`,`google_refresh_token`,`google_token_expires_at`) values (1,'Super Admin','admin@legalcore.com',NULL,NULL,NULL,'$2y$12$G16NoWON99F4hBiXatGPBuqQ6aRCIRNbrLMU0.r3D14VMMEroBR8q',NULL,'2026-01-15 00:10:14','2026-01-15 00:10:14',1,'super_admin','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(2,'Admin Méndez','admin@mendez.com',NULL,NULL,NULL,'$2y$12$4NTicPDzTINlSn0y4ka8DeWThkwlBqE8imhFnu/VJkKjzCYBfMNm.',NULL,'2026-01-15 00:10:14','2026-01-15 00:10:14',2,'admin','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(3,'Lic. Juan Pérez','juan@mendez.com',NULL,NULL,NULL,'$2y$12$dbE07o62MVPBLnLNDxBI5uklXrCRNrscrtEjCHyBvQtBjLvLYLmDW',NULL,'2026-01-15 00:10:14','2026-01-15 00:10:14',2,'abogado','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(4,'Juan Patito','contacto@patito.com',NULL,NULL,NULL,'$2y$12$6FCswvWTS/oHuxLrbjLOX.uhvvQNZ2tDxcsUbPZKLdLuBKNUIQM0u',NULL,'2026-01-15 00:10:15','2026-01-15 00:10:15',2,'cliente','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(5,'Carlos segura yoquigue','mail@bjca.com',NULL,NULL,NULL,'$2y$12$djnITmk8sV6Zr.eSHwi4o.6XIWyIemKrjgdNZLwZVC5KURHvXuQzi','kPZECbGcS0u2TEH4XRuELIF2TXn2nPhk2MkceGe5TDsWFiOxVeFtkUTHa3NL','2026-01-17 13:52:16','2026-01-17 13:52:16',NULL,'admin','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(6,'Javier Salazar Lunaa','mail@mail.com',NULL,NULL,NULL,'$2y$12$8GeAGvQueo2tLRL3Cas/QeAu1r8QyuwnwqNT2TenR5DeCBuPpzOQC',NULL,'2026-01-17 15:24:52','2026-01-17 15:24:52',3,'abogado','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(7,'despachoruis','admin@despachoruis.com',NULL,NULL,NULL,'$2y$12$3qT2kdeeT3c2U0jJfLw3SeTqjtgXvdjYv938XnRdNLWz/geU6dC2K',NULL,'2026-01-19 05:28:17','2026-01-19 05:28:17',4,'abogado','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),(8,'ab1','ab1@despachoruis.com',NULL,NULL,NULL,'$2y$12$V6K375hc2ej2Up7m0/abIujxEfnvfl6P5JcYwZ6/bamLzeZJRVXjO',NULL,'2026-01-19 05:29:49','2026-01-19 05:29:49',4,'abogado','active',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
