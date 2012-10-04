<?php
$row_actions = array(
	'10_view' => false,
	'20_edit' => false,
	'20_edit_locale' => array(
		'url' 	=> array('action' => 'edit_locale', '{{Translation.locale}}', '{{Translation.domain}}', '{{Translation.category}}', '{{Translation.ns}}'),
		'label'	=> 'Edit',
		'title'	=> '<i class="icon-app-edit"></i>'
	),
);
$this->set('row_actions', $row_actions);

echo $this->element('Shared.Crud/index', array(
	'model' => 'Translation',
	'title' => 'Translations List',
	'columns' => array(
		'locale' => function($view, $item, $model, $baseUrl) {
			return $view->viewVars['locales'][$item[$model]['locale']];
		},
		'key',
		'value' => array(
			'name' => 'value',
			'callback' => function($view, $item, $model, $baseUrl) use ($locale) {
				return $view->Text->truncate(__($item['Translation']['key'], array('locale' => $locale)), 100);
			}
		)
	),
	'top_actions' => array(
		'99_locales' => function($View, $model, $url) {
			return $View->Html->link(
				'Add Localization',
				array('action' => 'add_locale'),
				array('class' => 'btn')
			);
		}
	)
));
