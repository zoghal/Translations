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
		'plugin.translations.translation'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		$this->config = array(
			'Config.defaultLanguage' => Configure::read('Config.defaultLanguage'),
			'Config.language' => Configure::read('Config.language'),
			'Cache.disable' => Configure::read('Cache.disable')
		);
		Configure::write('Config.defaultLanguage', 'en');
		Configure::write('Config.language', 'en');

		Translation::reset();
		$this->Tag = ClassRegistry::init('Tag');
		$this->Tag->displayField = 'tag';
		$this->Tag->Behaviors->attach('Translations.Translate', array('fields' => 'tag'));
	}

/**
 * tearDown method
 *
 * Reapply original config
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();

		foreach ($this->config as $key => $value) {
			Configure::write($key, $value);
		}

		Translation::reset();
		unset($this->Tag);
	}

/**
 * testCount
 *
 * A count is an example of a query that shouldn't get hijacked
 *
 * @return void
 */
	public function testCount() {
		$result = $this->Tag->find('count');
		$this->assertSame(3, $result);
	}

/**
 * testReadTranslation
 *
 * Reading a translated field value should return the translated value
 *
 * @return void
 */
	public function testReadTranslation() {
		Translation::update('Tag.1.tag', 'Foo', array('domain' => 'data'));
		Translation::update('Tag.3.tag', 'Zum', array('domain' => 'data'));
		$expected = array(
			1 => 'Foo',
			'tag2',
			'Zum'
		);

		$result = $this->Tag->find('list');
		$this->assertSame($expected, $result);

		Translation::update('Tag.2.tag', 'Bar', array('domain' => 'data'));

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
 * Updating a translated field now should update the db record
 *
 * @return void
 */
	public function testUpdateTranslation() {
		$expected = array(
			1 => 'Updated',
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
			'Tag.1.tag' => 'Updated',
			'Tag.2.tag' => 'tag2',
			'Tag.3.tag' => 'tag3',
		);
		$translations = Translation::forLocale(null, array(
			'domain' => 'data',
			'section' => 'Tag',
			'nested' => false
		));
		$this->assertSame($expected, $translations);
	}

/**
 * testSaveAll
 *
 * Updating a translated field should now update the db record - if it's for the default language
 *
 * @return void
 */
	public function testSaveAll() {
		$expected = array(
			1 => 'Updated',
			'tag2',
			'tag3'
		);

		$this->Tag->saveAll(array(
			'Tag' => array(
				'id' => 1,
				'tag' => 'Updated'
			)
		));

		$this->Tag->Behaviors->disable('Translate');
		$result = $this->Tag->find('list');
		$this->assertSame($expected, $result);

		$expected[1] = 'Updated';

		$this->Tag->Behaviors->enable('Translate');
		$result = $this->Tag->find('list');
		$this->assertSame($expected, $result);

		$expected = array(
			'Tag.1.tag' => 'Updated',
			'Tag.2.tag' => 'tag2',
			'Tag.3.tag' => 'tag3',
		);
		$translations = Translation::forLocale(null, array(
			'domain' => 'data',
			'section' => 'Tag',
			'nested' => false
		));
		$this->assertSame($expected, $translations);
	}

/**
 * testAutoPopulate
 *
 * @return void
 */
	public function testAutoPopulate() {
		$this->Tag->find('all');

		$expected = array(
			'Tag.1.tag' => 'tag1',
			'Tag.2.tag' => 'tag2',
			'Tag.3.tag' => 'tag3',
		);
		$translations = Translation::forLocale(null, array(
			'domain' => 'data',
			'section' => 'Tag',
			'nested' => false
		));
		$this->assertSame($expected, $translations);
	}

/**
 * testAutoPopulateVirtualField
 *
 * @return void
 */
	public function testAutoPopulateVirtualField() {
		$this->Tag->virtualFields = array(
			'description' => 'CONCAT("description for: ", Tag.id)'
		);
		$this->Tag->Behaviors->attach('Translations.Translate', array('fields' => 'description'));

		$this->Tag->find('all');

		$expected = array(
			'Tag.1.description' => 'description for: 1',
			'Tag.2.description' => 'description for: 2',
			'Tag.3.description' => 'description for: 3',
		);
		$translations = Translation::forLocale(null, array(
			'domain' => 'data',
			'section' => 'Tag',
			'nested' => false
		));
		$this->assertSame($expected, $translations);
	}

/**
 * testAutoPopulateVirtualFieldFixedString
 *
 * @return void
 */
	public function testAutoPopulateVirtualFieldFixedString() {
		$this->Tag->virtualFields = array(
			'description' => '"description"'
		);
		$this->Tag->Behaviors->attach('Translations.Translate', array('fields' => 'description'));

		$this->Tag->find('all');

		$expected = array(
			'Tag.1.description' => 'description',
			'Tag.2.description' => 'description',
			'Tag.3.description' => 'description',
		);
		$translations = Translation::forLocale(null, array(
			'domain' => 'data',
			'section' => 'Tag',
			'nested' => false
		));
		$this->assertSame($expected, $translations);
	}

/**
 * testUpdateVirtualField
 *
 * @return void
 */
	public function testUpdateVirtualField() {
		$this->Tag->virtualFields = array(
			'description' => '"description"'
		);
		$this->Tag->Behaviors->attach('Translations.Translate', array('fields' => 'description'));

		$this->Tag->find('all');

		$this->Tag->id = 1;
		$this->Tag->save(array(
			'description' => 'Glorious technicolor'
		));

		$expected = array(
			'Tag.1.description' => 'Glorious technicolor',
			'Tag.2.description' => 'description',
			'Tag.3.description' => 'description',
		);
		$translations = Translation::forLocale(null, array(
			'domain' => 'data',
			'section' => 'Tag',
			'nested' => false
		));
		$this->assertSame($expected, $translations);

		$expected = array(
			'1' => 'Glorious technicolor',
			'2' => 'description',
			'3' => 'description',
		);
		$result = $this->Tag->find('list', array('fields' => array('id', 'description')));
		$this->assertSame($expected, $result);
	}

/**
 * testCreate
 *
 * Add a test for simple create
 *
 * @return void
 */
	public function testCreate() {
		$this->Tag->create();
		$this->Tag->save(array(
			'tag' => 'new'
		));

		$expected = array(
			1 => 'tag1',
			'tag2',
			'tag3',
			'new'
		);

		$this->Tag->Behaviors->disable('Translate');
		$result = $this->Tag->find('list');
		$this->assertSame($expected, $result);

		$this->Tag->Behaviors->enable('Translate');
		$result = $this->Tag->find('list');
		$this->assertSame($expected, $result);

		$expected = array(
			'Tag.1.tag' => 'tag1',
			'Tag.2.tag' => 'tag2',
			'Tag.3.tag' => 'tag3',
			'Tag.4.tag' => 'new',
		);
		$translations = Translation::forLocale(null, array(
			'domain' => 'data',
			'section' => 'Tag',
			'nested' => false
		));
		ksort($translations);
		$this->assertSame($expected, $translations);
	}

/**
 * testCreateNotDefaultLanguage
 *
 * If a record is created in a not-default language, it should still store
 * in the original field the translated value. While this isn't particularly
 * correct - it's better than the original translated field being blank in the
 * default language - and therefore in all other langauges.
 *
 * @return void
 */
	public function testCreateNotDefaultLanguage() {
		Configure::write('Config.defaultLanguage', 'en');
		Configure::write('Config.language', 'es');

		$this->Tag->create();
		$this->Tag->save(array(
			'tag' => 'nuevo'
		));

		$expected = array(
			1 => 'tag1',
			'tag2',
			'tag3',
			'nuevo'
		);

		$this->Tag->Behaviors->disable('Translate');
		$result = $this->Tag->find('list');
		$this->assertSame($expected, $result);

		$this->Tag->Behaviors->enable('Translate');
		$result = $this->Tag->find('list');
		$this->assertSame($expected, $result);

		$expected = array(
			'Tag.1.tag' => 'tag1',
			'Tag.2.tag' => 'tag2',
			'Tag.3.tag' => 'tag3',
			'Tag.4.tag' => 'nuevo',
		);
		$translations = Translation::forLocale('en', array(
			'domain' => 'data',
			'section' => 'Tag',
			'nested' => false
		));
		ksort($translations);
		$this->assertSame($expected, $translations);
	}
}
