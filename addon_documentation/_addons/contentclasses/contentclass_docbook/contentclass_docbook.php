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
 * ContentClass: Documentation book. Works with ContentClass docchapter.
 * @see contentclass_docchapter
 * @package Anwiki
 * @version $Id: contentclass_docbook.php 167 2009-04-05 14:43:26Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */
 
class AnwContentClassPageDefault_docbook extends AnwContentClassPage implements AnwIContentClassPageDefault_docbook, AnwDependancyManageable
{
	const DOCCHAPTER_CLASS = 'docchapter';
	
	function getComponentDependancies()
	{
		$aoDependancies = array();
		/*
		 * Depends on contentclass_docchapter.
		 */
		$aoDependancies[] = new AnwDependancyRequirement($this, AnwComponent::TYPE_CONTENTCLASS, self::DOCCHAPTER_CLASS);
		return $aoDependancies;
	}
	
	function init()
	{
		// book title
		$oContentField = new AnwContentFieldPage_string( self::FIELD_TITLE );
		$oContentField->indexAs(self::INDEX_TITLE);
		$this->addContentField($oContentField);
		
		
		// book beforeindex
		$oContentField = new AnwContentFieldPage_xhtml( self::FIELD_BEFOREINDEX );
		$oContentField->setDynamicParsingAllowed(true);
		$oContentField->setDynamicPhpAllowed(true);
		$this->addContentField($oContentField);
		
		
		// book afterindex
		$oContentField = new AnwContentFieldPage_xhtml( self::FIELD_AFTERINDEX );
		$oContentField->setDynamicParsingAllowed(true);
		$oContentField->setDynamicPhpAllowed(true);
		$this->addContentField($oContentField);
		
		
		// book chapters
		$oContentClassChapter = AnwContentClasses::getContentClass(self::DOCCHAPTER_CLASS);
		$aoPagesChapters = AnwStorage::fetchPagesByClass(
			array(),
			$oContentClassChapter,
			array(),
			9999,
			AnwUtils::SORT_BY_NAME,
			AnwUtils::SORTORDER_ASC
		);
		
		$asEnumValues = array();
		foreach ($aoPagesChapters as $oPageChapter)
		{
			$sChapterTitle = $oPageChapter->getContent()->getContentFieldValue(AnwIContentClassPageDefault_docchapter::FIELD_TITLE);
			$asEnumValues[$oPageChapter->getPageGroup()->getId()] = $oPageChapter->getName()." - ".$sChapterTitle;
		}
		
		$oContentField = new AnwContentFieldPage_select( self::FIELD_CHAPTERS );
		$oContentField->setEnumValues($asEnumValues);
		$oContentField->setTranslatable(false);
		$oContentMultiplicity = new AnwContentMultiplicity_multiple();
		$oContentField->setMultiplicity($oContentMultiplicity);
		$oContentField->indexAs(self::INDEX_CHAPTERS);
		$this->addContentField($oContentField);
	}
	
	function toHtml($oContent, $oPage)
	{
		$oOutputHtml = new AnwOutputHtml( $oPage );
		$oOutputHtml->setTitle( $oContent->getContentFieldValue(self::FIELD_TITLE) );
		
		//whole table of contents
		$sTocHtml = $this->getTocRendered($oPage, $oContent);	
		
		//render
		$sHtmlBody = $this->tpl()->docBook( 
			$oContent->getContentFieldValue(self::FIELD_TITLE),
			$oContent->getContentFieldValue(self::FIELD_BEFOREINDEX),
			$oContent->getContentFieldValue(self::FIELD_AFTERINDEX),
			$sTocHtml
		);
		
		$oOutputHtml->setBody( $sHtmlBody );
		return $oOutputHtml;
	}
	
	function toFeedItem($oContent, $oPage)
	{
		$oFeedItem = new AnwFeedItem(
			$oContent->getContentFieldValue(self::FIELD_TITLE, 0, true),
			AnwUtils::link($oPage),
			$oContent->getContentFieldValue(self::FIELD_TITLE, 0, true)
		);
		return $oFeedItem;
	}
	
	function pubcall($sArg, $oContent, $oPage)
	{
		switch($sArg)
		{
			case self::PUB_TITLE:
				return $oContent->getContentFieldValue(self::FIELD_TITLE);
				break;
		}
	}
	
	function onChange($oPageBook, $oPreviousContent=null)
	{		
		// clear cache for previously assigned chapters
		$aoPagesChapters = self::getDocChapters($oPageBook, $oPreviousContent);
		foreach ($aoPagesChapters as $oPageChapter)
		{
			AnwCache::clearCacheFromPageGroup($oPageChapter->getPageGroup());
		}
		
		// clear cache for new assigned chapters
		$aoPagesChapters = self::getDocChapters($oPageBook);
		foreach ($aoPagesChapters as $oPageChapter)
		{
			AnwCache::clearCacheFromPageGroup($oPageChapter->getPageGroup());
		}		
	}
	
	private function getTocRendered($oPage, $oContent)
	{
		$oContentClassChapter = self::getContentClassChapter();
		
		$sTocHtml = '';
		$aaBookTree = self::genTocTree($oPage, $oContent);
		if (count($aaBookTree) >= 1)
		{
			$sTocHtml = $this->tpl()->tocOpen();
			foreach ($aaBookTree as $asChapterInfos)
			{
				$sChapterTitle = $asChapterInfos['TITLE'];
				$sChapterUrl = $asChapterInfos['URL'];
				$nChapterNum = $asChapterInfos['NUMBER'];
				
				//chapter tree?
				$sChapterTocHtml = '';
				$asChapterTree = $asChapterInfos['SUBTREE'];
				if(count($asChapterTree) >= AnwUtils::CONSTANT('MIN_TOC_ENTRIES', $oContentClassChapter))
				{
					$sChapterTocHtml = $this->tpl()->chapterTocOpen();
					foreach ($asChapterTree as $asTitleInfos)
					{
						$sTitleTitle = $asTitleInfos['TITLE'];
						$sTitleUrl = $asTitleInfos['URL'];
						$sTitleNum = $asTitleInfos['NUMBER'];
						$sChapterTocHtml .= $this->tpl()->chapterTocEntry($sTitleTitle, $sTitleUrl, $sTitleNum);
					}
					$sChapterTocHtml .= $this->tpl()->chapterTocClose();
				}
				
				$sTocHtml .= $this->tpl()->tocEntry($sChapterTitle, $sChapterUrl, $sChapterTocHtml, $nChapterNum);
			}
			$sTocHtml .= $this->tpl()->tocClose();
		}
		return $sTocHtml;
	}
	
	/**
	 * Used by docchapter.
	 */
	public static function genTocTree($oPage, $oContent=null)
	{
		$oContentClassChapter = self::getContentClassChapter();
		
		//table of contents
		$aoPagesChapters = self::getDocChapters($oPage, $oContent);
		$aasTocTreeBook = array();
		$n = 1;
		foreach ($aoPagesChapters as $oPageChapter)
		{
			//chapter table of contents
			$asTocTreeChapter = $oContentClassChapter->genTocTree($oPageChapter, $oPageChapter->getContent());
			$aasTocTreeBook[] = array(
				'TITLE' => $oPageChapter->getContent()->getContentFieldValue( AnwUtils::CONSTANT('FIELD_TITLE', $oContentClassChapter) ), 
				'URL' => AnwUtils::link($oPageChapter),
				'NUMBER' => $n,
				'SUBTREE' => $asTocTreeChapter,
				'PAGEGROUPID' => $oPageChapter->getPageGroup()->getId()
			);
			$n++;
		}
		return $aasTocTreeBook;
	}
	
	/**
	 * Used by docchapter.
	 */
	static function getDocChapters($oPage, $oContent=null)
	{
		$oContentClassChapter = self::getContentClassChapter();
		
		if ($oContent == null)
		{
			$oContent = $oPage->getContent();
		}
		
		$aoPageChapters = array();
		$anChapterPageGroupIds = $oContent->getContentFieldValues(self::FIELD_CHAPTERS);
		foreach ($anChapterPageGroupIds as $nChapterPageGroupId)
		{
			$oChapterPageGroup = new AnwPageGroup($nChapterPageGroupId, "news");
			if ($oChapterPageGroup->exists())
			{
				//get category in current lang if available
				$oPageChapter = $oChapterPageGroup->getPreferedPage($oPage->getLang());
				$aoPageChapters[] = $oPageChapter;
			}
		}
		
		return $aoPageChapters;
	}
		
	private static function getContentClassChapter()
	{
		return AnwContentClasses::getContentClass(self::DOCCHAPTER_CLASS);
	}
}

?>