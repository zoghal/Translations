<?php
$top_actions = isset($top_actions) ? $top_actions : array();

$top_actions['000_language_switch'] = $this->element('language_switch');

if($this->plugin === 'Translations') {
	$top_actions['011_import'] = $this->Html->link(
		__d('translations', 'Import'),
		array('action' => 'import'),
		array('class' => 'btn')
	);
	$top_actions['012_export'] = $this->Html->link(
		__d('translations', 'Export'),
		array('action' => 'export'),
		array('class' => 'btn')
	);
	if($this->action === 'admin_index') {
		$top_actions['099_locales'] = $this->Html->link(
			__d('translations', 'Add Locale'),
			array('action' => 'add_locale'),
			array('class' => 'btn')
		);
	}
}

$this->set(compact('top_actions'));
