<?php

App::uses('PlistParser', 'Translations.Parser');

class PlistParserTest extends CakeTestCase {

	public function testParse() {
		$path = CakePlugin::path('Translations') . 'Test/Files/simple.plist';
		$result = PlistParser::parse($path);

		$expected = array(
			'count' => 1,
			'translations' => array(
				array(
					'locale' => 'en',
					'domain' => 'default',
					'category' => 'LC_MESSAGES',
					'key' => 'foo',
					'value' => 'foo value'
				)
			)
		);
		$this->assertSame($expected, $result);
	}
}
