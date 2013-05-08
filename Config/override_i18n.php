<?php
/**
 * Override all the translation functions, and point them at this plugin
 *
 * This MUST be included as early as possible in your code to be usable this means:
 * 	In your app/webroot/index.php
 * 	In your app/webroot/test.php
 * 	In your app/Console/cake.php
 */

/**
 * __replace
 *
 * Helper function for replacing args
 *
 * @param mixed $msg
 * @param mixed $args
 * @return string
 */
function __replace($msg, $args) {
	if (is_null($args)) {
		return $msg;
	}
	if (strpos($msg, '{') !== false) {
		if (count($args) === 1 && isset($args[0])) {
			$msg = preg_replace('@{\w+}@', $args[0], $msg, 1);
		} else {
			$msg = String::insert($msg, $args, array('before' => '{', 'after' => '}'));
		}
	} else {
		$msg = vsprintf($msg, $args);
	}
	return $msg;
}

/**
 * Returns a translated string if one is found; Otherwise, the submitted message.
 *
 * @param string $singular Text to translate
 * @param mixed $args Array with arguments or multiple arguments in function
 * @return mixed translated string
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#__
 */
function __($singular, $args = null) {
	if (!is_null($args)) {
		$args = is_array($args) ? $args : array_slice(func_get_args(), 1);
	}
	if (!$singular || !class_exists('Translation')) {
		return __replace($singular, $args);
	}
	$translated = Translation::translate($singular);
	return __replace($translated, $args);
}

/**
 * Returns correct plural form of message identified by $singular and $plural for count $count.
 * Some languages have more than one form for plural messages dependent on the count.
 *
 * @param string $singular Singular text to translate
 * @param string $plural Plural text
 * @param integer $count Count
 * @param mixed $args Array with arguments or multiple arguments in function
 * @return mixed plural form of translated string
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#__n
 */
function __n($singular, $plural, $count, $args = null) {
	if (!is_null($args)) {
		$args = is_array($args) ? $args : array_slice(func_get_args(), 3);
	}

	$args = $args ?: array();
	if (!isset($args['number'])) {
		$args['number'] = $count;
	}

	if (!$singular || !class_exists('Translation')) {
		return __replace($singular, $args);
	}

	$translated = Translation::translate($singular, compact('plural', 'count'));
	return __replace($translated, $args);
}

/**
 * Allows you to override the current domain for a single message lookup.
 *
 * @param string $domain Domain
 * @param string $msg String to translate
 * @param mixed $args Array with arguments or multiple arguments in function
 * @return translated string
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#__d
 */
function __d($domain, $msg, $args = null) {
	if (!is_null($args)) {
		$args = is_array($args) ? $args : array_slice(func_get_args(), 2);
	}
	if (!$msg || !class_exists('Translation')) {
		return __replace($msg, $args);
	}
	$translated = Translation::translate($msg, compact('domain', 'plural', 'count'));
	return __replace($translated, $args);
}

/**
 * Allows you to override the current domain for a single plural message lookup.
 * Returns correct plural form of message identified by $singular and $plural for count $count
 * from domain $domain.
 *
 * @param string $domain Domain
 * @param string $singular Singular string to translate
 * @param string $plural Plural
 * @param integer $count Count
 * @param mixed $args Array with arguments or multiple arguments in function
 * @return plural form of translated string
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#__dn
 */
function __dn($domain, $singular, $plural, $count, $args = null) {
	if (!is_null($args)) {
		$args = is_array($args) ? $args : array_slice(func_get_args(), 4);
	}

	if (!$singular || !class_exists('Translation')) {
		return __replace($singular, $args);
	}

	$args = $args ?: array();
	if (!isset($args['number'])) {
		$args['number'] = $count;
	}

	$translated = Translation::translate($singular, compact('domain', 'plural', 'count'));
	return __replace($translated, $args);
}

/**
 * Allows you to override the current domain for a single message lookup.
 * It also allows you to specify a category.
 *
 * The category argument allows a specific category of the locale settings to be used for fetching a message.
 * Valid categories are: LC_CTYPE, LC_NUMERIC, LC_TIME, LC_COLLATE, LC_MONETARY, LC_MESSAGES and LC_ALL.
 *
 * @param string $domain Domain
 * @param string $msg Message to translate
 * @param integer|string $category Category
 * @param mixed $args Array with arguments or multiple arguments in function
 * @return translated string
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#__dc
 */
function __dc($domain, $msg, $category, $args = null) {
	if (!is_null($args)) {
		$args = is_array($args) ? $args : array_slice(func_get_args(), 3);
	}
	if (!$msg || !class_exists('Translation')) {
		return __replace($msg, $args);
	}
	$translated = Translation::translate($msg, compact('domain', 'category'));
	return __replace($translated, $args);
}

/**
 * Allows you to override the current domain for a single plural message lookup.
 * It also allows you to specify a category.
 * Returns correct plural form of message identified by $singular and $plural for count $count
 * from domain $domain.
 *
 * The category argument allows a specific category of the locale settings to be used for fetching a message.
 * Valid categories are: LC_CTYPE, LC_NUMERIC, LC_TIME, LC_COLLATE, LC_MONETARY, LC_MESSAGES and LC_ALL.
 *
 * @param string $domain Domain
 * @param string $singular Singular string to translate
 * @param string $plural Plural
 * @param integer $count Count
 * @param integer|string $category Category
 * @param mixed $args Array with arguments or multiple arguments in function
 * @return plural form of translated string
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#__dcn
 */
function __dcn($domain, $singular, $plural, $count, $category, $args = null) {
	if (!is_null($args)) {
		$args = is_array($args) ? $args : array_slice(func_get_args(), 5);
	}
	if (!$singular || !class_exists('Translation')) {
		return __replace($singular, $args);
	}

	$args = $args ?: array();
	if (!isset($args['number'])) {
		$args['number'] = $count;
	}

	$translated = Translation::translate($singular, compact('domain', 'plural', 'count', 'category'));
	return __replace($translated, $args);
}

/**
 * The category argument allows a specific category of the locale settings to be used for fetching a message.
 * Valid categories are: LC_CTYPE, LC_NUMERIC, LC_TIME, LC_COLLATE, LC_MONETARY, LC_MESSAGES and LC_ALL.
 *
 * @param string $msg String to translate
 * @param integer|string $category Category
 * @param mixed $args Array with arguments or multiple arguments in function
 * @return translated string
 * @link http://book.cakephp.org/2.0/en/core-libraries/global-constants-and-functions.html#__c
 */
function __c($msg, $category, $args = null) {
	if (!is_null($args)) {
		$args = is_array($args) ? $args : array_slice(func_get_args(), 2);
	}
	if (!$msg || !class_exists('Translation')) {
		return __replace($msg, $args);
	}
	$translated = Translation::translate($singular, compact('category'));
	return __replace($translated, $args);
}
