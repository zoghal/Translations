<?php
App::uses('TranslationsRemoteSource', 'Translations.Model/Datasource');

class TranslationsRemoteSourceTest extends CakeTestCase {

	public function setUp() {
		$this->model = ClassRegistry::init('Translations.Translation');

		$this->ds = $this->getMock(
			'TranslationsRemoteSource',
			array('_curl'),
			array(
				array('host' => 'http://somedomain.com/:locale/:domain/:category')
			)
		);
		$class = get_class($this->ds);
		$class::$methodCache = array();
	}

	public function testRead() {
		$result = array(
			'success' => true,
			'data' => array(
				'Translation' => array(
					'foo' => 'bar'
				)
			)
		);

		$queryData = array(
			'conditions' => array(
				'locale' => 'en',
				'domain' => 'default',
				'category' => 'LC_MESSAGES'
			),
			'fields' => null
		);

		$this->ds
			->expects($this->once())
			->method('_curl')
			->with('http://somedomain.com/en/default/LC_MESSAGES')
			->will($this->returnValue($result));

		$expected = array(
			array(
				'Translation' => array(
					'key' => 'foo',
					'value' => 'bar',
					'plural_case' => null,
					'locale' => 'en',
					'domain' => 'default',
					'category' => 'LC_MESSAGES'
				)
			)
		);
		$return = $this->ds->read($this->model, $queryData);
		$this->assertSame($expected, $return);
	}

	public function testReadPlural() {
		$result = array(
			'success' => true,
			'data' => array(
				'Translation' => array(
					'foo' => array(
						'0 bar',
						'1 bar',
					)
				)
			)
		);

		$queryData = array(
			'conditions' => array(
				'locale' => 'en',
				'domain' => 'default',
				'category' => 'LC_MESSAGES'
			),
			'fields' => null
		);

		$this->ds
			->expects($this->once())
			->method('_curl')
			->with('http://somedomain.com/en/default/LC_MESSAGES')
			->will($this->returnValue($result));

		$expected = array(
			array(
				'Translation' => array(
					'key' => 'foo',
					'value' => '0 bar',
					'plural_case' => 0,
					'locale' => 'en',
					'domain' => 'default',
					'category' => 'LC_MESSAGES'
				)
			),
			array(
				'Translation' => array(
					'key' => 'foo',
					'value' => '1 bar',
					'plural_case' => 1,
					'locale' => 'en',
					'domain' => 'default',
					'category' => 'LC_MESSAGES'
				)
			),
		);
		$return = $this->ds->read($this->model, $queryData);
		$this->assertSame($expected, $return);
	}

/**
 * testReadCached
 *
 * There should only be one call the api, even if there are multiple request issued during the request
 *
 * @return void
 */
	public function testReadCached() {
		$result = array(
			'success' => true,
			'data' => array(
				'Translation' => array(
					'foo' => 'bar'
				)
			)
		);

		$queryData = array(
			'conditions' => array(
				'locale' => 'en',
				'domain' => 'default',
				'category' => 'LC_MESSAGES'
			),
			'fields' => null
		);

		$this->ds
			->expects($this->once())
			->method('_curl')
			->with('http://somedomain.com/en/default/LC_MESSAGES')
			->will($this->returnValue($result));

		$expected = array(
			array(
				'Translation' => array(
					'key' => 'foo',
					'value' => 'bar',
					'plural_case' => null,
					'locale' => 'en',
					'domain' => 'default',
					'category' => 'LC_MESSAGES'
				)
			)
		);
		$return = $this->ds->read($this->model, $queryData);
		$this->assertSame($expected, $return);

		$expected = array(
			array(
				'Translation' => array(
					'key' => 'foo',
					'value' => 'bar',
					'plural_case' => null,
					'locale' => 'en',
					'domain' => 'default',
					'category' => 'LC_MESSAGES'
				)
			)
		);
		$return = $this->ds->read($this->model, $queryData);
		$this->assertSame($expected, $return);

	}
}
