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
 * ContentFieldSettings for plugin: commontags.
 * @package Anwiki
 * @version $Id: plugin_commontags-settings.php 124 2009-02-08 16:28:08Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */
class AnwContentFieldSettings_pluginCommontagsSimpleItem extends AnwContentFieldSettings_container implements AnwISettings_pluginCommontagsSimpleItem
{
	function init()
	{
		parent::init();
		
		$oContentField = new AnwContentFieldSettings_xhtml(self::FIELD_SEARCH);
		$oContentMultiplicity = new AnwContentMultiplicity_multiple(1);
		$oContentField->setMultiplicity($oContentMultiplicity);
		$this->addContentField($oContentField);
		
		$oContentField = new AnwContentFieldSettings_xhtml(self::FIELD_REPLACE);
		$oContentField->setCheckPhpSyntax(false);
		$this->addContentField($oContentField);
	}
}

class AnwContentFieldSettings_pluginCommontagsCallbackItem extends AnwContentFieldSettings_container implements AnwISettings_pluginCommontagsCallbackItem
{
	function init()
	{
		parent::init();
		
		$oContentField = new AnwContentFieldSettings_xhtml(self::FIELD_SEARCH);
		$oContentMultiplicity = new AnwContentMultiplicity_multiple(1);
		$oContentField->setMultiplicity($oContentMultiplicity);
		$this->addContentField($oContentField);
		
		$oContentField = new AnwContentFieldSettings_xhtml(self::FIELD_REPLACE);
		$oContentField->setCheckPhpSyntax(false);
		$this->addContentField($oContentField);
	}
}

?>