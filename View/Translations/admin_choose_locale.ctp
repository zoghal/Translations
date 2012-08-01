<?php
echo $this->element('Shared.Crud/form', array(
	'model' => 'Translation',
	'title' => 'Choose locale',
	'columns' => array(
		'locale' => array('default' => 'en'),
	),
));
