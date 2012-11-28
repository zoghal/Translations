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
					'domain' => 'simple',
					'category' => 'LC_MESSAGES',
					'key' => 'foo',
					'value' => 'foo value'
				)
			),
			'settings' => array(
				'domain' => 'simple'
			)
		);
		$this->assertSame($expected, $result);
	}

	public function testParsePlural() {
		$path = CakePlugin::path('Translations') . 'Test/Files/plural.po';
		$result = PoParser::parse($path);

		$expected = array(
			'count' => 3,
			'translations' => array(
				array(
					'locale' => 'en',
					'domain' => 'plural',
					'category' => 'LC_MESSAGES',
					'key' => '%d post',
					'value' => '1 post',
				),
				array(
					'locale' => 'en',
					'domain' => 'plural',
					'category' => 'LC_MESSAGES',
					'key' => '%d posts',
					'value' => '1 post',
					'single_key' => '%d post',
					'plural_case' => 0
				),
				array(
					'locale' => 'en',
					'domain' => 'plural',
					'category' => 'LC_MESSAGES',
					'key' => '%d posts',
					'value' => '%d many posts',
					'single_key' => '%d post',
					'plural_case' => 1
				)
			),
			'settings' => array(
				'domain' => 'plural'
			)
		);
		$this->assertSame($expected, $result);
	}
}
