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
 * Plugin: short page names, accessing pages with language detection.
 * @package plugin_shortpagename
 * @version $Id: plugin_shortpagename.php 127 2009-02-08 16:29:17Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */

class AnwPluginDefault_shortpagename extends AnwPlugin
{
	const PREFIX_LANG_DELIMITER = '/'; //end delimiter for lang prefix
	
	/**
	 * Returns preferred existing page from a shortlink.
	 * Ex: "products" will return page named "en/products" or "fr/products" if it exists, or false.
	 */
	private function getPreferredPageFromShortName($sShortPageName)
	{
		$asLangs = AnwComponent::globalCfgLangs();
		
		//does the pagename already has a lang prefix (such as 'en/' or 'fr/')?
		$nLenShortPageName = strlen($sShortPageName);
		foreach ($asLangs as $sLang)
		{
			$nLenLang = strlen($sLang);
			if ($nLenShortPageName > $nLenLang && 
				substr($sShortPageName, 0, $nLenLang) == $sLang &&
				$sShortPageName[$nLenLang] == self::PREFIX_LANG_DELIMITER)
			{
				//language already given, this is not a shortname
				return false;
			}
		}
		
		//no language given, this may be a shortname. Does this page exists with any lang prefix?
		//(we browse langs in order of preference)
		foreach ($asLangs as $sLang)
		{
			$oTestPage = new AnwPageByName($sLang.self::PREFIX_LANG_DELIMITER.$sShortPageName);
			if ($oTestPage->exists())
			{
				//does this page exists in current session lang?
				$sSessionLang = AnwCurrentSession::getLang();
				if ($sLang != $sSessionLang && $oTestPage->getPageGroup()->hasLang($sSessionLang))
				{
					$oTestPage = $oTestPage->getPageGroup()->getPreferedPage($sSessionLang);
				}
				
				return $oTestPage;
			}
		}
		
		//no page found
		return false;
	}
	
	function vhook_page_notfound_secondchance($oPageNull, $sPageName)
	{
		$oExistingPage = $this->getPreferredPageFromShortName($sPageName);
		if ($oExistingPage)
		{
			$oPageNull = $oExistingPage;
		}
		return $oPageNull;
	}
}

?>