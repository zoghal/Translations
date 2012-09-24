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
		'plural_id' => array('type' => 'string', 'null' => true, 'default' => null, 'length' => 36, 'collate' => 'utf8_general_ci', 'comment' => 'If this translation has a plural id - this is it', 'charset' => 'utf8'),
		'plural_case' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 2, 'comment' => 'Only relevant for plural translations 0-6'),
		'locale' => array('type' => 'string', 'null' => false, 'default' => 'en', 'length' => 5, 'collate' => 'utf8_general_ci', 'comment' => 'ISO 3166-1 alpha-2 country code + optional (_ + Region subtag). e.g. en_US', 'charset' => 'utf8'),
		'domain' => array('type' => 'string', 'null' => true, 'default' => 'default', 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'category' => array('type' => 'string', 'null' => true, 'default' => 'LC_MESSAGES', 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array(
			'PRIMARY' => array('column' => 'id', 'unique' => 1)
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
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'key_one',
			'value' => 'Value One'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c762',
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'key_two',
			'value' => 'Value Two'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c763',
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'nested.key.one',
			'value' => 'Nested Value One'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c764',
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'nested.key.two',
			'value' => 'Nested Value Two'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c765',
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'numerical.key.0',
			'value' => 'Numerical Value One'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c766',
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'numerical.key.1',
			'value' => 'Numerical Value Two'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c767',
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'key.with.param',
			'value' => 'Value with {param}'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c768',
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'foo bar 42',
			'value' => 'Non-namespaced key'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c769',
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => '...a...b...c...',
			'value' => 'Dotted key'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c76a',
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'super.duper.nested.key.of.doom',
			'value' => 'Super duper nested key of doom'
		),

		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c771',
			'locale' => 'no',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'key_one',
			'value' => 'Verdi En'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c772',
			'locale' => 'no',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'key_two',
			'value' => 'Verdi To'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c773',
			'locale' => 'no',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'nested.key.one',
			'value' => 'Dyp Verdi En'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c774',
			'locale' => 'no',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'nested.key.two',
			'value' => 'Dyp Verdi To'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c775',
			'locale' => 'no',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'numerical.key.0',
			'value' => 'Tall Verdi En'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c776',
			'locale' => 'no',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'numerical.key.1',
			'value' => 'Tall Verdi To'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c777',
			'locale' => 'no',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'key.with.param',
			'value' => 'Verdi med {param}'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c778',
			'locale' => 'no',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'foo bar 42',
			'value' => 'Ikke-navnplass nøkkel'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c779',
			'locale' => 'no',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => '...a...b...c...',
			'value' => 'Prikkete nøkkel'
		),
		array(
			'id' => '4fc4a7ac-2468-4796-9655-31f92a72c77a',
			'locale' => 'no',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'super.duper.nested.key.of.doom',
			'value' => 'Super duper nøstet nøkkel av doom'
		),
	);

}
