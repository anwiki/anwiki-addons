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
 * Tests for Anwiki output rendering.
 * @package Anwiki
 * @version $Id: test_diffs.php 135 2009-02-08 16:57:41Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */

class AnwOutputTestCase extends UnitTestCase {
	function __construct() {
		$this->UnitTestCase('AnwOutput test');
	}
	
	function setUp() {
	
	}
	
	function tearDown() {
	}
	
	function testOutputTranslatableAttributes() {
		$sEditedContent = '<a class="toplink"><attr name="href">http://www.wikipedia.com</attr>Go to wikipedia</a>';
		$sExpectedOutput = '<a class="toplink" href="http://www.wikipedia.com">Go to wikipedia</a>';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<a><attr name="title">Wikipedia</attr>Go to wikipedia</a>';
		$sExpectedOutput = '<a title="Wikipedia">Go to wikipedia</a>';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<a>Go to wikipedia</a>';
		$sExpectedOutput = '<a>Go to wikipedia</a>';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<a><attr name="title"></attr>Go to wikipedia</a>';
		$sExpectedOutput = '<a title="">Go to wikipedia</a>';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<img src="foo"><attr name="alt">logo</attr></img>';
		$sExpectedOutput = '<img src="foo" alt="logo" />';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<img src="foo"> <attr name="alt">logo</attr> </img>';
		$sExpectedOutput = '<img src="foo" alt="logo" />';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<img src="foo"> 
								<attr name="alt">logo</attr> 
								<attr name="title">test</attr> 
							</img>';
		$sExpectedOutput = '<img src="foo" alt="logo" title="test" />';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<foo><attr name="bar">keyword1<fix>, </fix>keyword2<fix>, </fix>keyword3</attr></foo>';
		$sExpectedOutput = '<foo bar="keyword1, keyword2, keyword3"></foo>';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<foo><attr name="bar">keyword1<fix>, </fix>[untr]keyword2[/untr]<fix>, </fix>keyword3</attr></foo>';
		$sExpectedOutput = '<foo bar="keyword1, keyword2, keyword3"></foo>';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
	}
	
	function testOutputEndingTagsStandard() {
		$sEditedContent = '<script src="foo"/>';
		$sExpectedOutput = '<script src="foo"></script>';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<script src="foo" />';
		$sExpectedOutput = '<script src="foo" ></script>';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<textarea/>';
		$sExpectedOutput = '<textarea></textarea>';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<iframe src="bar"/>';
		$sExpectedOutput = '<iframe src="bar"></iframe>';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
	}
	
	function testOutputEndingTagsMinimized() {
		$sEditedContent = '<img src="foo"/>';
		$sExpectedOutput = '<img src="foo" />';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<img src="foo"></img>';
		$sExpectedOutput = '<img src="foo" />';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<img src="foo"> </img>';
		$sExpectedOutput = '<img src="foo" />';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<input type="submit"> 
							</input>';
		$sExpectedOutput = '<input type="submit" />';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<br></br>';
		$sExpectedOutput = '<br />';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<img foo:src="foo"/>';
		$sExpectedOutput = '<img foo:src="foo" />';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<bar:tag foo:src="foo" attr="value"/>';
		$sExpectedOutput = '<bar:tag foo:src="foo" attr="value"></bar:tag>';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<p><img /></p>';
		$sExpectedOutput = '<p><img /></p>';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<td>foo<br />bar</td>';
		$sExpectedOutput = '<td>foo<br />bar</td>';
		$this->_testOutput($sEditedContent, $sExpectedOutput);
	}
	
	function testDatatypeXhtmlCloseMinimizedEndTags() {
		$sEditedContent = '<br>';
		$sExpectedOutput = '<br />';
		$this->_testEditedContent($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<br > foo bar <hr >';
		$sExpectedOutput = '<br /> foo bar <hr />';
		$this->_testEditedContent($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<br> foo bar <hr>';
		$sExpectedOutput = '<br /> foo bar <hr />';
		$this->_testEditedContent($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<br><aa>';
		$sExpectedOutput = '<br /><aa>';
		$this->_testEditedContent($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<img src="foo">';
		$sExpectedOutput = '<img src="foo" />';
		$this->_testEditedContent($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<img foo:src="foo">';
		$sExpectedOutput = '<img foo:src="foo" />';
		$this->_testEditedContent($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<bar:tag foo:src="foo" attr="value">dummy</bar:tag>';
		$sExpectedOutput = '<bar:tag foo:src="foo" attr="value">dummy</bar:tag>';
		$this->_testEditedContent($sEditedContent, $sExpectedOutput);
		
		// TODO update AnwDatatype_xml.closeMinimizedEndTags() for passing this test (problem with slashes).
//		$sEditedContent = '<img src="http://www.anwiki.com/logo.png">';
//		$sExpectedOutput = '<img src="http://www.anwiki.com/logo.png" />';
//		$this->_testEditedContent($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<img src="foo"> foo bar <hr style="border:2px solid #000">';
		$sExpectedOutput = '<img src="foo" /> foo bar <hr style="border:2px solid #000" />';
		$this->_testEditedContent($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<img src="foo"><aa>';
		$sExpectedOutput = '<img src="foo" /><aa>';
		$this->_testEditedContent($sEditedContent, $sExpectedOutput);
		
		// TODO update regexs, this test doesn't pass for now
//		$sEditedContent = '<img src="foo">dummy</img>';
//		$sExpectedOutput = '<img src="foo" />';
//		$this->_testEditedContent($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<img src="foo"><attr name="alt">logo</attr></img>';
		$sExpectedOutput = '<img src="foo"><attr name="alt">logo</attr></img>';
		$this->_testEditedContent($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<img src="foo"> foo bar <hr style="border:2px solid #000"> 
							<img src="foo"> foo bar <hr style="border:2px solid #000">';
		$sExpectedOutput = '<img src="foo" /> foo bar <hr style="border:2px solid #000" /> 
							<img src="foo" /> foo bar <hr style="border:2px solid #000" />';
		$this->_testEditedContent($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<p><img /></p>';
		$sExpectedOutput = '<p><img /></p>';
		$this->_testEditedContent($sEditedContent, $sExpectedOutput);
		
		$sEditedContent = '<td>foo<br />bar</td>';
		$sExpectedOutput = '<td>foo<br />bar</td>';
		$this->_testEditedContent($sEditedContent, $sExpectedOutput);
	}
	
	private function _testEditedContent($sEditedContent, $sExpectedOutput)
	{
		AnwDebug::log("####################### NEW TEST #################".$this->_label);
		
		$sTransformedEditedContent = AnwDatatype_xhtml::__test_closeMinimizedEndTags($sEditedContent);
		$this->assertEqual($sTransformedEditedContent, $sExpectedOutput);
		
		// run again to make sure that output is no longer transformed
		$sTransformedEditedContent = AnwDatatype_xhtml::__test_closeMinimizedEndTags($sTransformedEditedContent);
		$this->assertEqual($sTransformedEditedContent, $sExpectedOutput);
	}
		
	private function _testOutput($sEditedContent, $sExpectedOutput)
	{
		AnwDebug::log("####################### NEW TEST #################".$this->_label);
		
		$sEditedContent = AnwDatatype_xhtml::__test_closeMinimizedEndTags($sEditedContent);
		
		$oOutput = new AnwOutputHtml(new AnwPageByName("unittest/en"));
		$oOutput->setBody($sEditedContent);
		$sOutputHtml = $oOutput->runBody();
		$this->assertEqual($sOutputHtml, $sExpectedOutput);
		
		// run again to make sure that output is no longer transformed
		$oOutput = new AnwOutputHtml(new AnwPageByName("unittest/en"));
		$oOutput->setBody($sExpectedOutput);
		$sOutputHtml = $oOutput->runBody();
		$this->assertEqual($sOutputHtml, $sExpectedOutput);
	}
	
}

?>