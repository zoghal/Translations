<?php
echo $this->element('Shared.Crud/form', array(
	'formOptions' => array('type' => 'file'),
	'model' => 'Translation',
	'title' => __d('translations', 'Upload translations'),
	'sections' => array(
		array(
			'title' => __d('translations', 'File settings'),
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
				'import' => array(
					'type' => 'file', 'label' => 'Import file',
					'help' => __d('translations', 'Supported formats: json, pot, po or plist')
				)
			)
		),
		array(
			'title' => 'Options',
			'columns' => array(
				'overwrite' => array(
					'type' => 'checkbox',
					'default' => true,
					'help' => __d('translations', 'Overwrite existing translations - or just create new ones?')
				),
				'purge' => array(
					'type' => 'checkbox',
					'default' => false,
					'help' => __d('translations', 'Delete translations that are not in the import file?')
				)
			)
		)
	)
));
