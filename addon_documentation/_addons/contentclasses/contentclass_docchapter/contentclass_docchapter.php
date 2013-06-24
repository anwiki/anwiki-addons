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
 * ContentClass: Documentation book chapter. Works with ContentClass docbook.
 * @see contentclass_docbook
 * @package Anwiki
 * @version $Id: contentclass_docchapter.php 170 2009-04-05 18:06:49Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */

class AnwContentClassPageDefault_docchapter extends AnwContentClassPage implements AnwIContentClassPageDefault_docchapter, AnwDependancyManageable
{
	const REGEXP_TOC_ENTRY = '!<h2>(.*?)</h2>!si';
	const MIN_TOC_ENTRIES = 1;
	
	const DOCBOOK_CLASS = 'docbook';
	const CSS_FILE = 'contentclass_docchapter.css';
	
	function getComponentDependancies()
	{
		$aoDependancies = array();
		/*
		 * Depends on contentclass_docbook.
		 */
		$aoDependancies[] = new AnwDependancyRequirement($this, AnwComponent::TYPE_CONTENTCLASS, self::DOCBOOK_CLASS);
		return $aoDependancies;
	}
	
	function init()
	{
		// chapter title
		$oContentField = new AnwContentFieldPage_string( self::FIELD_TITLE );
		$this->addContentField($oContentField);		
		
		// chapter body
		$oContentField = new AnwContentFieldPage_xhtml( self::FIELD_BODY );
		$oContentField->setDynamicParsingAllowed(true);
		$oContentField->setDynamicPhpAllowed(true);
		$this->addContentField($oContentField);
	}
	
	function toHtml($oContent, $oPage)
	{
		$oOutputHtml = new AnwOutputHtml( $oPage );
		
		//load contentclass CSS
		$oOutputHtml->setHead( $this->getCssSrcComponent(self::CSS_FILE) );
		
		//render table of contents
		$sHtmlBody = $oContent->getContentFieldValue(self::FIELD_BODY);
		$asTocTree = self::genTocTree($oPage, $oContent);
		$sTocHtml = "";
		if (count($asTocTree) >= self::MIN_TOC_ENTRIES)
		{
			//add anchors in body
			$sHtmlBody = self::setBodyAnchors($sHtmlBody);
			
			$sTocHtml = $this->tpl()->tocOpen();
			foreach ($asTocTree as $sTitleInfos)
			{
				$sTitle = $sTitleInfos['TITLE'];
				$sUrl = $sTitleInfos['URL'];
				$nNumber = $sTitleInfos['NUMBER'];
				$sTocHtml .= $this->tpl()->tocEntry($sTitle, $sUrl, $nNumber);
			}
			$sTocHtml .= $this->tpl()->tocClose();
		}
		
		$sCurChapterTitle = $oContent->getContentFieldValue(self::FIELD_TITLE);
		
		//get docbook
		$oBookPage = self::getDocBook($oPage);
		if ($oBookPage!=null)
		{		
			//book nav
			$oContentClassBook = self::getContentClassBook();
			$sBookTitle = $oBookPage->getContent()->getContentFieldValue( AnwUtils::CONSTANT('FIELD_TITLE', $oContentClassBook) );
			$sBookUrl = AnwUtils::link($oBookPage);
			$aasTocTreeBook = $oContentClassBook->genTocTree($oBookPage);
			
			//find chapter ID
			$nMyPagegroupId = $oPage->getPageGroup()->getId();
			$nChapterIndice = 0;
			foreach ($aasTocTreeBook as $i => $aasTocChapter)
			{
				if ($aasTocChapter['PAGEGROUPID']==$nMyPagegroupId)
				{
					$nChapterIndice = $i;
					break;
				}
			}
			
			if (isset($aasTocTreeBook[$nChapterIndice-1]))
			{
				$sPrevChapterTitle = $aasTocTreeBook[$nChapterIndice-1]['TITLE'];
				$sPrevChapterNumber = $aasTocTreeBook[$nChapterIndice-1]['NUMBER'];
				$sPrevChapterUrl = $aasTocTreeBook[$nChapterIndice-1]['URL'];
			}
			else
			{
				$sPrevChapterTitle = false;
				$sPrevChapterNumber = false;
				$sPrevChapterUrl = false;
			}
			if (isset($aasTocTreeBook[$nChapterIndice+1]))
			{
				$sNextChapterTitle = $aasTocTreeBook[$nChapterIndice+1]['TITLE'];
				$sNextChapterNumber = $aasTocTreeBook[$nChapterIndice+1]['NUMBER'];
				$sNextChapterUrl = $aasTocTreeBook[$nChapterIndice+1]['URL'];
			}
			else
			{
				$sNextChapterTitle = false;
				$sNextChapterNumber = false;
				$sNextChapterUrl = false;
			}
			$sCurChapterNumber = $nChapterIndice;
			
			//nav
			$sHtmlNav = $this->tpl()->navBook(
				$sBookTitle, $sBookUrl,
				$sPrevChapterTitle, $sPrevChapterNumber, $sPrevChapterUrl,
				$sCurChapterTitle, $sCurChapterNumber,
				$sNextChapterTitle, $sNextChapterNumber, $sNextChapterUrl
			);
		}
		else
		{
			$sCurChapterNumber = "";
			$sBookTitle = "";
			$sBookUrl = "#";
			$sHtmlNav = $this->t_("cc_docchapter_nobook_info");
		}
		
		//render
		$sHtmlBody = $this->tpl()->docChapter( 
			$sCurChapterTitle,
			$sCurChapterNumber,
			$sHtmlBody,
			$sTocHtml,
			$sBookTitle, $sBookUrl,
			$sHtmlNav
		);
			
		$oOutputHtml->setTitle( $sBookTitle.': '.$oContent->getContentFieldValue(self::FIELD_TITLE) );
		$oOutputHtml->setBody( $sHtmlBody );
		return $oOutputHtml;
	}
	
	function toFeedItem($oContent, $oPage)
	{
		//get docbook
		$oBookPage = self::getDocBook($oPage);
		if ($oBookPage) {
			$oContentClassBook = self::getContentClassBook();
			$sBookTitle = $oBookPage->getContent()->getContentFieldValue( AnwUtils::CONSTANT('FIELD_TITLE', $oContentClassBook) );
		}
		else {
			$sBookTitle = "";
		}
		
		$oFeedItem = new AnwFeedItem(
			$oContent->getContentFieldValue($sBookTitle.': '.self::FIELD_TITLE, 0, true),
			AnwUtils::link($oPage),
			$oContent->getContentFieldValue(self::FIELD_TITLE, 0, true)
		);
		return $oFeedItem;
	}
	
	function onChange($oPageChap, $oPreviousContent=null)
	{
		$oPageBook = self::getDocBook($oPageChap);
		
		if ($oPageBook!=null)
		{
			//clear cache of docchapters of the docbook
			$oContentClassBook = self::getContentClassBook();
			$aoPagesChapters = $oContentClassBook->getDocChapters($oPageBook);
			foreach ($aoPagesChapters as $oPageChapter)
			{
				AnwCache::clearCacheFromPageGroup($oPageChapter->getPageGroup());
			}
			
			//clear cache of docbook
			AnwCache::clearCacheFromPageGroup($oPageBook->getPageGroup());
		}
	}
		
	/**
	 * Used by docbook.
	 */
	public static function genTocTree($oPage, $oContent)
	{
		$sHtmlBody = $oContent->getContentFieldValue(self::FIELD_BODY);
		$sPattern = self::REGEXP_TOC_ENTRY;
		$sReturn = preg_match_all($sPattern, $sHtmlBody, $aaMatches);
		
		$asTocTreeChapter = array();
		$n = 1;
		foreach ($aaMatches[1] as $sTitle)
		{
			$asTocTreeChapter[] = array(
				'TITLE' => $sTitle, 
				'URL' => AnwUtils::link($oPage).'#'.self::tocAnchorName($n), 
				'NUMBER' => $n
			);
			$n++;
		}
		return $asTocTreeChapter;
	}
	
	private static function setBodyAnchors($sHtmlBody)
	{
		$sPattern = self::REGEXP_TOC_ENTRY;
		$sReturn = preg_replace_callback($sPattern, array('self','cbkSetBodyAnchors'), $sHtmlBody);
		return $sReturn;
	}
	
	private static function cbkSetBodyAnchors($asMatches)
	{
		static $n = 1;
		$sReturn = '<a name="'.self::tocAnchorName($n).'"></a>'.$asMatches[0];
		$n++;
		return $sReturn;
	}
	
	private static function tocAnchorName($nNumber)
	{
		return 't'.$nNumber;
	}
	
	// may return null
	private static function getDocBook($oPage)
	{
		//fetch docbooks linked to this chapter
		$asPatterns = array();
		$oContentClass = AnwContentClasses::getContentClass(self::DOCBOOK_CLASS);
		$asLangs = array(); // we search it on pagegroup level...
		$nLimit = 1;
		$sSortUser = AnwUtils::SORT_BY_NAME;
		$sOrder = AnwUtils::SORTORDER_ASC;
		$asFilters = array();
		$asFilters[] = array(
						'FIELD' => AnwIContentClassPageDefault_docbook::INDEX_CHAPTERS,
						'OPERATOR' => AnwUtils::FILTER_OP_EQUALS,
						'VALUE' => $oPage->getPageGroup()->getId()
		);
		$aoDocBookPages = AnwStorage::fetchPagesByClass($asPatterns, $oContentClass, $asLangs, $nLimit, $sSortUser, $sOrder, $asFilters);
		if (count($aoDocBookPages)>0)
		{
			// we found a page which belongs to this pagegroup
			$oDocBookPage = array_pop($aoDocBookPages);
			// get it in the preferred lang!
			$oDocBookPage = $oDocBookPage->getPageGroup()->getPreferedPage($oPage->getLang());
		}
		else
		{
			$oDocBookPage = null;
		}
		return $oDocBookPage;
	}
	
	private static function getContentClassBook()
	{
		return AnwContentClasses::getContentClass(self::DOCBOOK_CLASS);
	}
}

?>