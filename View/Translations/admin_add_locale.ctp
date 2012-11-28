<?php
$this->element('top_actions');
echo $this->element('Shared.Crud/form', array(
	'model' => 'Translation',
	'title' => 'Add Localization',
	'columns' => array(
		'locale',
		'based_on' => array(
			'type' => 'select',
			'options' => $based_on
		)
	),
));
