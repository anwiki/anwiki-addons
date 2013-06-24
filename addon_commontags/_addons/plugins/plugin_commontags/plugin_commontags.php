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
 * Plugin: commontags, using custom tags.
 * @package Anwiki
 * @version $Id: plugin_commontags.php 140 2009-02-08 23:16:19Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */

interface AnwISettings_pluginCommontagsSimpleItem
{
	const FIELD_SEARCH = "search";
	const FIELD_REPLACE = "replace";
}
interface AnwISettings_pluginCommontagsCallbackItem
{
	const FIELD_SEARCH = "search";
	const FIELD_REPLACE = "replace";
}

class AnwPluginDefault_commontags extends AnwPlugin implements AnwConfigurable
{
	const CFG_SIMPLE_ITEMS = "simple_items";
	const CFG_CALLBACK_ITEMS = "callback_items";
	
	private $aaTagsReplace;
	private $aaTagsCallbacks;
	private $asPluginBinds;
	
	function getConfigurableSettings()
	{
		$aoSettings = array();
		$oContentField = new AnwContentFieldSettings_pluginCommontagsSimpleItem(self::CFG_SIMPLE_ITEMS);
		$oContentMultiplicity = new AnwContentMultiplicity_multiple();
		$oContentField->setMultiplicity($oContentMultiplicity);
		$aoSettings[] = $oContentField;
		
		$oContentField = new AnwContentFieldSettings_pluginCommontagsCallbackItem(self::CFG_CALLBACK_ITEMS);
		$oContentMultiplicity = new AnwContentMultiplicity_multiple();
		$oContentField->setMultiplicity($oContentMultiplicity);
		$aoSettings[] = $oContentField;		
		
		return $aoSettings;
	}
	
	function vhook_outputhtml_clean_body_html($sHtml)
	{
		//load tags
		$this->loadTags();
		
		//TagsReplace
		$sHtml = preg_replace($this->aaTagsReplace['SEARCH'], $this->aaTagsReplace['REPLACE'], $sHtml);
		
		//TagsCallbacks (they are only loaded when phpeval is enabled)
		foreach ($this->aaTagsCallbacks as $asTagCallback)
		{
			$this->callbackCode = $asTagCallback['CALLBACK']; 
			$sHtml = preg_replace_callback(
				$asTagCallback['SEARCH'], 
				array($this, 'cbk_tagscallback'), //create_function('$a', $asTagCallback['CALLBACK']),
				$sHtml
			);
			$this->callbackCode = null;
		}
		
		//Plugin binds
		$sHtml = preg_replace_callback(
			'!<pluginbind_([a-zA-Z_]*?)>(.*?)</pluginbind_([a-zA-Z_]*?)>!si', 
			array($this, 'cbk_pluginbind'), //create_function('$a', $asTagCallback['CALLBACK']),
			$sHtml
		);
		
		//replace i18n : {t_("blah")} and {g_("blah")}...
		$sRegexp = '/{([t|g])_\("([^"]*?)"\)}/si';
		$sHtml = preg_replace_callback($sRegexp, array($this, 'cbk_i18n'), $sHtml);
		return $sHtml;
	}
	
	function cbk_i18n($a)
	{
		$sTranslationType = $a[1];
		$sTranslationName = $a[2];
		
		if ($sTranslationType == 't')
		{
			return $this->t_($sTranslationName);
		}
		else
		{
			return $this->g_($sTranslationName);
		}
	}
	
	function cbk_tagscallback($a)
	{
		$sReturn = "";
		try{
			$sReturn = AnwUtils::evalMixedPhpCode($this->callbackCode, array('a'=>$a, 'me'=>$this));
		}
		catch(AnwException $e){}
		return $sReturn;
	}
	
	function cbk_pluginbind($a)
	{
		$sMethodName = 'pluginbind_'.$a[1];
		$sHtml = $a[2];
		
		if (method_exists($this,$sMethodName))
		{
			$sHtml = call_user_func(array($this,$sMethodName), $sHtml);
		}
		return $sHtml;
	}
	
	/**
	 * Plugin bind for rendering code. 
	 * You can add your own plugin binds while overriding this plugin, and defining new functions starting with 'pluginbind_'.
	 */
	protected static function pluginbind_renderCode($sCode)
	{
		$sCode = AnwUtils::escapeTags($sCode);
		
		//strings
		$sCode = preg_replace('!"(.*?)"!m', '<span style="color:#4743f4; font-weight:bold;">"$1"</span>', $sCode);
		$sCode = preg_replace('!\'(.*?)\'!m', '<span style="color:#4743f4; font-weight:bold;">\'$1\'</span>', $sCode);
		
		//comments
		$sCode = preg_replace('!//(.*)$!m', '<span style="color:#07a616;">//$1</span>', $sCode);
		$sCode = preg_replace_callback('!/\*(.*?)\*//*!s', array('self','parseCodeMultiLineComment'), $sCode);
		$sCode = preg_replace('!^(.{2,}?)$!m', '<li>$1</li>', $sCode);
		
		return $sCode;
	}
	
	protected static function parseCodeMultiLineComment($asMatches)
	{
		$OPEN = '<span style="color:#07a616;">';
		$CLOSE = '</span>';
		$sCode = $asMatches[1];
		$sCode = explode("\n",$sCode);
		$sCode = $OPEN.'/*'.implode($CLOSE."\n".$OPEN, $sCode).'*//*'.$CLOSE;
		return $sCode;
	}
	
	private function loadTags()
	{
		if (!$this->aaTagsReplace)
		{
			$this->aaTagsReplace = array('SEARCH'=>array(), 'REPLACE'=>array());
			$this->aaTagsCallbacks = array();
			
			try
			{
				//load configured replacement items
				
				//simple replacements
				$aaTagsItems = $this->cfg(self::CFG_SIMPLE_ITEMS);
				foreach ($aaTagsItems as $amTagItem)
				{
					$asTagSearches = $amTagItem[AnwISettings_pluginCommontagsSimpleItem::FIELD_SEARCH];
					$sTagReplace = $amTagItem[AnwISettings_pluginCommontagsSimpleItem::FIELD_REPLACE];
					$sTagReplace = trim($sTagReplace);
					
					foreach ($asTagSearches as $sTagSearch)
					{
						$sTagSearch = trim($sTagSearch);
						//$this->aaTagsCallbacks[$sTagSearch] = array('SEARCH' => $sTagSearch, 'CALLBACK' => $sTagReplace);
						$this->aaTagsReplace['SEARCH'][] = $sTagSearch;
						$this->aaTagsReplace['REPLACE'][] = $sTagReplace;
					}
				}
				
				//callbacks - only when php eval is enabled
				if (AnwUtils::isPhpEvalEnabled())
				{
					$aaTagsCallbacks = $this->cfg(self::CFG_CALLBACK_ITEMS);
					foreach ($aaTagsCallbacks as $amTagCallback)
					{
						$asTagSearches = $amTagCallback[AnwISettings_pluginCommontagsCallbackItem::FIELD_SEARCH];
						$sTagReplace = $amTagCallback[AnwISettings_pluginCommontagsCallbackItem::FIELD_REPLACE];
						$sTagReplace = trim($sTagReplace);
						
						foreach ($asTagSearches as $sTagSearch)
						{
							$sTagSearch = trim($sTagSearch);
							$this->aaTagsCallbacks[$sTagSearch] = array('SEARCH' => $sTagSearch, 'CALLBACK' => $sTagReplace);
						}
					}
				}
				
			}
			catch(AnwException $e)
			{
				self::debug("*ERROR* loading tags mapping");
			}
		}
	}
}

?>