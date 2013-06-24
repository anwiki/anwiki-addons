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
 * Tests for Anwiki XML functions.
 * @package Anwiki
 * @version $Id: test_diffs.php 135 2009-02-08 16:57:41Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */

class AnwXmlTestCase extends UnitTestCase {
	function __construct() {
		$this->UnitTestCase('AnwXml test');
	}
	
	function setUp() {
	
	}
	
	function tearDown() {
	}
	
	function testXmlns1() {
		$sXmlSource = '<a href="abp:subscribe?location=http%3A%2F%2Fsubscription.example.com%2Fadblock.txt&amp;title=Not%20really%20a%20subscription">like this one</a>';
		$sExpectedPrepareXml = '<a href="abp:subscribe?location=http%3A%2F%2Fsubscription.example.com%2Fadblock.txt&amp;title=Not%20really%20a%20subscription">like this one</a>';
		$this->_testXml($sXmlSource, $sExpectedPrepareXml);
	}
	
	function testXmlns2() {
		$sXmlSource = '<p style="clear:both; border:1px solid #000">test<br/>blah</p><p style="clear:both; border:1px solid #000">test<br/>blah</p>';
		$sExpectedPrepareXml = '<p style="clear:both; border:1px solid #000">test<br/>blah</p><p style="clear:both; border:1px solid #000">test<br/>blah</p>';
		$this->_testXml($sXmlSource, $sExpectedPrepareXml);
	}
	
	function testXmlns3() {
		$sXmlSource = '<foo:p style="clear:both; border:1px solid #000">test:test<br/>blah</foo:p>';
		$sExpectedPrepareXml = '<foo'.AnwXml::FIX_NS_RENAME.'p style="clear:both; border:1px solid #000">test:test<br/>blah</foo'.AnwXml::FIX_NS_RENAME.'p>';
		$this->_testXml($sXmlSource, $sExpectedPrepareXml);
	}
	
	function testXmlns4() {
		$sXmlSource = '<foo:p style="test">test:test<br/>blah</foo:p>';
		$sExpectedPrepareXml = '<foo'.AnwXml::FIX_NS_RENAME.'p style="test">test:test<br/>blah</foo'.AnwXml::FIX_NS_RENAME.'p>';
		$this->_testXml($sXmlSource, $sExpectedPrepareXml);
	}
	
	function testXmlns5() {
		$sXmlSource = '<foo:p>testtestblah</foo:p>';
		$sExpectedPrepareXml = '<foo'.AnwXml::FIX_NS_RENAME.'p>testtestblah</foo'.AnwXml::FIX_NS_RENAME.'p>';
		$this->_testXml($sXmlSource, $sExpectedPrepareXml);
	}
	
	function testXmlns6() {
		$sXmlSource = 'AA:A<br/><foo xmlns="http://bar/"/>DDD';
		$sExpectedPrepareXml = 'AA:A<br/><foo '.AnwXml::FIX_XMLNS_DEFAULT_RENAME.'="http://bar/"/>DDD';
		$this->_testXml($sXmlSource, $sExpectedPrepareXml);
	}

	function testXmlns7() {
		$sXmlSource = '<bar foo:xmlns="http://example.com" foo:attr="value"/>';
		$sExpectedPrepareXml = '<bar foo'.AnwXml::FIX_NS_RENAME.'xmlns="http://example.com" foo'.AnwXml::FIX_NS_RENAME.'attr="value"/>';
		$this->_testXml($sXmlSource, $sExpectedPrepareXml);
	}
	
	function testXmlns8() {
		$sXmlSource = '<bar foo:attr="value"/>a:a';
		$sExpectedPrepareXml = '<bar foo'.AnwXml::FIX_NS_RENAME.'attr="value"/>a:a';
		$this->_testXml($sXmlSource, $sExpectedPrepareXml);
	}

	function testXmlns9() {
		$sXmlSource = '<foo:bar foo:xmlns="http://example.com" foo:attr="value"/>';
		$sExpectedPrepareXml = '<foo'.AnwXml::FIX_NS_RENAME.'bar foo'.AnwXml::FIX_NS_RENAME.'xmlns="http://example.com" foo'.AnwXml::FIX_NS_RENAME.'attr="value"/>';
		$this->_testXml($sXmlSource, $sExpectedPrepareXml);
	}

	function testXmlns10() {
		$sXmlSource = 'AAA<br/><foo:bar foo:xmlns="http://example.com" foo:attr="value" style="border:1px solid #000"/>';
		$sExpectedPrepareXml = 'AAA<br/><foo'.AnwXml::FIX_NS_RENAME.'bar foo'.AnwXml::FIX_NS_RENAME.'xmlns="http://example.com" foo'.AnwXml::FIX_NS_RENAME.'attr="value" style="border:1px solid #000"/>';
		$this->_testXml($sXmlSource, $sExpectedPrepareXml);
	}

	function testXmlns11() {
		$sXmlSource = '<foo:bar bar:xmlns="http://another" xmlns="http://default" foo:xmlns="http://example.com" foo:attr="value" foo:attr2="value2" attr3="value3"/>';
		$sExpectedPrepareXml = '<foo'.AnwXml::FIX_NS_RENAME.'bar bar'.AnwXml::FIX_NS_RENAME.'xmlns="http://another" '.AnwXml::FIX_XMLNS_DEFAULT_RENAME.'="http://default" foo'.AnwXml::FIX_NS_RENAME.'xmlns="http://example.com" foo'.AnwXml::FIX_NS_RENAME.'attr="value" foo'.AnwXml::FIX_NS_RENAME.'attr2="value2" attr3="value3"/>';
		$this->_testXml($sXmlSource, $sExpectedPrepareXml);
	}

	function testXmlns12() {
		$sXmlSource = '<p>Result: <a id="result">click here</a></p>';
		$sExpectedPrepareXml = '<p>Result: <a id="result">click here</a></p>';
		$this->_testXml($sXmlSource, $sExpectedPrepareXml);
	}
	
	private function _testXml($sXmlSource, $sExpectedPrepareXmlValueToXml)
	{
		$oXmlValue = AnwUtils::loadXML('<doc>'.$sXmlSource.'</doc>');
		
		$sPrepareXmlValueToXml = AnwXml::prepareXmlValueToXml($sXmlSource);
		$this->assertEqual($sExpectedPrepareXmlValueToXml, $sPrepareXmlValueToXml);
		
		$sPrepareXmlValueFromXml = AnwXml::prepareXmlValueFromXml($sPrepareXmlValueToXml);
		$this->assertEqual($sPrepareXmlValueFromXml, $sXmlSource);
		
		$sXmlSourceBack = AnwUtils::xmlDumpNodeChilds($oXmlValue);
		
		$this->assertEqual($sXmlSourceBack, $sXmlSource);
	}
	
}

?>