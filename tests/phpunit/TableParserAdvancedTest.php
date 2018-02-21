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
class TableParserAdvancedTest extends \PHPUnit_Framework_TestCase {

	protected  $instance = null;

	protected function setUp() {
		parent::setUp();
		$this->instance = new TableParser();
	}

	protected function tearDown() {
		parent::tearDown();
	}


	public function testParseWithLink( ) {
		$text = '{
{{Template:
|test=test
[[totoo:Hercule|titi={tt}]]
{|
|-
| cel1
| cel 2
|}
}}';
		//var_dump(substr($text, 53));

		$result = $this->instance->findTableInTemplate(20, $text);
		$expected = 53;
		$this->assertEquals($expected, $result);
	}


	public function testParseTricky( ) {


		//echo "\n\n\n --------- TEST PARSE LONG ------------";
		$text = '{{WikiPage
|Main_Picture=Tiroir_à_poudre_Tiroir_a_poudre_vue_Final.jpg
|Type=Instruction
|Produit=FormUp350
|Area=Electrical cabinet
|WikiPageContent=[[Fichier:Tiroir à poudre main.jpg|centré|vignette|267x267px]]
{{Idea|Salut l\'ami}}
{| class="wikitable sortable" border="1" cellspacing="0" cellpadding="0" width="439"
| width="205" valign="top" |\'\'\'Légende\'\'\'
| width="234" valign="top" |\'\'\'Significations\'\'\'
|-
| width="205" valign="top" |
| width="234" valign="top" |Arrêt machine
{{Idea|Salut}}
|-
| width="205" valign="top" |
| width="234" valign="top" |Sectionneurs
|-
| width="205" valign="top" |
| width="234" valign="top" |Pupitre principal
|-
| width="205" valign="top" |
| width="234" valign="top" |Porte inter  verrouillée
|-
| width="205" valign="top" | 
| width="234" valign="top" |Porte verrouillée
|}
|Tags=Doseur poudre,
}}
{{Tuto Status}}
		';
		$this->instance->parse($text);
		$result = $text;
		$expected = '{{WikiPage
|Main_Picture=Tiroir_à_poudre_Tiroir_a_poudre_vue_Final.jpg
|Type=Instruction
|Produit=FormUp350
|Area=Electrical cabinet
|WikiPageContent=[[Fichier:Tiroir à poudre main.jpg|centré|vignette|267x267px]]
{{Idea|Salut l\'ami}}
<table class="wikitable sortable" border="1" cellspacing="0" cellpadding="0" width="439">
<tr>
<td width="205" valign="top">\'\'\'Légende\'\'\'
</td><td width="234" valign="top">\'\'\'Significations\'\'\'
</td></tr><tr>
<td width="205" valign="top">
</td><td width="234" valign="top">Arrêt machine
{{Idea|Salut}}
</td></tr><tr>
<td width="205" valign="top">
</td><td width="234" valign="top">Sectionneurs
</td></tr><tr>
<td width="205" valign="top">
</td><td width="234" valign="top">Pupitre principal
</td></tr><tr>
<td width="205" valign="top">
</td><td width="234" valign="top">Porte inter  verrouillée
</td></tr><tr>
<td width="205" valign="top"> 
</td><td width="234" valign="top">Porte verrouillée
</td></tr></table>
|Tags=Doseur poudre,
}}
{{Tuto Status}}
		';
		//echo "\n\n\n --------- FIN TEST PARSE LONG 1 ------------";
		$this->assertEquals($expected, $result);
		//echo "\n\n\n --------- FIN TEST PARSE LONG 2------------";
	}



	public function testParseLong( ) {


		//echo "\n\n\n --------- TEST PARSE LONG ------------";
		$text = '{{WikiPage
|Main_Picture=Tiroir_à_poudre_Tiroir_a_poudre_vue_Final.jpg
|Type=Instruction
|Produit=FormUp350
|Area=Electrical cabinet
|WikiPageContent=[[Fichier:Tiroir à poudre main.jpg|centré|vignette|267x267px]]
{| class="wikitable"
!Salut : qfiodhs
!dfn / fezhqi
!fhdq
!azEQZFE
!sdfkpfs
|-
|DSGQF
|fgsfdds
|DSFQFD
|DSGF
|dqsfwf
|-
|QSFGD
|QFDS
|SFDQGD
|sgfdf
| fichier supprime
|-
|QSFGD
|DSQF
|gsfdf
|QDFKBSIX
|
|}
{| class="wikitable" border="1" cellspacing="0" cellpadding="0" width="510"
| colspan="4" width="510" |Equipement DE Protection Individuelle (EPI)
|-
| width="99" valign="top" |Symbole
| width="156" valign="top" |EPI
| width="99" valign="top" |Symbole
| width="156" valign="top" |EPI
|-
| width="99" valign="top" |
| width="156" |Port du casque [[Fichier:Tiroir à poudre main.jpg|sans_cadre|200x200px]]
| width="99" valign="top" |
| width="156" |Port de casque anti-bruit
|-
| width="99" valign="top" |
| width="156" |Port de lunettes de protection
| width="99" valign="top" |
| width="156" |Port de harnais de sécurité
|-
| width="99" valign="top" |
| width="156" |Port de protection des voies respiratoires
| width="99" valign="top" |
| width="156" |Port d’une tenue de protection corporelle
|-
| width="99" valign="top" |
| width="156" |Port de gants
| width="99" valign="top" |
| width="156" |Port de cagoule ventilée
|}
 {{Dont|Salut Pierro !}}

{| class="wikitable" border="1" cellspacing="0" cellpadding="0" width="439"
| width="205" valign="top" |Légende
| width="234" valign="top" |Significations
|-
| width="205" valign="top" |
| width="234" valign="top" |Arrêt machine
|-
| width="205" valign="top" |
| width="234" valign="top" |Sectionneurs
|-
| width="205" valign="top" |
| width="234" valign="top" |Pupitre principal
|-
| width="205" valign="top" |
| width="234" valign="top" |Porte inter  verrouillée
|-
| width="205" valign="top" | 
| width="234" valign="top" |Porte verrouillée
|}
{{Idea|Salut l\'ami}}
{| class="wikitable sortable" border="1" cellspacing="0" cellpadding="0" width="439"
| width="205" valign="top" |\'\'\'Légende\'\'\'
| width="234" valign="top" |\'\'\'Significations\'\'\'
|-
| width="205" valign="top" |
| width="234" valign="top" |Arrêt machine
{{Idea|Salut}}
|-
| width="205" valign="top" |
| width="234" valign="top" |Sectionneurs
|-
| width="205" valign="top" |
| width="234" valign="top" |Pupitre principal
|-
| width="205" valign="top" |
| width="234" valign="top" |Porte inter  verrouillée
|-
| width="205" valign="top" | 
| width="234" valign="top" |Porte verrouillée
|}
{| class="wikitable" border="1" cellspacing="0" cellpadding="0" width="510"
| colspan="4" width="510" |Chariot de transfert – 2015vig220100
|-
| colspan="4" width="510" valign="top" |
|-
| width="64" |Rep.
| width="49" |Elec
| width="191" |Désignation   / Description
| width="205" |Localisation   *
|-
| colspan="3" width="305" |
| width="205" valign="top" |
|-
| width="64" valign="top" |j
| width="49" valign="top" |
| width="191" valign="top" |Chariot de  transfert
| width="205" valign="top" |
|-
| colspan="3" width="305" | 
| width="205" valign="top" |
|-
| width="64" valign="top" |k
| width="49" valign="top" |
| width="191" valign="top" |Roulement à  billes
| width="205" valign="top" |
|-
| colspan="3" width="305" | 
| width="205" valign="top" |
|-
| width="64" valign="top" |
| width="49" valign="top" |
| width="191" valign="top" |
| width="205" valign="top" |
|-
| colspan="4" width="510" valign="top" |* Si pas dans l\'ensemble concerné / If not in the  concerned assembly.
|-
| width="64" |Rep.
| width="49" |Qté   / Qty
| width="191" |Référence   / Reference
| width="205" |Fournisseur   / Supplier
|-
| width="64" valign="top" |k
| width="49" valign="top" |8
| width="191" valign="top" |6301-2RS1
| width="205" valign="top" |INA
|-
| colspan="4" width="510" |
|-
| width="64" valign="top" |
| width="49" valign="top" |
| width="191" valign="top" |
| width="205" valign="top" |
|-
| colspan="4" width="510" |
|-
| width="64" valign="top" |
| width="49" |
| width="191" valign="top" |
| width="205" valign="top" |
|}
|Tags=Doseur poudre,
}}
{{Tuto Status}}
		';
		$this->instance->parse($text);
		$result = $text;
		$expected = '{{WikiPage
|Main_Picture=Tiroir_à_poudre_Tiroir_a_poudre_vue_Final.jpg
|Type=Instruction
|Produit=FormUp350
|Area=Electrical cabinet
|WikiPageContent=[[Fichier:Tiroir à poudre main.jpg|centré|vignette|267x267px]]
<table class="wikitable">
<tr>
<th>Salut : qfiodhs
</th><th>dfn / fezhqi
</th><th>fhdq
</th><th>azEQZFE
</th><th>sdfkpfs
</th></tr><tr>
<td>DSGQF
</td><td>fgsfdds
</td><td>DSFQFD
</td><td>DSGF
</td><td>dqsfwf
</td></tr><tr>
<td>QSFGD
</td><td>QFDS
</td><td>SFDQGD
</td><td>sgfdf
</td><td> fichier supprime
</td></tr><tr>
<td>QSFGD
</td><td>DSQF
</td><td>gsfdf
</td><td>QDFKBSIX
</td><td>
</td></tr></table>
<table class="wikitable" border="1" cellspacing="0" cellpadding="0" width="510">
<tr>
<td colspan="4" width="510">Equipement DE Protection Individuelle (EPI)
</td></tr><tr>
<td width="99" valign="top">Symbole
</td><td width="156" valign="top">EPI
</td><td width="99" valign="top">Symbole
</td><td width="156" valign="top">EPI
</td></tr><tr>
<td width="99" valign="top">
</td><td width="156">Port du casque [[Fichier:Tiroir à poudre main.jpg|sans_cadre|200x200px]]
</td><td width="99" valign="top">
</td><td width="156">Port de casque anti-bruit
</td></tr><tr>
<td width="99" valign="top">
</td><td width="156">Port de lunettes de protection
</td><td width="99" valign="top">
</td><td width="156">Port de harnais de sécurité
</td></tr><tr>
<td width="99" valign="top">
</td><td width="156">Port de protection des voies respiratoires
</td><td width="99" valign="top">
</td><td width="156">Port d’une tenue de protection corporelle
</td></tr><tr>
<td width="99" valign="top">
</td><td width="156">Port de gants
</td><td width="99" valign="top">
</td><td width="156">Port de cagoule ventilée
</td></tr></table>
 {{Dont|Salut Pierro !}}

<table class="wikitable" border="1" cellspacing="0" cellpadding="0" width="439">
<tr>
<td width="205" valign="top">Légende
</td><td width="234" valign="top">Significations
</td></tr><tr>
<td width="205" valign="top">
</td><td width="234" valign="top">Arrêt machine
</td></tr><tr>
<td width="205" valign="top">
</td><td width="234" valign="top">Sectionneurs
</td></tr><tr>
<td width="205" valign="top">
</td><td width="234" valign="top">Pupitre principal
</td></tr><tr>
<td width="205" valign="top">
</td><td width="234" valign="top">Porte inter  verrouillée
</td></tr><tr>
<td width="205" valign="top"> 
</td><td width="234" valign="top">Porte verrouillée
</td></tr></table>
{{Idea|Salut l\'ami}}
<table class="wikitable sortable" border="1" cellspacing="0" cellpadding="0" width="439">
<tr>
<td width="205" valign="top">\'\'\'Légende\'\'\'
</td><td width="234" valign="top">\'\'\'Significations\'\'\'
</td></tr><tr>
<td width="205" valign="top">
</td><td width="234" valign="top">Arrêt machine
{{Idea|Salut}}
</td></tr><tr>
<td width="205" valign="top">
</td><td width="234" valign="top">Sectionneurs
</td></tr><tr>
<td width="205" valign="top">
</td><td width="234" valign="top">Pupitre principal
</td></tr><tr>
<td width="205" valign="top">
</td><td width="234" valign="top">Porte inter  verrouillée
</td></tr><tr>
<td width="205" valign="top"> 
</td><td width="234" valign="top">Porte verrouillée
</td></tr></table>
<table class="wikitable" border="1" cellspacing="0" cellpadding="0" width="510">
<tr>
<td colspan="4" width="510">Chariot de transfert – 2015vig220100
</td></tr><tr>
<td colspan="4" width="510" valign="top">
</td></tr><tr>
<td width="64">Rep.
</td><td width="49">Elec
</td><td width="191">Désignation   / Description
</td><td width="205">Localisation   *
</td></tr><tr>
<td colspan="3" width="305">
</td><td width="205" valign="top">
</td></tr><tr>
<td width="64" valign="top">j
</td><td width="49" valign="top">
</td><td width="191" valign="top">Chariot de  transfert
</td><td width="205" valign="top">
</td></tr><tr>
<td colspan="3" width="305"> 
</td><td width="205" valign="top">
</td></tr><tr>
<td width="64" valign="top">k
</td><td width="49" valign="top">
</td><td width="191" valign="top">Roulement à  billes
</td><td width="205" valign="top">
</td></tr><tr>
<td colspan="3" width="305"> 
</td><td width="205" valign="top">
</td></tr><tr>
<td width="64" valign="top">
</td><td width="49" valign="top">
</td><td width="191" valign="top">
</td><td width="205" valign="top">
</td></tr><tr>
<td colspan="4" width="510" valign="top">* Si pas dans l\'ensemble concerné / If not in the  concerned assembly.
</td></tr><tr>
<td width="64">Rep.
</td><td width="49">Qté   / Qty
</td><td width="191">Référence   / Reference
</td><td width="205">Fournisseur   / Supplier
</td></tr><tr>
<td width="64" valign="top">k
</td><td width="49" valign="top">8
</td><td width="191" valign="top">6301-2RS1
</td><td width="205" valign="top">INA
</td></tr><tr>
<td colspan="4" width="510">
</td></tr><tr>
<td width="64" valign="top">
</td><td width="49" valign="top">
</td><td width="191" valign="top">
</td><td width="205" valign="top">
</td></tr><tr>
<td colspan="4" width="510">
</td></tr><tr>
<td width="64" valign="top">
</td><td width="49">
</td><td width="191" valign="top">
</td><td width="205" valign="top">
</td></tr></table>
|Tags=Doseur poudre,
}}
{{Tuto Status}}
		';
		//echo "\n\n\n --------- FIN TEST PARSE LONG 1 ------------";
		$this->assertEquals($expected, $result);
		//echo "\n\n\n --------- FIN TEST PARSE LONG 2------------";
	}
}
