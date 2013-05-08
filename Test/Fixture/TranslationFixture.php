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
		'locale' => array('type' => 'string', 'null' => false, 'default' => 'en', 'length' => 5, 'collate' => 'utf8_general_ci'),
		'domain' => array('type' => 'string', 'null' => true, 'default' => 'default', 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'category' => array('type' => 'string', 'null' => true, 'default' => 'LC_MESSAGES', 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'key' => array('type' => 'string', 'null' => false, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'value' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'plural_case' => array('type' => 'integer', 'null' => true, 'default' => null, 'length' => 2),
		'comments' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'references' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'history' => array('type' => 'text', 'null' => true, 'default' => null, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
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
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'key_one',
			'value' => 'Value One',
			'plural_case' => null
		),
		array(
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'key_two',
			'value' => 'Value Two',
			'plural_case' => null
		),
		array(
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'nested.key.one',
			'value' => 'Nested Value One',
			'plural_case' => null
		),
		array(
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'nested.key.two',
			'value' => 'Nested Value Two',
			'plural_case' => null
		),
		array(
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'numerical.key.0',
			'value' => 'Numerical Value One',
			'plural_case' => null
		),
		array(
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'numerical.key.1',
			'value' => 'Numerical Value Two',
			'plural_case' => null
		),
		array(
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'key.with.param',
			'value' => 'Value with {param}',
			'plural_case' => null
		),
		array(
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'foo bar 42',
			'value' => 'Non-namespaced key',
			'plural_case' => null
		),
		array(
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => '...a...b...c...',
			'value' => 'Dotted key',
			'plural_case' => null
		),
		array(
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'super.duper.nested.key.of.doom',
			'value' => 'Super duper nested key of doom',
			'plural_case' => null
		),
		array(
			'locale' => 'no',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'key_one',
			'value' => 'Verdi En',
			'plural_case' => null
		),
		array(
			'locale' => 'no',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'key_two',
			'value' => 'Verdi To',
			'plural_case' => null
		),
		array(
			'locale' => 'no',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'nested.key.one',
			'value' => 'Dyp Verdi En',
			'plural_case' => null
		),
		array(
			'locale' => 'no',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'nested.key.two',
			'value' => 'Dyp Verdi To',
			'plural_case' => null
		),
		array(
			'locale' => 'no',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'numerical.key.0',
			'value' => 'Tall Verdi En',
			'plural_case' => null
		),
		array(
			'locale' => 'no',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'numerical.key.1',
			'value' => 'Tall Verdi To',
			'plural_case' => null
		),
		array(
			'locale' => 'no',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'key.with.param',
			'value' => 'Verdi med {param}',
			'plural_case' => null
		),
		array(
			'locale' => 'no',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'foo bar 42',
			'value' => 'Ikke-navnplass nøkkel',
			'plural_case' => null
		),
		array(
			'locale' => 'no',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => '...a...b...c...',
			'value' => 'Prikkete nøkkel',
			'plural_case' => null
		),
		array(
			'locale' => 'no',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'super.duper.nested.key.of.doom',
			'value' => 'Super duper nøstet nøkkel av doom',
			'plural_case' => null
		),
		array(
			'locale' => 'en',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => 'untranslated.key',
			'value' => 'Only defined in English',
			'plural_case' => null
		),
		array(
			'locale' => 'ru',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => '1 message',
			'value' => 'It\'s one message',
			'plural_case' => null
		),
		array(
			'locale' => 'ru',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => '{number} messages',
			'value' => 'ends in 1 message',
			'plural_case' => 0
		),
		array(
			'locale' => 'ru',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => '{number} messages',
			'value' => 'ends in 2,3,4 message',
			'plural_case' => 1
		),
		array(
			'locale' => 'ru',
			'domain' => 'default',
			'category' => 'LC_MESSAGES',
			'key' => '{number} messages',
			'value' => 'ends in 5,6,7,8,9,0 message',
			'plural_case' => 2
		),

	);

	public function __construct() {
		foreach($this->records as &$row) {
			$row['id'] = String::uuid();
		}

		parent::__construct();
	}
}
