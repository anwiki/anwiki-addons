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
 * A generic tool to import, export and synchronize any kind of translation files with Anwiki.
 * @package component:action:synctrfiles
 * @version $Id: class_synctrfiles.php 174 2009-04-08 20:00:29Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */

class AnwSyncTrFilesInterruptionException extends AnwException {} //no translation

abstract class AnwSyncTrFiles
{
	const ACTION_IMPORT = "import";
	const ACTION_EXPORT = "export";
	
	private $aaPathsAndNamespaces = array();
	
	function __construct($asPathsAndNamespaces)
	{
		AnwContentClasses::getContentClass("trfile");
		
		$this->aaPathsAndNamespaces = $asPathsAndNamespaces;
	}
	
	function import()
	{
		$this->run(self::ACTION_IMPORT);
	}
	function export()
	{
		$this->run(self::ACTION_EXPORT);
	}
	
	abstract protected function isGoodFile($sFilename, $sLang);
	abstract protected function getGroupName($sFilename, $sLang);
	abstract protected function getTranslationFileName($sGroupName, $sLang);
	abstract protected function getPathImportTranslations($sLang, $sPathImport);
	abstract protected function getPathExportTranslations($sLang, $sPathExport);
	
	abstract protected function getRevFromFile($sTranslationFilePath);
	abstract protected function readTranslationsFromFile($sFilePath);
	abstract protected function generateTranslationFileFromContent($asLang, $nRev, $sWikiName, $sLang);

	/**
	 * Overridable callback called when a page has been exported.
	 */
    function exportPageFinished($oPage, $sPathExport, $sPagesNameSpace){}

	/**
	 * Overridable run process.
	 */
	protected function run($sAction)
	{
		//run synchronization
		$sLang = AnwComponent::globalCfgLangDefault();
		
		foreach ($this->aaPathsAndNamespaces as $asInfo)
		{
			$this->browseTrFiles($sAction, $sLang, $asInfo['PATH_IMPORT'], $asInfo['PATH_EXPORT'], $asInfo['WIKI_NAMESPACE']);
		}
	}
	
	
	function exportPageIfHandled($oPage)
	{
		foreach ($this->aaPathsAndNamespaces as $asInfo)
		{
			$sPagesNameBegin = $oPage->getLang().'/'.$asInfo['WIKI_NAMESPACE'];
			if (substr($oPage->getName(), 0, strlen($sPagesNameBegin)) == $sPagesNameBegin)
			{
				//ok, export this page with this filespath & namespace
				$this->exportPage($oPage, $asInfo['PATH_EXPORT'], $asInfo['WIKI_NAMESPACE']);
			}
		}
	}
	

	/**
	 * Overridable page content generation.
	 */
	protected function generatePageContentFromTranslations($oContent, $asTranslationsBuffer, $sGroupName, $sPagesNameSpace)
	{
		//prepare xml content
		$aoSubContentsItems = array();
		
		//browse translations
		foreach ($asTranslationsBuffer as $sId => $sValue)
		{
			$oContentFieldItems = $oContent->getContentFieldsContainer()->getContentField(AnwContentClassPageDefault_trfile::FIELD_ITEMS);
			$oSubContentItem = new AnwContentPage($oContentFieldItems);
						
			$sValue = trim($sValue); //translation values are always trimed as it's text field...
			$oSubContentItem->setContentFieldValues(AnwContentFieldPage_trfileItem::FIELD_ID, array($sId));
			$oSubContentItem->setContentFieldValues(AnwContentFieldPage_trfileItem::FIELD_VALUE, array($sValue));
			
			$aoSubContentsItems[] = $oSubContentItem;
		}
		
		//set new content
		$oContent->setContentFieldValues(AnwContentClassPageDefault_trfile::FIELD_NAME, array($sPagesNameSpace.$sGroupName));
		$oContent->setSubContents(AnwContentClassPageDefault_trfile::FIELD_ITEMS, $aoSubContentsItems);
		return $oContent;
	}	


	/**
	 * Overridable wiki name formatting.
	 */
	protected function getWikiName($sGroupName, $sLang, $sPagesNameSpace)
	{
		return $sLang.'/'.$sPagesNameSpace.$sGroupName;
	}
	
	function getGroupNameFromPage($oPage, $sPagesNameSpace)
	{
		$sPagesNameBegin = $oPage->getLang().'/'.$sPagesNameSpace;
		return str_replace($sPagesNameBegin, "", $oPage->getName());
	}

	/**
	 * Sync the wiki from ENGLISH translation files, then sync the others langs.
	 */
	protected function browseTrFiles($sAction, $sLang, $sPathImport, $sPathExport, $sPagesNameSpace)
	{
		if ($sAction != self::ACTION_IMPORT && $sAction != self::ACTION_EXPORT)
		{
			print "ERROR: Unknown action ".$sAction;
			exit;
		}
		
		if ($sAction == self::ACTION_IMPORT)
		{
			print "Importing translation files [".$sLang."] : Translation files ----> Wiki[".$sLang."] ----> Sync other langs";
		}
		else
		{
			print "Exporting translation files : Wiki ----> translations files";
		}
		
		print '<h2>'.$sPagesNameSpace.'</h2>';
		print '<p>'.$sPathImport.' --&gt; '.$sPathExport.'</p>';
		print '<ul style="font-size:12px">';
		
		//read all translation files
		$sPathTranslationsImport = $this->getPathImportTranslations($sLang, $sPathImport);
		if (!is_dir($sPathTranslationsImport) || !$mDirHandle = opendir($sPathTranslationsImport))
		{
			print "error reading ".$sPathTranslationsImport;
			return;
		}
		while (false !== ($sFilename = readdir($mDirHandle)))
		{
			if (!in_array($sFilename, array('.', '..', 'CVS')) && $this->isGoodFile($sFilename, $sLang))
			{
	    	   	$sGroupName = $this->getGroupName($sFilename, $sLang);
	        	print '<li>Reading translations from group : '.$sGroupName.' ';
				
				if ($sAction == self::ACTION_IMPORT)
				{
					try
					{
						//read translations from file
						$sFilePathImport = $sPathTranslationsImport.$sFilename;
	        			$mTranslationsBuffer = $this->readTranslationsFromFile($sFilePathImport);
						$this->doImport($sLang, $sGroupName, $mTranslationsBuffer, $sPagesNameSpace);
					}
					catch(AnwSyncTrFilesInterruptionException $e)
					{
						print '(skipping empty file)';
					}
				}
				else
				{
					$this->doExport($sLang, $sGroupName, $sPathExport, $sPagesNameSpace);
				}			
				
				print '</li>';
	        }
		}
		closedir($mDirHandle);
		print '</ul>';
		
		print '</ul><hr/>';
	}
	
	/**
	 * Overridable.
	 */
	protected function getTranslationsFromContent($oContent)
	{
		$asLang = array();
		
		//browse items and build $lang array, from the wiki contents
		$aoContentsTrfileItems = $oContent->getSubContents(AnwContentClassPageDefault_trfile::FIELD_ITEMS);
		foreach ($aoContentsTrfileItems as $oContentTrfileItem)
		{
	        $sTranslationId = $oContentTrfileItem->getContentFieldValue(AnwContentFieldPage_trfileItem::FIELD_ID, 0, true);
	        $sTranslationValue = $oContentTrfileItem->getContentFieldValue(AnwContentFieldPage_trfileItem::FIELD_VALUE);
	        if (!AnwXml::xmlIsUntranslatedTxt($sTranslationValue))
			{
				$asLang[$sTranslationId] = $sTranslationValue;
			}
		}

		if (count($asLang) == 0)
		{
			throw new AnwSyncTrFilesInterruptionException();
		}
		
		return $asLang;
	}

	private function doExport($sLang, $sGroupName, $sFilesPath, $sPagesNameSpace)
	{
		//browse langs excepted reference lang
		$asTranslationsLangs = AnwComponent::globalCfgLangs();
		foreach($asTranslationsLangs as $sTranslationLang)
		{
			if ($sTranslationLang != $sLang)
			{
				$bDoExport = true;
				
				$sWikiName = $this->getWikiName($sGroupName, $sTranslationLang, $sPagesNameSpace);
				$oPage = new AnwPageByName($sWikiName);
				$oPage->setSkipLoadingTranslationsContent(true);
				
				if ($oPage->exists())
				{
					print '<br/>Updating from : '.$sWikiName.' ';flush();
					
					//run export process for this page
					$mReturn = $this->exportPage($oPage, $sFilesPath, $sPagesNameSpace);
					print $mReturn;
				}
				else
				{
					print '<span style="color:red;font-weight:bold">X (page not found : '.$sWikiName.')</span>';
				}
			}
		}
	}
	
	function exportPage($oPage, $sPathExport, $sPagesNameSpace)
	{
		$sTranslationLang = $oPage->getLang();
		$sGroupName = $this->getGroupNameFromPage($oPage, $sPagesNameSpace);
		$sTranslationFilePath = $this->getPathExportTranslations($sTranslationLang, $sPathExport).$this->getTranslationFileName($sGroupName, $sTranslationLang);
		
		//check last build rev
        if (file_exists($sTranslationFilePath))
        {
        	try
        	{
        		$nBuildRev = $this->getRevFromFile($sTranslationFilePath);
        		if ($nBuildRev == $oPage->getChangeId())
	        	{
	        		return '<span style="font-weight:bold; color:green">(up to date, skipping...)</span>';
	        	}
        	}
        	catch(AnwSyncTrFilesInterruptionException $e){}
        }
        
    	try
		{
			$mReturn = "";
			
			//generate translation file content
			$oContent = $oPage->getContent();							
			$asLang = $this->getTranslationsFromContent($oContent);
			
			//update translation file
			$sFileContent = $this->generateTranslationFileFromContent($asLang, $oPage->getChangeId(), $oPage->getName(), $sTranslationLang);
			AnwUtils::file_put_contents($sTranslationFilePath, $sFileContent);
        	
        	$mReturn .= '<pre>'.htmlentities($sFileContent).'</pre>';
			$mReturn .= '--->'.$sTranslationFilePath.'<br/>';
        	$mReturn .= '<span style="color:red;font-weight:bold">v (updating...)</span>';
        	
        	//notify
        	$this->exportPageFinished($oPage, $sPathExport, $sPagesNameSpace);
        	
        	return $mReturn;
		}
		catch(AnwSyncTrFilesInterruptionException $e)
		{
			return '<span style="color:orange;font-weight:bold">X (empty, skipping)</span>';;
		}
	}

	private function doImport($sLang, $sGroupName, $mTranslationsBuffer, $sPagesNameSpace)
	{
		$sWikiName = $this->getWikiName($sGroupName, $sLang, $sPagesNameSpace);
		print '(Importing to : '.$sWikiName.') ';
		
		$oPage = new AnwPageByName($sWikiName);
		
		//create main page if it doesn't exist
		if (!$oPage->exists())
		{
			print '<span style="color:orange;font-weight:bold">(main creating)</span>';
			
			$oContentClass = AnwContentClasses::getContentClass("trfile");
			$oPage = AnwPage::createNewPage($oContentClass, $sWikiName, $sLang, "translation file sync");
		}
		else
		{
			print '(main existing)';
		}
		flush();
		
		$oContent = clone $oPage->getContent(); //clone very important here!
		$oContent = $this->generatePageContentFromTranslations($oContent, $mTranslationsBuffer, $sGroupName, $sPagesNameSpace);
		
		//print '<hr/>';
		//print htmlentities($oPage->getContent()->toXmlString()).'<hr/>';
		//print htmlentities($oContent->toXmlString());exit;
		$oDiff = new AnwDiffs(clone $oPage->getContent()->toXml()->documentElement, clone $oContent->toXml()->documentElement);
		if ($oDiff->hasChanges())
		{
			//save changes
			$oPage->saveEditAndDeploy($oContent, AnwChange::TYPE_PAGE_EDITION, "translation file sync");
			print '<br/><span style="color:orange;font-weight:bold">v (updated)</span>';
		}
		else
		{
			print '<br/><span style="color:green;font-weight:bold">X (up to date)</span>';
		}
	
		//create translations if it doesn't exist
		$aoTranslations = $oPage->getPageGroup()->getPages($oPage);
		$asTranslationsLangs = AnwComponent::globalCfgLangs();
		foreach($asTranslationsLangs as $sTranslationLang)
		{
			if ($sTranslationLang != AnwComponent::globalCfgLangDefault())
			{
				if (!isset($aoTranslations[$sTranslationLang]))
				{
					print '<span style="color:orange;font-weight:bold">('.$sTranslationLang.' : creating translation)</span>';
					$sWikiName = $this->getWikiName($sGroupName, $sTranslationLang, $sPagesNameSpace);
					$oPageTranslation = new AnwPageByName($sWikiName);
					if ($oPageTranslation->exists())
					{
						print '(WARNING:translation name already used, deleting)';
						$oPageTranslation->delete();
					}
					$oPageTranslation = $oPage->createNewTranslation($sWikiName, $sTranslationLang, "translation file sync");
				}
				else
				{
					print '('.$sTranslationLang.' : existing translation)';
				}
				flush();
			}
		}
		print '(done)';
	}
}




/**
 * Translation files containing serialized PHP array.
 */
abstract class AnwSyncTrFilesPhp extends AnwSyncTrFiles
{
	private $vVarName;			//var name in translation files
	
	function __construct($asPathsAndNamespaces, $vVarName)
	{
		parent::__construct($asPathsAndNamespaces);
		$this->vVarName = $vVarName;
	}
	
	protected function getRevFromFile($sTranslationFilePath)
	{
		$anwiki_build = array();
    	require($sTranslationFilePath);
    	if (!isset($anwiki_build['rev']))
    	{
    		throw new AnwSyncTrFilesInterruptionException();
    	}
    	return $anwiki_build['rev'];
	}
	
	protected function readTranslationsFromFile($sFilePath)
	{
		${$this->vVarName} = array(); //defined in lang file
		require($sFilePath);
		$asTranslationsBuffer = ${$this->vVarName};
		if (count($asTranslationsBuffer) == 0)
		{
			throw new AnwSyncTrFilesInterruptionException();
		}
		return $asTranslationsBuffer;
	}
	
	protected function generateTranslationFileFromContent($asLang, $nRev, $sWikiName, $sLang)
	{
		//build php code
		$sPhpCode = '<?php'."\n";
		$sPhpCode .= ' /**'."\n";
		$sPhpCode .= '  * This translation file was generated by Anwiki.'."\n";
		$sPhpCode .= '  * '."\n";
		$sPhpCode .= '  * Generated on: '.Anwi18n::datetime(time())."\n";
		$sPhpCode .= '  * By: '.AnwCurrentSession::getUser()->getLogin()."\n";
		$sPhpCode .= '  * From: '.AnwUtils::escapeQuote($sWikiName)."\n";
		$sPhpCode .= '  * Revision: '.$nRev."\n";
		$sPhpCode .= '  */'."\n";
		
		$sPhpCode .= '$anwiki_build = array('."\n";
		$sPhpCode .= '"from" => "'.AnwUtils::escapeQuote($sWikiName).'",'."\n";
		$sPhpCode .= '"rev" => "'.$nRev.'",'."\n";
		$sPhpCode .= '"time" => "'.time().'",'."\n";
		$sPhpCode .= '"lang" => "'.$sLang.'",'."\n";
		$sPhpCode .= ');'."\n";
		
		$sPhpCode .= '$'.$this->vVarName.' = '.AnwUtils::arrayToPhp($asLang)."\n";
		/*
		$sPhpCode .= '$'.$this->vVarName.' = array('."\n";
		foreach ($lang as $sTranslationId => $sTranslationValue)
		{
			$sPhpCode .= '"'.AnwUtils::escapeQuote($sTranslationId).'" => "'.AnwUtils::escapeQuote($sTranslationValue).'",'."\n"; 
		}
		$sPhpCode .= ');'."\n";*/
		
		
		$sPhpCode .= '?>';
		
		return $sPhpCode;
	}
}




/**
 * Translation files with standard gettext/.po format.
 * Thanks to Julien & Jeremy :)
 */
abstract class AnwSyncTrFilesPo extends AnwSyncTrFiles
{
	protected function getRevFromFile($sTranslationFilePath)
	{
		$sFileContent = AnwUtils::file_get_contents($sTranslationFilePath);
		
		$sPattern = '!# Rev: ([0-9]+?)'."\n".'!';
		if (!preg_match($sPattern, $sFileContent, $asMatches))
		{
    		throw new AnwSyncTrFilesInterruptionException();
		}
		return $asMatches[1];
	}

    protected function readTranslationsFromFile($sFilePath)
    {
    	$sFileContent = AnwUtils::file_get_contents($sFilePath);
		
		$asTranslations = array();
		
		//special regexp for handling \" (backslash-quote) inside strings
		$sPattern = '!msgid "(.*?)([^\\\])"(.*?)msgstr "(.*?)([^\\\])"!si';
		preg_match_all($sPattern, $sFileContent, $asMatches);
		
		foreach($asMatches[1] as $i => $sMsgId)
		{
			$sMsgId = $sMsgId . $asMatches[2][$i];
			$sMsgId = AnwUtils::unescapeQuote($sMsgId);
			
			$sMsgStr = $asMatches[4][$i] . $asMatches[5][$i];			
			$sMsgStr = AnwUtils::unescapeQuote($sMsgStr);
			
			$asTranslations[$sMsgId] = $sMsgStr;
		}
		unset($sFileContent);
		
		if (count($asTranslations) == 0)
		{
		        throw new AnwSyncTrFilesInterruptionException();
		}
		
		return $asTranslations;
    }

	protected function generateTranslationFileFromContent($asLang, $nRev, $sWikiName, $sLang)
    {		
		//file header
		$sPoCode = "#\n";
		$sPoCode .= '# Build on '.Anwi18n::datetime(time())."\n";
		$sPoCode .= '# From: '.$sWikiName."\n";
		$sPoCode .= '# Rev: '.$nRev."\n";
		$sPoCode .= '# Time: '.time()."\n";
		$sPoCode .= '# Lang: '.$sLang."\n";
		$sPoCode .= "#\n";		

		//append translations
		foreach ($asLang as $sKey => $sValue) 
		{
			$sKey = AnwUtils::escapeQuote($sKey);
			$sValue = AnwUtils::escapeQuote($sValue);
			
			$sPoCode .= 'msgid "'.$sKey.'"'."\n";
			$sPoCode .= 'msgstr "'.$sValue.'"'."\n";
			$sPoCode .= "\n";
		}
		
		return $sPoCode;
    }
}

?>