#Translations

A database driven solution for translations

##Features

 * Can transparently replace cake's own functions
 * Generates translation definitions as they are used
 * Human-readable replacement markers
 * Admin interface for editing translations, creating new locales importing/exporting

##Installation

Install like any other CakePHP plugin, e.g.:

    git submodule add git@github.com:nodesagency/Translations.git app/Plugin/Translations

OR

    cd app/Plugin
    git clone git://github.com/nodesagency/Translations.git

Load the db schema:

	mysql mydb < app/Plugin/Translations/Config/schema.sql

Then add the following to your `app/Config/bootstrap.php` file

	CakePlugin::load('Translations', array('bootstrap' => true));

If you want this plugin to take over all translations from cake - you MUST use CakePHP 2.3+ 
and you MUST include `Config/override_i18n.php` BEFORE loading Cakephp. To do this add the
following code to the beginning of each of these files:

    include_once dirname(__DIR__) . '/Plugin/Translations/Config/override_i18n.php';

 * 	In your app/webroot/index.php
 * 	In your app/webroot/test.php
 * 	In your app/Console/cake.php

##Usage

A simple example would be:

	$translated = Translation::translate('Original');

If you use the translate function defined in the bootstrap file, that can also be written as:

    $translated = t('Original');

If override_i18n is used, then it's even simpler, as it's the same as any other cake project:

    $translated = __('Original');

It's up to you whether you use codes or full sentances. If you want to add some replacement markers into translations
this can be done in a couple of ways. For example, both of these examples will output "Welcome back John":

    $translated = __('Welcome back {name}', array('name' => 'John'));
    $translated = __('Welcome back %s', 'John');

##Config

You can change language at any time:

	Configure::write('Config.language', 'en');
	$translatedEN = Translation::translate('original');
	Configure::write('Config.language', 'dk');
	$translatedDK = Translation::translate('original');

The plugin as a whole can be configured via the `Translation::config` function:

	Translation::config(array(
		'locale' => 'en',
		'domain' => 'default',
		'category' => 'LC_MESSAGES',
		'useDbConfig' => 'default',
		'useTable' => 'translations',
		'cacheConfig' => 'default',
		'autoPopulate' => false
	));

If you want missing entries to automatically be created it's necessary to set `autoPopulate` to true.
The advantage to doing that is that entries get created as you create them in your code, the (potentially
significant) disadvantage is that entries which you don't trigger during development won't be available
in the backend for translating - and may need creating manually.

##TODO

 * For Cake 3.0 - make this compatible with the proposed translations engine API (not yet defined)
