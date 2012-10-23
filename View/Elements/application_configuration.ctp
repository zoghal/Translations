<?php App::uses('Translation', 'Translations.Model'); ?>

<!-- Link to translations -->
<fieldset>
	<h3><?php echo __('Translations'); ?></h3>
	<div class="control-group">
		<div class="descr">
			<label class="control-label"><?php echo __('Available Localizations'); ?></label>
		</div>
		<div class="controls">
			<?php
			foreach (Translation::locales(false) as $key => $locale) {
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
			} ?>
		</div>
	</div>
</fieldset>
