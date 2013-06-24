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
 * ContentFieldSettings for plugin: smilies.
 * @package Anwiki
 * @version $Id: plugin_smilies-settings.php 157 2009-02-14 18:01:20Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */
class AnwContentFieldSettings_pluginSmiliesEmoticon extends AnwContentFieldSettings_container implements AnwISettings_pluginSmiliesEmoticon
{
	function init()
	{
		parent::init();
		
		$oContentField = new AnwContentFieldSettings_url(self::FIELD_IMAGE);
		$this->addContentField($oContentField);
		
		$oContentField = new AnwContentFieldSettings_string(self::FIELD_CODE);
		$oContentMultiplicity = new AnwContentMultiplicity_multiple(1);
		$oContentField->setMultiplicity($oContentMultiplicity);
		$this->addContentField($oContentField);
	}
}

?>