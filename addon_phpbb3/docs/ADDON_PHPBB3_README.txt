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

 _____________________________________________________________________
 
                    ANWIKI ADDON: PHPBB3
  accounts and sessions synchronization for Single Sign-On with phpBB3
 _____________________________________________________________________


Please visit http://www.anwiki.com/addons to get last instructions.
If you need help, read http://www.anwiki.com/support



COMPONENT DESCRIPTION
----------------------------------
This plugin synchronizes Anwiki accounts and sessions with phpBB3 <http://www.phpbb.com> (Single Sign-On).


COMPONENT REQUIREMENTS
----------------------------------
- A running setup of phpBB3 (latest version tested: 3.0.4)


COMPONENT FEATURES
----------------------------------

- Association is made between Anwiki login and phpBB login.
  If you already have existing phpBB accounts, they all must have created an Anwiki account with the same login, before enabling this plugin!
  They won't be able to register a new Anwiki account with their existing phpBB login when this plugin is enabled, to avoid collisions.

- When an user logs into Anwiki, a phpBB account will be created if there is no phpBB account with this login. Then, the phpBB session is opened.
  WARNING: When user logs into phpBB, he won't be logged into Anwiki.
  >> It's recommanded to DISABLE login from phpBB.

- When user updates his Anwiki profile (email, password), it will automatically update the phpBB one
  WARNING: When user updates his phpBB profile, it WON'T update Anwiki's one. 
  >> It's recommanded to DISABLE personal info edition on phpBB.

- When user registers in Anwiki, a phpBB account will automatically be created.
  WARNING: When user registers in phpBB, it WON'T create an Anwiki account.
  >> It's recommanded to DISABLE registration on phpBB.



INSTALL INSTRUCTIONS
----------------------------------
1. Unzip this component in your Anwiki directory.
2. Go to Anwiki config panel
3. Refresh components list
4. If necessary, configure this component from Anwiki config panel
5. Enable this component from "global > components"


About phpBB configuration:
If you run phpBB on a different subdomain than Anwiki, you will have to:
 - setup phpBB cookies path to /
 - setup phpBB cookies domain to .yourdomain.com
 - set Anwiki settings "cookies_path" to /
 - set Anwiki settings "cookies_domain" to .yourdomain.com
 - clear your web browser cookies



ADDITIONAL INFORMATION
----------------------------------

This plugin doesn't require any hack to phpBB files.
However, it's recommanded to disable some of phpBB functions and redirect to Anwiki's ones:
 * To keep accounts synchronized with Anwiki:
   - register
   - password change
   - email change
 * To keep sessions synchronized with Anwiki:
   - login/logout



---------------------
In ucp.php, search:
---------------------
// Basic parameter data
$id 	= request_var('i', '');
$mode	= request_var('mode', '');


---------------------
Replace with:
---------------------
// Basic parameter data
$id 	= request_var('i', '');
$mode	= request_var('mode', '');

//BEGIN ANWIKI HACK
//Force users to login/logout from Anwiki
if ($mode == 'login' || $mode == 'logout')
{
        header("Location: http://www.yourwebsite.com/anwiki-folder/?a=".$mode."&redirect=http://yourforum.yourwebsite.com/forum-folder/");
        exit;
}
//END ANWIKI HACK



---------------------
In ucp.php, search:
---------------------
		case 'register':

---------------------
Replace with:
---------------------
		case 'register':
                //BEGIN ANWIKI HACK
                //Force users to register from Anwiki
                header("Location: http://www.yourwebsite.com/anwiki-folder/?a=register");
                exit;
                //END ANWIKI HACK






---------------------------------------------
In includes/ucp/ucp_profile.php, search:
---------------------------------------------
                        case 'reg_details':
                        
---------------------
Replace with:
---------------------
                        case 'reg_details':
                                //BEGIN ANWIKI HACK
                                //Force users to change email and password from Anwiki
                                header("Location: http://www.yourwebsite.com/anwiki-folder/?a=settings");
                                exit;
                                //END ANWIKI HACK

