<?php

App::uses('Parser', 'Translations.Parser');

class TestParser extends Parser {

	public static function parseArray($translations, $defaults) {
		return self::_parseArray($translations, $defaults);
	}
}

class ParserTest extends CakeTestCase {

	public function testParseArray() {
		$data = array(
			'foo' => 'bar',
		);

		$settings = array(
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
		);


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
		$result = TestParser::parseArray($data, $settings);
		$this->assertSame($expected, $result);
	}
}
