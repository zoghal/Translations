##Remote Translations

There may be circumstances whereby you wish to have the translation definitions from one
application to feed another application. This can be achieved using the remote translations
datasource.

##Host setup

The host needs to provide an api interface for the client server to query. For example:

###Route definition

	Router::connect(
		'/api/translations/*',
		array('controller' => 'translations', 'action' => 'api_index', 'ext' => 'json', 'api' => true)
	);

###Controller method

	class TranslationsController extends AppController {

		public function api_index($locale = 'en', $domain = 'default', $category = 'LC_MESSAGES') {
			$options = compact('locale', 'domain', 'category');
			$options['nested'] = false;

			$data = Translation::forLocale($locale, $options);
			$this->set('data', $data);
		}
	}

###View file

	echo json_encode($data);

##Client setup

The client only needs to specify that it will use the remote datasource. In this way the first
request for any locale+domain+category will issue a http request to the host server to obtain
translation definitions. The response is cached, therefore all subsequent translations are read
from the cached response.

###database.php

Define a database connection, referring to the translations remote source

	public $translations = array(
		'datasource' => 'Translations.TranslationsRemoteSource',
		'host' => 'http://example.com/api/translations/:locale/:domain/:category',
	);

###bootstrap.php

Configure the translation model to use the translation connection

	Translation::config(array(
		'useDbConfig' => 'translations',
		'cacheConfig' => $yourChoice
	));

