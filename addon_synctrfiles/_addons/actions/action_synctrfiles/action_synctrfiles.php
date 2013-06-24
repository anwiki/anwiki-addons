<?php
/**
 * Anwiki is a multilingual content management system <http://www.anwiki.com>
 * Copyright (C) 2007-2009 Antoine Walter <http://www.anw.fr>
 * 
 * Anwiki is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, version 3 of the License.
 * 
 * Anwiki is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Anwiki.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Synchronizing any translation files with Anwiki. This is a generic action which won't do anything out of the box.
 * You need to override this generic action and register your own handlers for synchronizing the translation files of your choice.
 * @package component:action:synctrfiles
 * @version $Id: action_synctrfiles.php 174 2009-04-08 20:00:29Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */

loadApp( dirname(__FILE__).'/class_synctrfiles.php' );
ini_set('max_execution_time', 300);

class AnwActionDefault_synctrfiles extends AnwActionGlobal
{
	const DO_IMPORT = "import";
	const DO_EXPORT = "export";
	
	private $oSync;
	private static $asSync;
	private static $asDo = array(self::DO_IMPORT, self::DO_EXPORT);
	
	function getNavEntry()
	{
		return $this->createManagementGlobalNavEntry();
	}
	
	protected function initHandlers()
	{		
		//override with your own handlers here...
	}
	
	function run()
	{
		$this->initHandlers();
		
		//what should we synchronize?
		$sType = AnwEnv::_GET("type");
		if (!isset(self::$asSync[$sType]))
		{
			$this->showHelp();
			return;
		}
		
		//how should we synchronize?
		$sDo = AnwEnv::_GET("do");
		if (!in_array($sDo, self::$asDo))
		{
			throw new AnwBadCallException();
		}
		
		if (AnwEnv::_GET("dorun"))
		{
			$sClassName = self::$asSync[$sType];
			$this->oSync = new $sClassName();
			
			print '<html><body><div style="font-size:11px; font-family:Verdana">';
			if ($sDo == self::DO_IMPORT)
			{
				$this->oSync->import();
			}
			else if ($sDo == self::DO_EXPORT)
			{
				$this->oSync->export();
			}
			print '</div><div style="background-color:green; color:#FFF; font-weight:bold; padding:5px;">UPDATE FINISHED</div></body></html>';
			exit;
		}
		else
		{
			$this->showHelp();
			$this->showIframe($sType, $sDo);
		}
	}
	
	protected static function registerSync($sName, $sClassName)
	{
		self::$asSync[$sName] = $sClassName;	
	}
	
	protected function showIframe($sType, $sDo)
	{
		$sFrameUrl = AnwUtils::alink('synctrfiles').'&amp;type='.$sType.'&amp;do='.$sDo.'&amp;dorun=1';
		$this->out .= <<<EOF

	<iframe src="$sFrameUrl" style="border:1px solid #000; width:98%; height:400px">
	</iframe>
EOF;
	}
	
	protected function showHelp()
	{

		foreach (self::$asSync as $sType => $null)
		{
				$this->out .= <<<EOF

		<fieldset style="margin:8px; width:45%; float:left; padding:5px; border:1px solid #000;">
			<legend><b>$sType</b> translation files</legend>
			<ul style="margin-left:15px">
				<li><a href="?a=synctrfiles&type={$sType}&amp;do=export">Export</a> (wiki -&gt; translation files)</li>
				<li><a href="?a=synctrfiles&type={$sType}&amp;do=import">Synchronize</a> (translation files -&gt; wiki)</li>
			</ul>
		</fieldset>
EOF;
		}
		$this->out .= <<<EOF

	</ul>
EOF;
	}
	
	/**
	 * When this action is used with plugin_autosynctrfiles,
	 * the plugin calls this function when a translation file is changed,
	 * in order to automatically export corresponding file.
	 */
	function notify_onchange_from_plugin($oPage)
	{
		try
		{
			if ($oPage->exists())
			{
				$this->initHandlers();
				
				//execute the handler (if any) for this translation file
				foreach (self::$asSync as $sHandlerClassName)
				{
					$oHandler = new $sHandlerClassName();
					
					if ($oHandler->exportPageIfHandled($oPage))
					{
						//handler found
						return;
					}
				}
			}
		}
		catch(AnwException $e){}
	}
}

?>