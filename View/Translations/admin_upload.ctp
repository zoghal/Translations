<?php
echo $this->element('Shared.Crud/form', array(
	'formOptions' => array('type' => 'file'),
	'model' => 'Translation',
	'title' => 'Upload translations',
	'columns' => array(
		'locale' => array(),
		'reset' => array('type' => 'checkbox', 'label' => 'Delete existing values for this locale'),
		'upload' => array('type' => 'file', 'label' => 'IOS plist file'),
	)
));
