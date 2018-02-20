<?php

namespace TablesInSemantic\Tests;

use TablesInSemantic\TableParser;


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
class TableParserTest extends \PHPUnit_Framework_TestCase {

	protected  $instance = null;

	protected function setUp() {
		parent::setUp();
		$this->instance = new TableParser();
	}

	protected function tearDown() {
		parent::tearDown();
	}


	public function testIndexOfNextTemplate() {
		$text = '
{{Template:
|test=test
{|
|-
| cel1
| cel 2
|}
}}';
		$result = $this->instance->indexOfNextTemplate(0,$text);
		$expected = 19;
		$this->assertEquals($expected, $result);
	}

	public function testFindClosingAccoladeSimple( ) {
		$text = '   }{ {{}}}';
		$result = $this->instance->findClosingAccolade ($text,1, 0);
		$expected = 4;
		$this->assertEquals($expected, $result);
		$result = $this->instance->findClosingAccolade ($text,1, 5);
		$expected = 11;
		$this->assertEquals($expected, $result);
	}

	public function testFindClosingAccolade( ) {
		$text = '{
{{Template:
|test=test
{{#totoo:Hercule|titi={tt}}}
{|
|-
| cel1
| cel 2
|}
}}';
		//var_dump(substr($text, 27));
		//var_dump(substr($text, 53));
		$result = $this->instance->findClosingAccolade ($text,2, 27);
		$expected = 53;
		$this->assertEquals($expected, $result);
	}
	public function testFindClosingAccoladeFail( ) {
		$text = '{
{{Template:
|test=test
{{#totoo:Hercule|titi={tt}}
{|
|-
| cel1
| cel 2
|}
';
		$result = $this->instance->findClosingAccolade ($text,2, 27);
		$expected = false;
		$this->assertEquals($expected, $result);
	}

	public function testFindTableInTemplateNoTable( ) {
		$text = '{
{{Template:
|test=test
{{#totoo:Hercule|titi={tt}}
}}';

		$result = $this->instance->findTableInTemplate(19, $text);
		$expected = false;
		$this->assertEquals($expected, $result);
	}

	public function testFindTableInTemplate( ) {
		$text = '{
{{Template:
|test=test
{|
|-
| cel1
| cel 2
|}
}}';
		//var_dump(substr($text, 25));

		$result = $this->instance->findTableInTemplate(20, $text);
		$expected = 25;
		$this->assertEquals($expected, $result);
	}

	public function testFindTableInTemplateBis( ) {
		$text = '{
{{Template:
|test=test
{{#totoo:Hercule|titi={tt}}}
{|
|-
| cel1
| cel 2
|}
}}';
		//var_dump(substr($text, 54));

		$result = $this->instance->findTableInTemplate(19, $text);
		$expected = 54;
		$this->assertEquals($expected, $result);
	}

	public function testFindTableTemplateString( ) {
		$text = '{
{{Template:
|test=test
{|
|-
| cel1
| cel 2
|}
}}';
		//var_dump(substr($text, 19));

		$result = $this->instance->findTableTemplateString($text, 25);
		$expected = '{|
|-
| cel1
| cel 2
|}';
		$this->assertEquals($expected, $result);
	}

}
