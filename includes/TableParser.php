<?php
namespace TablesInSemantic;

class TableParser {

	var $text;

	var $i = 0;

	public function __construct() {
	}

	public function parse( &$text ) {

		$this->text = $text;

		$this->i = 0;
		$size = strlen($text);

		$this->i = $this->indexOfNextTemplate($this->i, $text);
		while ($this->i !== false) {

			$templateFound = $this->findTableInTemplate($this->i, $text);
			if ($templateFound) {
				$wikitextTable = $this->findTableTemplateString($text, $templateFound);

				if( $wikitextTable) {
					$converter = new TableConverter();
					$htmlTable = $converter->convert($wikitextTable);
					$text = str_replace($wikitextTable, $htmlTable, $text);
					$this->i = $this->i + strlen($htmlTable);
				} else {
					$this->i = $this->indexOfNextTemplate($this->i, $text);
				}
			} else {
				$this->i = $this->indexOfNextTemplate($this->i, $text);
			}

		}

	}

	/**
	 * return the index of next property definition ( "|PropertyName=" )
	 * @param int $i
	 * @param string $text
	 * @return int|boolean
	 */
	public function indexOfNextTemplate($i, &$text) {

		$pattern = '/\|([ a-zA-Z0-9_\-]+)=/';

		if (preg_match($pattern, $text, $matches, null, $i)) {
			$i = strpos($text, $matches[0], $i);
			return $i + strlen($matches[0]);
		}
		return false;
	}

	public function findTableInTemplate($i, &$text) {

		// recherche '{|' ou '{{' ou '|' ou '}'
		$pattern = '/({\||{{|\||})/';

		/*echo "\n";
		echo "findTableInTemplate\n";
		echo "findTableInTemplate $i\n";
		var_dump($text);
		var_dump(substr($text, $i));*/

		if (preg_match($pattern, $text, $matches, null, $i)) {

			if($matches[0] == '{{') {
				// recherche des accolades fermantes
				$this->i = strpos($text, $matches[0], $i) +2;
				$r = $this->findClosingAccolade($text, 2, $this->i);
				if ($r === false) {
					return false;
				} else {
					$this->i = $r;
				}
				$i = $this->findTableInTemplate($this->i, $text);
				//echo ("\nRESULT = $i\n\n\n");
				return $i;
			} else if($matches[0] == '|' || $matches[0] == '}'){
				// fin de recherche de template
				$this->i = strpos($text, $matches[0], $i);
				return false;
			} else {
				// template found, must be converted
				$this->i = strpos($text, $matches[0], $i);
				return $this->i;
			}
		}


		return false;
	}

	public function findTableTemplateString($text, $i) {
		$end = strpos($text, '|}', $i);
		if ($end===false) {
			return false;
		}
		return substr($text, $i, $end +2 - $i);
	}

	public function findClosingAccolade ($text, $nb, $i) {
		$pattern = '/({|})/';

		if (preg_match($pattern, $text, $matches, null, $i)) {
			$i = strpos($text, $matches[0], $i) +1;
			if($matches[0] == '{') {
				return $this->findClosingAccolade($text, $nb+1, $i);
			} else if($nb > 1){
				return $this->findClosingAccolade($text, $nb-1, $i);
			} else {
				return $i;
			}
		}
		return false;

	}
}