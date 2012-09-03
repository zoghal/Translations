CREATE TABLE IF NOT EXISTS `translations` (
  `id` char(36) NOT NULL,
  `application_id` char(36) DEFAULT NULL,
  `locale` char(5) NOT NULL DEFAULT 'en' COMMENT 'ISO 3166 codes',
  `key` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `translations_key` (`application_id`, `locale`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
