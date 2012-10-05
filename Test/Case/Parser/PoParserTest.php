<?php

App::uses('PoParser', 'Translations.Parser');

class PoParserTest extends CakeTestCase {

	public function testParse() {
		$path = CakePlugin::path('Translations') . 'Test/Files/simple.po';
		$result = PoParser::parse($path);

		$expected = array(
			'count' => 1,
			'translations' => array(
				array(
					'locale' => 'en',
					'domain' => 'default',
					'category' => 'LC_MESSAGES',
					'key' => 'foo',
					'value' => 'bar'
				)
			)
		);
		$this->assertSame($expected, $result);
	}
}
