#Config

If the default langauge for the applicaiton is not English, define the application default languages
in `app/Config/bootstrap.php`:

	Configure::write('Config.defaultLanguage', 'da');
	Configure::write('Config.language', 'da');

The `defaultLanguage` means litterally the default language, the language setting refers to the current
request.

The plugin as a whole can be configured via the `Translation::config` function:

	Translation::config(array(
		'locale' => 'en',
		'domain' => 'default',
		'category' => 'LC_MESSAGES',
		'useDbConfig' => 'default',
		'useTable' => 'translations',
		'cacheConfig' => 'default',
		'autoPopulate' => false,
		'supportedDomains' => array(),
		'supportedCategories' => array(),
		'supportedLocales' => array()
	));

##cacheConfig

With an empty or disabled cache, the plugin reads all the data for the combined locale+domain+category
on demand when the first translation is requested - and writes the combined data to this cache config.
Subsequent translations within the same request are served from an in-memory map; the first translation
on subsequent requests triggers the plugin to load the translation data from the cache.

A last-updated timestamp is used in the cache key - such that any updates to the translation data (via
the Translation model) will trigger the cached data to be refreshed.

##domain and category

Setting `domain` or `category` via the config function - sets the default domain and locale for the
plugin if not specifically passed to the translate function.

##autoPopulate

Set to true to have missing translations automatically created directly in the db. The advantage to
doing that is that entries are reached in your code, the (potentially significant) disadvantage is that
entries which are not triggered during development won't be translated when a user finally triggers
that rarely accessed translation string.

##useTable

The plugin will automatically switch to using no table if the specified table does not exist. This
prevents infinite loops if there is a setup problem (`useTable` value contains a typo) or the connection
to the datasource is lost. If for any reason the plugin should operate with no table - `useTable  can
explicitly be set to false - In this case it will hold translations in memory only. During unit tests
`useTable` is automatially set to false.

##supported*

If defined, these restrict which requests the Translate model will operate upon. A request for a translation
in a domain which is not in the `supportedDomains` array - will be returned directly. These config keys also
modify the response of the functions `locales`, `domains` and `categories`.
