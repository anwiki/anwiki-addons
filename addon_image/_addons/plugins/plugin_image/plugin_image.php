<?php
class AnwPluginDefault_image extends AnwPlugin
{
	private $oPage;
	
	function vhook_output_run_before($sContentHtml, $oPage)
	{
		$this->oPage = $oPage;
		return $sContentHtml;
	}

	/**
	 * Bind image tags and add ?show=1 automatically.
	 */
	function vhook_outputhtml_run_body($sHtml)
	{
		// Bind images to the correct target
		$sHtml = preg_replace_callback('/(<img\b[^<>]*\ssrc=")([^"]+)(")/', array(&$this, 'bindImage'), $sHtml);
		return $sHtml;
	}

	private function bindImage($matches) {
		$sLink = $matches[2];
	    if (strstr($sLink, '://') || substr($sLink,0,1) == '#' || substr($sLink,0,1) == '/' || !AnwPage::isValidPageName($sLink))
		{
			return $matches[0];
		}
	
		$oPageTarget = AnwPageByName::getByNameOrSecondChance($sLink);
		if (!$oPageTarget)
		{
			return $matches[0];
		}
	
		$sPreferedLang = $this->oPage->getLang();
		$oPageTarget = $oPageTarget->getPageGroup()->getPreferedPage($sPreferedLang);
	
		$sLinkTarget = AnwUtils::link($oPageTarget->getName(), "view", array("show"=>1));
	    return $matches[1] . $sLinkTarget . $matches[3];
	}
}

?>