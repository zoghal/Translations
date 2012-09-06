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
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c761',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'en',
			'key' => 'key_one',
			'value' => 'Value One'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c762',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'en',
			'key' => 'key_two',
			'value' => 'Value Two'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c763',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'en',
			'key' => 'nested.key.one',
			'value' => 'Nested Value One'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c764',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'en',
			'key' => 'nested.key.two',
			'value' => 'Nested Value Two'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c765',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'en',
			'key' => 'numerical.key.0',
			'value' => 'Numerical Value One'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c766',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'en',
			'key' => 'numerical.key.1',
			'value' => 'Numerical Value Two'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c767',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'en',
			'key' => 'key.with.param',
			'value' => 'Value with {param}'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c768',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'en',
			'key' => 'foo bar 42',
			'value' => 'Non-namespaced key'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c769',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'en',
			'key' => '...a...b...c...',
			'value' => 'Dotted key'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c76a',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'en',
			'key' => 'super.duper.nested.key.of.doom',
			'value' => 'Super duper nested key of doom'
		),

		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c771',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'no',
			'key' => 'key_one',
			'value' => 'Verdi En'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c772',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'no',
			'key' => 'key_two',
			'value' => 'Verdi To'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c773',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'no',
			'key' => 'nested.key.one',
			'value' => 'Dyp Verdi En'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c774',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'no',
			'key' => 'nested.key.two',
			'value' => 'Dyp Verdi To'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c775',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'no',
			'key' => 'numerical.key.0',
			'value' => 'Tall Verdi En'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c776',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'no',
			'key' => 'numerical.key.1',
			'value' => 'Tall Verdi To'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c777',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'no',
			'key' => 'key.with.param',
			'value' => 'Verdi med {param}'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c778',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'no',
			'key' => 'foo bar 42',
			'value' => 'Ikke-navnplass nøkkel'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c779',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'no',
			'key' => '...a...b...c...',
			'value' => 'Prikkete nøkkel'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c77a',
			'application_id' => '4fc7df26-a408-4283-8bfb-6109c0a80127',
			'locale' => 'no',
			'key' => 'super.duper.nested.key.of.doom',
			'value' => 'Super duper nøstet nøkkel av doom'
		),
	);

}
