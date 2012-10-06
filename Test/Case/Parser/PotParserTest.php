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

	public function testParseCake() {
		$path = CakePlugin::path('Translations') . 'Test/Files/cake.pot';
		$result = PotParser::parse($path);

		$next = $result['translations'][0];
		$expected = array(
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'Error',
			'value' => '',
			'references' => array(
				'View/Errors/error400.ctp:21',
				'View/Errors/error500.ctp:21',
			)
		);
		$this->assertSame($expected, $next);

		$next = $result['translations'][1];
		$expected = array(
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'The requested address %s was not found on this server.',
			'value' => '',
			'references' => array(
				'View/Errors/error400.ctp:23',
			)
		);
		$this->assertSame($expected, $next);
	}
}
