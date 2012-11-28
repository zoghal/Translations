<?php
$locales = Translation::locales();
echo $this->Form->select('locale', $locales, array(
	'empty' => __d('translations', 'change locale'),
	'id' => 'localeChange',
	'data-index-url' => Router::url(array('action' => 'index', null)),
	'data-ping-url' => Router::url(array('action' => 'set_locale', 'ext' => 'json')),
	'value' => Configure::read('Config.language'),
	'class' => 'btn'
));
$this->Html->scriptBlock(
	"
	(function($) {
		$('#localeChange').change(function() {
			$.post($(this).data('ping-url'), { locale: $(this).val() }, function() {
				document.location = $(this).data('index-url') + '/' + $(this).val();
			});
		});
	 }(jQuery));
	",
	array(
		'inline' => false,
		'safe' => false
	)
);
