<?php
$sections = array();
if (count($this->request['pass']) < 4) {
	$split = true;
} else {
	$split = false;
 	$sections['all']['title'] = $this->Html->link("Back to all {$this->request['pass'][0]} translations", array($this->request['pass'][0]));
}

foreach ($default as $key => $string) {
	$input = array(
		'label' => "dummy." . str_replace('.', ' ', strtolower($key)),
		'placeholder' => $string,
		'value' => !empty($toEdit[$key]) ? $toEdit[$key] : "",
		'type' => 'textarea',
		'rows' => 3,
		'cols' => 20,
		'style' => 'width:600px', // All textareas are 258px wide? wtfns
		'data-locale' => $locale,
		'data-domain' => $domain,
		'data-key' => $key,
		'before' => '<div class="pull-right"><button class="btn btn-primary disabled">Save</button></div>'
	);

	if ($split) {
		if (preg_match('/^(\w+\.)(\w+\.?)*$/', $key)) { // for keys of format xxx.yyy.zzz
			list($section, $rest) = explode('.', $key, 2);
		} else {
			$section = 'Free Text';
			$rest = $key;
		}
		if (empty($sections[$section])) {
 			$sections[$section]['title'] = Inflector::humanize(Inflector::underscore($section));
		}
		$input['label'] = "dummy." . str_replace('.', ' ', strtolower($rest));
 		$sections[$section]['columns'][str_replace('.', '¿', $key)] = $input;
	} else {
 		$sections['all']['columns'][str_replace('.', '¿', $key)] = $input;
	}
}
if (count($sections) > 1) {
	foreach ($sections as $name => &$section) {
		$section['columns'][$name] = function($view, $column, $model, $baseUrl) {
			?>
			<div class="form-actions">
				<button type="submit" class="btn btn-primary"><?php echo __d('common', 'Save changes');?></button>
				<?php echo $view->Html->link('View Just this section', array($view->request['pass'][0], $column), array('class' => 'btn')); ?>
			</div>
			<?php
		};
	}
}

echo $this->element('Shared.Crud/form', array(
		'model' => 'Translation',
		'title' => 'Edit translations',
		'sections' => $sections
));
