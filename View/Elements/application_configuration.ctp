<?php
App::uses('Translation', 'Translations.Model');
if (!empty($this->request->data['Application']['locales'])) {
	$usedLocales = Translation::locales($this->request->data['Application']['locales']);
} else {
	$usedLocales = Translation::locales();
}
?>

<!-- Link to translations -->
<fieldset>
	<h3><?php echo __('Translations'); ?></h3>
	<div class="control-group">
		<div class="descr">
			<label class="control-label"><?php echo __d('translations', 'Languages'); ?></label>
		</div>
		<div class="controls">
			<?php
			foreach ($usedLocales as $key => $locale) {
					echo $this->Html->link(
						$locale,
						array(
							'application_id' => $this->request->data['Application']['slug'],
							'admin' => true,
							'plugin' => 'translations',
							'controller' => 'translations',
							$key
						),
						array('class' => 'btn')
					) . '<br>';
			}
			?>
		</div>
		<br />
		<?php
		$allLocales = Translation::locales(true);
		$locales = array_diff($allLocales, $usedLocales);

		$stem = implode(array_keys($usedLocales), ',');
		$options = array();
		foreach ($locales as $code => $name) {
			$key = $stem . ',' . $code;
			$options[$key] = $name;
		}

		$url = array('plugin' => 'translations', 'controller' => 'translations', 'action' => 'add_locale');
		echo $this->Form->input('locales', array(
			'options' => $options,
			'empty' => true,
			'label' => __d('translations', 'add locale')
		));
		?>
	</div>
</fieldset>
