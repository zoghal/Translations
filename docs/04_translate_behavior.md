#Translate behavior

The translate behavior permits using the same mechanism to store translations for dynamic data

##Overview

A special domain is used for storing dynamic translations - this is the 'data' domain. The primary
key, and the field name are used to construct the translation key used. For example:

	$Post = ClassRegistry::init('Post');
	$Post->Behaviors->load('Translations.Translate', array('title'));

	$Post->id = 1;
	$Post->saveField('title', 'A title');

Assuming the default langauge is "en" and the current langauge is also "en", the above code will do
two things:

* update the title field of the original record to be "A title"
* create/update a translation record for this post.

The original record is updated so that if the translate behavior is disabled for a given request - the
default-language field value is still available. If the translate behavior is in use, the (identical)
translation record data is used.

The translation record will have the following structure:

* locale: "en"
* domain: "data"
* category: "LC_MESSAGES"
* key: "Post.1.title"
* value: "A title"

The key used for storing the translation data is of the format "name.id.field" where name is the model
_name_ (not alias). The name is used to prevent the model alias diassociating the translation data from
the database data - for example, assuming Post has Author and LastCommenter associations, and a user's
position is a translated field:

	$this->Post->Author->id = 1;
	$this->Post->Approver->id = 1;

	$one = $this->Post->Author->field('position');
	$two = $this->Post->lastCommenter->field('position');

	// Ensures that $one and $two are the same

##Creating records in the not-default language

If a record is created in a different language from the default - the behavior acts a little differently.

	$Post = ClassRegistry::init('Post');
	$Post->Behaviors->load('Translations.Translate', array('title'));

	Configure::write('Config.language', 'es');
	$Post->create();
	$Post->saveField('title', 'Un libro');

For consistency, The original record must have a value, and a translation record must exist in the
default language. Therefore the post's title field will be set to "Un libro" and the following translation
record will be created:

* locale: "en"
* domain: "data"
* category: "LC_MESSAGES"
* key: "Post.2.title"
* value: "Un libro"

There will only be one translation record, in the default langauge.
