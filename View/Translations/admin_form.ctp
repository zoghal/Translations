<?php
echo $this->element('Shared.Crud/form', array(
	'model' => 'Translation',
	'title' => 'Add/Edit translation',
	'columns' => array(
		'locale',
		'key',
		'value'
	)
));
