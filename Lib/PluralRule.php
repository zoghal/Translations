<?php

/**
 * Plural Rulez
 *
 * Plural rules differ by language - for some languages the rules are quite complex.
 * They are available online as part of the gettext/translate project, and are referenced
 * exactly as they appear in the Translation model indexed by language.
 * This class is responsible for translating the rule expressed as a string (exactly
 * as it appears in link below) into a php function which returns the plural-case
 * to be used when evaluating plural translations. In the event of a new plural-case
 * being added to the Translation model, this class will issue an exception with copy
 * and paste code for the method to create
 *
 * @link http://translate.sourceforge.net/wiki/l10n/pluralforms
 */
class PluralRule {

/**
 * check
 *
 * expects a gettext plural rule (string) as an input, runs it through a hash function
 * to get the name of a method on this clas which implements the same logic - and then
 * calls that function. In the event the method does not exist it'll throw an exception
 * the body of which is the method that needs to be implemented (copy paste and reformat).
 *
 * @param string $rule
 * @return string method name to execute
 */
	public static function check($rule, $n) {
		$method = self::getMethod($rule);

		$return = self::$method((int)$n, $rule);
		if ($return) {
			return (int)$return;
		}
		return $return;
	}

/**
 * For the given rule, get the test method to check
 *
 * @param string $rule
 * @return string method name to execute
 */
	public static function getMethod($rule) {
		$split = strpos($rule, 'plural=');
		$rule = rtrim(substr($rule, $split + 7), ' ;');
		$normalized = str_replace(' ', '', $rule);

		while ($normalized[0] === '(') {
			$normalized = substr($normalized, 1, -1);
		}

		if ($normalized === 'n!=1') {
			return '_checkRuleDefault';
		}

		$hash = crc32($normalized);

		return '_checkRule' . $hash;
	}

/**
 * n!=1
 *
 * @param int $n
 * @return mixed int or false
 */
	protected static function _checkRuleDefault($n) {
		return $n !== 1;
	}

/**
 * n==0?0:n==1?1:n==2?2:n%100>=3&&n%100<=10?3:n%100>=11?4:5
 *
 * @param int $n
 * @return mixed int or false
 */
	protected static function _checkRule954688076($n) {
		if ($n === 0) {
			return 0;
		}
		if ($n === 1) {
			return 1;
		}
		if ($n === 2) {
			return 2;
		}
		if ($n % 100 >= 3 && $n % 100 <= 10) {
			return 3;
		}
		if ($n % 100 >= 11) {
			return 4;
		}
		return 5;
	}

/**
 * n>1
 *
 * @param int $n
 * @return mixed int or false
 */
	protected static function _checkRule2903712895($n) {
		if ($n > 1) {
			return 1;
		}
		return 0;
	}

/**
 * 0
 *
 * @param int $n
 * @return mixed int or false
 */
	protected static function _checkRule4108050209($n) {
		return 0;
	}

/**
 * n%10==1&&n%100!=11?0:n%10>=2&&n%10<=4&&(n%100<10||n%100>=20)?1:2
 *
 * @param int $n
 * @return mixed int or false
 */
	protected static function _checkRule3848476649($n) {
		if ($n % 10 === 1 && $n % 100 !== 11) {
			return 0;
		}

		if (
			$n % 10 >= 2 &&
			$n % 10 <=4 &&
			(
				$n % 100 < 10 ||
				$n % 100 >= 20
			)
		) {
			return 1;
		}

		return 2;
	}

/**
 * n==1?0:n%10>=2&&n%10<=4&&(n%100<10||n%100>=20)?1:2
 *
 * @param int $n
 * @return mixed int or false
 */
    protected static function _checkRule3435329526($n) {
        if ($n === 1) {
			return 0;
		}

		if (
			$n % 10 >= 2 &&
			$n % 10 <=4 &&
			(
				$n % 100 < 10 ||
				$n % 100 >= 20
			)
		) {
			return 1;
		}
		return 2;
    }

/**
 * Handle calls for undefined rules
 *
 * @param string $method
 * @param array $args
 */
	public static function __callStatic($method, $args) {
		$rule = $args[1];

		$split = strpos($rule, 'plural=');
		$rule = rtrim(substr($rule, $split + 7), ';');
		while ($rule[0] === '(') {
			$rule = substr($rule, 1, -1);
		}

		$docRule = str_replace(' ', '', $rule);
		$rule = str_replace('n', '$n', $rule);

		$subrules = substr_count($rule, ':');
		if ($subrules) {
			$rule = str_replace(':', ': (', $rule) . str_repeat(')', $subrules);
		}

		$n = '$n';

		$phpVersion = <<<END
/**
 * $docRule
 *
 * @param int $n
 * @return mixed int or false
 */
    protected static function $method($n) {
        return $rule;
    }
END;

		throw new \InternalErrorException("PluralRule::$method doesn't exist\n\nCreate this method:\n\n$phpVersion");
	}
}
