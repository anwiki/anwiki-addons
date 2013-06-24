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
 * Running unit tests.
 * @package Anwiki
 * @version $Id: action_test.php 304 2010-09-12 15:05:38Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */
 
class AnwActionDefault_test extends AnwActionGlobal
{
	function getNavEntry()
	{
		return $this->createManagementGlobalNavEntry();
	}
	
	function run()
	{
		require_once($this->getMyComponentPathDefault().'lib/simpletest/unit_tester.php');
		require_once($this->getMyComponentPathDefault().'lib/simpletest/reporter.php');
		$this->runTests();
		AnwDebug::stopBench('GLOBAL');
		$sLog = AnwDebug::getLog();
	//	print '<hr/>'.$sLog;
		print '<hr/>'.substr($sLog, strpos($sLog, '(benchmark) SUM'));
		exit;
	}
	
	private function runTests()
	{
		$oTest = new GroupTest('AnWiki tests');
		$oTest->addTestFile($this->getMyComponentPathDefault().'tests/test_diffs.php');
		$oTest->addTestFile($this->getMyComponentPathDefault().'tests/test_utils.php');
		$oTest->addTestFile($this->getMyComponentPathDefault().'tests/test_settings.php');
		$oTest->addTestFile($this->getMyComponentPathDefault().'tests/test_output.php');
		$oTest->addTestFile($this->getMyComponentPathDefault().'tests/test_xml.php');
		$oTest->run(new HtmlReporter());
	}
}






?>