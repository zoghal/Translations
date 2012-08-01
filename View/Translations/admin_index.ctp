<?php
$row_actions = array(
	'10_view' => false,
	'20_edit' => false,
	'20_edit_locale' => array(
		'url' 	=> array('action' => 'edit_locale', '{{Translation.locale}}'),
		'label'	=> 'Edit locale',
		'title'	=> '<i class="icon-app-edit"></i>'
	),
);
$this->set('row_actions', $row_actions);

echo $this->element('Shared.Crud/index', array(
	'model' => 'Translation',
	'title' => 'Translations List',
	'columns' => array(
		'locale',
		'key',
		'value'
	)
));
