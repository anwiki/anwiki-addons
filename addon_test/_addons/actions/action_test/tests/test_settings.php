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
 * Tests for Anwiki overridable settings system.
 * @package Anwiki
 * @version $Id: test_settings.php 135 2009-02-08 16:57:41Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */

class AnwSettingsTestCase extends UnitTestCase {
	function __construct() {
		$this->UnitTestCase('AnwUtils test');
	}
	
	function setUp() {
	
	}
	
	function tearDown() {
		//print '<hr/>'.AnwDebug::getLog();
	}
	
	/*function testSettingsDefault1() {
		
		$cfgDefault = array (
			"setup"	=>	array(
				"location" => array(		
					"urlroot"	=>	"",
					"homepage"	=>	"en/home",
					"friendlyurl_enabled"	=>	false,
					"website_name"	=>	"Anwiki"
				),
				"i18n" => array(
					"lang_default" => "en",
					"langs" => array("en", "fr", "de"),
					"timezone_default" => 2
				),
				"cookies" => array(
					"path"	=>	"/",
					"domain"	=>	".yourwebsite.com",
				)
			),
			
			"components" => array(
				"drivers" => array(
					"storage" => "",
					"sessions" => "",
				),
				"modules" => array(
					"plugins" => array(),
					"contentclasses" => array("page", "news"),
					"actions" => array("edit", "translate", "create", "diff")
				)
			)
		);
		
		$this->doTestComponentGlobal($cfgDefault, null, $cfgDefault);
		
		// testing...
		$oComponent = AnwComponent::getGlobalComponent();
		$oSubContentSetupI18n = $oContent->getSubContent(AnwComponentGlobal_global::CFG_SETUP)->getSubContent(AnwISettings_globalSetup::FIELD_I18N);
		$sValueOrDefault = $oSubContentSetupI18n->getContentFieldValue(AnwISettings_globalSetupI18n::FIELD_LANG_DEFAULT);
		$this->assertEqual($sValueOrDefault, "en");
		
		$oContentField = $oSubContentSetupI18n->getContentFieldsContainer()->getContentField(AnwISettings_globalSetupI18n::FIELD_LANG_DEFAULT);
		$sDefaultValue = $oContentField->getDefaultValue();
		$this->assertEqual($sDefaultValue, "en");
		
		$this->doTestValues($oSubContentSetupI18n, AnwISettings_globalSetupI18n::FIELD_LANG_DEFAULT, true, true, array("en"));
		$this->doTestValues($oSubContentSetupI18n, AnwISettings_globalSetupI18n::FIELD_LANG_DEFAULT, true, false, array("en"));
		
		// ------------------------------------------ OVERRIDING.... ----------------------------------------
		
		$cfgOverride = array (
			"setup"	=>	array(
				"i18n" => array(
					"lang_default" => "fr"
				)
			),
			
			"components" => array(
				"modules" => array(
					"plugins" => array("mon-plugin")
				)
			)
		);
		
		$cfgFusioned = array (
			"setup"	=>	array(
				"location" => array(		
					"urlroot"	=>	"",
					"homepage"	=>	"en/home",
					"friendlyurl_enabled"	=>	false,
					"website_name"	=>	"Anwiki"
				),
				"i18n" => array(
					"lang_default" => "fr",
					"langs" => array("en", "fr", "de"),
					"timezone_default" => 2
				),
				"cookies" => array(
					"path"	=>	"/",
					"domain"	=>	".yourwebsite.com",
				)
			),
			
			"components" => array(
				"drivers" => array(
					"storage" => "",
					"sessions" => "",
				),
				"modules" => array(
					"plugins" => array("mon-plugin"),
					"contentclasses" => array("page", "news"),
					"actions" => array("edit", "translate", "create", "diff")
				)
			)
		);
		
		$this->doTestComponentGlobal($cfgDefault, $cfgOverride, $cfgFusioned);
		
		// testing...
		
		$oSubContentSetupI18n = $oContent->getSubContent(AnwComponentGlobal_global::CFG_SETUP)->getSubContent(AnwISettings_globalSetup::FIELD_I18N);
		$sValueOrDefault = $oSubContentSetupI18n->getContentFieldValue(AnwISettings_globalSetupI18n::FIELD_LANG_DEFAULT);
		$this->assertEqual($sValueOrDefault, "fr");
		
		$oContentField = $oSubContentSetupI18n->getContentFieldsContainer()->getContentField(AnwISettings_globalSetupI18n::FIELD_LANG_DEFAULT);
		$sDefaultValue = $oContentField->getDefaultValue();
		$this->assertEqual($sDefaultValue, "en");
		
		$this->doTestValues($oSubContentSetupI18n, AnwISettings_globalSetupI18n::FIELD_LANG_DEFAULT, true, true, array("fr"));
		$this->doTestValues($oSubContentSetupI18n, AnwISettings_globalSetupI18n::FIELD_LANG_DEFAULT, true, false, array("fr"));
		
	}
	
	function testSettingsDefault2() {
		
		$oComponent = AnwComponent::getGlobalComponent();
		
		$cfgDefault = array (
			"setup"	=>	array(
				"location" => array(		
					"urlroot"	=>	"",
					"homepage"	=>	"en/home",
					"friendlyurl_enabled"	=>	false,
					"website_name"	=>	"Anwiki"
				),
				"i18n" => array(
					"lang_default" => "en",
					"langs" => array("en", "fr", "de"),
					"timezone_default" => 2
				),
				"cookies" => array(
					"path"	=>	"/",
					"domain"	=>	".yourwebsite.com",
				)
			),
			
			"components" => array(
				"drivers" => array(
					"storage" => "",
					"sessions" => "",
				),
				"modules" => array(
					"plugins" => array(),
					"contentclasses" => array("page", "news"),
					"actions" => array("edit", "translate", "create", "diff")
				)
			)
		);
		
		$this->doTestComponentGlobal($cfgDefault, null, $cfgDefault);
		
		// testing...
		
		$oSubContentSetupI18n = $oContent->getSubContent(AnwComponentGlobal_global::CFG_SETUP)->getSubContent(AnwISettings_globalSetup::FIELD_I18N);
		$asValuesOrDefault = $oSubContentSetupI18n->getContentFieldValues(AnwISettings_globalSetupI18n::FIELD_LANGS);
		$this->assertEqual($asValuesOrDefault, array("en", "fr", "de"));
		
		$oContentField = $oSubContentSetupI18n->getContentFieldsContainer()->getContentField(AnwISettings_globalSetupI18n::FIELD_LANGS);
		$asDefaultValues = $oContentField->getDefaultValues();
		$this->assertEqual($asDefaultValues, array("en", "fr", "de"));
		
		$this->doTestValues($oSubContentSetupI18n, AnwISettings_globalSetupI18n::FIELD_LANGS, true, true, array("en", "fr", "de"));
		$this->doTestValues($oSubContentSetupI18n, AnwISettings_globalSetupI18n::FIELD_LANGS, true, false, array("en", "fr", "de"));
		
		// ------------------------------------------ OVERRIDING.... ----------------------------------------
		
		$cfgOverride = array (
			"setup"	=>	array(
				"location" => array(		
					"friendlyurl_enabled"	=>	true,
					"website_name"	=>	"Anwiki2"
				),
				"i18n" => array(
					"lang_default" => "fr",
					"langs" => array("en", "fr")
				)
			),
			
			"components" => array(
				"modules" => array(
					"plugins" => array("mon-plugin")
				)
			)
		);
		
		$cfgFusioned = array (
			"setup"	=>	array(
				"location" => array(		
					"urlroot"	=>	"",
					"homepage"	=>	"en/home",
					"friendlyurl_enabled"	=>	true,
					"website_name"	=>	"Anwiki2"
				),
				"i18n" => array(
					"lang_default" => "fr",
					"langs" => array("en", "fr"),
					"timezone_default" => 2
				),
				"cookies" => array(
					"path"	=>	"/",
					"domain"	=>	".yourwebsite.com",
				)
			),
			
			"components" => array(
				"drivers" => array(
					"storage" => "",
					"sessions" => "",
				),
				"modules" => array(
					"plugins" => array("mon-plugin"),
					"contentclasses" => array("page", "news"),
					"actions" => array("edit", "translate", "create", "diff")
				)
			)
		);
		
		$this->doTestComponentGlobal($cfgDefault, $cfgOverride, $cfgFusioned);
		
		// testing...
		
		$oSubContentSetupI18n = $oContent->getSubContent(AnwComponentGlobal_global::CFG_SETUP)->getSubContent(AnwISettings_globalSetup::FIELD_I18N);
		$asValuesOrDefault = $oSubContentSetupI18n->getContentFieldValues(AnwISettings_globalSetupI18n::FIELD_LANGS);
		$this->assertEqual($asValuesOrDefault, array("en", "fr"));
		
		$oContentField = $oSubContentSetupI18n->getContentFieldsContainer()->getContentField(AnwISettings_globalSetupI18n::FIELD_LANGS);
		$asDefaultValues = $oContentField->getDefaultValues();
		$this->assertEqual($asDefaultValues, array("en", "fr", "de"));
		
		$this->doTestValues($oSubContentSetupI18n, AnwISettings_globalSetupI18n::FIELD_LANGS, true, true, array("en", "fr"));
		$this->doTestValues($oSubContentSetupI18n, AnwISettings_globalSetupI18n::FIELD_LANGS, true, false, array("en", "fr"));
		
	}
	
	function testSettingsDefault3() {
		
		$oComponent = AnwComponent::getGlobalComponent();
		
		$cfgDefault = array (
			"setup"	=>	array(
				"location" => array(		
					"urlroot"	=>	"",
					"homepage"	=>	"en/home",
					"friendlyurl_enabled"	=>	false,
					"website_name"	=>	"Anwiki"
				),
				"i18n" => array(
					"lang_default" => "en",
					"langs" => array("en", "fr", "de"),
					"timezone_default" => 2
				),
				"cookies" => array(
					"path"	=>	"/",
					"domain"	=>	".yourwebsite.com",
				)
			),
			
			"components" => array(
				"drivers" => array(
					"storage" => "",
					"sessions" => "",
				),
				"modules" => array(
					"plugins" => array(),
					"contentclasses" => array("page", "news"),
					"actions" => array("edit", "translate", "create", "diff")
				)
			)
		);
		
		$this->doTestComponentGlobal($cfgDefault, null, $cfgDefault);
		
		// testing...
		
		$oSubContentSetupI18n = $oContent->getSubContent(AnwComponentGlobal_global::CFG_SETUP)->getSubContent(AnwISettings_globalSetup::FIELD_I18N);
		$asValuesOrDefault = $oSubContentSetupI18n->getContentFieldValues(AnwISettings_globalSetupI18n::FIELD_LANGS);
		$this->assertEqual($asValuesOrDefault, array("en", "fr", "de"));
		
		$oContentField = $oSubContentSetupI18n->getContentFieldsContainer()->getContentField(AnwISettings_globalSetupI18n::FIELD_LANGS);
		$asDefaultValues = $oContentField->getDefaultValues();
		$this->assertEqual($asDefaultValues, array("en", "fr", "de"));
		
		$this->doTestValues($oSubContentSetupI18n, AnwISettings_globalSetupI18n::FIELD_LANGS, true, true, array("en", "fr", "de"));
		$this->doTestValues($oSubContentSetupI18n, AnwISettings_globalSetupI18n::FIELD_LANGS, true, false, array("en", "fr", "de"));
		
		// ------------------------------------------ OVERRIDING.... ----------------------------------------
		
		$cfgOverride = array (
			"setup"	=>	array(
				"i18n" => array(
					"lang_default" => "fr"
				)
			),
			
			"components" => array(
				"modules" => array(
					"plugins" => array("mon-plugin")
				)
			)
		);
		
		$cfgFusioned = array (
			"setup"	=>	array(
				"location" => array(		
					"urlroot"	=>	"",
					"homepage"	=>	"en/home",
					"friendlyurl_enabled"	=>	false,
					"website_name"	=>	"Anwiki"
				),
				"i18n" => array(
					"lang_default" => "fr",
					"langs" => array("en", "fr", "de"),
					"timezone_default" => 2
				),
				"cookies" => array(
					"path"	=>	"/",
					"domain"	=>	".yourwebsite.com",
				)
			),
			
			"components" => array(
				"drivers" => array(
					"storage" => "",
					"sessions" => "",
				),
				"modules" => array(
					"plugins" => array("mon-plugin"),
					"contentclasses" => array("page", "news"),
					"actions" => array("edit", "translate", "create", "diff")
				)
			)
		);
		
		$this->doTestComponentGlobal($cfgDefault, $cfgOverride, $cfgFusioned);
		
		// testing...
		
		$oSubContentSetupI18n = $oContent->getSubContent(AnwComponentGlobal_global::CFG_SETUP)->getSubContent(AnwISettings_globalSetup::FIELD_I18N);
		$asValuesOrDefault = $oSubContentSetupI18n->getContentFieldValues(AnwISettings_globalSetupI18n::FIELD_LANGS);
		$this->assertEqual($asValuesOrDefault, array("en", "fr", "de"));
		
		$oContentField = $oSubContentSetupI18n->getContentFieldsContainer()->getContentField(AnwISettings_globalSetupI18n::FIELD_LANGS);
		$asDefaultValues = $oContentField->getDefaultValues();
		$this->assertEqual($asDefaultValues, array("en", "fr", "de"));
		
		$this->doTestValues($oSubContentSetupI18n, AnwISettings_globalSetupI18n::FIELD_LANGS, true, true, array("en", "fr", "de"));
		$this->doTestValues($oSubContentSetupI18n, AnwISettings_globalSetupI18n::FIELD_LANGS, true, false, array("en", "fr", "de"));
		
	}*/
	
	function testGetSettingsFromFileMultiple1() {
		
		$cfgDefault = array (
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT"
				)
			)
		);
		
		$cfgOverride = array (
			
		);
		
		$cfgExpected = array(
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT"
				)
			)
		);
		
		$this->doTestGetSettingsFromFile($cfgDefault, $cfgOverride, $cfgExpected);
	}
	
	function testGetSettingsFromFileMultiple2() {
		
		$cfgDefault = array (
			
		);
		
		$cfgOverride = array (
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT"
				)
			)
		);
		
		$cfgExpected = array(
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT"
				)
			)
		);
		
		$this->doTestGetSettingsFromFile($cfgDefault, $cfgOverride, $cfgExpected);
	}
	
	function testGetSettingsFromFileMultiple3() {
		
		$cfgDefault = array (
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT"
				)
			)
		);
		
		$cfgOverride = array (
			"items"	=>	array(
				array(
					"searches" => array(		
						"ONE SEARCH"
					),
					"replace" => "ONE REPLACE"
				)
			)
		);
		
		$cfgExpected = array(
			"items"	=>	array(
				array(
					"searches" => array(		
						"ONE SEARCH"
					),
					"replace" => "ONE REPLACE"
				)
			)
		);
		
		$this->doTestGetSettingsFromFile($cfgDefault, $cfgOverride, $cfgExpected);
	}
	
	function testGetSettingsFromFileMultiple4() {
		
		$cfgDefault = array (
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT1"
				),
				array(
					"searches" => array(		
						"SEARCH3",
						"SEARCH4"
					),
					"replace" => "REPLACEMENT2"
				)
			)
		);
		
		$cfgOverride = array (
			
		);
		
		$cfgExpected = array(
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT1"
				),
				array(
					"searches" => array(		
						"SEARCH3",
						"SEARCH4"
					),
					"replace" => "REPLACEMENT2"
				)
			)
		);
		
		$this->doTestGetSettingsFromFile($cfgDefault, $cfgOverride, $cfgExpected);
	}
	
	function testGetSettingsFromFileMultiple5() {
		
		$cfgDefault = array (
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT1"
				),
				array(
					"searches" => array(		
						"SEARCH3",
						"SEARCH4"
					),
					"replace" => "REPLACEMENT2"
				)
			)
		);
		
		$cfgOverride = array (
			"items"	=>	array()
		);
		
		$cfgExpected = array(
			"items"	=>	array()
		);
		
		$this->doTestGetSettingsFromFile($cfgDefault, $cfgOverride, $cfgExpected);
	}
	
	function testGetSettingsFromFileMultiple6() {
		
		$cfgDefault = array (
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT1"
				),
				array(
					"searches" => array(		
						"SEARCH3",
						"SEARCH4"
					),
					"replace" => "REPLACEMENT2"
				)
			)
		);
		
		$cfgOverride = array (
			"items"	=>	array(
				array(
					"replace" => "REPLACEMENT1"
				),
				array(
					"searches" => array(		
						"SEARCH3",
						"SEARCH4"
					)
				)
			)
		);
		
		$cfgExpected = array(
			"items"	=>	array(
				array(
					"replace" => "REPLACEMENT1"
				),
				array(
					"searches" => array(		
						"SEARCH3",
						"SEARCH4"
					)
				)
			)
		);
		
		$this->doTestGetSettingsFromFile($cfgDefault, $cfgOverride, $cfgExpected);
	}
	
	function testGetSettingsFromFileMono2() {
		
		$cfgDefault = array (
			"aaa" => "ok",
			"bbb" => "yes",
			"ccc" => "no"
		);
		
		$cfgOverride = array (
			"aaa" => "OOOOO",
			"ccc" => "NNNNN"
		);
		
		$cfgExpected = array(
			"aaa" => "OOOOO",
			"bbb" => "yes",
			"ccc" => "NNNNN"
		);
		
		$this->doTestGetSettingsFromFile($cfgDefault, $cfgOverride, $cfgExpected);
	}
	
	function testGetSettingsFromFileMono3() {
		
		$cfgDefault = array (
			"item" => array(
				"aaa" => "ok",
				"bbb" => "yes",
				"ccc" => "no"
			),
			"multi" => array(
				array(
					"aaa" => "ok",
					"bbb" => "yes",
					"ccc" => "no"
				)
			)
		);
		
		$cfgOverride = array (
			"item" => array(
				"aaa" => "OOO",
				"ccc" => "NNN"
			),
			"multi" => array(
				array(
					"aaa" => "OO",
					"ccc" => "NN"
				)
			)
		);
		
		$cfgExpected = array(
			"item" => array(
				"aaa" => "OOO",
				"bbb" => "yes",
				"ccc" => "NNN"
			),
			"multi" => array(
				array(
					"aaa" => "OO",
					"ccc" => "NN"
				)
			)
		);
		
		$this->doTestGetSettingsFromFile($cfgDefault, $cfgOverride, $cfgExpected);
	}
	
	function testComponent1Example1() {
		
		$cfgDefault = array (
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT"
				)
			)
		);
		
		$cfgOverride = array();
		
		$asFusionedCfg = array(
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT"
				)
			)
		);
		
		$this->doTestComponent(1, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	function testComponent1Example11() {
		
		$cfgDefault = array();
		
		$cfgOverride = array (
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT"
				)
			)
		);
				
		$asFusionedCfg = array (
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT"
				)
			)
		);
		
		$this->doTestComponent(1, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	function testComponent1Example2() {
		
		$cfgDefault = array (
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT"
				)
			)
		);
		
		$cfgOverride = array (
			"items"	=>	array(
				array(
					"searches" => array(		
						"MYSEARCH"
					),
					"replace" => "MYREPLACE"
				),
				array(
					"searches" => array(),
					"replace" => "ANOTHER"
				)
			)
		);
		
		$asFusionedCfg = array(
			"items"	=>	array(
				array(
					"searches" => array(		
						"MYSEARCH"
					),
					"replace" => "MYREPLACE"
				),
				array(
					"searches" => array(),
					"replace" => "ANOTHER"
				)
			)
		);
		
		$this->doTestComponent(1, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	function testComponent1Example3() {
		
		$cfgDefault = array ( //can't define default value for multiple composed field
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT"
				)
			)
		);
		
		$cfgOverride = array (
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "MYREPLACE"
				)
			)
		);
		
		$asFusionedCfg = array(
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "MYREPLACE"
				)
			)
		);
		
		$this->doTestComponent(1, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	function testComponent1Example4() {
		
		$cfgDefault = array (
			"items"	=>	array( //can't define default value for multiple composed field
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT"
				),
				array(
					"searches" => array(		
						"SEARCH3"
					),
					"replace" => "REPLACEMENT2"
				)
			)
		);
		
		$cfgOverride = array ();
		
		$asFusionedCfg = array(
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT"
				),
				array(
					"searches" => array(		
						"SEARCH3"
					),
					"replace" => "REPLACEMENT2"
				)
			)
		);
		
		$this->doTestComponent(1, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	function testComponent1Example5() {
		
		$cfgDefault = array (
			"items"	=>	array(
				array(
					"replace" => "REPLACEMENT",
					"searches" => array("SEARCH")
				)
			)
		);
		
		$cfgOverride = array ();
		
		$asFusionedCfg = array (
			"items"	=>	array(
				array(
					"replace" => "REPLACEMENT",
					"searches" => array("SEARCH")
				)
			)
		);
		
		$this->doTestComponent(1, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	function testComponent2Example1() {
		
		$cfgDefault = array (
			"mysql"	=>	array(
				"user" => "user_default",
				"password" => "password_default",
				"database" => "database_default",
				"host" => "host_default",
				"prefix" => "prefix_default"
			)
		);
		
		$cfgOverride = array (
			"mysql"	=>	array(
				"user" => "user_my",
				"password" => "password_my",
				"database" => "database_my",
				"host" => "host_my",
				"prefix" => "prefix_my"
			)
		);
		
		$asFusionedCfg = array (
			"mysql"	=>	array(
				"user" => "user_my",
				"password" => "password_my",
				"database" => "database_my",
				"host" => "host_my",
				"prefix" => "prefix_my"
			)
		);
		
		$this->doTestComponent(2, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	function testComponent2Example2() {
		
		$cfgDefault = array (
			"mysql"	=>	array(
				"user" => "user_default",
				"password" => "password_default",
				"database" => "database_default",
				"host" => "host_default",
				"prefix" => "prefix_default"
			)
		);
		
		$cfgOverride = array (
			"mysql"	=>	array(
				"user" => "user_my",
				"password" => "password_my",
				"database" => "database_my"
			)
		);
		
		$asFusionedCfg = array (
			"mysql"	=>	array(
				"user" => "user_my",
				"password" => "password_my",
				"database" => "database_my",
				"host" => "host_default",
				"prefix" => "prefix_default"
			)
		);
		
		$this->doTestComponent(2, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	function testComponent3Example1() {
		
		$cfgDefault  = array (
			"infos" => array (
				"langs"	=>	array("fr", "en", "de")
			)
		);
		
		$cfgOverride = array();
		
		$asFusionedCfg  = array (
			"infos" => array (
				"langs"	=>	array("fr", "en", "de"),
				"actions" => array()
			)
		);
		
		$this->doTestComponent(3, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	function testComponent3Example2() {
		
		$cfgDefault = array (
			"infos" => array (
				"langs"	=>	array("fr", "en", "de")
			)
		);
		
		$cfgOverride = array (
			"infos" => array (
				"langs"	=>	array("en", "fr")
			)
		);
		
		$asFusionedCfg = array (
			"infos" => array (
				"langs"	=>	array("en", "fr"),
				"actions" => array()
			)
		);
		
		$this->doTestComponent(3, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	function testComponent3Example3() {
		
		$cfgDefault = array (
			"infos" => array (
				"langs"	=>	array("fr", "en", "de")
			)
		);
		
		$cfgOverride = array (
			"infos" => array (
				"langs"	=>	array("fr", "es")
			)
		);
		
		$asFusionedCfg = array (
			"infos" => array (
				"langs"	=>	array("fr", "es"),
				"actions" => array()
			)
		);
		
		$this->doTestComponent(3, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	function testComponent3Example4() {
		
		$cfgDefault = array (
			"infos" => array (
				"langs"	=>	array("fr", "en", "de")
			)
		);
		
		$cfgOverride = array (
			"infos" => array (
				"langs"	=>	array("fr", "es"),
				"actions" => array("view", "test")
			)
		);
		
		$asFusionedCfg = array (
			"infos" => array (
				"langs"	=>	array("fr", "es"),
				"actions" => array("view", "test")
			)
		);
		
		$this->doTestComponent(3, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	function testComponent3Example5() {
		
		$cfgDefault = array (
			"infos" => array (
				"langs"	=>	array("fr", "en", "de"),
				"actions" => array("view", "test")
			)
		);
		
		$cfgOverride = array (
			"infos" => array (
				"langs"	=>	array("fr", "es")
			)
		);
		
		$asFusionedCfg = array (
			"infos" => array (
				"langs"	=>	array("fr", "es"),
				"actions" => array("view", "test")
			)
		);
		
		$this->doTestComponent(3, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	function testComponent4Example1() {
		
		$cfgDefault  = array (
			"infos" => array (
				array( //ok as we doesn't have overloaded the setting
					"langs"	=>	array("fr", "en", "de"),
					"actions" => array()
				)
			)
		);
		
		$cfgOverride = array();
		
		$asFusionedCfg  = array (
			"infos" => array (
				array( //ok as we doesn't have overloaded the setting
					"langs"	=>	array("fr", "en", "de"),
					"actions" => array()		
				)
			)
		);
		
		$this->doTestComponent(4, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	function testComponent4Example2() {
		
		$cfgDefault  = array (
			"infos" => array (
				array(
					"langs"	=>	array("fr", "en", "de"),
					"actions" => array("view", "test")
				),
				array(
					"langs"	=>	array("zh_CN", "it", "es"),
					"actions" => array("edit")
				)
			)
		);
		
		$cfgOverride = array (
			"infos" => array (
				array(
					"langs"	=>	array("fr", "es"),
					"actions" => array("view", "test")
				)
			)
		);
		
		$asFusionedCfg  = array (
			"infos" => array (
				array(
					"langs"	=>	array("fr", "es"),
					"actions" => array("view", "test")
				)
			)
		);
		
		$this->doTestComponent(4, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	function testComponent4Example3() {
		
		$cfgDefault  = array (
			"infos" => array (
				array(
					"langs"	=>	array("fr", "en", "de"),
					"actions" => array("view", "test")
				),
				array(
					"langs"	=>	array("fr2", "en2", "de2"),
					"actions" => array("view2", "test2")
				)
			)
		);
		
		$cfgOverride = array (
			"infos" => array (
				array(
					"langs"	=>	array("fr", "es"),
					"actions" => array("view", "test")
				)
			)
		);
		
		$asFusionedCfg  = array (
			"infos" => array (
				array(
					"langs"	=>	array("fr", "es"),
					"actions" => array("view", "test")
				)
			)
		);
		
		$this->doTestComponent(4, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	function testComponent4Example4() {
		
		$cfgDefault  = array (
			"infos" => array (
				array(
					"langs"	=>	array("fr", "en", "de"),
					"actions" => array("view", "test")
				),
				array(
					"langs"	=>	array("zh_CN", "it", "es"),
					"actions" => array("edit")
				)
			)
		);
		
		$cfgOverride = array ();
		
		$asFusionedCfg  = array (
			"infos" => array (
				array(
					"langs"	=>	array("fr", "en", "de"),
					"actions" => array("view", "test")
				),
				array(
					"langs"	=>	array("zh_CN", "it", "es"),
					"actions" => array("edit")
				)
			)
		);
		
		$this->doTestComponent(4, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	function testComponent5Example1() {
		
		$cfgDefault = array (
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT",
					"subitems" => array(
						array(
							"title" => "abcd",
							"matches" => array(
								"aa", "bb", "cc"
							)
						)
					)
				)
			),
			"emails" => array(
				"aa@bb.fr",
				"cc@dd.org"
			)
		);
		
		$cfgOverride = array ();
		
		$asFusionedCfg = array(
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT",
					"subitems" => array(
						array(
							"title" => "abcd",
							"matches" => array(
								"aa", "bb", "cc"
							)
						)
					)
				)
			),
			"emails" => array(
				"aa@bb.fr",
				"cc@dd.org"
			)
		);
		
		$this->doTestComponent(5, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	function testComponent5Example2() {
		
		$cfgDefault = array (
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT",
					"subitems" => array(
						array(
							"title" => "abcd",
							"matches" => array(
								"aa", "bb", "cc"
							)
						)
					)
				)
			),
			"emails" => array(
				"aa@bb.fr",
				"cc@dd.org"
			)
		);
		
		$cfgOverride = array (
			"emails" => array(
				"abc@def.fr"
			)
		);
		
		$asFusionedCfg = array(
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT",
					"subitems" => array(
						array(
							"title" => "abcd",
							"matches" => array(
								"aa", "bb", "cc"
							)
						)
					)
				)
			),
			"emails" => array(
				"abc@def.fr"
			)
		);
		
		$this->doTestComponent(5, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	
	function testComponent5Example3() {
		
		$cfgDefault = array (
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2"
					),
					"replace" => "REPLACEMENT",
					"subitems" => array(
						array(
							"title" => "abcd",
							"matches" => array(
								"aa", "bb", "cc"
							)
						)
					)
				)
			),
			"emails" => array(
				"aa@bb.fr",
				"cc@dd.org"
			)
		);
		
		$cfgOverride = array (
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2",
						"NEWSEARCH"
					),
					"replace" => "REPLACEMENT",
					"subitems" => array(
						array(
							"title" => "abcd",
							"matches" => array(
								"aa", "bb", "cc"
							)
						)
					)
				)
			)
		);
		
		$asFusionedCfg = array(
			"items"	=>	array(
				array(
					"searches" => array(		
						"SEARCH1",
						"SEARCH2",
						"NEWSEARCH"
					),
					"replace" => "REPLACEMENT",
					"subitems" => array(
						array(
							"title" => "abcd",
							"matches" => array(
								"aa", "bb", "cc"
							)
						)
					)
				)
			),
			"emails" => array(
				"aa@bb.fr",
				"cc@dd.org"
			)
		);
		
		$this->doTestComponent(5, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	function testComponent5Example4() {
		
		$cfgDefault = array (
			"items"	=>	array(
				array(
					"searches" => array(),
					"replace" => "REPLACEMENT",
					"subitems" => array(
						array(
							"title" => "abcd",
							"matches" => array("aa")
						)
					)
				)
			),
			"emails" => array(
				"aa@bb.fr",
				"cc@dd.org"
			)
		);
		
		$cfgOverride = array (
			"items"	=>	array(
				array(
					"searches" => array(),
					"replace" => "REPLACEMENT",
					"subitems" => array(
						array(
							"title" => "abcd",
							"matches" => array()
						)
					)
				)
			)
		);
		
		$asFusionedCfg = array(
			"items"	=>	array(
				array(
					"searches" => array(),
					"replace" => "REPLACEMENT",
					"subitems" => array(
						array(
							"title" => "abcd",
							"matches" => array()
						)
					)
				)
			),
			"emails" => array(
				"aa@bb.fr",
				"cc@dd.org"
			)
		);
		
		$this->doTestComponent(5, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	//-------------------------
	
	protected function doTestComponent($nComponentNumber, $cfgDefault=null, $cfgOverride=null, $asFusionedCfg) {
		
		$sClassName = "AnwComponentExample".$nComponentNumber;
		$oComponent = new $sClassName("example",false);
		$this->doTestComponentToOverrideCfgArray($oComponent, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	protected function doTestComponentGlobal($cfgDefault=null, $cfgOverride=null, $asFusionedCfg) {
		
		$oComponent = AnwComponent::getGlobalComponent();
		$this->doTestComponentToOverrideCfgArray($oComponent, $cfgDefault, $cfgOverride, $asFusionedCfg);
	}
	
	protected function doTestComponentToOverrideCfgArray($oComponent, $cfgDefault=array(), $cfgOverride=array(), $asFusionedCfg) {
		
		$oContent = new AnwContentSettings($oComponent->___unittest_getContentClassSettings($cfgDefault));
		
		// simulate readSettings
		if (count($cfgOverride)>0) {
			$oContent->___unittest_doReadSettings($cfgOverride, false);
		}
				
		$cfgResult = $oContent->___unittest_toOverrideCfgArray();
		$this->assertEqual($cfgResult, $cfgOverride);
		
		$cfgFusioned = $oContent->___unittest_toFusionedCfgArray();
		$this->assertEqual($cfgFusioned, $asFusionedCfg);
	}
	
	protected function doTestGetSettingsFromFile($cfgDefault, $cfgOverride, $cfgExpected)
	{
		//simulate cfg() / getSettingsFromFile...
		$allSettings = $cfgDefault;
		$allSettings = AnwUtils::___unittest_getSettingsFromFile_doOverride($allSettings, $cfgOverride);
		$this->assertEqual($allSettings, $cfgExpected);
	}
	
	protected function doTestValues($oSubContent, $sFieldName, $bWithDefaultValues, $bWithMissingValues, $asExpectedValues)
	{
		$sTestedValue = $oSubContent->getContentFieldValues($sFieldName, false, $bWithDefaultValues, $bWithMissingValues);
		$this->assertEqual($sTestedValue, $asExpectedValues);		
	}
}



class AnwComponentExample1 extends AnwPlugin implements AnwConfigurable
{
	const CFG_ITEMS = "items";
	
	function getConfigurableSettings()
	{
		$aoSettings = array();
		$oContentField = new AnwContentFieldSettings_Example1Item(self::CFG_ITEMS);
		$oContentMultiplicity = new AnwContentMultiplicity_multiple();
		$oContentField->setMultiplicity($oContentMultiplicity);
		$aoSettings[] = $oContentField;
		
		return $aoSettings;
	}
}

class AnwContentFieldSettings_Example1Item extends AnwContentFieldSettings_container
{
	const FIELD_SEARCHES = "searches";
	const FIELD_REPLACE = "replace";
	
	function init()
	{
		parent::init();
		
		$oContentField = new AnwContentFieldSettings_xhtml(self::FIELD_REPLACE);
		$this->addContentField($oContentField);
		
		$oContentField = new AnwContentFieldSettings_xhtml(self::FIELD_SEARCHES);
		$oContentMultiplicity = new AnwContentMultiplicity_multiple();
		$oContentField->setMultiplicity($oContentMultiplicity);
		$this->addContentField($oContentField);
	}
}

//----------

class AnwComponentExample2 extends AnwPlugin implements AnwConfigurable
{
	const CFG_MYSQL = "mysql";
	
	function getConfigurableSettings()
	{
		$aoSettings = array();
		$oContentField = new AnwContentFieldSettings_mysqlconnexion(self::CFG_MYSQL);
		$aoSettings[] = $oContentField;
		
		return $aoSettings;
	}
}

//----------

class AnwComponentExample3 extends AnwPlugin implements AnwConfigurable
{
	const CFG_INFOS = "infos";
	
	function getConfigurableSettings()
	{
		$aoSettings = array();
		$oContentField = new AnwContentFieldSettings_Example3Infos(self::CFG_INFOS);
		$aoSettings[] = $oContentField;
		
		return $aoSettings;
	}
}

class AnwContentFieldSettings_Example3Infos extends AnwContentFieldSettings_container
{
	const FIELD_LANGS = "langs";
	const FIELD_ACTIONS = "actions";
	
	function init()
	{
		parent::init();
		
		$oContentField = new AnwContentFieldSettings_string(self::FIELD_LANGS);
		$oContentMultiplicity = new AnwContentMultiplicity_multiple();
		$oContentField->setMultiplicity($oContentMultiplicity);
		$this->addContentField($oContentField);
		
		$oContentField = new AnwContentFieldSettings_string(self::FIELD_ACTIONS);
		$oContentMultiplicity = new AnwContentMultiplicity_multiple();
		$oContentField->setMultiplicity($oContentMultiplicity);
		$this->addContentField($oContentField);
	}
}

//----------

class AnwComponentExample4 extends AnwPlugin implements AnwConfigurable
{
	const CFG_INFOS = "infos";
	
	function getConfigurableSettings()
	{
		$aoSettings = array();
		$oContentField = new AnwContentFieldSettings_Example4Infos(self::CFG_INFOS);
		$oContentMultiplicity = new AnwContentMultiplicity_multiple();
		$oContentField->setMultiplicity($oContentMultiplicity);
		$aoSettings[] = $oContentField;
		
		return $aoSettings;
	}
}

class AnwContentFieldSettings_Example4Infos extends AnwContentFieldSettings_container
{
	const FIELD_LANGS = "langs";
	const FIELD_ACTIONS = "actions";
	
	function init()
	{
		parent::init();
		
		$oContentField = new AnwContentFieldSettings_string(self::FIELD_LANGS);
		$oContentMultiplicity = new AnwContentMultiplicity_multiple();
		$oContentField->setMultiplicity($oContentMultiplicity);
		$this->addContentField($oContentField);
		
		$oContentField = new AnwContentFieldSettings_string(self::FIELD_ACTIONS);
		$oContentMultiplicity = new AnwContentMultiplicity_multiple();
		$oContentField->setMultiplicity($oContentMultiplicity);
		$this->addContentField($oContentField);
	}
}

//------------------

class AnwComponentExample5 extends AnwPlugin implements AnwConfigurable
{
	const CFG_ITEMS = "items";
	const CFG_EMAILS = "emails";
	
	function getConfigurableSettings()
	{
		$aoSettings = array();
		$oContentField = new AnwContentFieldSettings_Example5Item(self::CFG_ITEMS);
		$oContentMultiplicity = new AnwContentMultiplicity_multiple();
		$oContentField->setMultiplicity($oContentMultiplicity);
		$aoSettings[] = $oContentField;
		
		$oContentField = new AnwContentFieldSettings_string(self::CFG_EMAILS);
		$oContentMultiplicity = new AnwContentMultiplicity_multiple();
		$oContentField->setMultiplicity($oContentMultiplicity);
		$aoSettings[] = $oContentField;
		
		return $aoSettings;
	}
}

class AnwContentFieldSettings_Example5Item extends AnwContentFieldSettings_container
{
	const FIELD_SEARCHES = "searches";
	const FIELD_REPLACE = "replace";
	const FIELD_SUBITEMS = "subitems";
	
	function init()
	{
		parent::init();
		
		$oContentField = new AnwContentFieldSettings_xhtml(self::FIELD_REPLACE);
		$this->addContentField($oContentField);
		
		$oContentField = new AnwContentFieldSettings_xhtml(self::FIELD_SEARCHES);
		$oContentMultiplicity = new AnwContentMultiplicity_multiple();
		$oContentField->setMultiplicity($oContentMultiplicity);
		$this->addContentField($oContentField);
		
		$oContentField = new AnwContentFieldSettings_Example5SubItem(self::FIELD_SUBITEMS);
		$oContentMultiplicity = new AnwContentMultiplicity_multiple();
		$oContentField->setMultiplicity($oContentMultiplicity);
		$this->addContentField($oContentField);		
	}
}

class AnwContentFieldSettings_Example5SubItem extends AnwContentFieldSettings_container
{
	const FIELD_TITLE = "title";
	const FIELD_MATCHES = "matches";
	
	function init()
	{
		parent::init();
		
		$oContentField = new AnwContentFieldSettings_string(self::FIELD_TITLE);
		$this->addContentField($oContentField);
		
		$oContentField = new AnwContentFieldSettings_string(self::FIELD_MATCHES);
		$oContentMultiplicity = new AnwContentMultiplicity_multiple();
		$oContentField->setMultiplicity($oContentMultiplicity);
		$this->addContentField($oContentField);
	}
}

?>