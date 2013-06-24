<?php
class AnwContentClassPageDefault_image extends AnwContentClassPage implements AnwCachedOutputKeyDynamic
{
	const FIELD_IMAGE = "image";
	const PUB_IMAGE = "image";

	const GET_SHOW = "show";

	function init()
	{
		// image data
		$oContentField = new AnwContentFieldPage_string(self::FIELD_IMAGE);
		$this->addContentField($oContentField);
	}

	function toHtml($oContent, $oPage)
	{
		$oOutputHtml = new AnwOutputHtml($oPage);

		if (AnwEnv::_GET(self::GET_SHOW)) {
			// show raw image
			$this->outputRaw($oContent);
		}
		else {
			// show <img src=.../>
			$sImageViewLink = AnwUtils::link($oPage, "view", array(self::GET_SHOW=>1));
			$oOutputHtml->setBody("<p style=\"text-align: center;\"><img src=\"$sImageViewLink\" /></p>");
			return $oOutputHtml;
		}
	}
	
	protected function outputRaw($oContent) {
		header("Content-Type: image/png; charset=UTF-8");
		$sImageData = $oContent->getContentFieldOutput(self::FIELD_IMAGE);
		$sImageData = preg_replace('!\[/?untr\]!', '', $sImageData);
		print base64_decode($sImageData);
		exit;
	}

	function getCachedOutputKeyDynamic()
	{
		//we need this in order to get the dynamic link "?show=1" working...
		return self::GET_SHOW.AnwEnv::_GET(self::GET_SHOW, "");
	}
	
	function toFeedItem($oContent, $oPage)
	{
		$oFeedItem = new AnwFeedItem(
			$oPage->getName(),
			AnwUtils::link($oPage),
			"..."
		);
		return $oFeedItem;
	}

	function pubcall($sArg, $oContent, $oPage)
	{
		switch($sArg)
		{
			case self::PUB_IMAGE:
				return $oContent->getContentFieldValue(self::FIELD_IMAGE);
		}
	}
}

?>