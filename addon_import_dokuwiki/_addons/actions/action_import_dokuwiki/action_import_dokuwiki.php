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
 * Import dokuwiki user accounts into Anwiki.
 * @package component:action:import_dokuwiki
 * @version $Id: action_import_dokuwiki.php 224 2009-11-11 14:12:08Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */

ini_set('max_execution_time', 300);

class AnwActionDefault_import_dokuwiki extends AnwActionGlobal implements AnwHttpsAction, AnwAdminAction
{
	const INPUT_NAME_RUN = "dokuRunImport";
	const INPUT_NAME_PATH = "dokuPath";
	const INPUT_NAME_SENDPASSWD = "dokuSendPwd";
	
	// set by readDokuwikiUsers()
	private $aoUsers;
	private $asUserErrors;
	
	function getNavEntry()
	{
		return $this->createManagementGlobalNavEntry();
	}
	
	function run()
	{
		//what should we synchronize?
		$sDokuPath = AnwEnv::_POST(self::INPUT_NAME_PATH);
		if (!$sDokuPath)
		{
			$this->showForm();
		}
		else
		{
			// append ending '/' to path
			if (substr($sDokuPath,-1,1) != '/')
			{
				$sDokuPath .= "/";
			}
			
			//check that dokuwiki is here!
			$asDokuFiles = $this->getDokuwikiRequiredFiles();
			foreach ($asDokuFiles as $sDokuFile)
			{
				$sDokuPathFile = $sDokuPath.$sDokuFile;
				if (!file_exists($sDokuPathFile))
				{
					$this->showForm($sDokuPath, $this->t_("form_path_file_not_found", array('file' => $sDokuPathFile)));
					return;
				}
			}
			
			$bDoImport = (AnwEnv::_POST(self::INPUT_NAME_RUN) ? true : false);
			if (!$bDoImport)
			{
				$this->simulateImport($sDokuPath);
			}
			else
			{
				$bSendPwd = (AnwEnv::_POST(self::INPUT_NAME_SENDPASSWD) ? true : false);
				$this->runImport($sDokuPath, $bSendPwd);
			}
		}
	}
	
	protected function showForm($sDokuPathValue="", $sError=false)
	{
		$this->out .= $this->tpl()->showForm(AnwUtils::alink("import_dokuwiki"), $sDokuPathValue, self::INPUT_NAME_PATH, $sError);
	}
	
	protected function simulateImport($sDokuPath)
	{
		$this->importDokuwikiUsers($sDokuPath, false);
		$this->out .= $this->tpl()->simulateImportBegin(AnwUtils::alink("import_dokuwiki"), self::INPUT_NAME_PATH, self::INPUT_NAME_RUN, self::INPUT_NAME_SENDPASSWD, $sDokuPath);
		foreach ($this->aoUsers as $mData)
		{
			$oUser = $mData[0];
			$sPassword = $mData[1];
			$this->out .= $this->tpl()->userImportSuccess($oUser, $sPassword);
		}
		foreach ($this->asUserErrors as $mData)
		{
			$sUserErrorLogin = $mData[0];
			$sUserErrorMail = $mData[1];
			$sErrorMessage = $mData[2];
			$this->out .= $this->tpl()->userImportFail($sUserErrorLogin, $sUserErrorMail, $sErrorMessage);
		}
		$this->out .= $this->tpl()->simulateImportEnd();
	}
	
	protected function runImport($sDokuPath, $bSendPwd)
	{
		$this->importDokuwikiUsers($sDokuPath, true);
		$this->out .= $this->tpl()->runImportBegin($bSendPwd);
		foreach ($this->aoUsers as $mData)
		{
			$oUser = $mData[0];
			$sPassword = $mData[1];
			$this->out .= $this->tpl()->userImportSuccess($oUser, $sPassword);
			if ($bSendPwd)
			{
				$sSubject = $this->t_("mail_subject", array("sitename"=>AnwComponent::globalCfgWebsiteName()));
				$sBody = $this->t_("mail_body", array("login"=>$oUser->getLogin(), "password"=>$sPassword, "displayname"=>$oUser->getDisplayName(), "urlroot"=>AnwComponent::globalCfgUrlRoot()));
				AnwUtils::mail($oUser->getEmail(), $sSubject, $sBody);
			}
		}
		foreach ($this->asUserErrors as $mData)
		{
			$sUserErrorLogin = $mData[0];
			$sUserErrorMail = $mData[1];
			$sErrorMessage = $mData[2];
			$this->out .= $this->tpl()->userImportFail($sUserErrorLogin, $sUserErrorMail, $sErrorMessage);
		}
		$this->out .= $this->tpl()->runImportEnd();
	}
	
	protected function getDokuwikiRequiredFiles()
	{
		$asDokuFiles = array();
		$asDokuFiles[] = "inc/init.php";
		$asDokuFiles[] = "inc/common.php";
		$asDokuFiles[] = "inc/auth.php";
		return $asDokuFiles;
	}
	
	protected function generateNewPassword()
	{
		return substr(AnwUtils::genStrongRandMd5(), 0, 15);
	}
	
	protected function importDokuwikiUsers($sDokuPath, $bDoImport)
	{
		$asDokuFiles = $this->getDokuwikiRequiredFiles();
		foreach ($asDokuFiles as $sDokuFile)
		{
			loadApp($sDokuPath.$sDokuFile);
		}
		
		$this->aoUsers = array();
		
		global $auth;
		$aasDokuwikiUsers = $auth->retrieveUsers();
		foreach ($aasDokuwikiUsers as $sDokuwikiLogin => $asData)
		{
			try
			{
				$sPassword = $this->generateNewPassword();
				// using dokuwiki's login as login and displayname
				$this->aoUsers[] = array($this->createUser($bDoImport, $sDokuwikiLogin, $sDokuwikiLogin, $asData['mail'], self::globalCfgLangDefault(), self::globalCfgTimezoneDefault(), $sPassword), $sPassword);
			}
			catch(AnwException $e)
			{
				$this->asUserErrors[] = array($sDokuwikiLogin, $asData['mail'], $e->getMessage());
			}
		}
	}
	
	//TODO refactoring AnwUsers
	protected function createUser($bDoImport, $sLogin, $sDisplayName, $sEmail, $sLang, $nTimezone, $sPassword)
	{
		$sEmail = strtolower($sEmail);
		
		try
		{		
			if ($bDoImport)
			{
				$oUser = AnwUsers::createUser($sLogin, $sDisplayName, $sEmail, $sLang, $nTimezone, $sPassword);
			}
			else
			{
				if (!AnwUsers::isValidLogin($sLogin))
				{
					throw new AnwBadLoginException();
				}
				if (!AnwUsers::isValidDisplayName($sDisplayName))
				{
					throw new AnwBadDisplayNameException();
				}
				if (!AnwUsers::isValidEmail($sEmail))
				{
					throw new AnwBadEmailException();
				}
				if (!Anwi18n::isValidLang($sLang))
				{
					throw new AnwBadLangException();
				}
				if (!AnwUsers::isValidTimezone($nTimezone))
				{
					throw new AnwBadTimezoneException();
				}
				if (!AnwUsers::isValidPassword($sPassword))
				{
					throw new AnwBadPasswordException();
				}
				if (!AnwUsers::isAvailableLogin($sLogin))
				{
					throw new AnwLoginAlreadyTakenException();
				}
				if (!AnwUsers::isAvailableDisplayName($sDisplayName))
				{
					throw new AnwDisplayNameAlreadyTakenException();
				}
				if (!AnwUsers::isAvailableEmail($sEmail))
				{
					throw new AnwEmailAlreadyTakenException();
				}				
				$oUser = AnwUserReal::rebuildUser(-1, $sLogin, $sDisplayName, $sEmail, $sLang, $nTimezone);
			}
		}
		catch(AnwLoginAlreadyTakenException $e)
		{
			$sError = $this->g_("err_loginalreadytaken");
			throw new AnwUnexpectedException($sError);
		}
		catch(AnwBadLoginException $e)
		{
			$sError = $this->g_("err_badlogin");
			throw new AnwUnexpectedException($sError);
		}
		catch(AnwDisplayNameAlreadyTakenException $e)
		{
			$sError = $this->g_("err_displaynamealreadytaken");
			throw new AnwUnexpectedException($sError);
		}
		catch(AnwBadDisplayNameException $e)
		{
			$sError = $this->g_("err_baddisplayname");
			throw new AnwUnexpectedException($sError);
		}
		catch(AnwEmailAlreadyTakenException $e)
		{
			$sError = $this->g_("err_emailalreadytaken");
			throw new AnwUnexpectedException($sError);
		}
		catch(AnwBadEmailException $e)
		{
			$sError = $this->g_("err_bademail");
			throw new AnwUnexpectedException($sError);
		}
		catch(AnwBadPasswordException $e)
		{
			$sError = $this->g_("err_badpassword");
			throw new AnwUnexpectedException($sError);
		}
		return $oUser;
	}
}

?>