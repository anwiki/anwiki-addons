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
 * Interfaces for content-class anwikiaddon.
 * They are separated from ContentClassPage for performances improvements.
 * @package Anwiki
 * @version $Id: contentclass_anwikiaddon-interface.php 173 2009-04-08 19:58:01Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */

interface AnwIContentClassPageDefault_anwikiaddon
{
	const FIELD_NAME = "name";
	const FIELD_TITLE = "title";
	const FIELD_DESCRIPTION = "description";
	const FIELD_BODY = "body";
	const FIELD_CATEGORIES = "categories";
	const FIELD_VERSIONS = "versions";
	
	const PUB_NAME = "name";
	const PUB_TITLE = "title";
	const PUB_DESCRIPTION = "description";
	const PUB_BODY = "body";
	const PUB_DATE = "date";
	const PUB_CATEGORIES = "categories";
	const PUB_VERSIONS = "versions";
	
}

?>