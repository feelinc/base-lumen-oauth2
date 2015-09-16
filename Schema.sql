SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE TABLE `oauth_access_token` (
  `access_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_id` int(10) unsigned NOT NULL,
  `expire_time` int(11) NOT NULL,
  PRIMARY KEY (`access_token`),
  KEY `session_id` (`session_id`),
  CONSTRAINT `oauth_access_token_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `oauth_session` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `oauth_access_token_scope` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `access_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `access_token` (`access_token`),
  KEY `scope` (`scope`),
  CONSTRAINT `oauth_access_token_scope_ibfk_1` FOREIGN KEY (`access_token`) REFERENCES `oauth_access_token` (`access_token`) ON DELETE CASCADE,
  CONSTRAINT `oauth_access_token_scope_ibfk_2` FOREIGN KEY (`scope`) REFERENCES `oauth_scope` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `oauth_auth_code` (
  `auth_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `session_id` int(10) unsigned NOT NULL,
  `expire_time` int(11) NOT NULL,
  `client_redirect_uri` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`auth_code`),
  KEY `session_id` (`session_id`),
  CONSTRAINT `oauth_auth_code_ibfk_1` FOREIGN KEY (`session_id`) REFERENCES `oauth_session` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `oauth_auth_code_scope` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `auth_code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `scope` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `auth_code` (`auth_code`),
  KEY `scope` (`scope`),
  CONSTRAINT `oauth_auth_code_scope_ibfk_1` FOREIGN KEY (`auth_code`) REFERENCES `oauth_auth_code` (`auth_code`) ON DELETE CASCADE,
  CONSTRAINT `oauth_auth_code_scope_ibfk_2` FOREIGN KEY (`scope`) REFERENCES `oauth_scope` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `oauth_client` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `request_limit` int(10) NOT NULL DEFAULT '5000',
  `current_total_request` int(10) NOT NULL DEFAULT '0',
  `request_limit_until` timestamp NULL DEFAULT NULL,
  `last_request_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `oauth_client` (`id`, `secret`, `name`, `request_limit`, `current_total_request`, `request_limit_until`, `last_request_at`) VALUES
('testclient',  'secret', 'Test Client',  5000, 0,  NULL, NULL);

CREATE TABLE `oauth_client_redirect_uri` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `client_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `redirect_uri` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `oauth_client_redirect_uri` (`id`, `client_id`, `redirect_uri`) VALUES
(1, 'testclient', 'http://localhost/api/');

CREATE TABLE `oauth_mac_key` (
  `key` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`key`),
  KEY `access_token` (`access_token`),
  CONSTRAINT `oauth_mac_key_ibfk_1` FOREIGN KEY (`access_token`) REFERENCES `oauth_access_token` (`access_token`) ON DELETE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `oauth_refresh_token` (
  `refresh_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expire_time` int(11) NOT NULL,
  `access_token` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`refresh_token`),
  KEY `access_token` (`access_token`),
  CONSTRAINT `oauth_refresh_token_ibfk_1` FOREIGN KEY (`access_token`) REFERENCES `oauth_access_token` (`access_token`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `oauth_scope` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `oauth_scope` (`id`, `description`) VALUES
('basic', 'Basic details about your account'),
('email', 'Your email address'),
('photo', 'Your photo');

CREATE TABLE `oauth_session` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `owner_type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `client_redirect_uri` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `client_id` (`client_id`),
  CONSTRAINT `oauth_session_ibfk_1` FOREIGN KEY (`client_id`) REFERENCES `oauth_client` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `oauth_session_scope` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `session_id` int(10) unsigned NOT NULL,
  `scope` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `scope` (`scope`),
  KEY `session_id` (`session_id`),
  CONSTRAINT `oauth_session_scope_ibfk_1` FOREIGN KEY (`scope`) REFERENCES `oauth_scope` (`id`) ON DELETE CASCADE,
  CONSTRAINT `oauth_session_scope_ibfk_2` FOREIGN KEY (`session_id`) REFERENCES `oauth_session` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `activated` tinyint(1) NOT NULL DEFAULT '0',
  `activation_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `activated_at` timestamp NULL DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `first_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  KEY `users_activation_code_index` (`activation_code`(191)),
  KEY `users_reset_password_code_index` (`remember_token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `email`, `password`, `activated`, `activation_code`, `activated_at`, `last_login`, `remember_token`, `first_name`, `last_name`, `created_at`, `updated_at`) VALUES
(1, 'me@sulaeman.com',  '$2y$10$MG0xtw9sWHDitFgEvZaOW.dOOhWUMMPpdZifRF06V1a/DSXRgzIEe', 1,  NULL, NULL, NULL, NULL, 'Sulaeman', 'Masia Weh',  '0000-00-00 00:00:00',  '0000-00-00 00:00:00');