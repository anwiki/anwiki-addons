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
 
                    ANWIKI PLUGIN: FLYSPRAY
 accounts and sessions synchronization for Single Sign-On with Flyspray
 _____________________________________________________________________


Please visit http://www.anwiki.com/addons to get last instructions.
If you need help, read http://www.anwiki.com/support



COMPONENT DESCRIPTION
----------------------------------
This plugin synchronizes Anwiki accounts and sessions with Flyspray <http://www.flyspray.org> (Single Sign-On).


COMPONENT REQUIREMENTS
----------------------------------
- A running setup of Flyspray (latest version tested: 0.9.9.5.1)


COMPONENT FEATURES
----------------------------------

- Association is made between Anwiki login and Flyspray login.
  If you already have existing Flyspray accounts, they all must have created an Anwiki account with the same login, before enabling this plugin!
  They won't be able to register a new Anwiki account with their existing Flyspray login when this plugin is enabled, to avoid collisions.

- Anwiki displayname is exported as Flyspray realname.

- When an user logs into Anwiki, a Flyspray account will be created if there is no Flyspray account with this login. Then, the Flyspray session is opened.
  WARNING: When user logs into Flyspray, he won't be logged into Anwiki.
  >> It's recommanded to DISABLE login from Flyspray.

- When user updates his Anwiki profile (email, password), it will automatically update the Flyspray one
  WARNING: When user updates his Flyspray profile, it WON'T update Anwiki's one. 
  >> It's recommanded to DISABLE personal info edition on Flyspray.

- When user registers in Anwiki, a Flyspray account will automatically be created.
  WARNING: When user registers in Flyspray, it WON'T create an Anwiki account.
  >> It's recommanded to DISABLE registration on Flyspray.



INSTALL INSTRUCTIONS
----------------------------------
1. Unzip this component in your Anwiki directory.
2. Go to Anwiki config panel
3. Refresh components list
4. If necessary, configure this component from Anwiki config panel
5. Enable this component from "global > components"


About Flyspray configuration:
If you run Flyspray on a different subdomain than Anwiki, you will have to:
 - setup Flyspray cookies path to /
 - setup Flyspray cookies domain to .yourdomain.com
 - set Anwiki settings "cookies_path" to /
 - set Anwiki settings "cookies_domain" to .yourdomain.com
 - clear your web browser cookies



ADDITIONAL INFORMATION
----------------------------------

This plugin doesn't require any hack to Flyspray files.
However, it's recommanded to disable some of Flyspray functions and redirect to Anwiki's ones:
 * To keep accounts synchronized with Anwiki:
   - register
   - password change
   - email change
 * To keep sessions synchronized with Anwiki:
   - login/logout



---------------------
In scripts/register.php, search:
---------------------
if (!defined('IN_FS')) {

---------------------
Replace with:
---------------------
//BEGIN ANWIKI HACK
//Force users to register from Anwiki
header("Location: http://www.yourwebsite.com/anwiki-folder/?a=register");
exit;
//END ANWIKI HACK

if (!defined('IN_FS')) {




---------------------
In scripts/lostpw.php, search:
---------------------
if (!defined('IN_FS')) {

---------------------
Replace with:
---------------------
//BEGIN ANWIKI HACK
//Force users to change passwords from Anwiki
header("Location: http://www.yourwebsite.com/anwiki-folder/?a=login");
exit;
//END ANWIKI HACK

if (!defined('IN_FS')) {





---------------------
In scripts/authenticate.php, search:
---------------------

if (Req::val('logout')) {
    $user->logout();
    Flyspray::Redirect($baseurl);
}

---------------------
Replace with:
---------------------

if (Req::val('logout')) {

	//BEGIN ANWIKI HACK
	//Force users to logout from Anwiki
	header("Location: http://www.yourwebsite.com/anwiki-folder/?a=logout&redirect=http://www.yourwebsite.com/flyspray-folder/");
	exit;
	//END ANWIKI HACK

    $user->logout();
    Flyspray::Redirect($baseurl);
}

//BEGIN ANWIKI HACK
//Force users to login from Anwiki
header("Location: http://www.yourwebsite.com/anwiki-folder/?a=login&redirect=http://www.yourwebsite.com/flyspray-folder/");
exit;
//END ANWIKI HACK




---------------------
In includes/modify.inc.php, search:
---------------------
    case 'myprofile.edituser':

---------------------
Replace with:
---------------------
    case 'myprofile.edituser':

    	//BEGIN ANWIKI HACK
    	//Disable email and realname change from Flyspray.
    	$_POST['real_name'] = $user->infos['real_name'];
    	$_POST['email_address'] = $user->infos['email_address'];
    	//END ANWIKI HACK




---------------------------------------------
In templates/common.profile.tpl, search:
---------------------------------------------
          <input id="realname" class="text" type="text" name="real_name" size="50" maxlength="100"
            value="{Req::val('real_name', $theuser->infos['real_name'])}" />
                        
---------------------
Replace with:
---------------------
          <!-- BEGIN ANWIKI HACK -->
          {Req::val('real_name', $theuser->infos['real_name'])} 
          <a href="http://www.yourwebsite.com/anwiki-folder/?a=settings">Edit</a>
          <!-- END ANWIKI HACK -->


---------------------------------------------
In templates/common.profile.tpl, search:
---------------------------------------------
          <input id="emailaddress" class="text" type="text" name="email_address" size="50" maxlength="100"
            value="{Req::val('email_address', $theuser->infos['email_address'])}" />
                        
---------------------
Replace with:
---------------------
          <!-- BEGIN ANWIKI HACK -->
          {Req::val('email_address', $theuser->infos['email_address'])} 
          <a href="http://www.yourwebsite.com/anwiki-folder/?a=settings">Edit</a>
          <!-- END ANWIKI HACK -->


---------------------------------------------
In templates/common.profile.tpl, search:
---------------------------------------------
      <?php if (!$user->perms('is_admin') || $user->id == $theuser->id): ?>
      <tr>
        <td><label for="oldpass">{L('oldpass')}</label></td>
        <td><input id="oldpass" class="password" type="password" name="oldpass" value="{Req::val('oldpass')}" size="40" maxlength="100" /></td>
      </tr>
      <?php endif; ?>
      <tr>
        <td><label for="changepass">{L('changepass')}</label></td>
        <td><input id="changepass" class="password" type="password" name="changepass" value="{Req::val('changepass')}" size="40" maxlength="100" /></td>
      </tr>
      <tr>
        <td><label for="confirmpass">{L('confirmpass')}</label></td>
        <td><input id="confirmpass" class="password" type="password" name="confirmpass" value="{Req::val('confirmpass')}" size="40" maxlength="100" /></td>
      </tr>
                        
---------------------
Replace with:
---------------------
      <!-- BEGIN ANWIKI HACK -->
      <tr>
        <td><label for="changepass">{L('changepass')}</label></td>
        <td><a href="http://www.yourwebsite.com/anwiki-folder/?a=settings">Edit</a></td>
      </tr>
      <!-- END ANWIKI HACK -->

