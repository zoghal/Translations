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
		'plugin.translations.translation',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		// Load config
		$this->config = array(
			'Boundary.enable' => Configure::read('Boundary.enable'),
			'Config.language' => Configure::read('Config.language')
		);
		Configure::write('Boundary.enable', false);
		Configure::write('Config.language', 'en');

		// Load translations
		$this->Translation = ClassRegistry::init('Translations.Translation');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		// Re-apply old config
		foreach ($this->config as $key => $value) {
			Configure::write($key, $value);
		}

		unset($this->Translation, $this->config);
		parent::tearDown();
	}

	public function testForLocaleFlat() {
		$result = $this->Translation->forLocale('en', array('nested' => false));

		$expected = array(
			'key.with.param' => 'Value with {param}',
			'key_one' => 'Value One',
			'key_two' => 'Value Two',
			'nested.key.one' => 'Nested Value One',
			'nested.key.two' => 'Nested Value Two',
			'numerical.key.0' => 'Numerical Value One',
			'numerical.key.1' => 'Numerical Value Two',
		);

		$this->assertSame($expected, $result);
	}

	public function testForLocaleNested() {
		$result = $this->Translation->forLocale();

		$expected = array(
			'key' => array(
				'with' => array(
					'param' => 'Value with {param}'
				)
			),
			'key_one' => 'Value One',
			'key_two' => 'Value Two',
			'nested' => array (
				   'key' => array (
					   'one' => 'Nested Value One',
					   'two' => 'Nested Value Two'
				   )
			),
			'numerical' => array (
				   'key' => array (
					   'Numerical Value One',
					   'Numerical Value Two'
				   )
			)
		);

		$this->assertSame($expected, $result);
	}

	public function testForSettingLanguageConfig() {
		Configure::write('Config.language', 'no');
		$result = $this->Translation->forLocale();

		$expected = array(
			'key' => array(
				'with' => array(
					'param' => 'Verdi med {param}'
				)
			),
			'key_one' => 'Verdi En',
			'key_two' => 'Verdi To',
			'nested' => array (
				   'key' => array (
					   'one' => 'Dyp Verdi En',
					   'two' => 'Dyp Verdi To'
				   )
			),
			'numerical' => array (
				   'key' => array (
					   'Tall Verdi En',
					   'Tall Verdi To'
				   )
			)
		);

		$this->assertSame($expected, $result);
	}

	public function testForDefaultTranslate() {
		$result = Translation::translate('key_one');
		$expected = 'Value One';
		$this->assertSame($expected, $result);

		$result = Translation::translate('nested.key.one');
		$expected = 'Nested Value One';
		$this->assertSame($expected, $result);

		$result = Translation::translate('numerical.key.0');
		$expected = 'Numerical Value One';
		$this->assertSame($expected, $result);
	}

	public function testForChangedLocaleTranslate() {
		Configure::write('Config.language', 'no');
		$result = Translation::translate('key_two');
		$expected = 'Verdi To';
		$this->assertSame($expected, $result);

		$result = Translation::translate('nested.key.two');
		$expected = 'Dyp Verdi To';
		$this->assertSame($expected, $result);

		$result = Translation::translate('numerical.key.1');
		$expected = 'Tall Verdi To';
		$this->assertSame($expected, $result);
	}

	public function testForChangingLocale() {
		$result = Translation::translate('key_one');
		$expected = 'Value One';
		$this->assertSame($expected, $result);

		Configure::write('Config.language', 'no');
		$result = Translation::translate('key_one');
		$expected = 'Verdi En';
		$this->assertSame($expected, $result);

		Configure::write('Config.language', 'en');
		$result = Translation::translate('key_one');
		$expected = 'Value One';
		$this->assertSame($expected, $result);
	}

	public function testForMissingLocale() {
		Configure::write('Config.language', 'de');
		$result = Translation::translate('key_one');
		$expected = 'key_one';
		$this->assertSame($expected, $result);

		Configure::write('Config.language', 'THIS IS NOT A LANGUAGE CODE');
		$result = Translation::translate('key_one');
		$expected = 'key_one';
		$this->assertSame($expected, $result);
	}

	public function testForMissingTranslation() {
		$result = Translation::translate('non-existant key');
		$expected = 'non-existant key';
		$this->assertSame($expected, $result);
	}

	public function testForHelperFunction() {
		$result = t('key_one');
		$expected = 'Value One';
		$this->assertSame($expected, $result);

		$result = t('key.with.param', array('param' => 'PARAMETER'));
		$expected = 'Value with PARAMETER';
		$this->assertSame($expected, $result);

		$result = t('key.with.param');
		$expected = 'Value with {param}';
		$this->assertSame($expected, $result);

		Configure::write('Config.language', 'no');
		$result = t('key.with.param');
		$expected = 'Verdi med {param}';
		$this->assertSame($expected, $result);
	}

	public function testForCachedLocale() {
		Configure::write('Config.langauge', 'no');
		Configure::delete('Config.language');
		$result = t('key_one');
		$expected = 'Verdi En';
		$this->assertSame($expected, $result);
	}
}
