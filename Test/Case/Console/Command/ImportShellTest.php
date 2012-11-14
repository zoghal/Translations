<?php
App::uses('ImportShell', 'Translations.Console/Command');
App::uses('ConsoleOutput', 'Console');
App::uses('ConsoleInput', 'Console');

/**
 * Test case for Translations
 *
 **/
class ImportShellTest extends CakeTestCase {

	public $fixtures = array(
		'plugin.translations.translation',
	);

/**
 * Temporary storage for the real root folder
 *
 * @var string
 */
	protected $_root;

/**
 * setup test
 *
 * @return void
 */
	public function setUp() {
		$out = $this->getMock('ConsoleOutput', array(), array(), '', false);
		$in = $this->getMock('ConsoleInput', array(), array(), '', false);

		$this->Shell = $this->getMock(
			'ImportShell',
			array('in', 'out', 'hr', 'err', '_stop'),
			array($out, $out, $in)
		);

		parent::setUp();
	}

/**
 * Tear down
 *
 * @return void
 **/
	public function tearDown() {
		unset($this->Shell);
		parent::tearDown();
	}

/**
 * testImport
 *
 * @return void
 */
	public function testImport() {
	}
}
