#Parsers

Parsers are used to convert translation data to string representations and vice-versa. They are used
in the import/export processes only.

##Implementing a new parser format

Not all of the existing parsers are complete - the exist to match the use cases that this plugin
has been used with to date. To implement a new parser format, create a parser class extending
from the abstract Parser, Implementing either or both of `parse` and `generate`. The json parser
is the only fully-implemented parser, and is a useful reference.

	App::uses('Parser', 'Translations.Parser');

	class XmlParser extends Parser {

		public static function parse($file, $defaults = array()) {
			...
			return $array;
		}

		public static function generate($array = array()) {
			...
			return $string;
		}
	}


