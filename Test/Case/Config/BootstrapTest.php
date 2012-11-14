<?php
/**
 * BootstrapTest
 *
 */
class BootstrapTest extends CakeTestCase {

	public $fixtures = array(
		'plugin.translations.translation',
	);

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		$files = get_included_files();
		$overrides = realpath(CakePlugin::path('Translations') . 'Config/bootstrap.php');
		$this->skipIf(!in_array($overrides, $files), "Cannot test t function if the bootstrap file isn't loaded");

		parent::setUp();

		// Load config
		$this->config = array(
			'Config.defaultLanguage' => Configure::read('Config.defaultLanguage'),
			'Config.language' => Configure::read('Config.language'),
			'Cache.disable' => Configure::read('Cache.disable')
		);
		Configure::write('Config.defaultLanguage', 'en');
		Configure::write('Config.language', 'en');

		ClassRegistry::removeObject('Translation');
		$this->Translation = ClassRegistry::init('Translations.Translation');

		Cache::clear(true, 'default');
		Translation::reset();
		Translation::config(array(
			'useTable' => 'translations',
			'cacheConfig' => false,
			'autoPopulate' => false
		));
	}

/**
 * tearDown method
 *
 * Reapply original config and destroy traces of the translate model
 *
 * @return void
 */
	public function tearDown() {
		foreach ($this->config as $key => $value) {
			Configure::write($key, $value);
		}

		ClassRegistry::removeObject('Translation');
		Translation::reset();

		unset($this->Translation, $this->config);

		parent::tearDown();
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

}
