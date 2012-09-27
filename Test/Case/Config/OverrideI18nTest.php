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

		$return = __('Simple %s', array('name' => 'something'));
		$this->assertSame('Simple something', $return);

		$return = __('Single %s', 'something');
		$this->assertSame('Single something', $return);

		$return = __('Multiple %s %s', 'something', 'else');
		$this->assertSame('Multiple something else', $return);

		$return = __('Simple {name}', array('name' => 'something'));
		$this->assertSame('Simple something', $return);

		$return = __('Single {replace}', 'something');
		$this->assertSame('Single something', $return);

		$return = __('Multiple {replace} {markers}', 'something');
		$this->assertSame('Multiple something {markers}', $return);

		$return = __('Multiple {replace} {markers}', 'something', 'else');
		$this->assertSame('Multiple {replace} {markers}', $return, 'When using named markers, if multiple args are passed they should be ignored (as it\'s ambiguous)');
	}

/**
 * testSubstitutionDomain
 *
 * @return void
 */
	public function testSubstitutionDomain() {
		$return = __d('testing', 'Simple');
		$this->assertSame('Simple', $return);

		$return = __d('testing', 'Simple %s', array('name' => 'something'));
		$this->assertSame('Simple something', $return);

		$return = __d('testing', 'Single %s', 'something');
		$this->assertSame('Single something', $return);

		$return = __d('testing', 'Multiple %s %s', 'something', 'else');
		$this->assertSame('Multiple something else', $return);

		$return = __d('testing', 'Simple {name}', array('name' => 'something'));
		$this->assertSame('Simple something', $return);

		$return = __d('testing', 'Single {replace}', 'something');
		$this->assertSame('Single something', $return);

		$return = __d('testing', 'Multiple {replace} {markers}', 'something');
		$this->assertSame('Multiple something {markers}', $return);

		$return = __d('testing', 'Multiple {replace} {markers}', 'something', 'else');
		$this->assertSame('Multiple {replace} {markers}', $return, 'When using named markers, if multiple args are passed they should be ignored (as it\'s ambiguous)');
	}
}
