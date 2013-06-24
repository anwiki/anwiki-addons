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
 * Tests for Anwiki XML Diff engine.
 * @package Anwiki
 * @version $Id: test_diffs.php 287 2010-09-09 22:19:30Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */

class AnwDiffsTestCase extends UnitTestCase {
	function __construct() {
		$this->UnitTestCase('AnwDiffs test');
	}
	
	function setUp() {
	
	}
	
	function tearDown() {
	}
	
	function testEdit1() {
		$sOldContent = "AAA<br/>BBB<br/>CCC<br/>DDD";
		$sNewContent = "AAA<br/>BBB EDITED!<br/>CCC<br/>DDD";
		
		$sSimilarContent = "AAA_FR<br/>BBB_FR<br/>CCC_FR<br/>DDD_FR";
		$sExpectedResult = "AAA_FR<br/>".AnwUtils::FLAG_UNTRANSLATED_OPEN."BBB EDITED!".AnwUtils::FLAG_UNTRANSLATED_CLOSE."<br/>CCC_FR<br/>DDD_FR";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testEdit2() {
		$sOldContent = "<div><p>AAA<br/>BBB<br/>CCC<br/>DDD</p></div>";
		$sNewContent = "<div><p>AAA<br/>BBB EDITED!<br/>CCC<br/>DDD</p></div>";
		
		$sSimilarContent = "<div><p>AAA_FR<br/>BBB_FR<br/>CCC_FR<br/>DDD_FR</p></div>";
		$sExpectedResult = "<div><p>AAA_FR<br/>".AnwUtils::FLAG_UNTRANSLATED_OPEN."BBB EDITED!".AnwUtils::FLAG_UNTRANSLATED_CLOSE."<br/>CCC_FR<br/>DDD_FR</p></div>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testEdit3() {
		$sOldContent = "<div><p><span>AAA<br/>BBB IS HERE<br/>CCC</span></p></div>";
		$sNewContent = "<div><p><span>AAA<br/>BBB<br/>IS HERE<br/>CCC</span></p></div>";
		
		$sSimilarContent = "<div><p><span>AAA_FR<br/>BBB_FR EST ICI<br/>CCC_FR</span></p></div>";
		$sExpectedResult = "<div><p><span>AAA_FR<br/>".AnwUtils::FLAG_UNTRANSLATED_OPEN."BBB".AnwUtils::FLAG_UNTRANSLATED_CLOSE."<br/>".AnwUtils::FLAG_UNTRANSLATED_OPEN."IS HERE".AnwUtils::FLAG_UNTRANSLATED_CLOSE."<br/>CCC_FR</span></p></div>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testEditAttributes1() {
		$sOldContent = "<div attribute=\"AAA\">hello</div>";
		$sNewContent = "<div attribute=\"BBB\">hello</div>";
		
		$sSimilarContent = "<div attribute=\"AAA\">bonjour</div>";
		$sExpectedResult = "<div attribute=\"BBB\">bonjour</div>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testEditAttributes2() {
		$sOldContent = "<div aa=\"1\" attribute=\"AAA\" zz=\"2\">hello</div>";
		$sNewContent = "<div zz=\"1\" aa=\"2\">hello</div>";
		
		$sSimilarContent = "<div aa=\"1\" attribute=\"AAA\" zz=\"2\">bonjour</div>";
		$sExpectedResult = "<div zz=\"1\" aa=\"2\">bonjour</div>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testEditAttributes3() {
		$sOldContent = "<div>hello</div>";
		$sNewContent = "<div attribute=\"AAA\">hello</div>";
		
		$sSimilarContent = "<div>bonjour</div>";
		$sExpectedResult = "<div attribute=\"AAA\">bonjour</div>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testEditPhp1() {
		$sOldContent = "<div><p><span>AAA<br/><?php print \"test\";?><br/>CCC</span></p></div>";
		$sNewContent = "<div><p><span>AAA<br/>BBB<br/><?php print \"test\";?><br/>CCC</span></p></div>";
		
		$sSimilarContent = "<div><p><span>AAA_FR<br/><?php print \"test\";?><br/>CCC_FR</span></p></div>";
		$sExpectedResult = "<div><p><span>AAA_FR<br/>".AnwUtils::FLAG_UNTRANSLATED_OPEN."BBB".AnwUtils::FLAG_UNTRANSLATED_CLOSE."<br/><?php print \"test\";?><br/>CCC_FR</span></p></div>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testEditPhp2() {
		$sOldContent = "<?php print \"test\";?>";
		$sNewContent = "<?php print \"EDITED\";?><br/>CCC";
		
		$sSimilarContent = "<?php print \"test\";?>";
		$sExpectedResult = "<?php print \"EDITED\";?><br/>".AnwUtils::FLAG_UNTRANSLATED_OPEN."CCC".AnwUtils::FLAG_UNTRANSLATED_CLOSE;
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testEditPhp3() {
		$sOldContent = "<p>Some text is here</p>";
		$sNewContent = "<p>Some text is here</p><?php print \"blahblah\";?>";
		
		$sSimilarContent = "<p>Du texte ici</p>";
		$sExpectedResult = "<p>Du texte ici</p><?php print \"blahblah\";?>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
		
	function testEditComment1() {
		$sOldContent = "<!-- some comment here -->";
		$sNewContent = "<!-- another comment here -->";
		
		$sSimilarContent = "<!-- some comment here -->";
		$sExpectedResult = "<!-- another comment here -->";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testEditComment2() {
		$sOldContent = "hello";
		$sNewContent = "<!-- a comment here -->hello";
		
		$sSimilarContent = "bonjour";
		$sExpectedResult = "<!-- a comment here -->bonjour";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testEditTextLayout1() {
		$sOldContent = "<p>LINE 1<br/>
<fix/>LINE 2</p>";
		$sNewContent = "<p>LINE 1
<fix/>LINE 2</p>";
		
		$sSimilarContent = "<p>LIGNE 1<br/>
<fix/>LIGNE 2</p>";
		$sExpectedResult = "<p>LIGNE 1
<fix/>LIGNE 2</p>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testEditTextLayout2() {
		$sOldContent = "<p>LINE 1
<fix/>LINE 2</p>";
		$sNewContent = "<p>LINE 1<br/>
<fix/>LINE 2</p>";
		
		$sSimilarContent = "<p>LIGNE 1
<fix/>LIGNE 2</p>";
		$sExpectedResult = "<p>LIGNE 1<br/>
<fix/>LIGNE 2</p>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testEditTextLayout3() {
		$sOldContent = "<p>LINE 1
<fix/>LINE 2</p>";
		$sNewContent = "<p>LINE 1


<fix/>LINE 2</p>";
		
		$sSimilarContent = "<p>LIGNE 1
<fix/>LIGNE 2</p>";
		$sExpectedResult = "<p>LIGNE 1


<fix/>LIGNE 2</p>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testEditTextLayout4() {
		$sOldContent = "<p>LINE 2</p>";
		$sNewContent = "<p> LINE 2 </p>";
		
		$sSimilarContent = "<p>LIGNE 2</p>";
		$sExpectedResult = "<p>LIGNE 2</p>"; // spaces differences are allowed for translations...
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testEditTextLayout41() {
		$sOldContent = "<p>LINE 2</p>";
		$sNewContent = "<p>
LINE 2
</p>";
		
		$sSimilarContent = "<p>LIGNE 2</p>";
		$sExpectedResult = "<p>
LIGNE 2
</p>"; // lines differences are NOT allowed for translations...
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testEditTextLayout42() {
		$sOldContent = "<p>LINE 2</p>";
		$sNewContent = "<p>	LINE 2	</p>";
		
		$sSimilarContent = "<p>LIGNE 2</p>";
		$sExpectedResult = "<p>	LIGNE 2	</p>"; // tabs differences are NOT allowed for translations...
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testEditTextLayout5() {
		$sOldContent = "
<p>LINE 1
<br/><fix/>LINE 2
<br/><fix/>LINE 3</p>";
		$sNewContent = "
<p>LINE 1
<fix/>LINE 2
<fix/>LINE 3</p>";
		
		$sSimilarContent = "
<p>LIGNE 1
<br/><fix/>LIGNE 2
<br/><fix/>LIGNE 3</p>";
		$sExpectedResult = "
<p>LIGNE 1
<fix/>LIGNE 2
<fix/>LIGNE 3</p>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testEditTextLayout6() {
		$sOldContent = "
<p>LINE 1<br/>
<fix/>LINE 2<br/>
<fix/>LINE 3</p>";
		$sNewContent = "
<p>LINE 1
<fix/>LINE 2
<fix/>LINE 3</p>";
		
		$sSimilarContent = "
<p>LIGNE 1<br/>
<fix/>LIGNE 2<br/>
<fix/>LIGNE 3</p>";
		$sExpectedResult = "
<p>LIGNE 1
<fix/>LIGNE 2
<fix/>LIGNE 3</p>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsAdded1() {
		$sOldContent = "<p>un premier paragraphe</p>";
		$sNewContent = "<p>un premier paragraphe</p><b>BOLD ADDED</b>";
		
		$sSimilarContent = "<p>a first paragraph</p>";
		$sExpectedResult = "<p>a first paragraph</p><b>".AnwUtils::FLAG_UNTRANSLATED_OPEN."BOLD ADDED".AnwUtils::FLAG_UNTRANSLATED_CLOSE."</b>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsAdded2() {
		$sOldContent = "<p>un premier paragraphe</p>";
		$sNewContent = "<b>BOLD ADDED</b><p>un premier paragraphe</p>";
		
		$sSimilarContent = "<p>a first paragraph</p>";
		$sExpectedResult = "<b>".AnwUtils::FLAG_UNTRANSLATED_OPEN."BOLD ADDED".AnwUtils::FLAG_UNTRANSLATED_CLOSE."</b><p>a first paragraph</p>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsAdded3() {
		$sOldContent = "<p>un premier paragraphe</p>";
		$sNewContent = "<p>un premier paragraphe<b>BOLD ADDED</b></p>";
		
		$sSimilarContent = "<p>a first paragraph</p>";
		$sExpectedResult = "<p>a first paragraph<b>".AnwUtils::FLAG_UNTRANSLATED_OPEN."BOLD ADDED".AnwUtils::FLAG_UNTRANSLATED_CLOSE."</b></p>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsAdded4() {
		$sOldContent = "<p>un premier paragraphe</p>";
		$sNewContent = "<p><b>BOLD ADDED</b>un premier paragraphe</p>";
		
		$sSimilarContent = "<p>a first paragraph</p>";
		$sExpectedResult = "<p><b>".AnwUtils::FLAG_UNTRANSLATED_OPEN."BOLD ADDED".AnwUtils::FLAG_UNTRANSLATED_CLOSE."</b>a first paragraph</p>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsAdded5() {
		$sOldContent = "<p>un premier paragraphe</p>\n\n";
		$sNewContent = "\n\n<p>un premier paragraphe</p>\n<p>ADDED</p>";
		
		$sSimilarContent = "<p>a first paragraph</p>\n\n";
		$sExpectedResult = "\n\n<p>a first paragraph</p>\n<p>".AnwUtils::FLAG_UNTRANSLATED_OPEN."ADDED".AnwUtils::FLAG_UNTRANSLATED_CLOSE."</p>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsAdded6() {
		$sOldContent = "<div><p>un premier paragraphe</p></div>";
		$sNewContent = "<div><p>un premier paragraphe</p><p>ADDED</p></div>";
		
		$sSimilarContent = "<div><p>a first paragraph</p></div>";
		$sExpectedResult = "<div><p>a first paragraph</p><p>".AnwUtils::FLAG_UNTRANSLATED_OPEN."ADDED".AnwUtils::FLAG_UNTRANSLATED_CLOSE."</p></div>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsAdded7() {
		$sOldContent = "<values><anwv><p>français</p></anwv></values>";
		$sNewContent = "<values><anwv><p>français</p>\n<p>ADDED</p></anwv></values>";
		
		$sSimilarContent = "<values><anwv><p>english</p></anwv></values>";
		$sExpectedResult = "<values><anwv><p>english</p>\n<p>".AnwUtils::FLAG_UNTRANSLATED_OPEN."ADDED".AnwUtils::FLAG_UNTRANSLATED_CLOSE."</p></anwv></values>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsEdited1() {
		$sOldContent = "<p>un premier paragraphe</p>";
		$sNewContent = "<p>un premier paragraphe modifie</p>";
		
		$sSimilarContent = "<p>a first paragraph</p>";
		$sExpectedResult = "<p>".AnwUtils::FLAG_UNTRANSLATED_OPEN."un premier paragraphe modifie".AnwUtils::FLAG_UNTRANSLATED_CLOSE."</p>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsEdited2() {
		$sOldContent = "<div><p>un premier paragraphe</p></div>";
		$sNewContent = "<div><p>un premier paragraphe modifie</p></div>";
		
		$sSimilarContent = "<div><p>a first paragraph</p></div>";
		$sExpectedResult = "<div><p>".AnwUtils::FLAG_UNTRANSLATED_OPEN."un premier paragraphe modifie".AnwUtils::FLAG_UNTRANSLATED_CLOSE."</p></div>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsEdited3() {
		$sOldContent = "<div><p>welcome to <fix>ANWIKI 0.5</fix> !</p></div>";
		$sNewContent = "<div><p>welcome to the famous <fix>ANWIKI 0.6</fix> !</p></div>";
		
		$sSimilarContent = "<div><p>bienvenue sur <fix>ANWIKI 0.5</fix> !</p></div>";
		$sExpectedResult = "<div><p>".AnwUtils::FLAG_UNTRANSLATED_OPEN."welcome to the famous".AnwUtils::FLAG_UNTRANSLATED_CLOSE." <fix>ANWIKI 0.6</fix> !</p></div>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsKept1() {
		$sOldContent = "<p>un premier paragraphe</p>";
		$sNewContent = "<p>un premier paragraphe</p>";
		
		$sSimilarContent = "<p>a first paragraph</p>";
		$sExpectedResult = "<p>a first paragraph</p>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsKept2() {
		$sOldContent = "<div><p>un premier paragraphe</p></div>";
		$sNewContent = "<div><p>un premier paragraphe</p></div>";
		
		$sSimilarContent = "<div><p>a first paragraph</p></div>";
		$sExpectedResult = "<div><p>a first paragraph</p></div>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsKept3() {
		$sOldContent = "<div><p>AA</p><p>BB</p><p>CC</p></div><div>ZZ</div>";
		$sNewContent = "<div><p>AA</p><p>BB</p><p>CC</p></div><div>ZZ</div>";
		
		$sSimilarContent = "<div><p>AA_</p><p>BB_</p><p>CC_</p></div><div>ZZ_</div>";
		$sExpectedResult = "<div><p>AA_</p><p>BB_</p><p>CC_</p></div><div>ZZ_</div>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsAttr1() {
		$sOldContent = "<div class=\"blah\"><p id=\"intro\">aa<input type=\"submit\" value=\"go\" class=\"button\"/>bb</p></div>";
		$sNewContent = "<div class=\"container\" id=\"container2\"><p><input name=\"test\" type=\"hidden\"/>bb</p></div>";
		
		$sSimilarContent = "<div class=\"blah\"><p id=\"intro\">cc<input type=\"submit\" value=\"go\" class=\"button\"/>dd</p></div>";
		$sExpectedResult = "<div class=\"container\" id=\"container2\"><p><input name=\"test\" type=\"hidden\"/>dd</p></div>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsMoved1() {
		$sOldContent = "<p>paragraphe</p><i>italique</i>";
		$sNewContent = "<i>italique</i><p>paragraphe</p>";
		
		$sSimilarContent = "<p>paragraph</p><i>italic</i>";
		$sExpectedResult = "<i>italic</i><p>paragraph</p>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsMoved2() {
		$sOldContent = "<div><p>paragraphe</p><i>italique</i></div>";
		$sNewContent = "<div><i>italique</i><p>paragraphe</p></div>";
		
		$sSimilarContent = "<div><p>paragraph</p><i>italic</i></div>";
		$sExpectedResult = "<div><i>italic</i><p>paragraph</p></div>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsMoved3() {
		$sOldContent = "<div><p>paragraphe</p><i>italique</i></div><div><p>un autre paragraphe</p><i>un autre italique</i></div>";
		$sNewContent = "<div><i>italique</i><p>paragraphe</p></div><div><p>un autre paragraphe</p><i>un autre italique</i></div>";
		
		$sSimilarContent = "<div><p>paragraph</p><i>italic</i></div><div><p>another paragraph</p><i>another italic</i></div>";
		$sExpectedResult = "<div><i>italic</i><p>paragraph</p></div><div><p>another paragraph</p><i>another italic</i></div>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsMoved4() {
		$sOldContent = "<div><p>paragraphe</p><i>italique</i></div><div><p>un autre paragraphe</p><i>un autre italique</i></div>";
		$sNewContent = "<div><p>paragraphe</p></div><div><p>un autre paragraphe</p><i>un autre italique</i></div><i>italique</i>";
		
		$sSimilarContent = "<div><p>paragraph</p><i>italic</i></div><div><p>another paragraph</p><i>another italic</i></div>";
		$sExpectedResult = "<div><p>paragraph</p></div><div><p>another paragraph</p><i>another italic</i></div><i>italic</i>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsMoved5() {
		$sOldContent = "<div><p>paragraphe</p><i>italique</i></div><div><p>un autre paragraphe</p><i>un autre italique</i></div>";
		$sNewContent = "<i>italique</i><div><p>paragraphe</p></div><div><p>un autre paragraphe</p><i>un autre italique</i></div>";
		
		$sSimilarContent = "<div><p>paragraph</p><i>italic</i></div><div><p>another paragraph</p><i>another italic</i></div>";
		$sExpectedResult = "<i>italic</i><div><p>paragraph</p></div><div><p>another paragraph</p><i>another italic</i></div>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsMoved6() {
		$sOldContent = "<p>FR1</p><p>FR2</p><p>FR3</p>";
		$sNewContent = "<p>FR2</p><p>FR3</p><p>FR1</p>";
		
		$sSimilarContent = "<p>EN1</p><p>EN2</p><p>EN3</p>";
		$sExpectedResult = "<p>EN2</p><p>EN3</p><p>EN1</p>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsMoved7() {
		$sOldContent = "<div><p>P1FR</p></div><div><i>I1FR</i></div>";
		$sNewContent = "<div/><div><i>I1FR</i><p>P1FR</p></div>";
		
		$sSimilarContent = "<div><p>P1EN</p></div><div><i>I1EN</i></div>";
		$sExpectedResult = "<div/><div><i>I1EN</i><p>P1EN</p></div>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsMoved8() {
		$sOldContent = "<div><p>P1FR</p></div><div><i>I1FR</i></div>";
		$sNewContent = "<div><p>P1FR</p><i>I1FR</i></div><div/>";
		
		$sSimilarContent = "<div><p>P1EN</p></div><div><i>I1EN</i></div>";
		$sExpectedResult = "<div><p>P1EN</p><i>I1EN</i></div><div/>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsMoved9() {
		$sOldContent = "<div><p>P1FR</p></div><div><i>I1FR</i></div>";
		$sNewContent = "<div/><i>I1FR</i><div><p>P1FR</p></div>";
		
		$sSimilarContent = "<div><p>P1EN</p></div><div><i>I1EN</i></div>";
		$sExpectedResult = "<div/><i>I1EN</i><div><p>P1EN</p></div>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsMoved10() {
		$sOldContent = "<div><p>P1FR</p></div><div><p>P2FR</p></div>";
		$sNewContent = "<p>ADDED</p><div/><p>P2FR</p><div><p>P1FR</p></div>";
		
		$sSimilarContent = "<div><p>P1EN</p></div><div><p>P2EN</p></div>";
		$sExpectedResult = "<p>".AnwUtils::FLAG_UNTRANSLATED_OPEN."ADDED".AnwUtils::FLAG_UNTRANSLATED_CLOSE."</p><div/><p>P2EN</p><div><p>P1EN</p></div>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsMoved11() {
		$sOldContent = "<p>FR2</p>";
		$sNewContent = "<span><p>FR2</p></span>";
		
		$sSimilarContent = "<p>EN2</p>";
		$sExpectedResult = "<span><p>EN2</p></span>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	
	function testApplyDiffsMoved12() {
		$sOldContent = "<div><p>paragraphe</p><i>italique</i></div><div><p>un autre paragraphe</p><i>un autre italique</i></div>";
		$sNewContent = "<div><i>un autre italique</i><p>paragraphe</p></div><div><p>un autre paragraphe</p><i>italique</i></div>";
		
		$sSimilarContent = "<div><p>paragraph</p><i>italic</i></div><div><p>another paragraph</p><i>another italic</i></div>";
		$sExpectedResult = "<div><i>another italic</i><p>paragraph</p></div><div><p>another paragraph</p><i>italic</i></div>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsMoved13() {
		$sOldContent = "<p>un premier paragraphe</p>";
		$sNewContent = "<p>un premier paragraphe</p><b>ADDED</b>";
		
		$sSimilarContent = "<p>a first paragraph</p>";
		$sExpectedResult = "<p>a first paragraph</p><b>".AnwUtils::FLAG_UNTRANSLATED_OPEN."ADDED".AnwUtils::FLAG_UNTRANSLATED_CLOSE."</b>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsMoved14() {
		$sOldContent = "<p>français</p>";
		$sNewContent = "<span>français</span>";
		
		$sSimilarContent = "<p>english</p>";
		$sExpectedResult = "<span>english</span>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsMoved15() {
		$sOldContent = "<p>P1</p><p>P2</p><p>P3</p>";
		$sNewContent = "<p>P1</p><p>ADDED</p><p>P3</p><p>P2</p>";
		
		$sSimilarContent = "<p>E1</p><p>E2</p><p>E3</p>";
		$sExpectedResult = "<p>E1</p><p>".AnwUtils::FLAG_UNTRANSLATED_OPEN."ADDED".AnwUtils::FLAG_UNTRANSLATED_CLOSE."</p><p>E3</p><p>E2</p>";
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsMoved16() {
		$sOldContent = <<<EOF
<p>P1</p>
<p>P2</p>
<p>P3</p>
EOF;
		$sNewContent = <<<EOF
<p>P1</p>
<p>ADDED</p>
<p>P3</p>
<p>P2</p>
EOF;
		
		$sSimilarContent = <<<EOF
<p>E1</p>
<p>E2</p>
<p>E3</p>
EOF;
		$sFlagOpen = AnwUtils::FLAG_UNTRANSLATED_OPEN;
		$sFlagClose = AnwUtils::FLAG_UNTRANSLATED_CLOSE;
		$sExpectedResult = <<<EOF
<p>E1</p>
<p>{$sFlagOpen}ADDED{$sFlagClose}</p>
<p>E3</p>
<p>E2</p>
EOF;
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	//tests special case on edited node
	function testApplyDiffsMoved17() {
		$sOldContent = <<<EOF
<p>P1</p>
<p>P2</p>
<p>P3</p>
EOF;
		$sNewContent = <<<EOF
<p>P1</p>
<p>P3</p>
<p>DELETED</p>
EOF;
		
		$sSimilarContent = <<<EOF
<p>E1</p>
<p>E2</p>
<p>E3</p>
EOF;
		$sFlagOpen = AnwUtils::FLAG_UNTRANSLATED_OPEN;
		$sFlagClose = AnwUtils::FLAG_UNTRANSLATED_CLOSE;
		$sExpectedResult = <<<EOF
<p>E1</p>
<p>E3</p>
<p>{$sFlagOpen}DELETED{$sFlagClose}</p>
EOF;
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsMoved18() {
		$sOldContent = <<<EOF
<p>EN1</p>
<p>EN2</p>

<p>EN3</p>
<p>EN4</p>
EOF;
		$sNewContent = <<<EOF
<h1>titre</h1>

<div style="border:1px solid black">
<p>EN1</p>
<p>EN2</p>
</div>

<div style="border:1px solid red">
<p>EN3</p>
<p>EN4</p>
</div>
EOF;
		
		$sSimilarContent = <<<EOF
<p>FR1</p>
<p>FR2</p>

<p>FR3</p>
<p>FR4</p>
EOF;
		$sFlagOpen = AnwUtils::FLAG_UNTRANSLATED_OPEN;
		$sFlagClose = AnwUtils::FLAG_UNTRANSLATED_CLOSE;
		$sExpectedResult = <<<EOF
<h1>{$sFlagOpen}titre{$sFlagClose}</h1>

<div style="border:1px solid black">
<p>FR1</p>
<p>FR2</p>
</div>

<div style="border:1px solid red">
<p>FR3</p>
<p>FR4</p>
</div>
EOF;
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	
	function testApplyDiffsMoved19() {
		$sOldContent = <<<EOF
<h1>TITLE</h1>

<div style="border:1px solid black">
<p>CC</p>
</div>

<p>DD</p>
EOF;
		$sNewContent = <<<EOF
<h1>TITLE</h1>

<p>CC</p>

<div style="border:1px solid red">
<p>DD</p>
</div>
EOF;
		
		$sSimilarContent = <<<EOF
<h1>TITLE_FR</h1>

<div style="border:1px solid black">
<p>CC_FR</p>
</div>

<p>DD_FR</p>
EOF;
		$sExpectedResult = <<<EOF
<h1>TITLE_FR</h1>

<p>CC_FR</p>

<div style="border:1px solid red">
<p>DD_FR</p>
</div>
EOF;
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsMoved20() {
		$sOldContent = '
<p>English news</p>
<anwloop>
  <p>{$news.title}</p>
  <p>Read more...</p>
</anwloop>
';
		$sNewContent = '
<p>English news</p>
<anwloop>
  <p><fix>{$news.title}</fix></p>
  <p>Read more...</p>
</anwloop>
';
		
		$sSimilarContent = '
<p>News en français</p>
<anwloop>
  <p>{$news.OldTitle}</p>
  <p>Lire la suite...</p>
</anwloop>
';
		$sExpectedResult = '
<p>News en français</p>
<anwloop>
  <p><fix>{$news.title}</fix></p>
  <p>Lire la suite...</p>
</anwloop>
';
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsMoved21() {
		$sOldContent = '
<div>(HEY!) some text here.. <?php echo "blah";?></div>
';
		$sNewContent = '
<div>(<?php print "HUHU"; ?>) some text here.. <?php echo "blah";?></div>
';
		
		$sSimilarContent = '
<div>(HEHO!) du texte ici... <?php echo "blah";?></div>
';
		$sExpectedResult = '
<div>[untr]([/untr]<?php print "HUHU"; ?>[untr]) some text here..[/untr] <?php echo "blah";?></div>
';
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	//AnwDiffAdded with submoved node
	function testApplyDiffsMoved22() {
		$sFlagOpen = AnwUtils::FLAG_UNTRANSLATED_OPEN;
		$sFlagClose = AnwUtils::FLAG_UNTRANSLATED_CLOSE;
		
		$sOldContent = <<<EOF
<ul>
<li>one</li>
<li>two</li>
<li>three</li>
</ul>
EOF;

		$sNewContent = <<<EOF
<p>there are <b>two</b> bugs here</p> 
<ul>
<li>one</li>
<li>three</li>
</ul>
EOF;
		
		$sSimilarContent = <<<EOF
<ul>
<li>un</li>
<li>deux</li>
<li>trois</li>
</ul>
EOF;

		$sExpectedResult = <<<EOF
<p>{$sFlagOpen}there are{$sFlagClose} <b>deux</b> {$sFlagOpen}bugs here{$sFlagClose}</p> 
<ul>
<li>un</li>
<li>trois</li>
</ul>
EOF;
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	//AnwDiffEdited with submoved node
	function testApplyDiffsMoved23() {
		$sFlagOpen = AnwUtils::FLAG_UNTRANSLATED_OPEN;
		$sFlagClose = AnwUtils::FLAG_UNTRANSLATED_CLOSE;
		
		$sOldContent = <<<EOF
<ul>
<li>one</li>
<li>two</li>
<li>three</li>
</ul>

<ul>
<li>dogs</li>
<li>cats</li>
</ul>

<div>
<ul>
<li>hello</li>
</ul>
</div>
EOF;

		$sNewContent = <<<EOF
<p>there are <span><b>two</b> <i>dogs</i></span> here</p>
<div>
<ul>
<li>one</li>
<li>this was edited</li>
<li>three</li>
</ul>
</div>

<ul>
<li>cats</li>
</ul>
EOF;
		
		$sSimilarContent = <<<EOF
<ul>
<li>un</li>
<li>deux</li>
<li>trois</li>
</ul>

<ul>
<li>chiens</li>
<li>chats</li>
</ul>

<div>
<ul>
<li>bonjour</li>
</ul>
</div>
EOF;

		$sExpectedResult = <<<EOF
<p>{$sFlagOpen}there are{$sFlagClose} <span><b>deux</b> <i>chiens</i></span> {$sFlagOpen}here{$sFlagClose}</p>
<div>
<ul>
<li>un</li>
<li>{$sFlagOpen}this was edited{$sFlagClose}</li>
<li>trois</li>
</ul>
</div>

<ul>
<li>chats</li>
</ul>
EOF;
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsBug1() {
		$sFlagOpen = AnwUtils::FLAG_UNTRANSLATED_OPEN;
		$sFlagClose = AnwUtils::FLAG_UNTRANSLATED_CLOSE;
		
		$sOldContent = <<<EOF
<p>a.</p>

<ul>
<anwloop sort="date" order="desc" match="en/news/*" limit="3" item="news" morelangs="en" cachetime="600" cacheblock="no" class="news">



</anwloop>
</ul>
EOF;

		$sNewContent = <<<EOF
<p>a.</p>


<ul>
<anwloop sort="date" order="desc" match="en/news/*" limit="3" item="news" morelangs="en" cachetime="600" cacheblock="no" class="news">

</anwloop>
</ul>
EOF;
		
		$sSimilarContent = <<<EOF
<p>[untr]a.[/untr]</p>

<ul>
<anwloop sort="date" order="desc" match="en/news/*" limit="3" item="news" morelangs="en" cachetime="600" cacheblock="no" class="news">



</anwloop>
</ul>
EOF;

		$sExpectedResult = <<<EOF
<p>[untr]a.[/untr]</p>


<ul>
<anwloop sort="date" order="desc" match="en/news/*" limit="3" item="news" morelangs="en" cachetime="600" cacheblock="no" class="news">

</anwloop>
</ul>
EOF;
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testApplyDiffsBug2() {
		$sFlagOpen = AnwUtils::FLAG_UNTRANSLATED_OPEN;
		$sFlagClose = AnwUtils::FLAG_UNTRANSLATED_CLOSE;
		
		$sOldContent = <<<EOF
<h1>Welcome to Anwiki!</h1>
<p>Thanks for choosing Anwiki.<br/>
This is the default home page, which you can edit as you want.</p>
<p>You are welcome to visit <a href="http://www.anwiki.com/">http://www.anwiki.com/</a> for getting documentation, tips & tricks, support and additional components (plugins, features, content classes and drivers).</p>

<ul>
<anwloop sort="date" order="desc" match="en/news/*" limit="3" item="news" morelangs="en" cachetime="600" cacheblock="no" class="news">
<li>{news.title} : {news.myurl}</li>



</anwloop>
</ul><p>LINE 1</p>
EOF;

		$sNewContent = <<<EOF
<h1>Welcome to Anwiki!</h1>
<p>Thanks for choosing Anwiki.<br/>
This is the default home page, which you can edit as you want.</p>
<p>You are welcome to visit <a href="http://www.anwiki.com/">http://www.anwiki.com/</a> for getting documentation, tips & tricks, support and additional components (plugins, features, content classes and drivers).</p>


<ul>
<anwloop sort="date" order="desc" match="en/news/*" limit="3" item="news" morelangs="en" cachetime="600" cacheblock="no" class="news">
<li>{news.title} : {news.myurl}</li>

</anwloop>
</ul><p>LINE 1</p>
EOF;
		
		$sSimilarContent = <<<EOF
<h1>[untr]Bienvenue![/untr]</h1>
<p>[untr]Thanks for choosing Anwiki.[/untr]<br/>
[untr]This is the default home page, which you can edit as you want.[/untr]</p>
<p>[untr]You are welcome to visit[/untr]<a href="http://www.anwiki.com/">[untr]http://www.anwiki.com/[/untr]</a>[untr]for getting documentation, tips & tricks, support and additional components (plugins, features, content classes and drivers).[/untr]</p>

<ul>
<anwloop sort="date" order="desc" match="en/news/*" limit="3" item="news" morelangs="en" cachetime="600" cacheblock="no" class="news">
<li>[untr]{news.title} : {news.myurl}[/untr]</li>



</anwloop>
</ul><p>[untr]LINE 1[/untr]</p>
EOF;

		$sExpectedResult = <<<EOF
<h1>[untr]Bienvenue![/untr]</h1>
<p>[untr]Thanks for choosing Anwiki.[/untr]<br/>
[untr]This is the default home page, which you can edit as you want.[/untr]</p>
<p>[untr]You are welcome to visit[/untr]<a href="http://www.anwiki.com/">[untr]http://www.anwiki.com/[/untr]</a>[untr]for getting documentation, tips & tricks, support and additional components (plugins, features, content classes and drivers).[/untr]</p>


<ul>
<anwloop sort="date" order="desc" match="en/news/*" limit="3" item="news" morelangs="en" cachetime="600" cacheblock="no" class="news">
<li>[untr]{news.title} : {news.myurl}[/untr]</li>

</anwloop>
</ul><p>[untr]LINE 1[/untr]</p>
EOF;
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
		
	
	function testEditStructuredContent2() {
		$sOldContent = '<mainlink>
	<anwv>
		<title><anwv>Menu 1</anwv></title>
		<url><anwv>en/menu1</anwv></url>
		<target><anwv>_self</anwv></target>
	</anwv>
</mainlink>
<subitems>
	<anwv>
		<link>
			<anwv>
				<title><anwv>Submenu1.1</anwv></title>
				<url><anwv>en/submenu11</anwv></url>
				<target><anwv>_self</anwv></target>
			</anwv>
		</link>
		<urlmatches/>
	</anwv>
	<anwv>
		<link>
			<anwv>
				<title><anwv>Submenu1.2</anwv></title>
				<url><anwv>en/submenu12</anwv></url>
				<target><anwv>_self</anwv></target>
			</anwv>
		</link>
		<urlmatches/>
	</anwv>
	<anwv>
		<link>
			<anwv>
				<title><anwv>Submenu1.3</anwv></title>
				<url><anwv>en/submenu13</anwv></url>
				<target><anwv>_self</anwv></target>
			</anwv>
		</link>
		<urlmatches/>
	</anwv>
</subitems>
<urlmatches/>';

		$sNewContent = '<mainlink>
	<anwv>
		<title><anwv>Menu 1***EDITED***</anwv></title>
		<url><anwv>en/menu1***EDITED***</anwv></url>
		<target><anwv>_self</anwv></target>
	</anwv>
</mainlink>
<subitems>
	<anwv>
		<link>
			<anwv>
				<title><anwv>Submenu1.1</anwv></title>
				<url><anwv>en/submenu11***EDITED***</anwv></url>
				<target><anwv>_self</anwv></target>
			</anwv>
		</link>
		<urlmatches/>
	</anwv>
	<anwv>
		<link>
			<anwv>
				<title><anwv>Submenu1.2</anwv></title>
				<url><anwv>en/submenu12</anwv></url>
				<target><anwv>_self</anwv></target>
			</anwv>
		</link>
		<urlmatches/>
	</anwv>
	<anwv>
		<link>
			<anwv>
				<title><anwv>Submenu1.3</anwv></title>
				<url><anwv>en/submenu13</anwv></url>
				<target><anwv>_self</anwv></target>
			</anwv>
		</link>
		<urlmatches/>
	</anwv>
</subitems>
<urlmatches/>';

		$sSimilarContent = '<mainlink>
	<anwv>
		<title><anwv>MenuFR 1</anwv></title>
		<url><anwv>en/menu1</anwv></url>
		<target><anwv>_self</anwv></target>
	</anwv>
</mainlink>
<subitems>
	<anwv>
		<link>
			<anwv>
				<title><anwv>SubmenuFR1.1</anwv></title>
				<url><anwv>en/submenu11</anwv></url>
				<target><anwv>_self</anwv></target>
			</anwv>
		</link>
		<urlmatches/>
	</anwv>
	<anwv>
		<link>
			<anwv>
				<title><anwv>SubmenuFR1.2</anwv></title>
				<url><anwv>en/submenu12</anwv></url>
				<target><anwv>_self</anwv></target>
			</anwv>
		</link>
		<urlmatches/>
	</anwv>
	<anwv>
		<link>
			<anwv>
				<title><anwv>SubmenuFR1.3</anwv></title>
				<url><anwv>en/submenu13</anwv></url>
				<target><anwv>_self</anwv></target>
			</anwv>
		</link>
		<urlmatches/>
	</anwv>
</subitems>
<urlmatches/>';

		$sExpectedResult = '<mainlink>
	<anwv>
		<title><anwv>[untr]Menu 1***EDITED***[/untr]</anwv></title>
		<url><anwv>en/menu1***EDITED***</anwv></url>
		<target><anwv>_self</anwv></target>
	</anwv>
</mainlink>
<subitems>
	<anwv>
		<link>
			<anwv>
				<title><anwv>SubmenuFR1.1</anwv></title>
				<url><anwv>en/submenu11***EDITED***</anwv></url>
				<target><anwv>_self</anwv></target>
			</anwv>
		</link>
		<urlmatches/>
	</anwv>
	<anwv>
		<link>
			<anwv>
				<title><anwv>SubmenuFR1.2</anwv></title>
				<url><anwv>en/submenu12</anwv></url>
				<target><anwv>_self</anwv></target>
			</anwv>
		</link>
		<urlmatches/>
	</anwv>
	<anwv>
		<link>
			<anwv>
				<title><anwv>SubmenuFR1.3</anwv></title>
				<url><anwv>en/submenu13</anwv></url>
				<target><anwv>_self</anwv></target>
			</anwv>
		</link>
		<urlmatches/>
	</anwv>
</subitems>
<urlmatches/>';

		AnwContentClasses::getContentClass("menu");
		//TODO?
		$sOldContent = self::cleanXml($sOldContent);
		$sNewContent = self::cleanXml($sNewContent);
		$sSimilarContent = self::cleanXml($sSimilarContent);
		$sExpectedResult = self::cleanXml($sExpectedResult);
		// TODO this test doesn't pass anymore!
		//$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult, "menu", AnwIContentClassPageDefault_menu::FIELD_ITEMS);
	}
	

	
	function testXmlns1() {
		$sOldContent = 'AA:A<br/><foo xmlns="http://bar/"/>DDD';
		$sNewContent = 'AA:A<br/><foo xmlns="http://bar/"/>EDITED';
		
		$sSimilarContent = 'AA:A_FR<br/><foo xmlns="http://bar/"/>DDD_FR';
		$sExpectedResult = 'AA:A_FR<br/><foo xmlns="http://bar/"/>'.AnwUtils::FLAG_UNTRANSLATED_OPEN."EDITED".AnwUtils::FLAG_UNTRANSLATED_CLOSE;
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}

	function testXmlns2() {
		$sOldContent = 'AAA<br/><foo xmlns="http://bar/"/>';
		$sNewContent = 'AAA<br/><foo xmlns="http://barEDITED/"/>';
		
		$sSimilarContent = 'AAA_FR<br/><foo xmlns="http://bar/"/>';
		$sExpectedResult = 'AAA_FR<br/><foo xmlns="http://barEDITED/"/>';
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}

	function testXmlns3() {
		$sOldContent = '<bar foo:xmlns="http://example.com" foo:attr="value"/>';
		$sNewContent = '<bar foo:xmlns="http://exampleEDITED.com" foo:attr="value"/>';
		
		$sSimilarContent = '<bar foo:xmlns="http://example.com" foo:attr="value"/>';
		$sExpectedResult = '<bar foo:xmlns="http://exampleEDITED.com" foo:attr="value"/>';
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	function testXmlns4() {
		$sOldContent = '<bar foo:attr="value"/>a:a';
		$sNewContent = '<bar foo:attr="value2"/>a:a';
		
		$sSimilarContent = '<bar foo:attr="value"/>a:a';
		$sExpectedResult = '<bar foo:attr="value2"/>a:a';
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
		print '<hr/>'.AnwDebug::getLog();
	}

	function testXmlns5() {
		$sOldContent = '<foo:bar foo:xmlns="http://example.com" foo:attr="value"/>';
		$sNewContent = '<foo:bar foo:xmlns="http://exampleEDITED.com" foo:attr="value"/>';
		
		$sSimilarContent = '<foo:bar foo:xmlns="http://example.com" foo:attr="value"/>';
		$sExpectedResult = '<foo:bar foo:xmlns="http://exampleEDITED.com" foo:attr="value"/>';
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}

	function testXmlns6() {
		$sOldContent = 'AAA<br/><foo:bar foo:xmlns="http://example.com" foo:attr="value" style="border:1px solid #000"/>';
		$sNewContent = 'AAA<br/><foo:bar foo:xmlns="http://exampleEDITED.com" foo:attr="value2" style="border:1px solid #000"/>';
		
		$sSimilarContent = 'AAA_FR<br/><foo:bar foo:xmlns="http://example.com" foo:attr="value" style="border:1px solid #000"/>';
		$sExpectedResult = 'AAA_FR<br/><foo:bar foo:xmlns="http://exampleEDITED.com" foo:attr="value2" style="border:1px solid #000"/>';
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}

	function testXmlns7() {
		$sOldContent = '<foo:bar bar:xmlns="http://another" xmlns="http://default" foo:xmlns="http://example.com" foo:attr="value" foo:attr2="value2" attr3="value3"/>';
		$sNewContent = '<foo:bar bar:xmlns="http://another" xmlns="http://defaultEDITED" foo:xmlns="http://exampleEDITED.com" foo:attr="valueEDITED" foo:attr2="value2" attr3="value3"/>';
		
		$sSimilarContent = '<foo:bar bar:xmlns="http://another" xmlns="http://default" foo:xmlns="http://example.com" foo:attr="value" foo:attr2="value2" attr3="value3"/>';
		$sExpectedResult = '<foo:bar bar:xmlns="http://another" xmlns="http://defaultEDITED" foo:xmlns="http://exampleEDITED.com" foo:attr="valueEDITED" foo:attr2="value2" attr3="value3"/>';
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}

	function testXmlns8() {
		$sOldContent = '<p>Result: <a id="result">click here</a></p>';
		$sNewContent = '<p>Result: <a id="resultNEW">click here</a></p>';
		
		$sSimilarContent = '<p>Result: <a id="result">cliquez ici</a></p>';
		$sExpectedResult = '<p>Result: <a id="resultNEW">cliquez ici</a></p>';
		
		$sResult = $this->_testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult);
	}
	
	static function cleanXml($sXml)
	{
		return preg_replace('!\>([[:space:]]*)\<!si','><',$sXml);
	}
	
	
	
	private function _testDiffs($sOldContent, $sNewContent, $sSimilarContent, $sExpectedResult, $sContentClass="page", $FIELD=false)
	{
		AnwDebug::log("####################### NEW TEST #################".$this->_label);
		$asPages = array("fr" => "unittest/fr", "en" => "unittest/en");
		
		try
		{
			//1. delete pages if already exist
			foreach ($asPages as $sLang => $sPageName)
			{
				$oPage = new AnwPageByName($sPageName);
				if ($oPage->exists())
				{
					$oPage->getPageGroup()->deletePages();
				}
			}
			
			
			//2. create page & set initial content
			$oContentClass = AnwContentClasses::getContentClass($sContentClass);
			if (!$FIELD) $FIELD = AnwIContentClassPageDefault_page::FIELD_BODY;
			
			$oContentOriginal = new AnwContentPage($oContentClass);
			$oContentField = $oContentOriginal->getContentFieldsContainer()->getContentField($FIELD);
			if ($oContentField instanceof AnwStructuredContentField_atomic)
			{
				$oContentOriginal->setContentFieldValues( $FIELD, array($sOldContent) );
			}
			else
			{
				$oSubContent = $oContentOriginal->rebuildSubContentFromXml($oContentField, $sOldContent);
				$oContentOriginal->setSubContents($FIELD, array($oSubContent));
			}
			
			$sLang = "en";
			$oPage = AnwPage::createNewPage($oContentClass, $asPages[$sLang], $sLang, "Initializing UnitTest...", $oContentOriginal);
						
			
			//3. create translation & set initial content
			$oContentTranslation = clone $oContentOriginal;
			if ($oContentField instanceof AnwStructuredContentField_atomic)
			{
				$oContentTranslation->setContentFieldValues( $FIELD, array($sSimilarContent) );
			}
			else
			{
				$oSubContentTranslation = $oContentTranslation->rebuildSubContentFromXml($oContentField, $sSimilarContent);
				$oContentTranslation->setSubContents($FIELD, array($oSubContentTranslation));
			}
			
			$sLang = "fr";
			$oPageTranslation = $oPage->createNewTranslation($asPages[$sLang], $sLang, "Initializing UnitTest...", $oContentTranslation);
						
			
			//5. now, test edit !
			$oContent = clone $oPage->getContent();
			if ($oContentField instanceof AnwStructuredContentField_atomic)
			{
				$oContent->setContentFieldValues( $FIELD, array($sNewContent) );
			}
			else
			{
				$oSubContent = $oContent->rebuildSubContentFromXml($oContentField, $sNewContent);
				$oContent->setSubContents($FIELD, array($oSubContent));
			}
			$oPage->saveEditAndDeploy($oContent, AnwChange::TYPE_PAGE_EDITION, "Running UnitTest...");		
			
			
			//6. finaly, compare translation content with expected result
			$sLang = "fr";
			$oPageTranslation = new AnwPageByName($asPages[$sLang]);
			$oContent = $oPageTranslation->getContent();
			
			
			if ($oContentField instanceof AnwStructuredContentField_atomic)
			{
				$aoValues = $oContent->getContentFieldValues($FIELD);
			}
			else
			{
				$aoSubContents = $oContent->getSubContents($FIELD);
				$aoValues = array();
				foreach ($aoSubContents as $oSubContent)
				{
					$aoValues[] = $oSubContent->toXmlString();
				}
			}
			
			$this->assertEqual(count($aoValues), 1);
			$sTestedValue = array_pop($aoValues);
			
			if ($sTestedValue != $sExpectedResult)
			{
				print "**UNITTEST VALUES MISMATCH**<br/>";
				print "Tested: ".htmlentities($sTestedValue);print '<br/>***<br/>';
				print "Expected: ".htmlentities($sExpectedResult);
			}
			
			$this->assertEqual($sTestedValue, $sExpectedResult);
			//print htmlentities($sTestedValue).'<hr/>'.htmlentities($sExpectedResult);
			
			//7. another test, make sure pagegroup is synchronized
			$oPageTranslation->checkPageGroupSynchronized();
			
			//8. clean-up
			$oPageTranslation->getPageGroup()->deletePages();
		}
		catch(AnwException $e){
			print '<hr/>'.AnwDebug::getLog();
			// mark the test as failed but continue the other ones...
			$this->assertEqual("no_exception","exception");
			//throw $e;
		}
	}
	
	
}

?>