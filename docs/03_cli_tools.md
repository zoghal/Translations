#CLI Tools

Several commands are provided to ease working with translations. These tools build upon Cake's
own translation tools, and use `pot` files as the primary means of storing translation definitions.
This permiits the Translations plugin to easily integrate with existing translations tools and
solutions.

In addition to pot files however, the plugin provides various import and export formats for
easier consumption via apis.

Supported import formats:

 * json
 * plist
 * pot
 * po

Supported export formats:

 * json

##Extract

To Extract all translations for your application (only, no plugins), call with no arguments:

	-> Console/cake Translations.extract


	Extracting...
	---------------------------------------------------------------
	Paths:
	/var/www/project/htdocs/app/
	Output Directory: /var/www/project/htdocs/app/Locale/
	---------------------------------------------------------------
	Processing /var/www/project/htdocs/app/Config/bootstrap.php...
	...
	Processing /var/www/project/htdocs/app/View/Helper/AppHelper.php...
	---------------------------------------------------------------
	Writing /var/www/project/htdocs/app/Locale/default.pot

	Done.


	Extracting...
	---------------------------------------------------------------
	Paths:
	/var/www/project/htdocs/app/webroot
	Output Directory: /var/www/project/htdocs/app/Locale/
	---------------------------------------------------------------
	Processing /var/www/project/htdocs/app/webroot/app.js
	...
	Processing /var/www/project/htdocs/app/webroot/shared/js/jquery/1.9.0/jquery.js...
	---------------------------------------------------------------
	Writing /var/www/project/htdocs/app/Locale/javascript.pot

	Done.
	->

It's possible to restrict the plugin to a path by passing it as the first argument to the command:

	-> Console/cake Translations.extract Plugin/Foo/


	Extracting...
	---------------------------------------------------------------
	Paths:
	Plugin/Foo/
	Output Directory: Plugin/Foo/Locale/
	---------------------------------------------------------------
	Processing /var/www/project/htdocs/app/Plugin/Foo/Config/bootstrap.php...
	...
	Processing /var/www/project/htdocs/app/Plugin/Foo/View/Helper/OurPaginatorHelper.php...
	---------------------------------------------------------------
	Writing /var/www/project/htdocs/app/Plugin/Foo/Locale/cake_dev.pot
	Writing /var/www/project/htdocs/app/Plugin/Foo/Locale/default.pot
	Writing /var/www/project/htdocs/app/Plugin/Foo/Locale/foo.pot

	Done.


	Extracting...
	---------------------------------------------------------------
	Paths:
	Plugin/Foo/webroot
	Output Directory: Plugin/Foo/Locale/
	---------------------------------------------------------------
	Processing /var/www/project/htdocs/app/Plugin/Foo/webroot/js/core.js...
	...
	Processing /var/www/project/htdocs/app/Plugin/Foo/webroot/js/libs/underscore.min.js...
	---------------------------------------------------------------
	Writing /var/www/project/htdocs/app/Plugin/Foo/Locale/javascript.pot

	Done.

Note that used in this way - the output directory is set to the Locale folder of the Plugin
if it exists.

More information on the extract command is provided via the `--help` option.

###Extract PHP

To extract only php translations and ignore js translations - use the php task

	-> Console/cake Translations.extract php

As with the main extract task, a path can also be specified to restrict which files are checked:

	-> Console/cake Translations.extract php Plugin/Foo/

###Extract JS

To extract only js translations - and ignore php translations - use the js task

	-> Console/cake Translations.extract js

As with the main extract task, a path can also be specified to restrict which files are checked:

	-> Console/cake Translations.extract js Plugin/Foo/

##Export

The primary means of interchanging translation data is via json files. To export a language
definition, pass the path to the export file as the first argument:

	Console/cake Translations.export Config/en.json

The format is derived from the extension of the export file.

##Import

To import translation definitions, pass the path to the translation file as the first argument:

	Console/cake Translations.import Config/en.json

The format of the file is derived from the filename. For json files there are currently two formats
supported, there is the simple:

	{
		"foo":"foo value"
	}

And the more complete:

	{
		"locale":"en",
		"domain":"default",
		"category":"LC_MESSAGES",
		"translations":{
			"foo":"foo value"
		}
	}

The export task generates a "complete" json file.

In the case of po and pot files - the domain is derived from the filename:

	Console/cake Translations.import Locale/foo.pot # import to domain foo
