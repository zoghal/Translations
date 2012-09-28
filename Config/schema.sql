CREATE TABLE IF NOT EXISTS `translations` (
  `id` varchar(36) NOT NULL,
  `application_id` char(36) DEFAULT NULL,
  `asset_file_id` varchar(36) DEFAULT NULL,
  `plural_id` varchar(36) DEFAULT NULL COMMENT 'If this translation has a plural id - this is it',
  `plural_case` int(2) DEFAULT NULL COMMENT 'Only relevant for plural translations 0-6',
  `locale` varchar(5) NOT NULL DEFAULT 'en' COMMENT 'ISO 3166-1 alpha-2 country code + optional (_ + Region subtag). e.g. en_US',
  `domain` varchar(50) DEFAULT 'default',
  `category` varchar(50) DEFAULT 'LC_MESSAGES',
  `key` varchar(255) NOT NULL,
  `value` text,
  PRIMARY KEY (`id`),
  UNIQUE KEY `locale` (`locale`,`domain`,`category`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
