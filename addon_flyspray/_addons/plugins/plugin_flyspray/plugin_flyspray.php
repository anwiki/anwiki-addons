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
 * Plugin: flyspray (accounts and sessions synchronization for Single Sign On).
 * @package Anwiki
 * @version $Id: plugin_flyspray.php 249 2010-02-23 20:27:50Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */

class AnwPluginDefault_flyspray extends AnwPlugin implements AnwConfigurable, AnwDependancyManageable
{
	private $oDb; //database link
	const PLUGIN_PHPBB = "phpbb3";
	
	//flyspray global variable
	private $conf = array();
	
	//flyspray tables names
	const T_USERS_IN_GROUPS = "users_in_groups";
	const T_USERS = "users";
	const T_GROUPS = "groups";
	const T_SEARCHES = "searches";
	
	//flyspray default group ID for users
	const ANON_GROUP = 4;
	
	//flyspray cookies
	const COOKIE_USERID = "flyspray_userid";
	const COOKIE_PASSHASH = "flyspray_passhash";
	const COOKIE_PROJECT = "flyspray_project";
	private static $asCOOKIE_SESSIONNAMES = array( 
						'GetFirefox',
                        'UseLinux',
                        'NoMicrosoft',
                        'ThinkB4Replying',
                        'FreeSoftware',
                        'ReadTheFAQ',
                        'RTFM',
                        'VisitAU',
                        'SubliminalAdvertising',
                      );
    
    const CFG_FLYSPRAY_ROOT_PATH = "flyspray_root_path";
	const CFG_FLYSPRAY_COOKIES_PATH = "flyspray_cookies_path";
	const CFG_FLYSPRAY_COOKIES_DOMAIN = "flyspray_cookies_domain";
	
	function getConfigurableSettings()
	{
		$aoSettings = array();
		$oSetting = new AnwContentFieldSettings_system_directory(self::CFG_FLYSPRAY_ROOT_PATH);
		$oSetting->setMandatory(true);
		$oSetting->addTestFile("flyspray.conf.php");
		$oSetting->addTestFile("includes/class.flyspray.php");
		$oSetting->addTestFile("includes/utf8.inc.php");
		$oSetting->addTestFile("includes/class.backend.php");
		$aoSettings[] = $oSetting;
		
		$aoSettings[] = new AnwContentFieldSettings_string(self::CFG_FLYSPRAY_COOKIES_PATH);
		
		$oSetting = new AnwContentFieldSettings_string(self::CFG_FLYSPRAY_COOKIES_DOMAIN);
		$oSetting->addForbiddenPattern("!^\.yourwebsite\.com!");
		$aoSettings[] = $oSetting;
		return $aoSettings;
	}
	
	function getComponentDependancies()
	{
		$aoDependancies = array();
		/*
		 * Solvable conflict with plugin_phpbb3 :
		 * As reported in http://bugs.flyspray.org/task/1451?opened=1306,
		 * phpbb uses the same functions names (such as utf8_substr, utf8_strlen...) than flyspray.
		 * 
		 * Floele added a fix to flyspray to not declare these functions when they already exists.
		 * That's why we must load plugin_flyspray *after* plugin_phpbb3.
		 */
		$aoDependancies[] = new AnwDependancyConflict($this, AnwComponent::TYPE_PLUGIN, self::PLUGIN_PHPBB, AnwDependancyConflict::SOLUTION_LOAD_AFTER);
		return $aoDependancies;
	}
	
	function init()
	{
		global $UTF8_ALPHA_CHARS;
		
		$sFlysprayRootPath = $this->cfg(self::CFG_FLYSPRAY_ROOT_PATH);
		
		//ported from includes/constants.inc.php (around line 12)
		$sConfigFile = $sFlysprayRootPath.'flyspray.conf.php';
		$this->conf = parse_ini_file($sConfigFile, true);
		
		//require flyspray tools
		define('IN_FS', true);
		define('BASEDIR', $sFlysprayRootPath);
		require_once($sFlysprayRootPath."includes/class.flyspray.php");
		require_once($sFlysprayRootPath."includes/utf8.inc.php");
		require_once($sFlysprayRootPath."includes/class.backend.php");
		
		//disable prefixes for flyspray cookies
		AnwEnv::setCookiesPrefix(self::COOKIE_USERID, "");
		AnwEnv::setCookiesPrefix(self::COOKIE_PASSHASH, "");		
		AnwEnv::setCookiesPrefix(self::COOKIE_PROJECT, "");
		foreach (self::$asCOOKIE_SESSIONNAMES as $sCookieName)
		{
			AnwEnv::setCookiesPrefix($sCookieName, "");
		}
	}
	
	function hook_check_valid_login($sLogin)
	{
		//ported from includes/modify.inc.php (around line 286)
		$user_name = Backend::clean_username($sLogin);
		if ($user_name != $sLogin)
		{
			self::debug("bad login");
			throw new AnwPluginInterruptionException("flyspray login validation error");
		}
	}
	
	function hook_check_valid_email($sEmail)
	{
		//ported from includes/modify.inc.php (around line 276)
		if (!FlySpray::check_email($sEmail))
		{
			self::debug("bad email");
			throw new AnwPluginInterruptionException("flyspray email validation error");
		}
	}
	
	//nothing to do for hook_check_valid_password()
	
	function hook_check_available_login($sLogin)
	{
		//ported from includes/class.flyspray.php (around line 600)
		$q = $this->getDb()->query("SELECT  user_id 
                                FROM  `#PFX#".self::T_USERS."` u 
                                WHERE  u.user_name = ".$this->getDb()->strtosql($sLogin));
		
		//check that user exists
        if ($this->getDb()->num_rows($q) != 0)
        {
        	//login used by a flyspray user. Deny account creation to avoid collisions.
        	throw new AnwPluginInterruptionException();
        }
	}
	
	
	/**
	 * 1. Make sure flyspray user exists. If not, create it.
	 * 2. Open flyspray session.
	 */
	function hook_user_loggedin($oUser, $sPassword, $bResume)
	{
		$sUsername = $oUser->getLogin();
		
		try
		{
			try
			{
				//search if flyspray user exists for this Anwiki user
				self::debug("checking user account in flyspray");
				$this->flyspray_open_session($oUser);
			}
			catch(AnwUserNotFoundException $e)
			{
				//if it doesn't exist, create it
				self::debug("unable to open session for flyspray user");
				$sEmail = $oUser->getEmail();
				$sRealName = $oUser->getDisplayName();
				$this->flyspray_create_user($sUsername, $sPassword, $sEmail, $sRealName);
				
				//open flyspray session for this new user
				self::debug("trying to open a session for the new flyspray user we created");
				$this->flyspray_open_session($oUser);
			}
		}
		catch(AnwException $e)
		{
			//strange problem occurred
			self::debug("unknown error");
		}
	}
	
	function hook_user_loggedout($oUser)
	{
		//ported from includes/class.user.php (around line 291)
		
		$this->flyspray_unsetcookie(self::COOKIE_USERID);
		$this->flyspray_unsetcookie(self::COOKIE_PASSHASH);
		$this->flyspray_unsetcookie(self::COOKIE_PROJECT);
		
		// (Why did they use a so complicated session system?)
		foreach (self::$asCOOKIE_SESSIONNAMES as $sSessionName)
		{
			$this->flyspray_unsetcookie($sSessionName);
		}
	}	
	
	function hook_session_keepalive_loggedin($oUser)
	{
		//reopen session even if already opened
		try
		{
			self::debug("checking user account in flyspray");
			$this->flyspray_open_session($oUser);
		}
		catch(AnwException $e)
		{
			self::debug("unable to open session for flyspray user");
		}
	}
	
	function hook_user_changed_email($oUser, $sNewEmail)
	{
		self::debug("updating email");
		
		//update user email
		$asUpdate = array(
			'email_address'         => $this->getDb()->strtosql($sNewEmail)
		);
		$this->flyspray_update_user($oUser, $asUpdate);
	}
	
	function hook_user_changed_password($oUser, $sPassword)
	{
		self::debug("updating password");
		
		//update user password
		$asUpdate = array(
			'user_pass'         => $this->getDb()->strtosql( $this->flyspray_hash($sPassword) )
		);
		$this->flyspray_update_user($oUser, $asUpdate);
	}
	
	function hook_user_changed_displayname($oUser, $sNewDisplayName)
	{
		self::debug("updating realname from displayname");
		
		$sNewDisplayName = $this->flyspray_clean_realname($sNewDisplayName);
		
		//update user realname
		$asUpdate = array(
			'real_name'         => $this->getDb()->strtosql($sNewDisplayName)
		);
		$this->flyspray_update_user($oUser, $asUpdate);
	}
	
	
	protected function flyspray_open_session($oUser)
	{
		self::debug("flyspray_open_session");
		
		$username = $oUser->getLogin();
		
		$conf = $this->conf;
		
		//ported from scripts/authenticate.php (around line 17)
		
		//ported from includes/class.flyspray.php (around line 600)
		$q = $this->getDb()->query("SELECT  u.user_id, u.user_pass 
                                FROM  `#PFX#".self::T_USERS_IN_GROUPS."` uig
                           LEFT JOIN  `#PFX#".self::T_GROUPS."` g ON uig.group_id = g.group_id
                           LEFT JOIN  `#PFX#".self::T_USERS."` u ON uig.user_id = u.user_id
                               WHERE  u.user_name = ".$this->getDb()->strtosql($username)." AND g.project_id = 0
                            ORDER BY  g.group_id ASC");
		
		//check that user exists
        if ($this->getDb()->num_rows($q) != 1)
        {
        	//user not found
        	throw new AnwUserNotFoundException();
        }
        
        //ok, account exists. Now, check the password.
        $asData = $this->getDb()->fto($q);
        $nUserId = $asData->user_id;
        $sUserPassHash = $asData->user_pass;
        
        //we don't check password as we assume that flyspray account belongs to anwiki account even if passwords mismatch
		
		//authentication success
		self::debug("flyspray_open_session: success");
		
		$cookie_time = 0; // Set cookies to expire when session ends (browser closes)
		
		// Set a couple of cookies
		$passweirded = md5($sUserPassHash . $conf['general']['cookiesalt']);
		// --cookies will be set a few lines below
		
		// ported from includes/class.flyspray.php (around line 729)
		$this->flyspray_setcookie(self::COOKIE_USERID, $nUserId, $cookie_time);
		$this->flyspray_setcookie(self::COOKIE_PASSHASH, $passweirded, $cookie_time);
	}
	
	protected function flyspray_setcookie($sCookieName, $sValue, $nExpires)
	{
		AnwEnv::putCookie($sCookieName, $sValue, $nExpires, $this->cfg(self::CFG_FLYSPRAY_COOKIES_PATH), $this->cfg(self::CFG_FLYSPRAY_COOKIES_DOMAIN));
	}
	
	protected function flyspray_unsetcookie($sCookieName)
	{
		AnwEnv::unsetCookie($sCookieName, $this->cfg(self::CFG_FLYSPRAY_COOKIES_PATH), $this->cfg(self::CFG_FLYSPRAY_COOKIES_DOMAIN));
	}
	
	protected function getDb()
	{
		if (!$this->oDb)
		{
			//database link
			$this->oDb = AnwMysql::getInstance(
							$this->conf['database']['dbuser'], 
							$this->conf['database']['dbpass'], 
							$this->conf['database']['dbhost'], 
							$this->conf['database']['dbname'], 
							$this->conf['database']['dbprefix']);
		}
		return $this->oDb;		
	}
	
	protected function flyspray_hash($password)
	{
	    $conf = $this->conf;
	    
	    //ported from includes/class.flyspray.php (around line 578)
	    
        $pwcrypt = $conf['general']['passwdcrypt'];

        if (strtolower($pwcrypt) == 'sha1') {
            return sha1($password);
        } elseif (strtolower($pwcrypt) == 'md5') {
            return md5($password);
        } else {
            return crypt($password);
        }
	}
	
	
	protected function flyspray_clean_realname($sRealName)
	{
		// Limit length
        $sRealName = substr(trim($sRealName), 0, 100);
        // Remove doubled up spaces and control chars
        $sRealName = preg_replace('![\x00-\x1f\s]+!u', ' ', $sRealName);
        return $sRealName;
	}
	
	
	protected function flyspray_create_user($sUsername, $sPassword, $sEmail, $sRealName)
	{
		self::debug("flyspray_create_user");
		
		$oDb = $this->getDb();
				
		//ported from includes/class.backend.php (around line 445)
		
		$sRealName = $this->flyspray_clean_realname($sRealName);
		
		$amInsert = array(
			'user_name'			=>	$oDb->strtosql($sUsername),
			'user_pass'			=>	$oDb->strtosql($this->flyspray_hash($sPassword)),
			'real_name'			=> 	$oDb->strtosql($sRealName), 
			'jabber_id'			=>	$oDb->strtosql(''),
			'magic_url'			=>	$oDb->strtosql(''),
			'email_address'		=>	$oDb->strtosql(strtolower($sEmail)),
			'notify_type'		=>	$oDb->inttosql(0),
			'account_enabled'	=>	$oDb->inttosql(1),
			'tasks_perpage'		=>	$oDb->inttosql(25),
			'register_date'		=>	$oDb->inttosql(time()),
			'time_zone'			=>	$oDb->inttosql(0)
		);
		$q = $oDb->do_insert($amInsert, self::T_USERS);
		
		// Get this user's id for the record
        $uid = $oDb->insert_id();
        
        // Now, create a new record in the users_in_groups table
        $amInsert = array(
        	'user_id'			=>	$oDb->inttosql($uid),
        	'group_id'			=>	$oDb->inttosql(self::ANON_GROUP)
        );
		$q = $oDb->do_insert($amInsert, self::T_USERS_IN_GROUPS);
		
		// Set some weird stuff / would be great if guys would have put this into a function...
		$iwatch = $atome = $iopened = "";
		// -- BEGIN OF COPY/PASTE --
		$varnames = array('iwatch','atome','iopened');
        $toserialize = array('string' => NULL,
                        'type' => array (''),
                        'sev' => array (''),
                        'due' => array (''),
                        'dev' => NULL,
                        'cat' => array (''),
                        'status' => array ('open'),
                        'order' => NULL,
                        'sort' => NULL,
                        'percent' => array (''),
                        'opened' => NULL,
                        'search_in_comments' => NULL,
                        'search_for_all' => NULL,
                        'reported' => array (''),
                        'only_primary' => NULL,
                        'only_watched' => NULL);


            foreach($varnames as $tmpname) {

                if($tmpname == 'iwatch') {

                    $tmparr = array('only_watched' => '1');

                } elseif ($tmpname == 'atome') {

                    $tmparr = array('dev'=> $uid);

                } elseif($tmpname == 'iopened') {

                    $tmparr = array('opened'=> $uid);
                }

                $$tmpname = $tmparr + $toserialize;
            }
		// -- END OF COPY/PASTE --
		
		// Now give him his default searches
		$amInsert = array(
			'user_id'			=>	$oDb->inttosql($uid),
			'name'				=>	$oDb->strtosql('taskswatched'),
			'search_string'		=>	$oDb->strtosql(serialize($iwatch)),
			'time'				=>	$oDb->inttosql(time())
		);
		$oDb->do_insert($amInsert, self::T_SEARCHES);
		
		$amInsert = array(
			'user_id'			=>	$oDb->inttosql($uid),
			'name'				=>	$oDb->strtosql('assignedtome'),
			'search_string'		=>	$oDb->strtosql(serialize($atome)),
			'time'				=>	$oDb->inttosql(time())
		);
		$oDb->do_insert($amInsert, self::T_SEARCHES);
		
		$amInsert = array(
			'user_id'			=>	$oDb->inttosql($uid),
			'name'				=>	$oDb->strtosql('tasksireported'),
			'search_string'		=>	$oDb->strtosql(serialize($iopened)),
			'time'				=>	$oDb->inttosql(time())
		);
		$oDb->do_insert($amInsert, self::T_SEARCHES);
	}
	
	protected function flyspray_update_user($oUser, $amUpdate)
	{
		$oDb = $this->getDb();
		
		//get flyspray username
		$sUserName = $oUser->getLogin();
		
		//update user infos
		$oDb->do_update($amUpdate, 
						self::T_USERS, 
						"WHERE user_name=".$oDb->strtosql($sUserName));
	}
	
	
	protected function debug($str)
	{
		AnwDebug::log('(flyspray)'.$str);
	}
}

?>