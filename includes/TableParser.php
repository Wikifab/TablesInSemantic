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

			//echo " FIND PROPERTY  :" . str_replace("\n","\\n",substr($text, $this->i, 10)). "\n";

			$templateFound = $this->findTableInTemplate($this->i, $text);
			if ($templateFound) {
				//echo " FIND template  :" . str_replace("\n","\\n",substr($templateFound, 0, 10))."\n";

				$wikitextTable = $this->findTableTemplateString($text, $templateFound);
				if( $wikitextTable) {
					$converter = new TableConverter();
					$htmlTable = $converter->convert($wikitextTable);
					$text = str_replace($wikitextTable, $htmlTable, $text);
					$this->i = $this->i + strlen($htmlTable);
				} else {
					//echo " FAIL TO CONVERT\n";

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

		// recherche '{|' ou '{{' ou '|' ou '}' ou '['
		$pattern = '/({\||{{|\||}|\[)/';

		/*echo "\n";
		echo "findTableInTemplate\n";
		echo "findTableInTemplate $i\n";
		var_dump($text);
		var_dump(substr($text, $i));*/

		if (preg_match($pattern, $text, $matches, null, $i)) {

			if($matches[0] == '[') {
				// recherche des accolades fermantes
				$this->i = strpos($text, $matches[0], $i) +1;
				$r = $this->findClosingCrochet($text, 1, $this->i);
				if ($r === false) {
					//echo "CANNOT FIND CLOSING ]";
					return false;
				} else {
					$this->i = $r;
				}
				$i = $this->findTableInTemplate($this->i, $text);
				//echo ("\nRESULT = $i\n\n\n");
				return $i;
			} if($matches[0] == '{{') {
				// recherche des accolades fermantes
				$this->i = strpos($text, $matches[0], $i) +2;
				$r = $this->findClosingAccolade($text, 2, $this->i);
				if ($r === false) {
					//echo "CANNOT FIND CLOSING";
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

	public function findClosingCrochet ($text, $nb, $i) {
		$nextOpen = strpos($text, '[',$i);
		$nextClose = strpos($text, ']',$i);
		$openCount = $nb;

		while ($nextClose !== false) {
			if ($nextOpen === false || $nextClose < $nextOpen) {
				$openCount--;
				if ($openCount == 0) {
					return $nextClose +1;
				}
				$i = $nextClose +1;
			} else {
				$openCount++;
				$i = $nextOpen +1;
			}
			$nextOpen = strpos($text, '[',$i);
			$nextClose = strpos($text, ']',$i);
		}

		return false;
	}

	public function findClosingAccolade ($text, $nb, $i) {
		$nextOpen = strpos($text, '{',$i);
		$nextClose = strpos($text, '}',$i);
		$openCount = $nb;

		while ($nextClose !== false) {
			if ($nextOpen === false || $nextClose < $nextOpen) {
				$openCount--;
				if ($openCount == 0) {
					return $nextClose +1;
				}
				$i = $nextClose +1;
			} else {
				$openCount++;
				$i = $nextOpen +1;
			}
			$nextOpen = strpos($text, '{',$i);
			$nextClose = strpos($text, '}',$i);
		}

		return false;
	}
}