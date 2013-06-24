<?php

class AnwTemplateDefault_contentclass_docchapter extends AnwTemplateOverride_global
{
	function docChapter($sChapterTitle, $nChapterNumber, $sChapterBody, $sTableOfContents, $sBookTitle, $sBookUrl, $sHtmlNav)
	{
		$HTML = <<<EOF

	<h1><a href="$sBookUrl">$sBookTitle</a>: $sChapterTitle</h1>
	$sHtmlNav
	$sTableOfContents
	$sChapterBody
	$sHtmlNav
EOF;
		return $HTML;
	}
	
	function tocOpen()
	{
		$HTML = <<<EOF

	<div class="docindex">
	<h2>{$this->t_('cc_docchapter_toc')}</h2>
	<ol>
EOF;
		return $HTML;
	}
	
	function tocEntry($sTitle, $sUrl, $n)
	{
		$HTML = <<<EOF

	<li><a href="$sUrl">$sTitle</a></li>
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
	
	function navBook(
			$sBookTitle, $sBookUrl,
			$sPrevChapterTitle=false, $sPrevChapterNumber, $sPrevChapterUrl,
			$sCurChapterTitle, $sCurChapterNumber,
			$sNextChapterTitle=false, $sNextChapterNumber, $sNextChapterUrl
	)
	{
		$HTML = <<<EOF

	<div class="navbook">
		<div class="navbook_left">
EOF;
		if ($sPrevChapterTitle)
		{
			$HTML .= <<<EOF

			<span>&lt;&lt;<a href="$sPrevChapterUrl">$sPrevChapterNumber.$sPrevChapterTitle</a></span>
EOF;
		}
		else
		{
			$HTML .= '&nbsp;';
		}
		$HTML .= <<<EOF
		</div>
		<div class="navbook_center">
			&nbsp;
		</div>
		<div class="navbook_right">
EOF;
		if ($sNextChapterTitle)
		{
			$HTML .= <<<EOF

			<span><a href="$sNextChapterUrl">$sNextChapterNumber.$sNextChapterTitle</a>&gt;&gt;</span>
EOF;
		}
		else
		{
			$HTML .= '&nbsp;';
		}
		$HTML .= <<<EOF
		</div>
		<div style="clear:both"></div>
	</div>
EOF;
		return $HTML;
	}
}

?>