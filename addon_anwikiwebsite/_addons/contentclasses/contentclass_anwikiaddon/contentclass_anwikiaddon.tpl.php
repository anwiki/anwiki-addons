<?php

class AnwTemplateDefault_contentclass_anwikiaddon extends AnwTemplateOverride_global
{
	function showAddon($sAddonName, $sAddonTitle, $sAddonDescription, $sAddonBody, $sCategoriesHtml, $sHtmlVersions, $sAddonLang)
	{
		$HTML = <<<EOF

<h1><span class="addon_name">$sAddonName -</span> $sAddonTitle</h1>

$sCategoriesHtml

<div class="addon_description">
	$sAddonDescription
</div>

<h2>{$this->t_('anwikiaddon_versions_title')}</h2>
$sHtmlVersions

<div class="addon_body">
	$sAddonBody
</div>
EOF;
		return $HTML;
	}
	
	// categories...
	
	function categoriesStart()
	{
		$HTML = <<<EOF
	<div class="addon_categories">
EOF;
		return $HTML;
	}
	
	function categoriesItem($sCategoryTitle, $sCategoryUrl)
	{
		$HTML = <<<EOF
		<span><a href="$sCategoryUrl">$sCategoryTitle</a></span>
EOF;
		return $HTML;
	}
	
	function categoriesEnd()
	{
		$HTML = <<<EOF
	</div>
EOF;
		return $HTML;
	}
	
	// versions...
	
	function versionsStart()
	{
		$HTML = <<<EOF
	<div class="addon_versions">
		<table>
		<tr>
		<th>{$this->t_('anwikiaddon_version_date')}</th>
		<th>{$this->t_('anwikiaddon_version_name')}</th>
		<th>{$this->t_('anwikiaddon_version_download')}</th>
		<th>{$this->t_('anwikiaddon_version_anwikirelease')}</th>
		</tr>
EOF;
		return $HTML;
	}
	
	function versionsItem($sAddonVersionName, $sAddonVersionDate, $sAnwikiReleasesHtml, $sAddonVersionDownloadZip, $sAddonVersionDownloadTgz)
	{
		$HTML = <<<EOF
		<tr>
		<td class="addon_version_date">$sAddonVersionDate</td>
		<td class="addon_version_name">$sAddonVersionName</td>
		<td class="addon_version_download">
			<span><a href="$sAddonVersionDownloadZip">zip</a></span>
			<span><a href="$sAddonVersionDownloadTgz">tgz</a></span>
		</td>
		<td class="addon_version_anwikirelease">$sAnwikiReleasesHtml</td>
		</tr>
EOF;
		return $HTML;
	}
	
	function versionsEnd()
	{
		$HTML = <<<EOF
		</table>
	</div>
EOF;
		return $HTML;
	}
	
	
	function versionsNone()
	{
		$HTML = <<<EOF
	<div class="addon_versions">
		{$this->t_('anwikiaddon_versions_none')}
	</div>
EOF;
		return $HTML;
	}
	
	// anwiki releases...
	
	function anwikiReleasesStart()
	{
		$HTML = <<<EOF
	<div class="addon_anwikireleases">
EOF;
		return $HTML;
	}
	
	function anwikiReleasesItem($sAnwikiReleaseName, $sAnwikiReleaseUrl)
	{
		$HTML = <<<EOF
		<span><a href="$sAnwikiReleaseUrl">$sAnwikiReleaseName</a></span>
EOF;
		return $HTML;
	}
	
	function anwikiReleasesEnd()
	{
		$HTML = <<<EOF
	</div>
EOF;
		return $HTML;
	}
}

?>