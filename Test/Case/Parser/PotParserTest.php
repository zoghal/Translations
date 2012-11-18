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
					'domain' => 'simple',
					'category' => 'LC_MESSAGES',
					'key' => 'foo',
					'value' => 'foo'
				)
			),
			'settings' => array(
				'domain' => 'simple',
				'overwrite' => false
			)
		);
		$this->assertSame($expected, $result);
	}

	public function testParsePlural() {
		$path = CakePlugin::path('Translations') . 'Test/Files/plural.pot';
		$result = PotParser::parse($path);

		$expected = array(
			'count' => 3,
			'translations' => array(
				array(
					'locale' => 'en',
					'domain' => 'plural',
					'category' => 'LC_MESSAGES',
					'key' => '%d post',
					'value' => '%d post'
				),
				array(
					'locale' => 'en',
					'domain' => 'plural',
					'category' => 'LC_MESSAGES',
					'key' => '%d posts',
					'value' => '%d posts',
					'single_key' => '%d post',
					'plural_case' => 0
				),
				array(
					'locale' => 'en',
					'domain' => 'plural',
					'category' => 'LC_MESSAGES',
					'key' => '%d posts',
					'value' => '%d posts',
					'single_key' => '%d post',
					'plural_case' => 1
				)
			),
			'settings' => array(
				'domain' => 'plural',
				'overwrite' => false
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
			'domain' => 'cake',
			'category' => 'LC_MESSAGES',
			'key' => 'Error',
			'value' => 'Error',
			'references' => array(
				'View/Errors/error400.ctp:21',
				'View/Errors/error500.ctp:21',
			)
		);
		$this->assertSame($expected, $next);

		$next = $result['translations'][1];
		$expected = array(
			'locale' => 'en',
			'domain' => 'cake',
			'category' => 'LC_MESSAGES',
			'key' => 'The requested address %s was not found on this server.',
			'value' => 'The requested address %s was not found on this server.',
			'references' => array(
				'View/Errors/error400.ctp:23',
			)
		);
		$this->assertSame($expected, $next);

		$expected = array(
			'Error',
			'The requested address %s was not found on this server.',
			'An Internal Error Has Occurred.',
			'Scaffold :: ',
			'Invalid %s',
			'updated',
			'saved',
			'The %1$s has been %2$s',
			'Please correct errors below.',
			'The %1$s with id: %2$s has been deleted.',
			'There was an error deleting the %1$s with id: %2$s',
			'You are not authorized to access that location.',
			'There is no "%s" adapter.',
			'The "%s" adapter, does not have a dump() method.',
			'Not Found',
			'The requested file was not found',
			'%d KB',
			'%.2f MB',
			'%.2f GB',
			'%.2f TB',
			'%d Byte',
			'%d Bytes',
			'%d Bytes',
			'Today, %s',
			'Yesterday, %s',
			'Tomorrow, %s',
			'Sunday',
			'Monday',
			'Tuesday',
			'Wednesday',
			'Thursday',
			'Friday',
			'Saturday',
			'On %s %s',
			'just now',
			'on %s',
			'%s ago',
			'days',
			'abday',
			'day',
			'd_t_fmt',
			'abmon',
			'mon',
			'am_pm',
			't_fmt_ampm',
			'd_fmt',
			't_fmt',
			'%d year',
			'%d years',
			'%d years',
			'%d month',
			'%d months',
			'%d months',
			'%d week',
			'%d weeks',
			'%d weeks',
			'%d day',
			'%d days',
			'%d days',
			'%d hour',
			'%d hours',
			'%d hours',
			'%d minute',
			'%d minutes',
			'%d minutes',
			'%d second',
			'%d seconds',
			'%d seconds',
			'Error in field %s',
			'New %s',
			'Edit %s',
			'Submit',
			'January',
			'February',
			'March',
			'April',
			'May',
			'June',
			'July',
			'August',
			'September',
			'October',
			'November',
			'December',
			' of ',
			'Actions',
			'Delete',
			'Are you sure you want to delete # %s?',
			'List',
			'List %s',
			'View',
			'Edit',
			'Are you sure you want to delete',
			'Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}',
			'previous',
			'next',
			'View %s',
			'Delete %s',
			'Related %s'
		);
		$ids = Hash::extract($result['translations'], '{n}.key');
		$this->assertSame($expected, $ids);
	}
}
