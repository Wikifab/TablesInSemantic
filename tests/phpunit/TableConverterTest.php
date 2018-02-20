<?php

namespace TablesInSemantic\Tests;

use TablesInSemantic\TableConverter;


/**
 * @uses \Bootstrap\BootstrapManager
 *
 * @ingroup Test
 *
 * @group extension-bootstrap
 * @group mediawiki-databaseless
 *
 * @license GNU GPL v3+
 * @since 1.0
 *
 * @author mwjames
 */
class TableConverterTest extends \PHPUnit_Framework_TestCase {

	protected  $instance = null;

	protected function setUp() {
		parent::setUp();
		$this->instance = new TableConverter();
	}

	protected function tearDown() {
		parent::tearDown();
	}


	public function testCreateTable() {
		$this->instance->init();
		$this->instance->startRow("\n");
		$this->instance->startCell("\n");
		$this->instance->startCell("c1\n");
		$this->instance->startRow("c2\n");
		$this->instance->startCell("\n");
		$this->instance->startCell("c3\n");
		$this->instance->end("c4\n");
		$expected = '<table>
<tr>
<td>c1
</td><td>c2
</td></tr><tr>
<td>c3
</td><td>c4
</td></tr></table>';
		$result = $this->instance->getHtml();

		$this->assertEquals($expected, $result);
	}


	public function testConvert() {
		$text = '{|
|-
| cel1
| cel 2
|}';
		$result = $this->instance->convert($text);
		$expected = '<table>
<tr>
<td> cel1
</td><td> cel 2
</td></tr></table>';
		$this->assertEquals($expected, $result);
	}

	public function testConvertLine() {
		$text = '{|
|-
| cel1 || cel 2
|}';
		$result = $this->instance->convert($text);
		$expected = '<table>
<tr>
<td> cel1 </td><td> cel 2
</td></tr></table>';
		$this->assertEquals($expected, $result);
	}

	public function testConvertComplex() {
		$text = '{| cellspacing="0" border="0"
| height="17" align="left" |234d
| align="right" |1234
|-
| height="17" align="right" |324
| align="left" |SFZSD
|}';
		$result = $this->instance->convert($text);
		$expected = '<table cellspacing="0" border="0">
<tr>
<td height="17" align="left">234d
</td><td align="right">1234
</td></tr><tr>
<td height="17" align="right">324
</td><td align="left">SFZSD
</td></tr></table>';
		$this->assertEquals($expected, $result);
	}


}
