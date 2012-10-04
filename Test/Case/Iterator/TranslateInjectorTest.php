<?php

App::uses('TranslateInjector', 'Translations.Iterator');

class TranslateInjectorTest extends CakeTestCase {

	public function testEmptyArray() {
		$iterator = new TranslateInjector(array(), array());
		$data = iterator_to_array($iterator);
		$this->assertEmpty($data);
	}

	public function testSingleItem() {
		Translation::update('Model.field.foo', 'Updated foo');
		Translation::update('Model.another_field.bar', 'Updated bar');

		$in = array(
			array(
				'Model' => array(
					'field' => 'foo',
					'another_field' => 'bar'
				),
				'AnotherModel' => array(
					'field' => 'foo',
					'another_field' => 'bar',
				)
			)
		);
		$iterator = new TranslateInjector($in, array('Model.field', 'Model.another_field'));
		$data = iterator_to_array($iterator);

		$expected = array(
			array(
				'Model' => array(
					'field' => 'Updated foo',
					'another_field' => 'Updated bar'
				),
				'AnotherModel' => array(
					'field' => 'foo',
					'another_field' => 'bar',
				)
			)
		);
		$this->assertSame($expected, $data);
	}

}
