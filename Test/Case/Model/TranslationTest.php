<?php
App::uses('Translation', 'Translations.Model');

class TestTranslation extends Translation {

	public static function pluralCase($n, $locale = null) {
		return self::_pluralCase($n, $locale);
	}

	public static function pluralCases($locale = null) {
		return self::_pluralCases($locale);
	}

	public static function pluralRule($locale = null) {
		return self::_pluralRule($locale);
	}
}

/**
 * Translation Test Case
 *
 */
class TranslationTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'plugin.translations.translation',
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();

		// Load config
		$this->config = array(
			'Config.defaultLanguage' => Configure::read('Config.defaultLanguage'),
			'Config.language' => Configure::read('Config.language'),
			'Cache.disable' => Configure::read('Cache.disable')
		);
		Configure::write('Config.defaultLanguage', 'en');
		Configure::write('Config.language', 'en');

		ClassRegistry::removeObject('Translation');
		$this->Translation = ClassRegistry::init('Translations.Translation');

		Cache::clear(true, 'default');
		Translation::reset();
		Translation::config(array(
			'useTable' => 'translations',
			'cacheConfig' => false,
			'autoPopulate' => false
		));
	}

/**
 * tearDown method
 *
 * Reapply original config and destroy traces of the translate model
 *
 * @return void
 */
	public function tearDown() {
		foreach ($this->config as $key => $value) {
			Configure::write($key, $value);
		}

		ClassRegistry::removeObject('Translation');
		Translation::reset();

		unset($this->Translation, $this->config);

		parent::tearDown();
	}

/**
 * testCreateDefaultLocale
 *
 * @return void
 */
	public function testCreateDefaultLocale() {
		$this->Translation->deleteAll(true);

		$result = $this->Translation->save(array(
			'locale' => 'en',
			'domain' => 'test',
			'category' => 'LC_MESSAGES',
			'key' => 'yes',
			'value' => 'yes'
		));
		$this->assertTrue((bool)$result);
	}

/**
 * testCreateDefaultNotDefault
 *
 * Make sure that if the site langauge is different from defaultLanguage
 * It's still possible to create translations. This is effectively a regression test
 *
 * @return void
 */
	public function testCreateDefaultNotDefault() {
		$this->Translation->deleteAll(true);

		Configure::write('Config.defaultLanguage', 'en');
		Configure::write('Config.language', 'da');

		$result = $this->Translation->save(array(
			'locale' => 'en',
			'domain' => 'test',
			'category' => 'LC_MESSAGES',
			'key' => 'yes',
			'value' => 'yes'
		));
		$this->assertTrue((bool)$result);
	}

/**
 * testCreateEmpties
 *
 * Shouldn't be able to create empty translations
 *
 * @return void
 */
	public function testCreateEmpties() {
		$this->Translation->deleteAll(true);

		$result = $this->Translation->save(array(
			'locale' => 'en',
			'domain' => 'test',
			'category' => 'LC_MESSAGES',
			'key' => 'yes',
			'value' => ''
		));
		$this->assertTrue((bool)$result);

		$this->Translation->create();
		$result = $this->Translation->save(array(
			'locale' => 'en_GB',
			'domain' => 'test',
			'category' => 'LC_MESSAGES',
			'key' => 'no',
			'value' => '',
		));
		$this->assertFalse($result);
	}

/**
 * testCreateBlocked
 *
 * Unless it's the base language, shouldn't be possible to create empty translations.
 *
 * @return void
 */
	public function testCreateBlocked() {
		$this->Translation->deleteAll(true);

		$result = $this->Translation->save(array(
			'locale' => 'en',
			'domain' => 'test',
			'category' => 'LC_MESSAGES',
			'key' => 'yes',
			'value' => 'yes'
		));
		$this->assertTrue((bool)$result);

		$this->Translation->create();
		$result = $this->Translation->save(array(
			'locale' => 'en_GB',
			'domain' => 'test',
			'category' => 'LC_MESSAGES',
			'key' => 'yes',
			'value' => 'yes',
		));
		$this->assertFalse($result);
	}

/**
 * testUpdateBumpsCacheTs
 *
 * @return void
 */
	public function testUpdateBumpsCacheTs() {
		Configure::write('Cache.disable', false);
		$config = Translation::config(array(
			'cacheConfig' => 'default'
		));

		Cache::write('translations-ts', 42);
		$result = $this->Translation->save(array(
			'locale' => 'en',
			'domain' => 'test',
			'category' => 'LC_MESSAGES',
			'key' => 'yes',
			'value' => 'yes'
		));

		$this->assertTrue((bool)$result);

		$ts = Cache::read('translations-ts');
		$this->assertNotSame(42, $ts);
	}

/**
 * testUpdateDeleted
 *
 * If a translation is edited such that it's the same as the inherited translation - it should be deleted
 *
 * @return void
 */
	public function testUpdateDeleted() {
		$this->Translation->deleteAll(true);

		$result = $this->Translation->save(array(
			'locale' => 'en',
			'domain' => 'test',
			'category' => 'LC_MESSAGES',
			'key' => 'yes',
			'value' => 'yes'
		));
		$this->assertTrue((bool)$result);

		$this->Translation->create();
		$result = $this->Translation->save(array(
			'locale' => 'en_GB',
			'domain' => 'test',
			'category' => 'LC_MESSAGES',
			'key' => 'yes',
			'value' => 'aye',
		));
		$this->assertTrue((bool)$result);

		$result = $this->Translation->save(array(
			'locale' => 'en_GB',
			'domain' => 'test',
			'category' => 'LC_MESSAGES',
			'key' => 'yes',
			'value' => 'yes',
		));

		$all = $this->Translation->find('all', array(
			'fields' => array('locale', 'domain', 'category', 'key', 'value')
		));

		$expected = array(
			array(
				'Translation' => array(
					'locale' => 'en',
					'domain' => 'test',
					'category' => 'LC_MESSAGES',
					'key' => 'yes',
					'value' => 'yes'
				)
			)
		);
		$this->assertSame($expected, $all, 'There should only be one translation');
	}

/**
 * testCategories
 *
 * @return void
 */
	public function testCategories() {
		$categories = Translation::categories();
		$expected = array(
			'LC_ALL' => 'LC_ALL',
			'LC_COLLATE' => 'LC_COLLATE',
			'LC_CTYPE' => 'LC_CTYPE',
			'LC_MONETARY' => 'LC_MONETARY',
			'LC_NUMERIC' => 'LC_NUMERIC',
			'LC_TIME' => 'LC_TIME',
			'LC_MESSAGES' => 'LC_MESSAGES'
		);

		$this->assertSame($expected, $categories);
	}

	public function testForLocaleFlat() {
		$result = $this->Translation->forLocale('en', array('nested' => false));

		$expected = array(
			'...a...b...c...' => 'Dotted key',
			'foo bar 42' => 'Non-namespaced key',
			'key.with.param' => 'Value with {param}',
			'key_one' => 'Value One',
			'key_two' => 'Value Two',
			'nested.key.one' => 'Nested Value One',
			'nested.key.two' => 'Nested Value Two',
			'numerical.key.0' => 'Numerical Value One',
			'numerical.key.1' => 'Numerical Value Two',
			'super.duper.nested.key.of.doom' => 'Super duper nested key of doom',
			'untranslated.key' => 'Only defined in English'
		);

		$this->assertSame($expected, $result);
	}

	public function testForLocaleNested() {
		$result = $this->Translation->forLocale();

		$expected = array(
			'...a...b...c...' => 'Dotted key',
			'foo bar 42' => 'Non-namespaced key',
			'key' => array(
				'with' => array(
					'param' => 'Value with {param}'
				)
			),
			'key_one' => 'Value One',
			'key_two' => 'Value Two',
			'nested' => array (
				   'key' => array (
					   'one' => 'Nested Value One',
					   'two' => 'Nested Value Two'
				   )
			),
			'numerical' => array (
				   'key' => array (
					   'Numerical Value One',
					   'Numerical Value Two'
				   )
			),
			'super' => array(
				'duper' => array(
					'nested' => array(
						'key' => array(
							'of' => array(
								'doom' => 'Super duper nested key of doom'
							)
						)
					)
				)
			),
			'untranslated' => array(
				'key' => 'Only defined in English'
			)
		);

		$this->assertSame($expected, $result);
	}

	public function testForLocaleSection() {
		$result = $this->Translation->forLocale('en', array('section' => 'key'));

		$expected = array(
			'with' => array(
				'param' => 'Value with {param}'
			)
		);

		$this->assertSame($expected, $result);
	}

	public function testForLocaleCache() {
		Configure::write('Cache.disable', false);

		$Translation = $this->getMock(
			'Translation',
			array('_forLocale'),
			array(array('name' => 'Translation', 'ds' => 'test'))
		);
		ClassRegistry::removeObject('Translation');
		ClassRegistry::addObject('Translation', $Translation);

		Translation::reset();
		Translation::config(array(
			'useTable' => 'translations',
			'cacheConfig' => 'default',
			'autoPopulate' => false
		));

		$Translation->expects($this->once())
			->method('_forLocale')
			->will($this->returnValue(array('foo' => 'bar')));

		$this->Translation->forLocale('en', array('nested' => false));
		$result = $this->Translation->forLocale('en', array('nested' => false));

		$expected = array(
			'foo' => 'bar'
		);
		$this->assertSame($expected, $result);
	}

	public function testForLocaleCacheInheritance() {
		Configure::write('Cache.disable', false);

		Translation::config(array(
			'cacheConfig' => 'default',
		));

		$enBefore = $this->Translation->forLocale('en', array('nested' => false));
		$noBefore = $this->Translation->forLocale('no', array('nested' => false));

		$ts = Cache::read('translations-ts', 'default');
		$this->assertTrue((bool)$ts, 'The timestamp should have been set to a value');

		$key = "en-default-lc_messages-flat-defaults-$ts";
		$enCached = Cache::read($key, 'default');
		$key = "no-default-lc_messages-flat-defaults-$ts";
		$noCached = Cache::read($key, 'default');

		$this->assertSame($enBefore, $enCached, 'The cached result should exactly match the returned value');
		$this->assertSame($noBefore, $noCached, 'The cached result should exactly match the returned value');

		$enAfter = $this->Translation->forLocale('en', array('nested' => false));
		$noAfter = $this->Translation->forLocale('no', array('nested' => false));

		$this->assertSame($enBefore, $enAfter, 'The result of a cache-miss (1st call) and cache-hit (2nd call) should not differ');
		$this->assertSame($noBefore, $noAfter, 'The result of a cache-miss (1st call) and cache-hit (2nd call) should not differ');
	}

	public function testForSettingLanguageConfig() {
		Configure::write('Config.language', 'no');
		$result = $this->Translation->forLocale();

		$expected = array(
			'...a...b...c...' => 'Prikkete nøkkel',
			'foo bar 42' => 'Ikke-navnplass nøkkel',
			'key' => array(
				'with' => array(
					'param' => 'Verdi med {param}'
				)
			),
			'key_one' => 'Verdi En',
			'key_two' => 'Verdi To',
			'nested' => array (
				   'key' => array (
					   'one' => 'Dyp Verdi En',
					   'two' => 'Dyp Verdi To'
				   )
			),
			'numerical' => array (
				   'key' => array (
					   'Tall Verdi En',
					   'Tall Verdi To'
				   )
			),
			'super' => array(
				'duper' => array(
					'nested' => array(
						'key' => array(
							'of' => array(
								'doom' => 'Super duper nøstet nøkkel av doom'
							)
						)
					)
				)
			),
			'untranslated' => array(
				'key' => 'Only defined in English'
			)
		);

		$this->assertSame($expected, $result);
	}

	public function testForDefaultTranslate() {
		$result = Translation::translate('key_one');
		$expected = 'Value One';
		$this->assertSame($expected, $result);

		$result = Translation::translate('nested.key.one');
		$expected = 'Nested Value One';
		$this->assertSame($expected, $result);

		$result = Translation::translate('numerical.key.0');
		$expected = 'Numerical Value One';
		$this->assertSame($expected, $result);
	}

	public function testForChangedLocaleTranslate() {
		Configure::write('Config.language', 'no');
		$result = Translation::translate('key_two');
		$expected = 'Verdi To';
		$this->assertSame($expected, $result);

		$result = Translation::translate('nested.key.two');
		$expected = 'Dyp Verdi To';
		$this->assertSame($expected, $result);

		$result = Translation::translate('numerical.key.1');
		$expected = 'Tall Verdi To';
		$this->assertSame($expected, $result);
	}

	public function testForChangingLocale() {
		$result = Translation::translate('key_one');
		$expected = 'Value One';
		$this->assertSame($expected, $result);

		Configure::write('Config.language', 'no');
		$result = Translation::translate('key_one');
		$expected = 'Verdi En';
		$this->assertSame($expected, $result);

		Configure::write('Config.language', 'en');
		$result = Translation::translate('key_one');
		$expected = 'Value One';
		$this->assertSame($expected, $result);
	}

/**
 * testForMissingLocale
 *
 * If there is no language specific translations - it should use use the inheritance.
 * Config.defaultLangauge is always added as a top level fallback
 *
 * @return void
 */
	public function testForMissingLocale() {
		Configure::write('Config.language', 'de');
		$result = Translation::translate('key_one');
		$expected = 'Value One';
		$this->assertSame($expected, $result);

		Configure::write('Config.language', 'THIS IS NOT A LANGUAGE CODE');
		$result = Translation::translate('key_one');
		$expected = 'Value One';
		$this->assertSame($expected, $result);
	}

	public function testForMissingTranslation() {
		$result = Translation::translate('non-existant key');
		$expected = 'non-existant key';
		$this->assertSame($expected, $result);
	}

	public function testLocales() {
		$result = Translation::locales();
		$expected = array(
			'en' => 'English',
			'no' => 'Norwegian',
			'ru' => 'Russian'
		);
		$this->assertSame($expected, $result);
	}

	public function testForCreateLocale() {
		$result = $this->Translation->createLocale('dk');
		$expected = $this->Translation->forLocale();
		$this->assertSame($expected, $result);
	}

	public function testForCreateLocaleBasedOn() {
		$result = $this->Translation->createLocale('dk', 'no');
		$expected = $this->Translation->forLocale('no');
		$this->assertSame($expected, $result);
	}

	public function testForCreateLocaleSettings() {
		$settings = array(
			'basedOn' => 'no',
			'nested' => false
		);
		$result = $this->Translation->createLocale('dk', $settings);
		$expected = $this->Translation->forLocale('no', $settings);
		$this->assertSame($expected, $result);
	}

/**
 * Check en plural rules
 *
 * It's either a plural form - or false (singular)
 */
	public function testPluralCase() {
		$result = TestTranslation::pluralCase(0, 'en');
		$this->assertSame(1, $result);

		$result = TestTranslation::pluralCase(1, 'en');
		$this->assertSame(false, $result);

		$result = TestTranslation::pluralCase(2, 'en');
		$this->assertSame(1, $result);
	}

/**
 * Check russian plural rules
 *
 * In russian it's:
 *	Numbers ending in 1
 *	Numbers ending in 2,3,4
 *	Numbers ending in 5,6,7,8,9,0
 */
	public function testPluralCaseRussian() {
		$result = TestTranslation::pluralCase(0, 'ru');
		$this->assertSame(2, $result);

		$result = TestTranslation::pluralCase(1, 'ru');
		$this->assertSame(0, $result);

		$result = TestTranslation::pluralCase(2, 'ru');
		$this->assertSame(1, $result);

		$result = TestTranslation::pluralCase(3, 'ru');
		$this->assertSame(1, $result);

		$result = TestTranslation::pluralCase(4, 'ru');
		$this->assertSame(1, $result);

		$result = TestTranslation::pluralCase(5, 'ru');
		$this->assertSame(2, $result);

		$result = TestTranslation::pluralCase(6, 'ru');
		$this->assertSame(2, $result);

		$result = TestTranslation::pluralCase(7, 'ru');
		$this->assertSame(2, $result);

		$result = TestTranslation::pluralCase(8, 'ru');
		$this->assertSame(2, $result);

		$result = TestTranslation::pluralCase(9, 'ru');
		$this->assertSame(2, $result);

		$result = TestTranslation::pluralCase(10, 'ru');
		$this->assertSame(2, $result);
	}

/**
 * Check arabic plural rules
 *
 * @TODO These are complex, need to check with some source that they are correct
 */
	public function testPluralCaseArabic() {
		$result = TestTranslation::pluralCase(0, 'ar');
		$this->assertSame(0, $result);

		$result = TestTranslation::pluralCase(1, 'ar');
		$this->assertSame(1, $result);

		$result = TestTranslation::pluralCase(2, 'ar');
		$this->assertSame(2, $result);

		$result = TestTranslation::pluralCase(3, 'ar');
		$this->assertSame(3, $result);

		$result = TestTranslation::pluralCase(10, 'ar');
		$this->assertSame(3, $result);

		$result = TestTranslation::pluralCase(11, 'ar');
		$this->assertSame(4, $result);

		$result = TestTranslation::pluralCase(99, 'ar');
		$this->assertSame(4, $result);

		$result = TestTranslation::pluralCase(100, 'ar');
		$this->assertSame(5, $result);

		$result = TestTranslation::pluralCase(102, 'ar');
		$this->assertSame(5, $result);

		$result = TestTranslation::pluralCase(103, 'ar');
		$this->assertSame(3, $result);
	}

	public function testPluralCases() {
		$result = TestTranslation::pluralCases('ar');
		$this->assertSame(6, $result);

		$result = TestTranslation::pluralCases('en');
		$this->assertSame(2, $result);

		$result = TestTranslation::pluralCases('en_GB');
		$this->assertSame(2, $result);

		$result = TestTranslation::pluralCases('fr');
		$this->assertSame(2, $result);

		$result = TestTranslation::pluralCases('fr_XX');
		$this->assertSame(2, $result);

		$result = TestTranslation::pluralCases('ja');
		$this->assertSame(1, $result);

		$result = TestTranslation::pluralCases('ru');
		$this->assertSame(3, $result);

		$result = TestTranslation::pluralCases('xx');
		$this->assertSame(2, $result);
	}

	public function testPluralRule() {
		$result = TestTranslation::pluralRule('ar');
		$this->assertSame('nplurals=6; plural= n==0 ? 0 : n==1 ? 1 : n==2 ? 2 : n%100>=3 && n%100<=10 ? 3 : n%100>=11 ? 4 : 5;', $result);

		$result = TestTranslation::pluralRule('en');
		$this->assertSame('nplurals=2; plural=(n != 1)', $result);

		$result = TestTranslation::pluralRule('en_GB');
		$this->assertSame('nplurals=2; plural=(n != 1)', $result);

		$result = TestTranslation::pluralRule('fr');
		$this->assertSame('nplurals=2; plural=(n > 1)', $result);

		$result = TestTranslation::pluralRule('fr_XX');
		$this->assertSame('nplurals=2; plural=(n > 1)', $result);

		$result = TestTranslation::pluralRule('ja');
		$this->assertSame('nplurals=1; plural=0', $result);

		$result = TestTranslation::pluralRule('ru');
		$this->assertSame('nplurals=3; plural=(n%10==1 && n%100!=11 ? 0 : n%10>=2 && n%10<=4 && (n%100<10 || n%100>=20) ? 1 : 2)', $result);

		$result = TestTranslation::pluralRule('xx');
		$this->assertSame('nplurals=2; plural=(n != 1)', $result);
	}

	public function testPluralTranslation() {
		$options = array(
			'plural' => '{number} messages',
			'locale' => 'ru'
		);

		$options['count'] = 0;
		$result = Translation::translate('1 message', $options);
		$this->assertSame('ends in 5,6,7,8,9,0 message', $result);

		$options['count'] = 1;
		$result = Translation::translate('1 message', $options);
		$this->assertSame('It\'s one message', $result);

		$options['count'] = 2;
		$result = Translation::translate('1 message', $options);
		$this->assertSame('ends in 2,3,4 message', $result);

		$options['count'] = 3;
		$result = Translation::translate('1 message', $options);
		$this->assertSame('ends in 2,3,4 message', $result);

		$options['count'] = 4;
		$result = Translation::translate('1 message', $options);
		$this->assertSame('ends in 2,3,4 message', $result);

		$options['count'] = 5;
		$result = Translation::translate('1 message', $options);
		$this->assertSame('ends in 5,6,7,8,9,0 message', $result);

		$options['count'] = 6;
		$result = Translation::translate('1 message', $options);
		$this->assertSame('ends in 5,6,7,8,9,0 message', $result);

		$options['count'] = 7;
		$result = Translation::translate('1 message', $options);
		$this->assertSame('ends in 5,6,7,8,9,0 message', $result);

		$options['count'] = 8;
		$result = Translation::translate('1 message', $options);
		$this->assertSame('ends in 5,6,7,8,9,0 message', $result);

		$options['count'] = 9;
		$result = Translation::translate('1 message', $options);
		$this->assertSame('ends in 5,6,7,8,9,0 message', $result);

		$options['count'] = 10;
		$result = Translation::translate('1 message', $options);
		$this->assertSame('ends in 5,6,7,8,9,0 message', $result);
	}
}
