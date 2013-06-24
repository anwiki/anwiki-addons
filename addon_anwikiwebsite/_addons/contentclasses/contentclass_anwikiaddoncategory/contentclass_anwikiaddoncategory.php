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
 * ContentClass: anwiki addon category.
 * @package Anwiki
 * @version $Id: contentclass_anwikiaddoncategory.php 173 2009-04-08 19:58:01Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */

class AnwContentClassPageDefault_anwikiaddoncategory extends AnwContentClassPage implements AnwDependancyManageable, AnwIContentClassPageDefault_anwikiaddoncategory
{	
	const ADDON_CLASS = "anwikiaddon";
	
	const CSS_FILE = 'contentclass_anwikiaddoncategory.css';
	
	function getComponentDependancies()
	{
		$aoDependancies = array();
		/*
		 * Depends on contentclass_anwikiaddon.
		 */
		$aoDependancies[] = new AnwDependancyRequirement($this, AnwComponent::TYPE_CONTENTCLASS, self::ADDON_CLASS);
		return $aoDependancies;
	}
	
	function init()
	{
		// category title
		$oContentField = new AnwContentFieldPage_string( self::FIELD_TITLE );
		$oContentField->indexAs(self::PUB_TITLE);
		$this->addContentField($oContentField);
		
		// category intro
		$oContentField = new AnwContentFieldPage_xhtml( self::FIELD_DESCRIPTION );
		$this->addContentField($oContentField);
	}
	
	function toHtml($oContent, $oPage)
	{
		$oOutputHtml = new AnwOutputHtml( $oPage );
		$oOutputHtml->setTitle( $oContent->getContentFieldValue(self::FIELD_TITLE) );
		
		//load contentclass CSS
		$oOutputHtml->setHead( $this->getCssSrcComponent(self::CSS_FILE) );
		
		$sAddonCategoryTitle = $oContent->getContentFieldValue( self::FIELD_TITLE );
		$sAddonCategoryIntro = $oContent->getContentFieldValue( self::FIELD_DESCRIPTION );
		
		//render addons list
		$aoAddonsList = self::getAddonsList($oPage);
		$sHtmlAddonsList = "";
		if (count($aoAddonsList)>0)
		{
			$sHtmlAddonsList .= $this->tpl()->addonsListStart();
			foreach ($aoAddonsList as $oAddonPage)
			{
				$oAddonContent = $oAddonPage->getContent();
				$sAddonName = $oAddonContent->getContentFieldValue( AnwIContentClassPageDefault_anwikiaddon::FIELD_NAME );
				$sAddonTitle = $oAddonContent->getContentFieldValue( AnwIContentClassPageDefault_anwikiaddon::FIELD_TITLE );
				$sAddonDescription = $oAddonContent->getContentFieldValue( AnwIContentClassPageDefault_anwikiaddon::FIELD_DESCRIPTION );
				$sAddonUrl = AnwUtils::link($oAddonPage);
				$sHtmlAddonsList .= $this->tpl()->addonsListItem($sAddonName, $sAddonTitle, $sAddonDescription, $sAddonUrl, $oAddonPage->getLang());
			}
			$sHtmlAddonsList .= $this->tpl()->addonsListEnd();
		}
		unset($aoAddonsList);
		
		//render the addoncategory
		$sHtmlBody = $this->tpl()->showAddoncategory($sAddonCategoryTitle, $sAddonCategoryIntro, $sHtmlAddonsList, $oPage->getLang());
		
		$oOutputHtml->setBody( $sHtmlBody );
		return $oOutputHtml;
	}
	
	protected static function getAddonsList($oPage)
	{
		//fetch addons linked to this category
		$asPatterns = array();
		$oContentClass = AnwContentClasses::getContentClass(self::ADDON_CLASS);
		$asLangs = array($oPage->getLang());
		$nLimit = 0;
		$sSortUser = AnwIContentClassPageDefault_anwikiaddon::PUB_NAME;
		$sOrder = AnwUtils::SORTORDER_ASC;
		$asFilters = array();
		$asFilters[] = array(
						'FIELD' => AnwIContentClassPageDefault_anwikiaddon::PUB_CATEGORIES,
						'OPERATOR' => AnwUtils::FILTER_OP_EQUALS,
						'VALUE' => $oPage->getPageGroup()->getId()
		);
		$aoAddonsPages = AnwStorage::fetchPagesByClass($asPatterns, $oContentClass, $asLangs, $nLimit, $sSortUser, $sOrder, $asFilters);
		return $aoAddonsPages;
	}
	
	function toFeedItem($oContent, $oPage)
	{
		$oFeedItem = new AnwFeedItem(
			$oContent->getContentFieldValue(self::FIELD_TITLE, 0, true),
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
			case self::PUB_TITLE:
				return $oContent->getContentFieldValue(self::FIELD_TITLE);
				break;
			
			case self::PUB_DESCRIPTION:
				return $oContent->getContentFieldValue(self::FIELD_DESCRIPTION);
				break;
		}
	}
	
	//delete cache from related addon on category change
	function onChange($oPage, $oPreviousContent=null)
	{
		$aoPagesAddons = self::getAddonsList($oPage);
		foreach ($aoPagesAddons as $oPageAddon)
		{
			AnwCache::clearCacheFromPageGroup($oPageAddon->getPageGroup());
		}
	}
	
}

?>