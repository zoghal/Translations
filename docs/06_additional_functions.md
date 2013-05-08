#Additional functions

Notes on the functions of the Translation model class not mentioned elsewhere

##createLocale

To create a new locale which inherits from the  `Config.defaultLanguage` - no specific action is required.
The plugin will not store a translation record if it has the same value as the inherited translation record.
This reduces the amount of data stored in the datasource, and prevents language translations being un-intentionally
disassociated from the inherited language. If a translation record is edited to be the same as the inherited
translation (via the update method) - it will be deleted.

There are some circumstances whereby you may wish to create a new locale based on a language that is _not_
the default langauge. An example would be where there are English and Danish texts - and a new language
similar to Danish will be added such as Norweigian. To do this:

	Configure::write('Config.defaultLanguage', 'en');
	Configure::write('Config.language', 'da');
	...
	$this->Translation->createLocale('no', array('basedOn' => 'da'));

In this case, wheras normally a request for a Norweigian translation would return english (the defaultLanguage)
it will now return the Danish translation. This does not modify the locale inheritance - it will copy the
translation definitions from Danish to Norweigian - permitting dk -> no translations.

##forLocale

This method returns an array of translations matching the passed arguments. The default behavior
(only relevant if dot delimited translation keys are used) is to return a nested array:

	$nested = Translation::forLocale();
	$nested = array(
			'key' => array(
				'one' => array(
					'two' => 'value'
				),
				'three' => 'value'
			)
	);

To obtain a flat array - pass the option `'nested' => false`:

	$flat = Translation::forLocale(null, array('nested' => false));
	$flat = array(
			'key.one.two' => 'value',
			'key.three' => 'value'
	);

If dot-delimited keys are not used, the return value from this function is always a flat array.
If dot-delimited keys are being used however, it's also possible to request a subsection of
translations. To do this - specify the section of translations to be returned:

	$return = Translation::forLocale(null, array('section' => 'key.one'));
	$return = array(
		'two' => 'value'
	);

The domain, category and locale can all be specified via the arguments:

	$return = Translation::forLocale('da', array('domain' => 'foo', 'category' => LC_TIME));

##hasTranslation

Returns a boolean if a translation definition exists.

	$exists = Translation::hasTranslation('some string');

Note that the behavior of translations for a translation string which exists but is the default
value, and a translation which doesn't exist is the same. In both cases the translation key
is returned.

##import

Import translation definitions from a file or external resource:

	Translation::import($translations);

This function accepts:

 * An array of translations
 * The field data for an uploaded file
 * A url which returns translation definitions as json
 * A file in one of the supported formats

Supported formats are determined by the parsers which implement a parse function.

If a file is used or uploaded - the parser used is determined from the file extension

##export

The workhorse function used by the cli export command.

	$return = Translation::export('translations.json');

This function generates translations in one of the supported formats. Supported formats are
determined by the parsers which implement a generate function.

The return value summarises what has been done:

	$return = array(
		...
		'locale' => 'en',
		'domain' => 'default',
		'category' => 'LC_MESSAGES',
		'translations' => array(
			'example' => 'string'
		),
		'count' => 1,
		'string' => '{"locale": "en","domain": "default","category": "LC_MESSAGES","translations": {"example": "string"}}',
		'success' => true
	);

To generate only the export data without writing to a file - specify false as the filename:

	$return = Translation::export(false);

##locales

Returns the list of implemented locales - or all locales

	$allUsedLocales = Translation::locales(false); // default
	$allLocales = Translation::locales(true);
	$ofTheListLocales = Translation::locales(array('en', 'da', 'ja'));
	$ofTheListLocales = Translation::locales('en,da,ja');

In the last two cases, only locales which are implemented and in the list will be returned.

##domains

Returns the list of domains in use:

	$domains = Translation::domains();

if the config setting `supportedDomains` is used, this is returned directly, otheriwse the
datastore is queried to list all domains with translation definitions

##categories

Returns the list of categories in use:

	$domains = Translation::categories();

if the config setting `supportedCategories` is used, this is returned directly, otherwise an array
of all categories is returned.

##purge

This function is called by the import method, if the option `'purge' => true` is specified (the
default is false). It deletes translation definitions that don't exist in the import file:

	$translations = Translation::forLocale(); // big array
	Translation::import('en.json', array('purge' => true)); // import a file with one translation in it
	$translations = Translation::forLocale(); // array with one translation in it

##parse

Parses translation definitions from an export format returning an array of translations. Accepts:

 * An array of translations
 * The field data for an uploaded file
 * A url which returns translation definitions as json
 * A file in one of the supported formats

	$array = Translation::parse('en.json');

Supported formats are determined by the parsers which implement a parse function.

##reset

Mostly used in testing, this resets the class state such that there are no in-memory translations

	Translation::reset();
