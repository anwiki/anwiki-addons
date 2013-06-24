<?php

class AnwTemplateDefault_contentclass_anwikihook extends AnwTemplateOverride_global
{
	function anwikihook($sName, $sComment, $sDetails, $sHtmlArgs, $sHtmlExceptions, $sPhpExample)
	{
		$HTML = <<<EOF

<h1><a href="en/hooks-reference" style="text-decoration:none">{$this->t_('local_cc_anwikihook_hooksreference')}:</a> $sName</h1>
<div>$sComment</div>
<div style="margin-top:1.2em">$sDetails</div>

<h2>{$this->t_('local_cc_anwikihook_args')}</h2>
$sHtmlArgs

$sHtmlExceptions

<h2>{$this->t_('local_cc_anwikihook_example')}</h2>
<code>$sPhpExample</code>
EOF;
		return $HTML;
	}
	
	
	
	
	//--------
	
	function argsOpen()
	{
		$HTML = <<<EOF

	<ul>
EOF;
		return $HTML;
	}
	
	function argReturnRow($sArgName, $sArgType, $sArgComment)
	{
		$HTML = <<<EOF

		<li>
			<b>$sArgName </b><i>($sArgType)</i> : $sArgComment.
			<warn>{$this->t_('local_cc_anwikihook_args_mustreturn')}</warn>
		</li>
EOF;
		return $HTML;
	}
	
	function argRow($sArgName, $sArgType, $sArgComment)
	{
		$HTML = <<<EOF

		<li>
			<b>$sArgName </b><i>($sArgType)</i> : $sArgComment
		</li>
EOF;
		return $HTML;
	}
	
	function argsClose()
	{
		$HTML = <<<EOF

	</ul>
EOF;
		return $HTML;
	}
	
	//--------
	
	function exceptionsOpen()
	{
		$HTML = <<<EOF

	<h2>{$this->t_('local_cc_anwikihook_exceptions')}</h2>
	<ul>
EOF;
		return $HTML;
	}	
	function exceptionRow($sExceptionName, $sExceptionComment)
	{
		$HTML = <<<EOF

		<li><b>$sExceptionName :</b> $sExceptionComment</li>
EOF;
		return $HTML;
	}	
	function exceptionsClose()
	{
		$HTML = <<<EOF

	</ul>
EOF;
		return $HTML;
	}
}

?>