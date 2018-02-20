<?php
namespace TablesInSemantic;

class TableConverter {

	var $text;

	var $debug = false;

	var $i = 0, $out = '';
	var $rowStarted = false, $cellStarted = false;

	public function __construct() {

	}

	public function init() {
		$this->out = '<table>';
	}
	public function end($previous) {
		$this->out .= $previous;
		if ($this->cellStarted) {
			$this->out .= '</td>';
			$this->cellStarted = false;
		}
		if ($this->rowStarted) {
			$this->out .= '</tr>';
			$this->rowStarted = false;
		}
		$this->out .= '</table>';
	}

	public function startCaption($previous) {
		trigger_error('Not implemented ', E_USER_NOTICE);
	}

	public function startHeader($previous) {
		trigger_error('Not implemented ', E_USER_NOTICE);
	}

	public function startRow($previous) {
		$this->out .= $previous;
		if ($this->cellStarted) {
			$this->out .= '</td>';
			$this->cellStarted = false;
		}
		if ($this->rowStarted) {
			$this->out .= '</tr>';
		}
		$this->out .= '<tr>';
		$this->rowStarted = true;
	}

	public function startCell($previous) {
		$this->out .= $previous;
		if ( ! $this->rowStarted) {
			$this->out .= '<td>';
			$this->rowStarted = true;
		}
		if ($this->cellStarted) {
			$this->out .= '</td>';
			$this->cellStarted = false;
		}
		$this->out .= '<td>';
		$this->cellStarted = true;
		$this->rowStarted = true;
	}

	public function convert($wikitext) {

		// remove
		// |} 	table end,
		// {| 	table start,
		$wikitext = substr($wikitext, 2, strlen($wikitext) - 4);

		$this->init();
		$i = 0;

		// recherche :
		// |+ 	table caption, optional; only between table start and table row
		// |- 	table row, optional on first row—wiki engine assumes the first row
		// ! or !! 	table header cell, optional. Consecutive table header cells may be added on same line separated by double marks (!!) or start on new lines, each with its own single mark (!).
		// | or ||	table data cell, optional. Consecutive table data cells may be added on same line separated by double marks (||) or start on new lines, each with its own single mark (|).

		// recherche :

		$pattern = "/(\n\|\+)|(\n\\|\-)|(\\|\\|)|(\n\!)|(\n\\|)[^\|\-]/";
		//$pattern = '/(\|\+)|(\\|\\-)/';

		while (preg_match($pattern, $wikitext,$matches,null,$i)) {

			$newPos = strpos($wikitext,$matches[0], $i);
			if ($newPos === false) {
				trigger_error('Fail to find string', E_USER_WARNING);
				return false;
			}
			for ( $j = 1; $j < count($matches); $j++) {
				if ($matches[$j]) {
					$matched = $matches[$j];
					break;
				}
			}

			$previous = substr($wikitext, $i, $newPos - $i);
			$newPos += strlen($matched);
			switch($matched) {
				case "\n|+" :
					$this->startCaption($previous ."\n");
					break;
				case "\n|-" :
					$this->startRow($previous."\n");
					break;
				case "\n!" :
					$this->startHeader($previous."\n");
					break;
				case "!!" :
					$this->startHeader($previous);
					break;
				case "\n|" :
					$this->startCell($previous."\n");
					break;
				case "||" :
					$this->startCell($previous );
					break;
				default :
					var_dump($matches);
					trigger_error('Should not come here : "' . $matches[0] . '"', E_USER_WARNING);
					break;
			}
			if($i == $newPos) {
				trigger_error('Fail to increment', E_USER_WARNING);
				return false;
			}
			if($i > $newPos) {
				trigger_error('Fail to increment', E_USER_WARNING);
				return false;
			}
			$i = $newPos;
		}


		$previous = substr($wikitext, $i);
		$this->end($previous);

		return $this->out;

	}
}