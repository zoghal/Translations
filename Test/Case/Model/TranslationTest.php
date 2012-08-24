<?php
App::uses('Translation', 'Translations.Model');

/**
 * Translation Test Case
 *
 */
class TranslationTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.tuborg_coins.translation',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Translation = ClassRegistry::init('Translations.Translation');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Translation);

		parent::tearDown();
	}

	public function testForLocaleNested() {
		$result = $this->Translation->forLocale();

		$expected = array(
			'one' => array(
				'two' => array(
					array(
						'google' => 'http://google.com',
						'nodes' => 'http://nodesagency.com',
					)
				)
			)
		);
		$this->assertSame($expected, $result);
	}

	public function testForLocaleFlat() {
		$result = $this->Translation->forLocale('en', array('nested' => false));

		$expected = array(
			'one.two.0.google' => 'http://google.com',
			'one.two.1.nodes' => 'http://nodesagency.com'
		);
		$this->assertSame($expected, $result);
	}

}
