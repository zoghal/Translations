<?php
$this->element('top_actions');
echo $this->element('Shared.Crud/form', array(
	'model' => 'Translation',
	'title' => 'Export translations',
	'columns' => array(
		'locale' => array(
			'default' => Configure::read('Config.defaultLanguage') ?: Configure::read('Config.language')
		),
		'domain' => array(
			'default' => 'default'
		),
		'category' => array(
			'default' => 'LC_MESSAGES'
		),
		'format' => array(
			'options' => array('json' => 'json')
		)
	)
));
