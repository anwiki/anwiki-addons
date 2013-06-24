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
 * ContentClass: addon.
 * @package Anwiki
 * @version $Id: contentclass_anwikiaddon.php 173 2009-04-08 19:58:01Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */

class AnwContentFieldPage_anwikiaddonversion extends AnwContentFieldPage_container
{
	const FIELD_NAME = "name";
	const FIELD_DATE = "date";
	const FIELD_ANWIKIRELEASE = "anwikirelease";
	
	const PUB_NAME = "name";
	const PUB_DATE = "date";
	const PUB_ANWIKIRELEASE = "anwikirelease";
	
	const INDEX_NAME = "name";
	const INDEX_DATE = "date";
	const INDEX_ANWIKIRELEASE = "anwikirelease";
	
	const ANWIKIRELEASE_CLASS = "anwikirelease";
	
	const PUB_DOWNLOAD_ZIP = "zip";
	const PUB_DOWNLOAD_TGZ = "tgz";
	const PUB_DOWNLOAD_REPOSITORY = "repository";
	
	const DOWNLOAD_REPOSITORY = "http://download.tuxfamily.org/anwiki/addons/";
	
	
	function init()
	{
		// addon version name
		$oContentField = new AnwContentFieldPage_string( self::FIELD_NAME );
		$oContentField->indexAs( self::INDEX_NAME );
		$oContentField->setTranslatable(false);
		$this->addContentField($oContentField);
		
		// date
		$oContentField = new AnwContentFieldPage_date( self::FIELD_DATE );
		$oContentField->indexAs( self::INDEX_DATE );
		$oContentField->setTranslatable(false);
		$this->addContentField($oContentField);
		
		// anwiki release
		$oFetchingContentClass = AnwContentClasses::getContentClass(self::ANWIKIRELEASE_CLASS);
		$oContentField = new AnwContentFieldPage_pageGroup( self::FIELD_ANWIKIRELEASE, $oFetchingContentClass );
		$oContentField->setTranslatable(false);
		$oContentMultiplicity = new AnwContentMultiplicity_multiple();
		$oContentField->setMultiplicity($oContentMultiplicity);
		$oContentField->indexAs(self::INDEX_ANWIKIRELEASE);
		$this->addContentField($oContentField);
	}
	
	function pubcall($sArg, $oContent, $oPage)
	{
		switch($sArg)
		{
			case self::PUB_NAME:
				return $oContent->getContentFieldValue(self::FIELD_NAME);
				break;
			
			case self::PUB_DATE:
				return AnwUtils::dateToTime($oContent->getContentFieldValue(self::FIELD_DATE));
				break;
			
			case self::PUB_ANWIKIRELEASE:
				return getAnwikiReleases($oContent, $oPage);
				break;
			
			case self::PUB_DOWNLOAD_REPOSITORY:
				return self::DOWNLOAD_REPOSITORY;
				break;
			
			case self::PUB_DOWNLOAD_ZIP:
				return $this->getDownloadLinkZip($oContent->getContentFieldValue(self::FIELD_SHORTNAME));
				break;
			
			case self::PUB_DOWNLOAD_TGZ:
				return $this->getDownloadLinkTgz($oContent->getContentFieldValue(self::FIELD_SHORTNAME));
				break;
		}
	}
	
	public static function getDownloadLinkZip($sAddonName, $sAddonVersionName)
	{
		return self::DOWNLOAD_REPOSITORY.$sAddonName."/".$sAddonName."-".$sAddonVersionName.".zip";
	}
	
	public static function getDownloadLinkTgz($sAddonName, $sAddonVersionName)
	{
		return self::DOWNLOAD_REPOSITORY.$sAddonName."/".$sAddonName."-".$sAddonVersionName.".tar.gz";
	}
	
	public static function getAnwikiReleases($oContent, $sLang)
	{
		$aoPageReleases = array();
		$anAnwikiReleasePageGroupIds = $oContent->getContentFieldValues( self::FIELD_ANWIKIRELEASE );
		foreach ($anAnwikiReleasePageGroupIds as $nAnwikiReleasePageGroupId)
		{
			try
			{
				$oAnwikiReleaseGroup = new AnwPageGroup($nAnwikiReleasePageGroupId, self::ANWIKIRELEASE_CLASS);
				if ($oAnwikiReleaseGroup->exists())
				{
					//get release in current lang if available
					$aoPageReleases[] = $oAnwikiReleaseGroup->getPreferedPage($sLang);
				}
			}
			catch(AnwException $e){}
		}
		return $aoPageReleases;
	}
}

class AnwContentClassPageDefault_anwikiaddon extends AnwContentClassPage implements AnwDependancyManageable, AnwIContentClassPageDefault_anwikiaddon
{
	const ADDONCATEGORY_CLASS = "anwikiaddoncategory";
	const ADDON_CLASS = "anwikiaddon";
	
	const CSS_FILE = 'contentclass_anwikiaddon.css';
	
	
	function getComponentDependancies()
	{
		$aoDependancies = array();
		/*
		 * Depends on contentclass_anwikiaddoncategory.
		 */
		$aoDependancies[] = new AnwDependancyRequirement($this, AnwComponent::TYPE_CONTENTCLASS, self::ADDONCATEGORY_CLASS);
		$aoDependancies[] = new AnwDependancyRequirement($this, AnwComponent::TYPE_CONTENTCLASS, AnwContentFieldPage_anwikiaddonversion::ANWIKIRELEASE_CLASS);
		return $aoDependancies;
	}
	
	function init()
	{
		// addon name
		$oContentField = new AnwContentFieldPage_string( self::FIELD_NAME );
		$oContentField->indexAs(self::PUB_NAME);
		$oContentField->setTranslatable(false);
		$this->addContentField($oContentField);
		
		// addon title
		$oContentField = new AnwContentFieldPage_string( self::FIELD_TITLE );
		$this->addContentField($oContentField);
		
		// addon description
		$oContentField = new AnwContentFieldPage_xhtml( self::FIELD_DESCRIPTION );
		$this->addContentField($oContentField);
		
		// addon body
		$oContentField = new AnwContentFieldPage_xhtml( self::FIELD_BODY );
		$oContentField->setDynamicParsingAllowed(true);
		$oContentField->setDynamicPhpAllowed(true);
		$this->addContentField($oContentField);

		// addon categories
		$oFetchingContentClass = AnwContentClasses::getContentClass(self::ADDONCATEGORY_CLASS);
		$oContentField = new AnwContentFieldPage_pageGroup( self::FIELD_CATEGORIES, $oFetchingContentClass );
		$oContentField->setTranslatable(false);
		$oContentMultiplicity = new AnwContentMultiplicity_multiple();
		$oContentField->setMultiplicity($oContentMultiplicity);
		$oContentField->indexAs(self::PUB_CATEGORIES);
		$this->addContentField($oContentField);
				
		// versions
		$oContentField = new AnwContentFieldPage_anwikiaddonversion(self::FIELD_VERSIONS);
		$oMultiplicity = new AnwContentMultiplicity_multiple(0, 999);
		$oContentField->setMultiplicity($oMultiplicity);
		$this->addContentField($oContentField);
	}
	
	function toHtml($oContent, $oPage)
	{
		$oOutputHtml = new AnwOutputHtml( $oPage );
		$oOutputHtml->setTitle( $oContent->getContentFieldValue(self::FIELD_NAME) );
		
		//load contentclass CSS
		$oOutputHtml->setHead( $this->getCssSrcComponent(self::CSS_FILE) );
		
		$sAddonName = $oContent->getContentFieldValue( self::FIELD_NAME );
		$sAddonTitle = $oContent->getContentFieldValue( self::FIELD_TITLE );
		$sAddonDescription = $oContent->getContentFieldValue( self::FIELD_DESCRIPTION );
		$sAddonBody = $oContent->getContentFieldValue( self::FIELD_BODY );
		
		//render categories
		$aoCategoriesPages = self::getCategoriesPages($oContent, $oPage);
		$sHtmlCategories = "";
		if (count($aoCategoriesPages)>0)
		{
			$sHtmlCategories .= $this->tpl()->categoriesStart();
			foreach($aoCategoriesPages as $oCategoryPage)
			{
				$oCategoryContent = $oCategoryPage->getContent();
				$sCategoryTitle = $oCategoryContent->getContentFieldValue( AnwIContentClassPageDefault_anwikiaddoncategory::FIELD_TITLE );
				$sCategoryUrl = AnwUtils::link($oCategoryPage);
				$sHtmlCategories .= $this->tpl()->categoriesItem($sCategoryTitle, $sCategoryUrl);
			}
			$sHtmlCategories .= $this->tpl()->categoriesEnd();
		}
		unset($aoCategoriesPages);
		
		//addon versions...
		$aoSubContentsVersions = $oContent->getSubContents(self::FIELD_VERSIONS);
		$sHtmlVersions = "";
		if (count($aoSubContentsVersions)>0)
		{
			$sHtmlVersions .= $this->tpl()->versionsStart();
			foreach ($aoSubContentsVersions as $oSubContentVersion)
			{
				$sAddonVersionName = $oSubContentVersion->getContentFieldValue(AnwContentFieldPage_anwikiaddonversion::FIELD_NAME);
				$sAddonVersionDate = Anwi18n::date( AnwUtils::dateToTime($oSubContentVersion->getContentFieldValue(AnwContentFieldPage_anwikiaddonversion::FIELD_DATE), $oPage->getLang()) );
				
				$sAddonVersionDownloadZip = AnwContentFieldPage_anwikiaddonversion::getDownloadLinkZip($sAddonName, $sAddonVersionName);
				$sAddonVersionDownloadTgz = AnwContentFieldPage_anwikiaddonversion::getDownloadLinkTgz($sAddonName, $sAddonVersionName);
				
				$sAnwikiReleasesHtml = "";
				$aoPageAnwikiReleases = AnwContentFieldPage_anwikiaddonversion::getAnwikiReleases($oSubContentVersion, $oPage->getLang());
				if (count($aoPageAnwikiReleases > 0))
				{
					$sAnwikiReleasesHtml .= $this->tpl()->anwikiReleasesStart();
					foreach ($aoPageAnwikiReleases as $oPageAnwikiRelease)
					{
						$oContentRelease = $oPageAnwikiRelease->getContent();
						$sAnwikiReleaseName = $oContentRelease->getContentFieldValue(AnwContentClassPageDefault_anwikirelease::FIELD_VERSION_NAME);
						$sAnwikiReleaseLink = AnwUtils::link($oPageAnwikiRelease);
						$sAnwikiReleasesHtml .= $this->tpl()->anwikiReleasesItem($sAnwikiReleaseName, $sAnwikiReleaseLink);
					}
					$sAnwikiReleasesHtml .= $this->tpl()->anwikiReleasesEnd();
				}				
				$sHtmlVersions .=  $this->tpl()->versionsItem($sAddonVersionName, $sAddonVersionDate, $sAnwikiReleasesHtml, $sAddonVersionDownloadZip, $sAddonVersionDownloadTgz);
			}
			$sHtmlVersions .= $this->tpl()->versionsEnd();
		}
		else
		{
			$sHtmlVersions .= $this->tpl()->versionsNone();
		}		
		
		//render the addon
		$sHtmlBody = $this->tpl()->showAddon($sAddonName, $sAddonTitle, $sAddonDescription, $sAddonBody, $sHtmlCategories, $sHtmlVersions, $oPage->getLang());
		
		$oOutputHtml->setBody( $sHtmlBody );
		return $oOutputHtml;
	}
	
	protected static function getCategoriesPages($oContent, $oPage)
	{
		$anCategoriesPageGroupIds = $oContent->getContentFieldValues( self::FIELD_CATEGORIES );
		$aoCategoriesPage = array();
		foreach ($anCategoriesPageGroupIds as $nCategoryPageGroupId)
		{
			try
			{
				$oCategoryPageGroup = new AnwPageGroup($nCategoryPageGroupId, self::ADDON_CLASS);
				if ($oCategoryPageGroup->exists())
				{
					//get category in current lang if available
					$oPageAddon = $oCategoryPageGroup->getPreferedPage($oPage->getLang());
					$aoCategoriesPage[] = $oPageAddon;
				}
			}
			catch(AnwException $e){}
		}
		return $aoCategoriesPage;
	}
	
	function toFeedItem($oContent, $oPage)
	{
		$oFeedItem = new AnwFeedItem(
			$oContent->getContentFieldValue(self::FIELD_NAME, 0, true),
			AnwUtils::link($oPage),
			$oContent->getContentFieldValue(self::FIELD_DESCRIPTION, 0, true)
		);
		return $oFeedItem;
	}
	
	function pubcall($sArg, $oContent, $oPage)
	{
		switch($sArg)
		{
			//TODO: executeHtmlAndPhpCode
			case self::PUB_NAME:
				return $oContent->getContentFieldValue(self::FIELD_NAME);
				break;
			
			case self::PUB_TITLE:
				return $oContent->getContentFieldValue(self::FIELD_TITLE);
				break;
			
			case self::PUB_DESCRIPTION:
				return $oContent->getContentFieldValue(self::FIELD_DESCRIPTION);
				break;
			
			case self::PUB_BODY:
				return $oContent->getContentFieldValue(self::FIELD_BODY);
				break;
			
			case self::PUB_VERSIONS:
				return $oContent->getSubContents(self::FIELD_VERSIONS);
				break;
			
			case self::PUB_DOWNLOAD_REPOSITORY:
				return self::DOWNLOAD_REPOSITORY;
				break;
			
			case self::PUB_DOWNLOAD_ZIP:
				return $this->getDownloadLinkZip($oContent->getContentFieldValue(self::FIELD_SHORTNAME));
				break;
			
			case self::PUB_DOWNLOAD_TGZ:
				return $this->getDownloadLinkTgz($oContent->getContentFieldValue(self::FIELD_SHORTNAME));
				break;
				
			case self::PUB_DOWNLOAD_BUTTON:
				
				$sReleaseVersion = $oContent->getContentFieldValue(self::FIELD_VERSION_NAME);
				$sReleaseTitle = $this->getReleaseTitle($sReleaseVersion, $oContent);
				
				$sReleaseFileName = $oContent->getContentFieldValue(self::FIELD_SHORTNAME);
				$sReleaseDownloadZip = $this->getDownloadLinkZip($sReleaseFileName);
				$sReleaseDownloadTgz = $this->getDownloadLinkTgz($sReleaseFileName);
				
				$sReleaseDate = Anwi18n::date( AnwUtils::dateToTime($oContent->getContentFieldValue(self::FIELD_DATE), $oPage->getLang()) );
				$sReleaseMyLink = AnwUtils::link($oPage->getName());
				return $this->tpl()->downloadButton($oPage->getLang(), $sReleaseVersion, $sReleaseMyLink, $sReleaseDate, $sReleaseDownloadZip, $sReleaseDownloadTgz);
			
			/*case self::PUB_CATEGORIES:
				return self::getCategoriesPages($oContent);
				break;*/
		}
	}
	
	//delete cache from related categories on addon change
	function onChange($oPage, $oPreviousContent=null)
	{
		//clear cache from previous categories, in case of addon is no more under this category
		if ($oPreviousContent!=null)
		{
			$aoPagesCategoriesPrevious = self::getCategoriesPages($oPreviousContent,$oPage);
			foreach ($aoPagesCategoriesPrevious as $oPageCategory)
			{
				AnwCache::clearCacheFromPageGroup($oPageCategory->getPageGroup());
			}
		}
		
		//clear cache from current categories, in case addon was not already under these categories
		$aoPagesCategoriesCurrent = self::getCategoriesPages($oPage->getContent(),$oPage);
		foreach ($aoPagesCategoriesCurrent as $oPageCategory)
		{
			AnwCache::clearCacheFromPageGroup($oPageCategory->getPageGroup());
		}
	}
}

?>