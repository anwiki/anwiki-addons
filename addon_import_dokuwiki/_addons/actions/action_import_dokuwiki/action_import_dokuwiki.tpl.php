<?php

class AnwTemplateDefault_action_import_dokuwiki extends AnwTemplateOverride_global
{
	function showForm($sFormAction, $sDokuPathValue, $sInputNamePath, $sError=false)
	{
		$sErrorHtml = ($sError ? $sError : '');
		
		$HTML = <<<EOF

	<h1>{$this->t_('form_title')}</h1>
	<div class="explain">{$this->t_('form_explain')}</div>
	{$this->errorList($sError)}
	<form action="$sFormAction" method="post">
		{$this->t_('form_path')} <input type="text" name="$sInputNamePath" value="$sDokuPathValue"/>
		<input type="submit" value="{$this->t_('form_submit')}" class="insubmit"/>
	</form>
EOF;
		return $HTML;
	}
	
	function simulateImportBegin($sFormAction, $sInputNamePath, $sInputNameRun, $sInputNameSendPwd, $sDokuPath)
	{
		$HTML = <<<EOF

	<h1>{$this->t_('simulation_title')}</h1>
	<div class="explain">{$this->t_('simulation_explain')}</div>
	<form action="$sFormAction" method="post">
		<input type="hidden" name="$sInputNamePath" value="$sDokuPath"/>
		<input type="checkbox" id="sendpwd" name="$sInputNameSendPwd" value="true" checked="checked"/> <label for="sendpwd">{$this->t_('simulation_sendpwd')}</label><br/>
		<input name="$sInputNameRun" type="submit" value="{$this->t_('simulation_submit')}" class="insubmit"/>
	</form>
	
	<table style="margin:0px auto;">
		<tr>
			<th>{$this->t_('imported_login')}</th>
			<th>{$this->t_('imported_email')}</th>
			<th>{$this->t_('imported_password')}</th>
			<th>{$this->t_('imported_result')}</th>
		</tr>
EOF;
		return $HTML;
	}
	
	function userImportSuccess($oUser, $sPassword)
	{
		$HTML .= <<<EOF
		
		<tr>
			<td style="width:30%">{$oUser->getLogin()}</td>
			<td style="width:30%">{$oUser->getEmail()}</td>
			<td style="width:20%">{$sPassword}</td>
			<td style="color:green">OK</td>
		</tr>
EOF;
		return $HTML;
	}
	
	function userImportFail($sLogin, $sEmail, $sError)
	{
		$HTML .= <<<EOF
		
		<tr>
			<td style="width:30%">$sLogin</td>
			<td style="width:30%">$sEmail</td>
			<td style="width:20%">-</td>
			<td style="color:red">$sError</td>
		</tr>
EOF;
		return $HTML;
	}
	
	function simulateImportEnd()
	{
		$HTML = <<<EOF

	</table>
EOF;
		return $HTML;
	}
	
	function runImportBegin($bSendPwd)
	{
		$sInfoMail = ($bSendPwd ? $this->t_('run_explain_mailsent') : $this->t_('run_explain_mailnotsent'));
		$HTML = <<<EOF

	<h1>{$this->t_('run_title')}</h1>
	<div class="explain">{$this->t_('run_explain')}<br/><br/>$sInfoMail</div>
	
	<table style="margin:0px auto;">
		<tr>
			<th>{$this->t_('imported_login')}</th>
			<th>{$this->t_('imported_email')}</th>
			<th>{$this->t_('imported_password')}</th>
			<th>{$this->t_('imported_result')}</th>
		</tr>
EOF;
		return $HTML;
	}
	
	function runImportEnd()
	{
		$HTML = <<<EOF

	</table>
EOF;
		return $HTML;
	}
}

?>