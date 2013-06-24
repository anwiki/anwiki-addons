<?php

class AnwTemplateDefault_contentclass_docbook extends AnwTemplateOverride_global
{
	function docBook($sBookTitle, $sBookBeforeIndex, $sBookAfterIndex, $sTableOfContents)
	{
		$HTML = <<<EOF

<div class="docchapter">
	<h1>$sBookTitle</h1>
	$sBookBeforeIndex
	$sTableOfContents
	$sBookAfterIndex
</div>
EOF;
		return $HTML;
	}
	
	function tocOpen()
	{
		$HTML = <<<EOF

	<div class="docbook_index"><h2>{$this->t_('cc_docbook_toc')}</h2>
	<ol style="list-style:upper-roman">
EOF;
		return $HTML;
	}
	
	function tocEntry($sTitle, $sUrl, $sChapterTocHtml, $n)
	{
		$HTML = <<<EOF

	<li><a href="$sUrl" style="font-weight:bold">$sTitle</a>$sChapterTocHtml</li>
EOF;
		return $HTML;
	}
	
	function tocClose()
	{
		$HTML = <<<EOF

	</ol>
	</div>
EOF;
		return $HTML;
	}
	
	//------------------------------
	
	function chapterTocOpen()
	{
		$HTML = <<<EOF

	<ol style="margin-bottom:10px;">
EOF;
		return $HTML;
	}
	
	function chapterTocEntry($sTitle, $sUrl, $n)
	{
		$HTML = <<<EOF

	<li><a href="$sUrl">$sTitle</a></li>
EOF;
		return $HTML;
	}
	
	function chapterTocClose()
	{
		$HTML = <<<EOF

	</ol>
EOF;
		return $HTML;
	}
}

?>