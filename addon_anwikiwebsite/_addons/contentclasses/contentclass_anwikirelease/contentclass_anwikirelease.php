<?php

class AnwContentFieldPage_changelog extends AnwContentFieldPage_container
{
	const FIELD_FEATURES = "features";
	const FIELD_BUGFIXES = "bugfixes";
	
	const PUB_FEATURES = "features";
	const PUB_BUGFIXES = "bugfixes";
	
	function init()
	{
		// features
		$oContentField = new AnwContentFieldPage_xhtml(self::FIELD_FEATURES);
		$oMultiplicity = new AnwContentMultiplicity_multiple(0, 999);
		$oContentField->setMultiplicity($oMultiplicity);
		$this->addContentField($oContentField);
		
		// bug fixes
		$oContentField = new AnwContentFieldPage_xhtml(self::FIELD_BUGFIXES);
		$oMultiplicity = new AnwContentMultiplicity_multiple(0, 999);
		$oContentField->setMultiplicity($oMultiplicity);
		$this->addContentField($oContentField);
	}
	
	function pubcall($sArg, $oContent, $oPage)
	{
		switch($sArg)
		{
			case self::PUB_FEATURES:
				return $oContent->getContentFieldValues(self::FIELD_FEATURES);
				break;
			case self::PUB_BUGFIXES:
				return $oContent->getContentFieldValues(self::FIELD_BUGFIXES);
				break;
		}
	}
}


class AnwContentClassPageDefault_anwikirelease extends AnwContentClassPage
{
	const FIELD_VERSION_ID = "version_id";
	const FIELD_VERSION_NAME = "version_name";
	const FIELD_DATE = "date";
	const FIELD_SUMMARY = "summary";
	const FIELD_NOTICE = "notice";
	const FIELD_CHANGELOG = "changelog";
	const FIELD_SHORTNAME = "shortname";
	
	const INDEX_VERSION_ID = "version_id";
	const INDEX_VERSION_NAME = "version_name";
	const INDEX_DATE = "date";
	
	const PUB_VERSION_ID = "version_id";
	const PUB_VERSION_NAME = "version_name";
	const PUB_DATE = "date";
	const PUB_SUMMARY = "summary";
	const PUB_NOTICE = "notice";
	const PUB_SHOWNOTICE = "shownotice";
	const PUB_CHANGELOG = "changelog";
	const PUB_GETTITLE = "title";
	const PUB_DOWNLOAD_ZIP = "zip";
	const PUB_DOWNLOAD_TGZ = "tgz";
	const PUB_DOWNLOAD_BUTTON = "showbutton";
	const PUB_DOWNLOAD_REPOSITORY = "repository";
	
	const DOWNLOAD_REPOSITORY = "http://download.tuxfamily.org/anwiki/releases/";
	
	function init()
	{
		// release version
		$oContentField = new AnwContentFieldPage_integer( self::FIELD_VERSION_ID );
		$oContentField->indexAs( self::INDEX_VERSION_ID );
		$oContentField->setTranslatable(false);
		$this->addContentField($oContentField);
		
		
		// release name
		$oContentField = new AnwContentFieldPage_string( self::FIELD_VERSION_NAME );
		$oContentField->indexAs( self::INDEX_VERSION_NAME );
		$oContentField->setTranslatable(false);
		$this->addContentField($oContentField);
		
		
		// release date
		$oContentField = new AnwContentFieldPage_date( self::FIELD_DATE );
		$oContentField->indexAs( self::INDEX_DATE );
		$oContentField->setTranslatable(false);
		$this->addContentField($oContentField);
		
		
		// release summary
		$oContentField = new AnwContentFieldPage_xhtml( self::FIELD_SUMMARY );
		$this->addContentField($oContentField);
		
		
		// release notice
		$oContentField = new AnwContentFieldPage_xhtml( self::FIELD_NOTICE );
		$this->addContentField($oContentField);
		
		
		// release changelog
		$oContentField = new AnwContentFieldPage_changelog( self::FIELD_CHANGELOG );
		$this->addContentField($oContentField);
		
		// release shortname
		$oContentField = new AnwContentFieldPage_string( self::FIELD_SHORTNAME );
		$oContentField->setTranslatable(false);
		$this->addContentField($oContentField);
	}
	
	function toHtml($oContent, $oPage)
	{
		$oOutputHtml = new AnwOutputHtml( $oPage );
		
		$sReleaseVersion = $oContent->getContentFieldValue(self::FIELD_VERSION_NAME);
		$sReleaseTitle = $this->getReleaseTitle($sReleaseVersion, $oContent);
		
		$oOutputHtml->setTitle( $this->getReleaseTitle($oContent->getContentFieldValue(self::FIELD_VERSION_NAME)) );
		
		$sReleaseDate = Anwi18n::date( AnwUtils::dateToTime($oContent->getContentFieldValue(self::FIELD_DATE)), $oPage->getLang() );
		$sReleaseSummary = $oContent->getContentFieldValue(self::FIELD_SUMMARY);
		$sReleaseNotice = $oContent->getContentFieldValue(self::FIELD_NOTICE);
		
		$oChangelogContent = $oContent->getSubContent(self::FIELD_CHANGELOG);
		
		//changelog features
		$asChangelogFeaturesValues = $oChangelogContent->getContentFieldValues(AnwContentFieldPage_changelog::FIELD_FEATURES);
		$sHtmlFeatures = '';
		if (count($asChangelogFeaturesValues) > 0)
		{
			$sHtmlFeatures .= $this->tpl()->changelogFeaturesOpen($oPage->getLang());
			foreach ($asChangelogFeaturesValues as $sChangelogFeature)
			{
				$sHtmlFeatures .= $this->tpl()->changelogFeature($sChangelogFeature);
			}
			$sHtmlFeatures .= $this->tpl()->changelogFeaturesClose();
		}
		
		//changelog bugfixes
		$asChangelogBugfixesValues = $oChangelogContent->getContentFieldValues(AnwContentFieldPage_changelog::FIELD_BUGFIXES);
		$sHtmlBugfixes = '';
		if (count($asChangelogBugfixesValues) > 0)
		{
			$sHtmlBugfixes = $this->tpl()->changelogBugfixesOpen();
			foreach ($asChangelogBugfixesValues as $sChangelogBugfix)
			{
				$sHtmlBugfixes .= $this->tpl()->changelogBugfix($sChangelogBugfix);
			}
			$sHtmlBugfixes .= $this->tpl()->changelogBugfixesClose();
		}
		
		$sReleaseFileName = $oContent->getContentFieldValue(self::FIELD_SHORTNAME);
		$sReleaseDownloadZip = $this->getDownloadLinkZip($sReleaseFileName);
		$sReleaseDownloadTgz = $this->getDownloadLinkTgz($sReleaseFileName);
		$sReleaseMyLink = AnwUtils::link($oPage->getName());
			
		$sHtmlBody = $this->tpl()->anwikirelease($oPage->getLang(), $sReleaseTitle, $sReleaseMyLink, $sReleaseVersion, $sReleaseDate, $sReleaseSummary, $sReleaseNotice, $sHtmlFeatures, $sHtmlBugfixes, $sReleaseDownloadZip, $sReleaseDownloadTgz);
		
		$oOutputHtml->setBody( $sHtmlBody );
		return $oOutputHtml;
	}
	
	function toFeedItem($oContent, $oPage)
	{
		$oFeedItem = new AnwFeedItem(
			$this->getReleaseTitle($oContent->getContentFieldValue(self::FIELD_VERSION_NAME, 0, true)),
			AnwUtils::link($oPage),
			$oContent->getContentFieldValue(self::FIELD_SUMMARY, 0, true)
		);
		return $oFeedItem;
	}
	
	function pubcall($sArg, $oContent, $oPage)
	{
		switch($sArg)
		{
			case self::PUB_VERSION_NAME:
				return $oContent->getContentFieldValue(self::FIELD_VERSION_NAME);
				break;
			
			case self::PUB_VERSION_ID:
				return $oContent->getContentFieldValue(self::FIELD_VERSION_ID);
				break;
			
			case self::PUB_DATE:
				return AnwUtils::dateToTime($oContent->getContentFieldValue(self::FIELD_DATE));
				break;
			
			case self::PUB_SUMMARY:
				return $oContent->getContentFieldValue(self::FIELD_SUMMARY);
				break;
			
			case self::PUB_NOTICE:
				return $oContent->getContentFieldValue(self::FIELD_NOTICE);
				break;
			
			case self::PUB_SHOWNOTICE:
				$sReleaseTitle = $this->getReleaseTitle($oContent->getContentFieldValue(self::FIELD_VERSION_NAME));
				return $this->tpl()->showNotice($oPage->getLang(), $sReleaseTitle, $oContent->getContentFieldValue(self::FIELD_NOTICE));
				break;
			
			case self::PUB_CHANGELOG:
				return $oContent->getSubContent(self::FIELD_CHANGELOG);
				break;
			
			case self::PUB_GETTITLE:
				return $this->getReleaseTitle($oContent->getContentFieldValue(self::FIELD_VERSION_NAME));
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
		}
	}
	
	private function getReleaseTitle($sReleaseVersion)
	{
		return 'Anwiki '.$sReleaseVersion;
	}
	
	private function getDownloadLinkZip($sFileName)
	{
		return self::DOWNLOAD_REPOSITORY.$sFileName.".zip";
	}
	
	private function getDownloadLinkTgz($sFileName)
	{
		return self::DOWNLOAD_REPOSITORY.$sFileName.".tar.gz";
	}
}

?>