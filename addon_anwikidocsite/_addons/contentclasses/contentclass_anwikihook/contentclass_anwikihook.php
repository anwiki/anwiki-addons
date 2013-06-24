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
 * ContentClass: Anwiki hook documentation.
 * @package Anwiki
 * @version $Id: contentclass_anwikihook.php 172 2009-04-05 21:51:33Z anw $
 * @copyright 2007-2009 Antoine Walter
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License 3
 */
 
class AnwContentFieldPage_anwikihook_arg extends AnwContentFieldPage_container implements AnwIContentFieldPage_anwikihook_arg
{
	function init()
	{
		// arg name
		$oContentField = new AnwContentFieldPage_string(self::FIELD_NAME);
		$oContentField->setTranslatable(false);
		$this->addContentField($oContentField);
		
		// arg type
		$oContentField = new AnwContentFieldPage_radio(self::FIELD_TYPE);
		$oContentField->setTranslatable(false);
		
		$asEnumValues = array();
		$asEnumValues['string'] = 'string';
		$asEnumValues['integer'] = 'integer';
		$asEnumValues['boolean'] = 'boolean';
		$asEnumValues['float'] = 'float';
		$asEnumValues['object'] = 'object';
		$oContentField->setEnumValues($asEnumValues);
		$oContentField->setTranslatable(false);
		$this->addContentField($oContentField);
		
		// arg class (if type == instance)
		$oContentField = new AnwContentFieldPage_radio(self::FIELD_TYPECLASS);
		$oContentField->setTranslatable(false);
		
		$asEnumValues = array();
		$asEnumValues['AnwPage'] = 'AnwPage';
		$asEnumValues['AnwUser'] = 'AnwUser';
		$oContentField->setEnumValues($asEnumValues);
		
		$oContentMultiplicity = new AnwContentMultiplicity_multiple(0, 1);
		$oContentField->setMultiplicity($oContentMultiplicity);			
		$this->addContentField($oContentField);
		
		// arg comment
		$oContentField = new AnwContentFieldPage_xhtml(self::FIELD_COMMENT);
		$this->addContentField($oContentField);
	}
	
	function pubcall($sArg, $oContent, $oPage)
	{
		switch($sArg)
		{
			case self::PUB_TYPE:
				return self::argTypeStr($oContent, $this->getComponent());
				break;
		}
	}
	
	static function argTypeStr($oContent, $oInstance)
	{
		$sType = $oContent->getContentFieldValue(self::FIELD_TYPE);
		
		if ($sType == 'object')
		{
			$asClasses = $oContent->getContentFieldValues(self::FIELD_TYPECLASS);
			$sClass = $asClasses[0];
			$sType = $oInstance->t_('cc_anwikihookarg_type_object',array('classname'=>$sClass));
		}
		else
		{		
			if ($sType == 'boolean') $sType = $oInstance->t_('cc_anwikihookarg_type_boolean');
			else if ($sType == 'integer') $sType = $oInstance->t_('cc_anwikihookarg_type_integer');
			else if ($sType == 'float') $sType = $oInstance->t_('cc_anwikihookarg_type_float');
			else $sType = $oInstance->t_('cc_anwikihookarg_type_string');
		}
		return $sType;
	}
	
	
}

class AnwContentFieldPage_anwikihook_exception extends AnwContentFieldPage_container implements AnwIContentFieldPage_anwikihook_exception
{
	function init()
	{
		// arg class
		$oContentField = new AnwContentFieldPage_string(self::FIELD_CLASS);
		$oContentField->setTranslatable(false);
		$this->addContentField($oContentField);
		
		// arg comment
		$oContentField = new AnwContentFieldPage_xhtml(self::FIELD_COMMENT);
		$this->addContentField($oContentField);
	}
}

class AnwContentClassPageDefault_anwikihook extends AnwContentClassPage implements AnwIContentClassPageDefault_anwikihook
{
	function init()
	{
		// hook name
		$oContentField = new AnwContentFieldPage_string( self::FIELD_NAME );
		$oContentField->indexAs( self::INDEX_NAME );
		$oContentField->setTranslatable(false);
		$this->addContentField($oContentField);
		
		// hook comment
		$oContentField = new AnwContentFieldPage_xhtml( self::FIELD_COMMENT );
		$this->addContentField($oContentField);
		
		// hook details
		$oContentField = new AnwContentFieldPage_xhtml( self::FIELD_DETAILS );
		$this->addContentField($oContentField);
		
		// hook argreturn (if it's a vhook)
		$oContentField = new AnwContentFieldPage_anwikihook_arg( self::FIELD_ARGRETURN );
		$oContentMultiplicity = new AnwContentMultiplicity_multiple(0, 1);
		$oContentField->setMultiplicity($oContentMultiplicity);			
		$this->addContentField($oContentField);
		
		// hook args (excepted the returned arg for vhooks)
		$oContentField = new AnwContentFieldPage_anwikihook_arg( self::FIELD_ARGS );
		$oContentMultiplicity = new AnwContentMultiplicity_multiple(0, 99);
		$oContentField->setMultiplicity($oContentMultiplicity);			
		$this->addContentField($oContentField);
		
		// hook supported exceptions
		$oContentField = new AnwContentFieldPage_anwikihook_exception( self::FIELD_EXCEPTIONS );
		$oContentMultiplicity = new AnwContentMultiplicity_multiple(0, 99);
		$oContentField->setMultiplicity($oContentMultiplicity);			
		$this->addContentField($oContentField);
	}
	
	function toHtml($oContent, $oPage)
	{
		$oOutputHtml = new AnwOutputHtml( $oPage );
		
		$sName = $oContent->getContentFieldValue(self::FIELD_NAME);
		$sComment = $oContent->getContentFieldValue(self::FIELD_COMMENT);
		$sDetails = $oContent->getContentFieldValue(self::FIELD_DETAILS);
		$bIsVhook = self::isVhook($oContent);
		
		$oOutputHtml->setTitle($sName);
		
		$sHtmlArgs = $this->tpl()->argsOpen();
		
		//returned arg
		if ($bIsVhook)
		{
			$oHookArgReturnContent = $oContent->getSubContent(self::FIELD_ARGRETURN);
			$sArgReturnName = '$'.$oHookArgReturnContent->getContentFieldValue(AnwContentFieldPage_anwikihook_arg::FIELD_NAME);
			$sArgType = AnwContentFieldPage_anwikihook_arg::argTypeStr($oHookArgReturnContent, $this);
			$sArgComment = $oHookArgReturnContent->getContentFieldValue(AnwContentFieldPage_anwikihook_arg::FIELD_COMMENT);
			$sHtmlArgs .= $this->tpl()->argReturnRow($sArgReturnName, $sArgType, $sArgComment);
		}
		
		//all args
		$aoHookArgsContents = $oContent->getSubContents(self::FIELD_ARGS);
		$aaArgs = array();
		foreach ($aoHookArgsContents as $oHookArgContent)
		{
			$sArgName = '$'.$oHookArgContent->getContentFieldValue(AnwContentFieldPage_anwikihook_arg::FIELD_NAME);
			$sArgType = AnwContentFieldPage_anwikihook_arg::argTypeStr($oHookArgContent, $this);
			$sArgComment = $oHookArgContent->getContentFieldValue(AnwContentFieldPage_anwikihook_arg::FIELD_COMMENT);
			$sHtmlArgs .= $this->tpl()->argRow($sArgName, $sArgType, $sArgComment);
			$aaArgs[] = array($sArgName, $sArgType);
		}
		$sHtmlArgs .= $this->tpl()->argsClose();
		
		//exceptions
		$aaExceptions = array();
		$sHtmlExceptions = '';
		$aoHookExceptionsContents = $oContent->getSubContents(self::FIELD_EXCEPTIONS);
		if (count($aoHookExceptionsContents) > 0)
		{
			$sHtmlExceptions .= $this->tpl()->exceptionsOpen();
			foreach ($aoHookExceptionsContents as $oHookExceptionContent)
			{
				$sExceptionClass = $oHookExceptionContent->getContentFieldValue(AnwContentFieldPage_anwikihook_exception::FIELD_CLASS);
				$sExceptionComment = $oHookExceptionContent->getContentFieldValue(AnwContentFieldPage_anwikihook_exception::FIELD_COMMENT);
				$sHtmlExceptions .= $this->tpl()->exceptionRow($sExceptionClass, $sExceptionComment);
				$aaExceptions[] = array($sExceptionClass);
			}
			$sHtmlExceptions .= $this->tpl()->exceptionsClose();
		}
		
		//example
		$sPhpExample = 'function '.($bIsVhook?'vhook':'hook').'_'.$sName.'(';
		if ($bIsVhook) $sPhpExample .= $sArgReturnName.', ';
		foreach ($aaArgs as $asArg)
		{
			$sArgName = $asArg[0];
			$sArgType = $asArg[1];
			$sPhpExample .= $sArgName.', ';
		}
		$sPhpExample = substr($sPhpExample, 0, -2);
		$sPhpExample .= ')'."\n";
		$sPhpExample .= '{'."\n";
		$sPhpExample .= "\t".'//'.$this->t_('local_cc_anwikihook_example_codehere')."\n";
		foreach ($aaExceptions as $asException)
		{
			$sExceptionClass = $asException[0];
			$sPhpExample .= "\t".'if (/* '.$this->t_('local_cc_anwikihook_example_specialcase').' */) throw new '.$sExceptionClass.'();'."\n";
		}
		if ($bIsVhook) $sPhpExample .= "\t".'return '.$sArgReturnName.';'."\n";
		$sPhpExample .= '}'."\n";
		
		$sHtmlBody = $this->tpl()->anwikihook($sName, $sComment, $sDetails, $sHtmlArgs, $sHtmlExceptions, $sPhpExample);
			
		$oOutputHtml->setBody( $sHtmlBody );
		return $oOutputHtml;
	}
	
	function toFeedItem($oContent, $oPage)
	{
		$oFeedItem = new AnwFeedItem(
			$oContent->getContentFieldValue(self::FIELD_NAME, 0, true),
			AnwUtils::link($oPage),
			$oContent->getContentFieldValue(self::FIELD_COMMENT, 0, true)
		);
		return $oFeedItem;
	}
	
	function pubcall($sArg, $oContent, $oPage)
	{
		switch($sArg)
		{
			case self::PUB_NAME:
				return $oContent->getContentFieldValue(self::FIELD_NAME);
				break;
			
			case self::PUB_COMMENT:
				return $oContent->getContentFieldValue(self::FIELD_COMMENT);
				break;
			
			case self::PUB_ARGRETURN:
				return $oContent->getSubContent(self::FIELD_ARGRETURN);
				break;
			
			case self::PUB_ISVHOOK:
				return self::isVhook($oContent);
				break;
		}
	}
	
	private static function isVhook($oContent)
	{
		try
		{
			$oContent->getSubContent(self::FIELD_ARGRETURN);
			return true;
		}
		catch(AnwException $e){
			return false;
		}
	}
	
	
}

?>