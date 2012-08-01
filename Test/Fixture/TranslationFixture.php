<?php
/**
 * TranslationFixture
 *
 */
class TranslationFixture extends CakeTestFixture {

/**
 * Fields
 *
 * @var array
 */
	public $fields = array(
		'id' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 36, 'key' => 'primary', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'application_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'locale' => array('type' => 'string', 'null' => false, 'default' => 'en', 'length' => 5, 'key' => 'index', 'collate' => 'utf8_general_ci', 'comment' => 'ISO 3166 codes', 'charset' => 'utf8'),
		'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'value' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1),
			'translations_key' => array('column' => array('locale', 'key'), 'unique' => 0)
		),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c762',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'en',
			'key' => 'one.two.0.google',
			'value' => 'http://google.com'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c763',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'en',
			'key' => 'one.two.1.nodes',
			'value' => 'http://nodesagency.com'
		)
	);

}
