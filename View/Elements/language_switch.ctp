<?php
$locales = Translation::locales();
if (count($locales) < 2) {
	return;
}
echo $this->Form->select('locale', $locales, array(
	'empty' => __d('translations', 'change locale'),
	'id' => 'localeChange',
	'data-ping-url' => Router::url(array('plugin' => 'translations', 'controller' => 'translations', 'action' => 'set_locale', 'ext' => 'json')),
	'value' => Configure::read('Config.language'),
	'class' => 'btn'
));
