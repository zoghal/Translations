<?php

App::uses('JsonParser', 'Translations.Parser');

class JsonParserTest extends CakeTestCase {

	public function testParse() {
		$path = CakePlugin::path('Translations') . 'Test/Files/simple.json';
		$result = JsonParser::parse($path);

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
