<?php
namespace TablesInSemantic;

class Hooks {

	public static function onParserBeforeStrip( &$parser, &$text, &$strip_state ) {

		$tableParser = new TableParser();

		$tableParser->parse($text);
	}
}