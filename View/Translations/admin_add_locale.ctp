<h2>Add Localization</h2>
<div class="edit">
	<?php echo $this->Form->create('Localization'); ?>
	<fieldset>
		<?php
		echo $this->Form->input('locale', array(
			'type' => 'select'
		));
		echo $this->Form->input('based_on', array(
			'type' => 'select',
			'options' => $based_on
		));
		?>
	</fieldset>
	<div class="form-actions">
		<button type="submit" class="btn btn-primary">Add Locale</button>
		<?php
		echo $this->Html->link(
			__('Cancel'),
			array(
				'action' => 'index'
			),
			array('class' => 'btn')
		);
		?>
	</div>
	<?php echo $this->Form->end(); ?>
</div>
