<?php

class AnwTemplateDefault_contentclass_anwikirelease extends AnwTemplateOverride_global
{
	function anwikirelease($sLang, $sReleaseTitle, $sReleaseMyLink, $sReleaseVersion, $sReleaseDate, $sReleaseSummary, $sReleaseNotice, $sHtmlFeatures, $sHtmlBugfixes, $sDownloadLinkZip, $sDownloadLinkTgz)
	{
		$HTML = <<<EOF

<h1>$sReleaseTitle</h1>
<div style="float:right; font-weight:bold">{$this->t_('local_daterelease',array('date'=>$sReleaseDate), $sLang)}</div>
$sReleaseSummary

<div style="text-align:center; margin:0px auto">
	{$this->downloadButton($sLang, $sReleaseVersion, $sReleaseMyLink, $sReleaseDate, $sDownloadLinkZip, $sDownloadLinkTgz)}
</div>
{$this->showNotice($sLang, $sReleaseTitle, $sReleaseNotice)}

$sHtmlFeatures

$sHtmlBugfixes
EOF;
		return $HTML;
	}
	
	function showNotice($sLang, $sReleaseTitle, $sReleaseNotice)
	{
		$HTML = "";
		if (trim($sReleaseNotice))
		{
			$HTML .= <<<EOF

<div style="background:#F5E1CD; padding:1em 2em; margin:1em auto; -moz-border-radius: 0.5em; border-radius: 0.5em; -khtml-border-radius: 0.5em; padding:1em; width:80%;">
	<h3 style="margin:0 0 1em 0">$sReleaseTitle - {$this->t_('local_notice', array(), $sLang)}</h4>
	$sReleaseNotice
</div>
EOF;
		}
		return $HTML;
	}
	
	function downloadButton($sLang, $sReleaseVersion, $sReleaseMyLink, $sReleaseDate, $sDownloadLinkZip, $sDownloadLinkTgz)
	{
		$sDownloadLink = AnwUtils::link('download');
		$HTML = <<<EOF

<div class="anwiki-download">
	<span class="download-get"><a href="$sDownloadLink">{$this->t_('local_download', array(), $sLang)}</a></span>
	<span class="download-version">$sReleaseVersion</span>
	<span class="download-date">$sReleaseDate</span>
	<div class="download-files">
		<a class="download-zip" href="$sDownloadLinkZip">zip</a>
		<a class="download-tgz" href="$sDownloadLinkTgz">tar.gz</a><br/>
		<a class="download-info" href="$sReleaseMyLink">{$this->t_('local_download_releasenotes', array(), $sLang)}</a>
	</div>
</div>
EOF;
		return $HTML;
	}
	
	//features
	function changelogFeaturesOpen($sLang)
	{
		$HTML = <<<EOF

	<h2>{$this->t_('local_featureslist', array(), $sLang)}</h2>
	<ul>
EOF;
		return $HTML;
	}
	function changelogFeature($sChangelogFeature)
	{
		$HTML = <<<EOF

	<li>$sChangelogFeature</li>
EOF;
		return $HTML;
	}
	function changelogFeaturesClose()
	{
		$HTML = <<<EOF

	</ul>
EOF;
		return $HTML;
	}	
	
	//bugfixes
	function changelogBugfixesOpen($sLang)
	{
		$HTML = <<<EOF

	<h2>{$this->t_('local_bugfixeslist', array(), $sLang)}</h2>
	<ul>
EOF;
		return $HTML;
	}
	function changelogBugfix($sChangelogBugfix)
	{
		$HTML = <<<EOF

	<li>$sChangelogBugfix</li>
EOF;
		return $HTML;
	}
	function changelogBugfixesClose()
	{
		$HTML = <<<EOF

	<ul>
EOF;
		return $HTML;
	}
	
}

?>