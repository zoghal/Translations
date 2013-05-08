<?php
App::uses('Parser', 'Translations.Parser');

class PlistParser extends Parser{

/**
 * parse
 *
 * @throws \Exception if the file cannot be loaded
 * @param string $file
 * @param array $defaults
 * @return array
 */
	public static function parse($file, $defaults = array()) {
		$doc = new DomDocument();
		if (!$doc->load($file)) {
			throw new \Exception("File could not be loaded");
		}
		$array = self::_parsePlist($doc);

		$defaults += Translation::config();

		$parsed = array();
		self::_flatten($array, $parsed);
		return self::_parseArray($parsed, $defaults);
	}

/**
 * _flatten
 *
 * Turn a nested array into a flat array appropriate for the translations plugin
 * to directly insert in the db
 *
 * @param mixed $array
 * @param mixed $return
 * @param string $prefix
 * @return void
 */
	protected static function _flatten($array, &$return, $prefix = '') {
		if (!$array) {
			return;
		}
		$keys = array_keys($array);
		foreach ($array as $key => $value) {
			if ($keys[0] === 0) {
				$key += 1;
			}
			if (is_array($value)) {
				self::_flatten($value, $return, ltrim("$prefix.$key", '.'));
				continue;
			}
			if ($prefix) {
				$return[$prefix . '.' . $key] = $value;
			} else {
				$return[$key] = $value;
			}
		}
	}

/**
 * _parsePlist
 *
 * Parse a plist document, returning an array
 *
 * @param mixed $document
 * @return void
 */
	protected static function _parsePlist($document) {
		$plistNode = $document->documentElement;

		$root = $plistNode->firstChild;

		// skip any text nodes before the first value node
		while ($root->nodeName == "#text") {
			$root = $root->nextSibling;
		}

		return self::_parseValue($root);
	}

/**
 * _parseValue
 *
 * @param mixed $valueNode
 * @return void
 */
	protected static function _parseValue($valueNode) {
		$valueType = ucfirst($valueNode->nodeName);
		$transformerName = "_parse{$valueType}Value";

		return self::$transformerName($valueNode);
	}

/**
 * _parseArray
 *
 * @param mixed $arrayNode
 * @return array
 */
	protected static function _parseArrayValue($arrayNode) {
		$array = array();

		for (
			$node = $arrayNode->firstChild;
			$node != null;
			$node = $node->nextSibling
		) {
			if ($node->nodeType == XML_ELEMENT_NODE) {
				array_push($array, self::_parseValue($node));
			}
		}

		return $array;
	}

/**
 * _parseDate
 *
 * @param mixed $dateNode
 * @return mixed
 */
	protected static function _parseDateValue($dateNode) {
		return $dateNode->textContent;
	}

/**
 * _parseDict
 *
 * @param mixed $dictNode
 * @return array
 */
	protected static function _parseDictValue($dictNode) {
		$dict = array();

		// for each child of this node
		for (
			$node = $dictNode->firstChild;
			$node != null;
			$node = $node->nextSibling
		) {
			if ($node->nodeName == "key") {
				$key = $node->textContent;

				$valueNode = $node->nextSibling;

				// skip text nodes
				while ($valueNode->nodeType == XML_TEXT_NODE) {
					$valueNode = $valueNode->nextSibling;
				}

				// recursively parse the children
				$value = self::_parseValue($valueNode);

				$dict[$key] = $value;
			}
		}

		return $dict;
	}

/**
 * _parseInteger
 *
 * @param mixed $integerNode
 * @return int
 */
	protected static function _parseIntegerValue($integerNode) {
		return $integerNode->textContent;
	}

/**
 * _parseString
 *
 * @param mixed $stringNode
 * @return string
 */
	protected static function _parseStringValue($stringNode) {
		return $stringNode->textContent;
	}

/**
 * _parseTrue
 *
 * @param mixed $trueNode
 * @return bool
 */
	protected static function _parseTrueValue($trueNode) {
		return true;
	}

/**
 * _parseFalse
 *
 * @param mixed $trueNode
 * @return bool
 */
	protected static function _parseFalseValue($trueNode) {
		return false;
	}
}
