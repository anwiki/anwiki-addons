<?php
/**
 * Anwiki is a multilingual content management system <http://www.anwiki.com>
 * Copyright (C) 2007-2009 Antoine Walter <http://www.anw.fr>
 * 
 * Anwiki is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 * 
 * Anwiki is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Anwiki.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Tests for Anwiki toolbox.
 * @package Anwiki
 * @version $Id: test_utils.php 374 2012-02-10 00:12:26Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */

class AnwUtilsTestCase extends UnitTestCase {
	function __construct() {
		$this->UnitTestCase('AnwUtils test');
	}
	
	function setUp() {
	
	}
	
	function tearDown() {
		//print '<hr/>'.AnwDebug::getLog();
	}
	
	function testXmlAreSimilarNodes1() { //nothing changed
		$sXml1 = '<div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div>';
		$sXml2 = '<div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlAreSimilarNodes($oNode1, $oNode2), true);
	}
	
	function testXmlAreSimilarNodes2() { //attribute changed
		$sXml1 = '<div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div>';
		$sXml2 = '<div style="float:right" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlAreSimilarNodes($oNode1, $oNode2), false);
		$this->assertEqual( AnwXml::xmlAreSimilarNodes($oNode1, $oNode2, true, false), true);
	}
	
	function testXmlAreSimilarNodes3() { //attribute added
		$sXml1 = '<div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div>';
		$sXml2 = '<div style="float:left" class="test"><p align="left" class="blah">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlAreSimilarNodes($oNode1, $oNode2), false);
		$this->assertEqual( AnwXml::xmlAreSimilarNodes($oNode1, $oNode2, true, false), true);
	}
	
	function testXmlAreSimilarNodes4() { //attribute added
		$sXml1 = '<div style="float:left" class="test"><p align="left" class="blah">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div>';
		$sXml2 = '<div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlAreSimilarNodes($oNode1, $oNode2), false);
		$this->assertEqual( AnwXml::xmlAreSimilarNodes($oNode1, $oNode2, true, false), true);
	}
	
	function testXmlAreSimilarNodes5() { //text changed
		$sXml1 = '<div style="float:left" class="test"><p align="left">this text is <i>italic CHANGED</i>... and <b>bold<u>!</u></b></p></div>';
		$sXml2 = '<div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlAreSimilarNodes($oNode1, $oNode2), false);
		$this->assertEqual( AnwXml::xmlAreSimilarNodes($oNode1, $oNode2, false, true), true);
	}
	
	function testXmlAreSimilarNodes6() { //text changed
		$sXml1 = '<div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div>';
		$sXml2 = '<div style="float:left" class="test"><p align="left">this text is <i>italic CHANGED</i>... and <b>bold<u>!</u></b></p></div>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlAreSimilarNodes($oNode1, $oNode2), false);
		$this->assertEqual( AnwXml::xmlAreSimilarNodes($oNode1, $oNode2, false, true), true);
	}
	
	function testXmlAreSimilarNodes7() { //node changed
		$sXml1 = '<div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div>';
		$sXml2 = '<span style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></span>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlAreSimilarNodes($oNode1, $oNode2), false);
	}
	
	function testXmlAreSimilarNodes8() { //node changed
		$sXml1 = '<div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div>';
		$sXml2 = '<div style="float:left" class="test"><p align="left">this text is <b>italic</b>... and <b>bold<u>!</u></b></p></div>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlAreSimilarNodes($oNode1, $oNode2), false);
	}
	
	function testXmlAreSimilarNodes9() { //node added
		$sXml1 = '<div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div>';
		$sXml2 = '<div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p><p align="left">...</p></div>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlAreSimilarNodes($oNode1, $oNode2), false);
	}
	
	//------------------
	
	function testXmlSimilarStructure1() { //nothing changed
		$sXml1 = '<doc><div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div></doc>';
		$sXml2 = '<doc><div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div></doc>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlSimilarStructure($oNode1, $oNode2), true);
	}
	
	function testXmlSimilarStructure2() { //attribute changed
		$sXml1 = '<doc><div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div></doc>';
		$sXml2 = '<doc><div style="float:right" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div></doc>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlSimilarStructure($oNode1, $oNode2), false);
		$this->assertEqual( AnwXml::xmlAreSimilarNodes($oNode1, $oNode2, true, false), true);
	}
	
	function testXmlSimilarStructure3() { //attribute added
		$sXml1 = '<doc><div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div></doc>';
		$sXml2 = '<doc><div style="float:left" class="test"><p align="left" class="blah">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div></doc>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlSimilarStructure($oNode1, $oNode2), false);
	}
	
	function testXmlSimilarStructure4() { //attribute added
		$sXml1 = '<doc><div style="float:left" class="test"><p align="left" class="blah">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div></doc>';
		$sXml2 = '<doc><div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div></doc>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlSimilarStructure($oNode1, $oNode2), false);
		$this->assertEqual( AnwXml::xmlAreSimilarNodes($oNode1, $oNode2, true, false), true);
	}
	
	function testXmlSimilarStructure5() { //text changed
		$sXml1 = '<doc><div style="float:left" class="test"><p align="left">this text is <i>italic CHANGED</i>... and <b>bold<u>!</u></b></p></div></doc>';
		$sXml2 = '<doc><div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div></doc>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlSimilarStructure($oNode1, $oNode2), true);
	}
	
	function testXmlSimilarStructure6() { //text changed
		$sXml1 = '<doc><div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div></doc>';
		$sXml2 = '<doc><div style="float:left" class="test"><p align="left">this text is <i>italic CHANGED</i>... and <b>bold<u>!</u></b></p></div></doc>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlSimilarStructure($oNode1, $oNode2), true);
	}
	
	function testXmlSimilarStructure7() { //node changed
		$sXml1 = '<doc><div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div></doc>';
		$sXml2 = '<doc><span style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></span></doc>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlSimilarStructure($oNode1, $oNode2), false);
	}
	
	function testXmlSimilarStructure8() { //node changed
		$sXml1 = '<doc><div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div></doc>';
		$sXml2 = '<doc><div style="float:left" class="test"><p align="left">this text is <b>italic</b>... and <b>bold<u>!</u></b></p></div></doc>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlSimilarStructure($oNode1, $oNode2), false);
	}
	
	function testXmlSimilarStructure9() { //node added
		$sXml1 = '<doc><div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p></div></doc>';
		$sXml2 = '<doc><div style="float:left" class="test"><p align="left">this text is <i>italic</i>... and <b>bold<u>!</u></b></p><p align="left">...</p></div></doc>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlSimilarStructure($oNode1, $oNode2), false);
	}
	
	function testXmlSimilarStructure10() { //fix node
		$sXml1 = '<doc><div><p align="left">this text is <fix>fixed</fix>... nobody can change it</p></div></doc>';
		$sXml2 = '<doc><div><p align="left">this text is <fix>fixed and changed</fix>... nobody can change it</p></div></doc>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlSimilarStructure($oNode1, $oNode2), false);
	}
	
	function testXmlSimilarStructure11() { //fix node
		$sXml1 = '<doc><div><p align="left">this text is <fix>fixed</fix>... nobody can change it</p></div></doc>';
		$sXml2 = '<doc><div><p align="left">this text is <fix>fixed</fix>... nobody can change it</p></div></doc>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlSimilarStructure($oNode1, $oNode2), true);
	}
	
	function testXmlSimilarStructure12() { //fix node
		$sXml1 = '<doc><div><p align="left">this text is <?php print "fixed";?>... nobody can change it</p></div></doc>';
		$sXml2 = '<doc><div><p align="left">this text is <?php print "fixed and changed";?>... nobody can change it</p></div></doc>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlSimilarStructure($oNode1, $oNode2), false);
	}
	
	function testXmlSimilarStructure13() { //fix node
		$sXml1 = '<doc><div><p align="left">this text is <?php print "fixed";?>... nobody can change it</p></div></doc>';
		$sXml2 = '<doc><div><p align="left">this text is <?php print "fixed";?>... nobody can change it</p></div></doc>';
		
		$oNode1 = AnwUtils::loadXML($sXml1);
		$oNode2 = AnwUtils::loadXML($sXml2);
		$this->assertEqual( AnwXml::xmlSimilarStructure($oNode1, $oNode2), true);
	}
	
	//-------------------
	
	function testXmlPreserveTextLayout1() {
		$sText = "Antoine";
		$sOriginal = "Blah";
		$sExpected = "Antoine";
		$this->assertEqual( AnwXml::xmlPreserveTextLayout($sText, $sOriginal), $sExpected);
	}
	
	function testXmlPreserveTextLayout2() {
		$sText = "    \t   \n   Antoine";
		$sOriginal = "Blah";
		$sExpected = " Antoine";
		$this->assertEqual( AnwXml::xmlPreserveTextLayout($sText, $sOriginal), $sExpected);
	}
	
	function testXmlPreserveTextLayout3() {
		$sText = "Antoine    \t   \n   ";
		$sOriginal = "Blah";
		$sExpected = "Antoine ";
		$this->assertEqual( AnwXml::xmlPreserveTextLayout($sText, $sOriginal), $sExpected);
	}
	
	function testXmlPreserveTextLayout4() {
		$sText = " Antoine";
		$sOriginal = "Blah";
		$sExpected = " Antoine";
		$this->assertEqual( AnwXml::xmlPreserveTextLayout($sText, $sOriginal), $sExpected);
	}
	
	function testXmlPreserveTextLayout5() {
		$sText = "Antoine ";
		$sOriginal = "Blah";
		$sExpected = "Antoine ";
		$this->assertEqual( AnwXml::xmlPreserveTextLayout($sText, $sOriginal), $sExpected);
	}	
	
	function testXmlPreserveTextLayout6() {
		$sText = "Antoine";
		$sOriginal = " Blah";
		$sExpected = "Antoine";
		$this->assertEqual( AnwXml::xmlPreserveTextLayout($sText, $sOriginal), $sExpected);
	}
		
	function testXmlPreserveTextLayout7() {
		$sText = "Antoine";
		$sOriginal = "Blah ";
		$sExpected = "Antoine";
		$this->assertEqual( AnwXml::xmlPreserveTextLayout($sText, $sOriginal), $sExpected);
	}
	
	function testXmlPreserveTextLayout8() {
		$sText = "Antoine";
		$sOriginal = " \n \t  Blah";
		$sExpected = " \n \tAntoine";
		$this->assertEqual( AnwXml::xmlPreserveTextLayout($sText, $sOriginal), $sExpected);
	}	
	
	function testXmlPreserveTextLayout9() {
		$sText = "Antoine";
		$sOriginal = "Blah \t \t  \n ";
		$sExpected = "Antoine\t \t  \n ";
		$this->assertEqual( AnwXml::xmlPreserveTextLayout($sText, $sOriginal), $sExpected);
	}	
	
	function testXmlPreserveTextLayout10() {
		$sText = " Antoine";
		$sOriginal = "\t Blah";
		$sExpected = "\t Antoine";
		$this->assertEqual( AnwXml::xmlPreserveTextLayout($sText, $sOriginal), $sExpected);
	}
	
	function testXmlPreserveTextLayout11() {
		$sText = "Antoine ";
		$sOriginal = "Blah \t";
		$sExpected = "Antoine \t";
		$this->assertEqual( AnwXml::xmlPreserveTextLayout($sText, $sOriginal), $sExpected);
	}
	
	function testXmlPreserveTextLayout12() {
		$sText = " Antoine";
		$sOriginal = "\tBlah";
		$sExpected = "\t Antoine";
		$this->assertEqual( AnwXml::xmlPreserveTextLayout($sText, $sOriginal), $sExpected);
	}
	
	
	function testXmlPreserveTextLayout13() {
		$sText = "Antoine ";
		$sOriginal = "Blah\t";
		$sExpected = "Antoine \t";
		$this->assertEqual( AnwXml::xmlPreserveTextLayout($sText, $sOriginal), $sExpected);
	}
		
	function testXmlPreserveTextLayout14() {
		$sText = "\t\t"."Antoine"."\t\t";
		$sOriginal = "\tBlah\t";
		$sExpected = "\tAntoine\t";
		$this->assertEqual( AnwXml::xmlPreserveTextLayout($sText, $sOriginal), $sExpected);
	}
		
	function testXmlPreserveTextLayout15() {
		$sText = "\t\t   Antoine   \t\t";
		$sOriginal = "\tBlah\t";
		$sExpected = "\t Antoine \t";
		$this->assertEqual( AnwXml::xmlPreserveTextLayout($sText, $sOriginal), $sExpected);
	}
	
	function testXmlPreserveTextLayoutStrict1() {
		$sText = "\t\t"."Antoine"."\t\t";
		$sOriginal = "\t \nBlah\n \t";
		$sExpected = "\t \nAntoine\n \t";
		$this->assertEqual( AnwXml::xmlPreserveTextLayoutStrict($sText, $sOriginal), $sExpected);
	}
	
	function testXmlPreserveTextLayoutStrict2() {
		$sText = "\t\t   Antoine   \t\t";
		$sOriginal = "\tBlah\t";
		$sExpected = "\tAntoine\t";
		$this->assertEqual( AnwXml::xmlPreserveTextLayoutStrict($sText, $sOriginal), $sExpected);
	}
	
	function testXmlGetUntranslatedTxt1() {
		$bUntranslated = true;
		$sOriginal = "\t\t\n Antoine \t\n";
		$sExpected = "\t\t\n ".AnwUtils::FLAG_UNTRANSLATED_OPEN."Antoine".AnwUtils::FLAG_UNTRANSLATED_CLOSE." \t\n";
		$this->assertEqual( AnwXml::xmlGetUntranslatedTxt($sOriginal, $bUntranslated), $sExpected);
	}
	
	function testXmlGetUntranslatedTxt2() {
		$bUntranslated = false;
		$sOriginal = "\t\t\n Antoine \t\n";
		$sExpected = "\t\t\n Antoine \t\n";
		$this->assertEqual( AnwXml::xmlGetUntranslatedTxt($sOriginal, $bUntranslated), $sExpected);
	}
	
	function testXmlGetUntranslatedTxt3() {
		$bUntranslated = false;
		$sOriginal = "\t\t\n ".AnwUtils::FLAG_UNTRANSLATED_OPEN."Antoine".AnwUtils::FLAG_UNTRANSLATED_CLOSE." \t\n";
		$sExpected = "\t\t\n Antoine \t\n";
		$this->assertEqual( AnwXml::xmlGetUntranslatedTxt($sOriginal, $bUntranslated), $sExpected);
	}
	/*
	function testGetRelativePath1() {
		$sUrlFrom = "/wiki/en/home";
		$sUrlTo = "/wiki/en/another";
		$sExpected = "another";
		$this->_testGetRelativePath($sUrlFrom, $sUrlTo, $sExpected);
	}
	
	function testGetRelativePath2() {
		$sUrlFrom = "/wiki/index.php";
		$sUrlTo = "/wiki/en/another";
		$sExpected = "en/another";
		$this->_testGetRelativePath($sUrlFrom, $sUrlTo, $sExpected);
	}
	
	function testGetRelativePath3() {
		$sUrlFrom = "/wiki/en/another";
		$sUrlTo = "/wiki/index.php";
		$sExpected = "../index.php";
		$this->_testGetRelativePath($sUrlFrom, $sUrlTo, $sExpected);
	}
	
	function testGetRelativePath4() {
		$sUrlFrom = "/wiki/en/home/sub";
		$sUrlTo = "/wiki/en/sub";
		$sExpected = "../sub";
		$this->_testGetRelativePath($sUrlFrom, $sUrlTo, $sExpected);
	}
	
	function testGetRelativePath5() {
		$sUrlFrom = "/wiki/en/sub";
		$sUrlTo = "/wiki/en/home/sub";
		$sExpected = "home/sub";
		$this->_testGetRelativePath($sUrlFrom, $sUrlTo, $sExpected);
	}
	
	function testGetRelativePath6() {
		$sUrlFrom = "/wiki/";
		$sUrlTo = "/wiki/en/home/sub";
		$sExpected = "en/home/sub";
		$this->_testGetRelativePath($sUrlFrom, $sUrlTo, $sExpected);
	}
	
	function testGetRelativePath7() {
		$sUrlFrom = "/wiki/en/home/sub";
		$sUrlTo = "/wiki/";
		$sExpected = "../..";
		$this->_testGetRelativePath($sUrlFrom, $sUrlTo, $sExpected);
	}
	
	protected function _testGetRelativePath($sUrlFrom, $sUrlTo, $sExpected) {
		$sResult = AnwUtils::__test_getRelativePath($sUrlTo, $sUrlFrom);
		$this->assertEqual($sResult, $sExpected);
	}*/
}

?>