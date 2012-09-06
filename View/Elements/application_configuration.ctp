<?php App::uses('Translation', 'Translations.Model'); ?>

<!-- Link to translations -->
<fieldset>
	<h3><?php echo __('Translations'); ?></h3>
	<div class="control-group">
		<div class="descr">
			<label class="control-label"><?php echo __('Available Localizations'); ?></label>
		</div>
		<div class="controls">
			<ul class="locales">
				<?php
				$application = !empty($this->request->data['Application']['id']) ? $this->request->data['Application']['id'] : null;
				foreach (Translation::locales(false, array('application' => $application)) as $locale) {
					echo '<li>' . $locale . '</li>';
				} ?>
			</ul>
		</div>
	</div>
	<div class="controls">
		<?php echo $this->Html->link(
			__('Manage Translations'),
			array(
				'application_id' => $this->request->data['Application']['slug'],
				'admin' => true,
				'plugin' => 'translations',
				'controller' => 'translations'
			),
			array('class' => 'btn')
		); ?>
	</div>
</fieldset>
