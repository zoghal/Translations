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
