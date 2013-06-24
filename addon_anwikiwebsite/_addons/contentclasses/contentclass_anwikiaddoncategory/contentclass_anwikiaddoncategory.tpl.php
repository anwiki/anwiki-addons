<?php

class AnwTemplateDefault_contentclass_anwikiaddoncategory extends AnwTemplateOverride_global
{
	function showAddoncategory($sAddonCategoryTitle, $sAddonCategoryIntro, $sAddonsListHtml, $sLang)
	{
		$HTML = <<<EOF

<h1>{$this->t_('local_anwikiaddoncategory_title', array('categorytitle'=>$sAddonCategoryTitle), $sLang)}</h1>

<div class="addoncategory_intro">
	$sAddonCategoryIntro
</div>

$sAddonsListHtml
EOF;
		return $HTML;
	}
	
		
	function addonsListStart()
	{
		$HTML = <<<EOF
	<div class="addoncategory_addonslist">
EOF;
		return $HTML;
	}
	
	function addonsListItem($sAddonName, $sAddonTitle, $sAddonIntro, $sAddonUrl, $sAddonLang)
	{
		$HTML = <<<EOF
	<div class="addon">
		<h2><a href="$sAddonUrl" class="addon_name">$sAddonName</a> - $sAddonTitle</h2>
		<p>$sAddonIntro</p>
	</div>
EOF;
		return $HTML;
	}
	
	function addonsListEnd()
	{
		$HTML = <<<EOF
	</div>
EOF;
		return $HTML;
	}
}

?>