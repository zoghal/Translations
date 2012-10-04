<?php

App::uses('TranslateInjector', 'Translations.Iterator');

class TranslateInjectorTest extends CakeTestCase {

	public function testEmptyArray() {
		$iterator = new TranslateInjector(array(), array());
		$data = iterator_to_array($iterator);
		$this->assertEmpty($data);
	}

	public function testSingleItem() {
		Translation::update('Model.1.field', 'Updated foo');
		Translation::update('Model.1.another_field', 'Updated bar');

		$in = array(
			array(
				'Model' => array(
					'id' => 1,
					'field' => 'foo',
					'another_field' => 'bar'
				),
				'AnotherModel' => array(
					'id' => 2,
					'field' => 'foo',
					'another_field' => 'bar',
				)
			)
		);
		$iterator = new TranslateInjector(
			$in,
			array('Model.field', 'Model.another_field'),
			array(
				'modelAlias' => 'Model',
				'modelName' => 'Model',
			)
		);
		$data = iterator_to_array($iterator);

		$expected = array(
			array(
				'Model' => array(
					'id' => 1,
					'field' => 'Updated foo',
					'another_field' => 'Updated bar'
				),
				'AnotherModel' => array(
					'id' => 2,
					'field' => 'foo',
					'another_field' => 'bar',
				)
			)
		);
		$this->assertSame($expected, $data);
	}

}
