<?php

App::uses('PotParser', 'Translations.Parser');

class PotParserTest extends CakeTestCase {

	public function testParse() {
		$path = CakePlugin::path('Translations') . 'Test/Files/simple.pot';
		$result = PotParser::parse($path);

		$expected = array(
			'count' => 1,
			'translations' => array(
				array(
					'locale' => 'en',
					'domain' => 'default',
					'category' => 'LC_MESSAGES',
					'key' => 'foo',
					'value' => ''
				)
			)
		);
		$this->assertSame($expected, $result);
	}
}
