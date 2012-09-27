<?php
/**
 * OverrideI18nTest
 *
 */
class OverrideI18nTest extends CakeTestCase {

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
		$overrides = CakePlugin::path('Translations') . 'Config/override_i18n.php';
		$this->skipIf(!in_array($overrides, $files), "Cannot test __ overrides if the config file isn't loaded");
	}

/**
 * testSubstitution
 *
 * @return void
 */
	public function testSubstitution() {
		$return = __('Simple');
		$this->assertSame('Simple', $return);

		$return = __('Simple {name}', array('name' => 'something'));
		$this->assertSame('Simple something', $return);
	}
}
