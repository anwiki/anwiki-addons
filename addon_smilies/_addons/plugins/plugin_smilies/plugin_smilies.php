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
 * Plugin: smilies, using custom smilies.
 * @package Anwiki
 * @version $Id: plugin_smilies.php 157 2009-02-14 18:01:20Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */
 
interface AnwISettings_pluginSmiliesEmoticon
{
	const FIELD_CODE = "code";
	const FIELD_IMAGE = "image";
}

class AnwPluginDefault_smilies extends AnwPlugin implements AnwConfigurable
{
	const CFG_EMOTICONS = "emoticons";
	
	const SMILIES_IMG_DIR = 'plugin_smilies/';
	private $aaSmilies;
	
	function getConfigurableSettings()
	{
		$aoSettings = array();
		$oContentField = new AnwContentFieldSettings_pluginSmiliesEmoticon(self::CFG_EMOTICONS);
		$oContentMultiplicity = new AnwContentMultiplicity_multiple();
		$oContentField->setMultiplicity($oContentMultiplicity);
		$aoSettings[] = $oContentField;
		return $aoSettings;
	}
	
	function vhook_outputhtml_clean_body_html($sHtml)
	{
		//only match HTML values, no attributes
		$sPattern = '!>([^<]+?)<!si';
		$sHtml = '>'.$sHtml.'<';
		$sHtml = preg_replace_callback($sPattern, array($this, 'replaceHtmlValues'), $sHtml);
		$sHtml = substr($sHtml, 1, -1);
		return $sHtml;
	}
	
	private function replaceHtmlValues($asMatches)
	{
		$sContentHtml = $asMatches[1];
		
		$aaSmilies = $this->getSmilies();
		$sContentHtml = str_ireplace($aaSmilies['SEARCH'], $aaSmilies['REPLACE'], $sContentHtml);
		return '>'.$sContentHtml.'<';
	}
	
	private function getSmilies()
	{
		if (!$this->aaSmilies)
		{
			$sSmiliesDirUrl = $this->getMyComponentUrlStaticDefault();
			$this->aaSmilies = array('SEARCH'=>array(), 'REPLACE'=>array());
			
			try
			{
				//load configured smilies
				$aaEmoticonsSettings = $this->cfg(self::CFG_EMOTICONS);
				foreach ($aaEmoticonsSettings as $amEmoticonItem)
				{
					$asEmoticonCodes = $amEmoticonItem[AnwISettings_pluginSmiliesEmoticon::FIELD_CODE];
					$sEmoticonImage = $amEmoticonItem[AnwISettings_pluginSmiliesEmoticon::FIELD_IMAGE];
					$sEmoticonImage = trim($sEmoticonImage);
					$sEmoticonImageSrc = (strstr($sEmoticonImage, "://") ? $sEmoticonImage : $this->getMyComponentUrlStaticDefault().$sEmoticonImage );
					
					foreach ($asEmoticonCodes as $sEmoticonCode)
					{
						$sEmoticonCode = trim($sEmoticonCode);
						$sEmoticonHtml = '<img src="'.$sEmoticonImageSrc.'" alt="'.$sEmoticonCode.'" title="'.$sEmoticonCode.'"/>';
						
						$this->aaSmilies['SEARCH'][$sEmoticonCode] = $sEmoticonCode;
						$this->aaSmilies['REPLACE'][$sEmoticonCode] = $sEmoticonHtml;
					}
				}
			}
			catch(AnwException $e)
			{
				self::debug("*ERROR* loading smilies mapping");
			}
		}
		return $this->aaSmilies;
	}
}

?>