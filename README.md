Translations
============

Backend / Platform translations plugin

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

##Usage

This plugin is very simple to use:

	$translated = Translation::translate('Original');

It's up to you whether you use codes or full sentances. Note however that this plugin offers no mechanism
(at least not right now) for handling plurals, domains or categories.

You can change language at any time:

	Configure::write('Config.language', 'en');
	$translatedEN = Translation::translate('original');
	Configure::write('Config.language', 'dk');
	$translatedDK = Translation::translate('original');

Missing entries are automatically added on first use in development mode - so you can just add the markers
into your code as appropriate, and then update via the admin interface.
