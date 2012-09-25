<?php
echo $this->element('Shared.Crud/form', array(
	'formOptions' => array('type' => 'file'),
	'model' => 'Translation',
	'title' => 'Upload translations',
	'columns' => array(
		'locale' => array(
			'default' => Configure::read('Config.defaultLanguage') ?: Configure::read('Config.language'),
			'options' => $allLocales
		),
		'domain' => array(
			'default' => 'default'
		),
		'category' => array(
			'default' => 'LC_MESSAGES'
		),
		'reset' => array('type' => 'checkbox', 'label' => 'Delete existing translations before import'),
		'import' => array('type' => 'file', 'label' => 'Import file. json or plist format'),
	)
));
