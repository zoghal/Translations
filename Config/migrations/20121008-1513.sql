ALTER TABLE `translations`
	ADD `is_active` tinyint(1) DEFAULT '1' AFTER `value`,
	ADD `comments` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'description for the translation in the default locale, translator\'s own notes for other locales' AFTER `plural_case`,
	ADD `references` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'Where is this translation used' AFTER `comments`,
	ADD `history` text CHARACTER SET utf8 COLLATE utf8_general_ci DEFAULT NULL COMMENT 'timestamp:value - Stack of previous versions of this translation' AFTER `references`,
	DROP `plural_id`,
	CHANGE `plural_case` `plural_case` int(2) DEFAULT NULL COMMENT 'Only relevant for plural translations. 0-6',
	DROP KEY `locale`,
	ADD UNIQUE KEY `locale` (`locale`, `domain`, `category`, `key`, `plural_case`);
