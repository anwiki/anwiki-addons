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
 * Plugin: phpBB3 (accounts and sessions synchronization for Single Sign On).
 * @package Anwiki
 * @version $Id: plugin_phpbb3.php 249 2010-02-23 20:27:50Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */

class AnwPluginDefault_phpbb3 extends AnwPlugin implements AnwConfigurable
{
	//some phpBB global vars
	private $db;
	private $user;
	private $config;
	private $auth;
	
	const CFG_PHPBB_ROOT_PATH = "phpbb_root_path";
	
	function getConfigurableSettings()
	{
		$aoSettings = array();
		$oSetting = new AnwContentFieldSettings_system_directory(self::CFG_PHPBB_ROOT_PATH);
		$oSetting->addTestFile("common.php");
		$oSetting->addTestFile("includes/functions_display.php");
		$oSetting->addTestFile("includes/functions_user.php");
		$oSetting->addTestFile("includes/functions_profile_fields.php");
		$aoSettings[] = $oSetting;
		return $aoSettings;
	}
	
	function init()
	{
		global $db, $user, $config, $auth, $phpEx, $cache, $template, $phpbb_root_path; //woooow!
		
		//simulate phpBB environment
		if (!defined('IN_PHPBB')) define('IN_PHPBB',true);
			
		$phpbb_root_path = $this->cfg(self::CFG_PHPBB_ROOT_PATH);
		$phpEx = "php";		
		set_include_path(get_include_path().PATH_SEPARATOR.$phpbb_root_path);
		
		//require phpBB files
		$db = $user = $config = $auth = null;
		
		require_once($phpbb_root_path . "common.php");
		include_once($phpbb_root_path . 'includes/functions_display.php');
		include_once($phpbb_root_path . 'includes/functions_user.php');
		include_once($phpbb_root_path . 'includes/functions_profile_fields.php');
		
		//save phpBB global vars
		$this->db = $db;
		$this->user = $user;
		$this->config = $config;
		$this->auth = $auth;
	}
	
	function hook_check_valid_login($sLogin)
	{
		$config = $this->config;
		
		//ported from includes/ucp/ucp_register.php (around line 174)
		$data = array(
			'username'	=> utf8_normalize_nfc($sLogin)
		);
		
		$error = validate_data(
			$data, 
			array(
				'username'	=> array(
					array('string', false, $config['min_name_chars'], $config['max_name_chars']),
					array('username', '')
				)
			)
		);
		
		//ignore "already taken" error
		foreach ($error as $i => $sError)
		{
			if ($sError == 'USERNAME_TAKEN_USERNAME' || $sError == 'USERNAME_TAKEN')
			{
				unset($error[$i]);
			}
		}
		
		if (count($error) != 0)
		{
			self::debug("bad login: ".implode('/',$error));
			throw new AnwPluginInterruptionException("PHPBB login validation error: ".implode('/', $error));
		}
	}
	
	function hook_check_valid_email($sEmail)
	{
		$config = $this->config;
		
		//ported from includes/ucp/ucp_register.php (around line 177)
		$data = array(
			'email'		=> strtolower($sEmail),
		);
		
		$error = validate_data(
			$data, 
			array(
				'email'	=> array(
					array('string', false, 6, 60),
					array('email')
				)
			)
		);
		
		//ignore "already taken" error
		foreach ($error as $i => $sError)
		{
			if ($sError == 'EMAIL_TAKEN_EMAIL' || $sError == 'EMAIL_TAKEN')
			{
				unset($error[$i]);
			}
		}
		
		if (count($error) != 0)
		{
			self::debug("bad email: ".implode('/',$error));
			throw new AnwPluginInterruptionException("PHPBB email validation error: ".implode('/', $error));
		}
	}
	
	function hook_check_valid_password($sPassword)
	{
		$config = $this->config;
		
		//ported from includes/ucp/ucp_register.php (around line 175)
		$data = array(
			'new_password'	=> $sPassword,
		);
		
		$error = validate_data(
			$data, 
			array(
				'new_password'		=> array(
					array('string', false, $config['min_pass_chars'], $config['max_pass_chars']),
					array('password')),
			)
		);
		
		if (count($error) != 0)
		{
			self::debug("bad password: ".implode('/',$error));
			throw new AnwPluginInterruptionException("PHPBB password validation error: ".implode('/', $error));
		}
	}
	
	function hook_check_available_login($sLogin)
	{
		try
		{
			$user_id = $this->phpbb_get_userid($sLogin);
			
			//login is already used by a phpBB user. Deny account creation to avoid collisions.
			self::debug("login already in use");
			throw new AnwPluginInterruptionException("Login already used by a phpBB user");
		}
		catch(AnwUserNotFoundException $e)
		{
			//ok, we can use this login!
		}
	}
	
	
	/**
	 * 1. Make sure phpBB user exists. If not, create it.
	 * 2. Open phpBB session.
	 */
	function hook_user_loggedin($oUser, $sPassword, $bResume)
	{
		$sUsername = $oUser->getLogin();
		
		try
		{
			try
			{
				//search if phpBB user exists for this Anwiki user
				self::debug("checking user account in phpBB");
				$this->phpbb_open_session($oUser);
			}
			catch(AnwUserNotFoundException $e)
			{
				//if it doesn't exist, create it
				self::debug("unable to open session for phpBB user");
				$sEmail = $oUser->getEmail();
				$phpbb_user_id = $this->phpbb_create_user($sUsername, $sPassword, $sEmail);
				
				//open phpBB session for this new user
				self::debug("trying to open a session for the new phpBB user we created");
				$this->phpbb_open_session($oUser);
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
		$user = $this->user;
		
		//ported from ucp.php (around line 84)
		
		if ($user->data['user_id'] != ANONYMOUS)
		{
			self::debug("logging out");
			$user->session_kill();
			$user->session_begin();			
		}
		else
		{
			self::debug("already logged out");
		}
	}	
	
	function hook_session_keepalive_loggedin($oUser)
	{
		try
		{
			//search if phpBB user exists for this Anwiki user
			self::debug("(keepalive) checking user account in phpBB");
			$this->phpbb_open_session($oUser);
		}
		catch(AnwUserNotFoundException $e)
		{
			//if it doesn't exist, create it
			self::debug("(keepalive) unable to open session for phpBB user");
		}
	}
	
	function hook_user_changed_email($oUser, $sNewEmail)
	{
		self::debug("updating email");
		
		//update user email
		$sql_ary = array(
			'user_email'         => $sNewEmail
		);
		$this->phpbb_update_user($oUser, $sql_ary);
	}
	
	function hook_user_changed_password($oUser, $sPassword)
	{
		self::debug("updating password");
		
		//update user password
		$sql_ary = array(
			'user_password'         => phpbb_hash($sPassword),
			'user_passchg'          => time(),
		);
		$this->phpbb_update_user($oUser, $sql_ary);
	}
	
	/*
	protected function phpbb_open_session($oUser, $sPassword)
	{
		self::debug("phpbb_open_session");
		
		$user = $this->user;
		$auth = $this->auth;
		
				
		//ported from ucp.php (around line 27)
		
		define('IN_LOGIN', true);
		
		$user->session_begin();
		$auth->acl($user->data);
		$user->setup('ucp');		
		
		
		//ported from includes/fonctions.php (around line 2247)
		
		$username	= $oUser->getLogin();
		$autologin	= false;
		$viewonline = 1;
		$admin 		= 0;
		$password = $sPassword;
		
		// If authentication is successful we redirect user to previous page
		$result = $auth->login($username, $password, $autologin, $viewonline, $admin);
		
		if ($result['status'] == LOGIN_SUCCESS)
		{
			//done
			self::debug("phpbb_open_session: success");
		}
		else
		{
			self::debug("phpbb_open_session: failure");
			throw new AnwUserNotFoundException();
		}
	}
	*/
	
	function phpbb_open_session($oUser)
	{
		self::debug("phpbb_open_session");
		
		$user = $this->user;
		$auth = $this->auth;
		
		//ported from ucp.php (around line 27)
		
		define('IN_LOGIN', true);
		
		$user->session_begin();
		$auth->acl($user->data);
		$user->setup('ucp');
		
		$user_id = $this->phpbb_get_userid($oUser->getLogin());
		
		//ported from includes/fonctions.php (around line 838)
		$autologin	= false;
		$viewonline = 1;
		$admin 		= 0;
		$ok = $user->session_create($user_id, $admin, $autologin, $viewonline);
		
		if ($ok)
		{
			//done
			self::debug("phpbb_open_session: success");
		}
		else
		{
			self::debug("phpbb_open_session: failure");
			throw new AnwUserNotFoundException();
		}
	}
	
	protected function phpbb_get_userid($sLogin)
	{
		$db = $this->db;
		
		$sql ='SELECT user_id 
			FROM ' . USERS_TABLE . "
			WHERE username_clean = '" . $db->sql_escape(utf8_clean_string($sLogin)) . "'";
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);
		
		if (!$row)
		{
			//phpBB user not found
			throw new AnwUserNotFoundException("phpbb_get_userid(): phpBB user not found");
		}
				
		$user_id = $row['user_id'];
		return $user_id;
	}
	
	
	
	protected function phpbb_create_user($sUsername, $sPassword, $sEmail)
	{
		self::debug("phpbb_create_user");
		
		$config = $this->config;
		$user = $this->user;
		$db = $this->db;
		
		
		//ported from includes/ucp/ucp_register.php (around line 91)
		
		$cp = new custom_profile();
		
		$error = $cp_data = array();
		
		
		
		//ported from includes/ucp/ucp_register.php (around line 154)
		
		// Try to manually determine the timezone and adjust the dst if the server date/time complies with the default setting +/- 1
		$timezone = date('Z') / 3600;
		$is_dst = date('I');

		if ($config['board_timezone'] == $timezone || $config['board_timezone'] == ($timezone - 1))
		{
			$timezone = ($is_dst) ? $timezone - 1 : $timezone;

			if (!isset($user->lang['tz_zones'][(string) $timezone]))
			{
				$timezone = $config['board_timezone'];
			}
		}
		else
		{
			$is_dst = $config['board_dst'];
			$timezone = $config['board_timezone'];
		}
		
		
		//Take anwiki values, no need to validate it again.
		$data = array(
			'username'			=> utf8_normalize_nfc($sUsername),
			'new_password'		=> $sPassword,
			'password_confirm'	=> $sPassword,
			'email'				=> $sEmail,
			'email_confirm'		=> $sEmail,
			'lang'				=> $user->lang_name,
			'tz'				=> (float) $timezone,
		);
		
		
		//ported from includes/ucp/ucp_register.php (around line 216)
		
		// validate custom profile fields
		$cp->submit_cp_field('register', $user->get_iso_lang_id(), $cp_data, $error);
		
		
		
		
		//ported from includes/ucp/ucp_register.php (around line 280)
		
		// Which group by default?
		$group_name = 'REGISTERED'; //bypass COPPA

		$sql = 'SELECT group_id
			FROM ' . GROUPS_TABLE . "
			WHERE group_name = '" . $db->sql_escape($group_name) . "'
				AND group_type = " . GROUP_SPECIAL;
		$result = $db->sql_query($sql);
		$row = $db->sql_fetchrow($result);
		$db->sql_freeresult($result);

		if (!$row)
		{
			return;
		}

		$group_id = $row['group_id'];
		
		//Bypass COPPA
		$user_type = USER_NORMAL;
		$user_actkey = '';
		$user_inactive_reason = 0;
		$user_inactive_time = 0;
		
		$user_row = array(
			'username'				=> $sUsername,
			'user_password'			=> phpbb_hash($sPassword),
			'user_email'			=> $sEmail,
			'group_id'				=> (int) $group_id,
			'user_timezone'			=> (float) $data['tz'],
			'user_dst'				=> $is_dst,
			'user_lang'				=> $data['lang'],
			'user_type'				=> $user_type,
			'user_actkey'			=> $user_actkey,
			'user_ip'				=> $user->ip,
			'user_regdate'			=> time(),
			'user_inactive_reason'	=> $user_inactive_reason,
			'user_inactive_time'	=> $user_inactive_time,
		);

		// Register user...
		$user_id = user_add($user_row, $cp_data);
		
		return $user_id;
	}
	
	protected function phpbb_update_user($oUser, $sql_ary)
	{
		global $db, $user;
		
		//get phpBB user ID
		$sLogin = $oUser->getLogin();
		
		//update user infos
		$sql = "UPDATE " . USERS_TABLE . " ".
				"SET " . $db->sql_build_array('UPDATE', $sql_ary) . " " .
				"WHERE username_clean = '" . $db->sql_escape(utf8_clean_string($sLogin)) . "'";
		$db->sql_query($sql);
	}
	
	
	protected function debug($str)
	{
		AnwDebug::log('(phpBB3)'.$str);
	}
}

?>