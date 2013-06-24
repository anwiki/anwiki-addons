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
 * WARNING: This override action is given as an example, it WON'T work out of the box!
 * You need to edit this override action and register your own handlers for synchronizing the translation files of your choice.
 * @package component:action:synctrfiles
 * @version $Id: action_synctrfiles.php 202 2009-04-08 23:48:12Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */

class AnwActionOverride_synctrfiles extends AnwActionDefault_synctrfiles
{
        protected function initHandlers()
        {
                parent::initHandlers();

				// REGISTER YOUR OWN HANDLERS HERE!
                self::registerSync("anwikiDefaultActions", 'AnwSyncTrFiles_anwikiDefaultExample');
        }
}

/*
 * Synchronization of Anwiki default translation files.
 * - Original english PHP translation files are stored on file system, in _override/_i18n_sync/import/default/actions/[directory]/lang/[file].lang.en.php
 * - They are imported and synchronized into Anwiki, under the name "[lang]/_i18n/default/actions/[directory]/[file]"
 * - Then, translation files are exported to _override/_i18n_sync/export/default/actions/[directory]/lang/[file].lang.[lang].php
 */

abstract class AnwSyncTrFiles_anwikiDefaultExample extends AnwSyncTrFilesPhp // we are synchronizing PHP-array translation files
{
        const DIR_IMPORT_TRANSLATIONS = '_i18n_sync/import/';
        const DIR_EXPORT_TRANSLATIONS = '_i18n_sync/export/';

        function __construct()
        {        		
	        	$sComponentDir = 'default/'.ANWDIR_ACTIONS;
                $sSubDirBegin = "action_";
                $sNamespace = "_i18n/default/actions/";                
	        	
                $asPathsAndNamespaces = array();
         
                $sPathParentImport = ANWPATH_OVERRIDE.self::DIR_IMPORT_TRANSLATIONS.$sComponentDir;

                if (!is_dir($sPathParentImport) || !$mDirHandle = opendir($sPathParentImport))
                {
                        print "error reading ".$sPathParentImport;
                        return;
                }
                while (false !== ($sFilename = readdir($mDirHandle)))
                {
                        $sFilenameFull = $sPathParentImport.$sFilename.'/';
                        if (is_dir($sFilenameFull) && substr($sFilename, 0, strlen($sSubDirBegin)) == $sSubDirBegin)
                        {
                                //ok, synchronize translation files found in this directory
                                $sPathForImport = ANWPATH_OVERRIDE.self::DIR_IMPORT_TRANSLATIONS.$sComponentDir.$sFilename.'/'.ANWDIR_LANG;
                                if (is_dir($sPathForImport))
                                {
                                        $sPathForExport = ANWPATH_OVERRIDE.self::DIR_EXPORT_TRANSLATIONS.$sComponentDir.$sFilename.'/'.ANWDIR_LANG;
                                        AnwUtils::createDirIfNotExists($sPathForExport, 3); //create export dir if not exists
                                        $sWikiNamespace = $sNamespace.$sFilename.'/';
                                        $asPathsAndNamespaces[] = array('PATH_IMPORT' => $sPathForImport, 'PATH_EXPORT' => $sPathForExport, 'WIKI_NAMESPACE' => $sWikiNamespace);
                                }
                        }
                }
                closedir($mDirHandle);

                $vVarName = 'lang';
                parent::__construct($asPathsAndNamespaces, $vVarName);
        }

        protected function isGoodFile($sFilename, $sLang)
        {
                $sPattern = '!(.*?)\.lang\.'.$sLang.'\.php$!si';
                if (!preg_match($sPattern, $sFilename))
                {
                        return false;
                }

                return true;
        }
        
		protected function getGroupName($sFilename, $sLang)
        {
                return str_replace('.lang.'.$sLang.'.php', '', $sFilename);
        }

        protected function getTranslationFileName($sGroupName, $sLang)
        {
                return $sGroupName.'.lang.'.$sLang.'.php';
        }

        protected function getPathImportTranslations($sLang, $sPathImport)
        {
                //all languages in the same directory
                return $sPathImport;
        }

        protected function getPathExportTranslations($sLang, $sPathExport)
        {
                //all languages in the same directory
                return $sPathExport;
        }
}

?>