<?php
$locales = Translation::locales();
echo $this->Form->select('locale', $locales, array(
	'empty' => __d('translations', 'change locale'),
	'id' => 'localeChange',
	'data-root' => Router::url(array('action' => 'index', null)),
	'value' => Configure::read('Config.language'),
	'class' => 'btn'
));
$this->Html->scriptBlock(
	"
	(function($) {
		$('#localeChange').change(function() {
			document.location = $(this).data('root') + '/' + $(this).val();
		});
	 }(jQuery));
	",
	array(
		'inline' => false,
		'safe' => false
	)
);
