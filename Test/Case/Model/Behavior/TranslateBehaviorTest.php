<?php

App::uses('Model', 'Model');
App::uses('AppModel', 'Model');

class TranslateBehaviorTest extends CakeTestCase {

/**
 * fixtures property
 *
 * @var array
 */
	public $fixtures = array(
		'core.tag',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		Translation::reset();
		$this->Tag = ClassRegistry::init('Tag');
		$this->Tag->displayField = 'tag';
		$this->Tag->Behaviors->attach('Translations.Translate', array('fields' => 'tag'));
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();

		Translation::reset();
		unset($this->Tag);
	}

/**
 * testReadTranslation
 *
 * Reading a translated field value should return the translated value
 *
 * @return void
 */
	public function testReadTranslation() {
		Translation::update('Tag.tag.tag1', 'Foo');
		Translation::update('Tag.tag.tag3', 'Zum');
		$expected = array(
			1 => 'Foo',
			'tag2',
			'Zum'
		);

		$result = $this->Tag->find('list');
		$this->assertSame($expected, $result);

		Translation::update('Tag.tag.tag2', 'Bar');

		$expected = array(
			1 => 'Foo',
			'Bar',
			'Zum'
		);

		$result = $this->Tag->find('list');
		$this->assertSame($expected, $result);
	}

/**
 * testUpdateTranslation
 *
 * Updating a translated field should not update the db record - but instead update the translation record
 *
 * @return void
 */
	public function testUpdateTranslation() {
		$expected = array(
			1 => 'tag1',
			'tag2',
			'tag3'
		);

		$this->Tag->id = 1;
		$this->Tag->saveField('tag', 'Updated');

		$this->Tag->Behaviors->disable('Translate');
		$result = $this->Tag->find('list');
		$this->assertSame($expected, $result);

		$expected[1] = 'Updated';

		$this->Tag->Behaviors->enable('Translate');
		$result = $this->Tag->find('list');
		$this->assertSame($expected, $result);

		$expected = array(
			'Tag.tag.tag1' => 'Updated'
		);
		$translations = Translation::forLocale();
		$this->assertSame($expected, $translations);
	}
}
