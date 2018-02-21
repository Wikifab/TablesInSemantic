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

	public function testParse( ) {
		$text = '{{Template:
|test=test
{|
|-
| cel1
| cel 2
|}
}}';
		//var_dump(substr($text, 19));

		$this->instance->parse($text);
		$result = $text;
		$expected = '{{Template:
|test=test
<table>
<tr>
<td> cel1
</td><td> cel 2
</td></tr></table>
}}';
		$this->assertEquals($expected, $result);
	}

	public function testParseComplexe( ) {
		$text = '{{Template:
|test=test
{|
|-
| cel1
| cel 2
|}

{{#Attention|test}}
{| cellspacing="0" border="0"
| height="17" align="left" |234d
| align="right" |1234
|-
| height="17" align="right" |324
| align="left" |SFZSD
|}
}}';
		//var_dump(substr($text, 19));

		$this->instance->parse($text);
		$result = $text;
		$expected = '{{Template:
|test=test
<table>
<tr>
<td> cel1
</td><td> cel 2
</td></tr></table>

{{#Attention|test}}
<table cellspacing="0" border="0">
<tr>
<td height="17" align="left">234d
</td><td align="right">1234
</td></tr><tr>
<td height="17" align="right">324
</td><td align="left">SFZSD
</td></tr></table>
}}';
		$this->assertEquals($expected, $result);
	}

	public function testParseHeader( ) {
		$text = '{{Template:
|test=test
{| cellspacing="0" border="0"
!header1
!2nd header
|-
| cel1
| cel 2
|}

{{#Attention|test}}
}}';
		//var_dump(substr($text, 19));

		$this->instance->parse($text);
		$result = $text;
		$expected = '{{Template:
|test=test
<table cellspacing="0" border="0">
<tr>
<th>header1
</th><th>2nd header
</th></tr><tr>
<td> cel1
</td><td> cel 2
</td></tr></table>

{{#Attention|test}}
}}';
		$this->assertEquals($expected, $result);
	}


	public function testParseLong( ) {


		//echo "\n\n\n --------- TEST PARSE LONG ------------";
		$text = '{{ {{tntn|Tuto Details}}
|Type=Creation
|Area=Robotics
|Tags=Arduino, Robot, Bois
|Description=<translate><!--T:34-->
Ce robot est fait en bois.234d	1234
324	SFZSD</translate>
|Difficulty=Medium
|Cost=100
|Currency=EUR (€)
|Duration=2
|Duration-type=hour(s)
|Licences=Attribution-ShareAlike (CC BY-SA)
|Main_Picture=Construire_le_robot_"ABC"_Robot V1 00_00_00-00_00_10.gif
|SourceLanguage=none
|Language=fr
|IsTranslation=0
}}
{{ {{tntn|Introduction}}
|Introduction=<translate><!--T:67--> Vous allez apprendre a construire un petit robot piloté par télécommande. Ce robot est construit en bois avec une carte électronique (arduino uno) et une extension de contrôle pour les deux moteurs.

second tableau :
{| cellspacing="0" border="0"
| height="17" align="left" |234d
| align="right" |1234
|-
| height="17" align="right" |324
| align="left" |SFZSD
|}</translate>
}}';
		$this->instance->parse($text);
		$result = $text;
		$expected = '{{ {{tntn|Tuto Details}}
|Type=Creation
|Area=Robotics
|Tags=Arduino, Robot, Bois
|Description=<translate><!--T:34-->
Ce robot est fait en bois.234d	1234
324	SFZSD</translate>
|Difficulty=Medium
|Cost=100
|Currency=EUR (€)
|Duration=2
|Duration-type=hour(s)
|Licences=Attribution-ShareAlike (CC BY-SA)
|Main_Picture=Construire_le_robot_"ABC"_Robot V1 00_00_00-00_00_10.gif
|SourceLanguage=none
|Language=fr
|IsTranslation=0
}}
{{ {{tntn|Introduction}}
|Introduction=<translate><!--T:67--> Vous allez apprendre a construire un petit robot piloté par télécommande. Ce robot est construit en bois avec une carte électronique (arduino uno) et une extension de contrôle pour les deux moteurs.

second tableau :
<table cellspacing="0" border="0">
<tr>
<td height="17" align="left">234d
</td><td align="right">1234
</td></tr><tr>
<td height="17" align="right">324
</td><td align="left">SFZSD
</td></tr></table></translate>
}}';
		//echo "\n\n\n --------- FIN TEST PARSE LONG 1 ------------";
		$this->assertEquals($expected, $result);
		//echo "\n\n\n --------- FIN TEST PARSE LONG 2------------";
	}
}
