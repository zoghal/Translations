#Usage

The plugin makes no assumptions about whether translations are codes or full sentances - however translation
keys should be less than 255 characters with the default schema provided in the plugin. To translate
longer strings (not recommended) - modify the schema such that the `key` column is a text field.

##Singular translations

A simple example:

	$translated = Translation::translate('Original');

A simple example using `override_i18n.php` (recommended);

    $translated = __('Original');

##Plural translations

A simple example:

	$translated = Translation::translate('Original', array('plural' => 'Originals', 'count' => $count));

A simple example using `override_i18n.php` (recommended);

	$translated = __n('Original', 'Originals', $count);

##Other translation functions

All of the standard translations functions are supported:

	__($singular)
	__n($singular, $plural, $count)
	__d($domain, $singular)
	__c($singular, $category)
	__dn($domain, $singular, $plural, $count)
	__dc($domain, $singular, $category)
	__dcn($domain, $singular, $plural, $count, $category)

A complete example using the Translation model:

	$translated = Translation::translate('Original', array(
		'plural' => 'Originals',
		'domain' => 'other',
		'category' => 'LC_MESSAGES',
		'locale' => 'en_gb',
		'count' => $count
	));


##Marker replacement

This only applies if the `override_i18n.php` files is being used.

Replacement markers are implemented in several ways. For example, all of the below will output
"Welcome back John":

    $translated = __('Welcome back {name}', array('name' => 'John'));
    $translated = __('Welcome back %s', 'John');
    $translated = __('Welcome back {name}', 'John');

Plural translations automatically have the key `{number}` available:

	$translated = __n('You have one new message', 'You have {number} new message', $count);

##Changing the language; locale inheritance

The language can be changed at any time:

	Configure::write('Config.language', 'en');
	$translatedEN = Translation::translate('original');
	Configure::write('Config.language', 'da');
	$translatedDK = Translation::translate('original');

The language used for translations is the first of:

* The explicit language passed in the parameters to the translate function
* the value of `$_SESSION['Config']['language']`
* the value of `Configure::read('Config.language')
* the value of `Configure::read('Config.defaultLanguage')

The plugin will work with either 2 or 5 char language codes - and honor locale "inheritance". This means
for example, with the following config:

	Configure::write('Config.defaultLanguage', 'en_us');
	Configure::write('Config.language', 'de_ch');

	$hello = __('hello');

The first translation definition found for these locales, in this order, will be returned:

* de_ch
* de
* en_us
* en

##Autodetecting Changing the language, and locale inheritance

To choose the defined locale based on a requests `accept-language`:

	Translation::autoDetectLocale();

This will call `Configure::write('Config.language', $lang)` with the most specific locale with translation
definitions. The result is also returned from the function.

To choose the defined locale based from a predefined list - pass the list of possibilities to the function
in the order of preference:

	$firstImplementedLocale = Translation::autoDetectLocale('zh,ja,de,en');
