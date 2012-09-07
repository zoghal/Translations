<?php
echo $this->element('Shared.Crud/form', array(
	'model' => 'Localization',
	'title' => 'Add Localization',
	'columns' => array(
		'locale',
		'based_on' => array(
			'type' => 'select',
			'options' => $based_on
		)
	),
));
