<?php
namespace TablesInSemantic;

class TableConverter {

	var $text;

	var $debug = false;

	var $i = 0, $out = '', $elements = '';
	var $rowStarted = false, $cellStarted = false, $headerStarted = false;

	public function __construct() {

	}

	public function addElement($type, $close = false) {
		$this->elements[] = ['type' => $type, 'isClosing' => $close, 'content' => "\n"];
	}
	public function addElementContent($content) {

		$i = count($this->elements) -1;
		$type = $this->elements[$i]['type'];
		switch ($type) {
			case 'table' :
				$this->elements[$i]['params'] = str_replace("\n", " ", $content);
				break;
			case 'tr' :
				$this->elements[$i]['params'] = str_replace("\n", " ", $content);
				break;
			case 'td' :
			case 'th' :
				if(preg_match('/^([^\\|]+)\\|(.*)$/s', $content, $matches)) {
					$this->elements[$i]['params'] = $matches[1];
					$this->elements[$i]['content'] = substr($content, strlen($matches[1])+1);
				} else {
					$this->elements[$i]['content'] = $content;
				}
				break;
		}
	}

	public function init() {
		$this->out = '<table>';
		$this->elements = [];
		$this->addElement('table');
	}
	public function end($previous) {
		$this->addElementContent($previous);
		$this->out .= $previous;

		$this->closePreviousCell();
		$this->closePreviousRow();

		$this->addElement('table', true);
		$this->out .= '</table>';
	}

	public function startCaption($previous) {
		trigger_error('Not implemented ', E_USER_NOTICE);
	}

	public function startHeader($previous) {
		$this->addElementContent($previous);
		$this->out .= $previous;
		if ( ! $this->rowStarted) {
			$this->startRow('');
		}
		$this->closePreviousCell();
		$this->addElement('th');
		$this->out .= '<th>';
		$this->headerStarted = true;
	}

	public function closePreviousCell() {
		if ($this->cellStarted) {
			$this->addElement('td', true);
			$this->out .= '</td>';
			$this->cellStarted = false;
		}
		if ($this->headerStarted) {
			$this->addElement('th', true);
			$this->out .= '</th>';
			$this->headerStarted = false;
		}
	}
	public function closePreviousRow() {
		if ($this->rowStarted) {
			$this->addElement('tr', true);
			$this->out .= '</tr>';
		}
	}

	public function startRow($previous) {
		if ($previous) {
			$this->addElementContent($previous);
		}
		$this->out .= $previous;

		$this->closePreviousCell();
		$this->closePreviousRow();
		$this->addElement('tr');
		$this->out .= '<tr>';
		$this->rowStarted = true;
	}

	public function startCell($previous) {
		$this->addElementContent($previous);
		$this->out .= $previous;
		if ( ! $this->rowStarted) {
			$this->startRow('');
		}
		$this->closePreviousCell();
		$this->addElement('td');
		$this->out .= '<td>';
		$this->cellStarted = true;
	}

	public function getElementHtml() {

		$out = '';
		foreach ($this->elements as $element) {
			$out .= $element['isClosing'] ? '</' : '<';
			$out .= $element['type'];
			if(isset($element['params']) && trim($element['params'])) {
				$out .= ' ' . trim($element['params']);
			}
			$out .= '>';
			if ( ! $element['isClosing']) {
				$out .= $element['content'];
			}
		}
		return $out;
	}

	public function getHtml() {
		return $this->getElementHtml();
		return $this->out;
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

		return $this->getHtml();

	}
}