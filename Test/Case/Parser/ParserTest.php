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
			'it\'s a normal key' => 'bar',
			'and another' => 'bar',
		);

		$settings = array(
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
		);

		$expected = array(
			'count' => 3,
			'translations' => array(
				array(
					'locale' => 'en',
					'domain' => 'default',
					'category' => 'LC_MESSAGES',
					'key' => 'foo',
					'value' => 'bar'
				),
				array(
					'locale' => 'en',
					'domain' => 'default',
					'category' => 'LC_MESSAGES',
					'key' => 'it\'s a normal key',
					'value' => 'bar'
				),
				array(
					'locale' => 'en',
					'domain' => 'default',
					'category' => 'LC_MESSAGES',
					'key' => 'and another',
					'value' => 'bar'
				)
			)
		);
		$result = TestParser::parseArray($data, $settings);
		$this->assertSame($expected, $result);
	}

/**
 * testParseArrayCamelCased
 *
 * Mobile team send us files with camel cased keys - on some project. So butcher them
 *
 * @return void
 */
	public function testParseArrayCamelCased() {
		$data = array(
			'camelCasedKey' => 'bar',
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
					'key' => 'camel.cased.key',
					'value' => 'bar'
				)
			)
		);
		$result = TestParser::parseArray($data, $settings);
		$this->assertSame($expected, $result, 'camel cased keys are expected to be converted to dot delimited');
	}
}
