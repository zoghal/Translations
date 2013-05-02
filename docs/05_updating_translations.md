#Updating translations

There are several ways to update translations, the "best" being determined by your intended use
of the plugin.

##Import and export files

If translations are to be defined by the same developer using the application - the use of export
files is the simplest way to use the plugin. An example work flow for this is:

* Extract translation definitions (pot file)
* Import translation definitions (pot file)
* Export translations to file (json file)
* Edit translations (json file)
* Import translations from file (json file)

Depending on how frequently new translations are added, it may only be necessary to edit and re-import
the json translation file.

It's recommended to add both `pot` files from extracting translations - and langauge export files to
your source code repository. Adding translation export files to your source repository makes it easily
possible to set up a new install (for example - when deploying a site for the first time), or recover
from an error or data loss.

##Via the update method

The translation model provides a simple api for updating translations:

	Translation::update($key, $value, $options);

This makes it easily possible to update translations via any means (such as an admin screen):

	Translation::update('header.title', 'CakePHP awesomeness');

An example controller action which you could use would be:

	public function admin_update() {
		$data = $this->data;
		$return = Translation::update($data['key'], $data['value'], $data);
		$success = (bool)$return;

		$this->set(compact('success', 'return'));
	}

Where it would be called with the equivalent of:

	$.post(
		"/admin/translations/update.json",
		{
			"key": "header.title",
			"value": "CakePHP awesomeness",
			"locale": "en",
			"category": "LC_MESSAGES",
			"domain": "default"
		}
	);

