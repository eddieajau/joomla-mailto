<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_mailto
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Mailto display helper.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_mailto
 * @since       8.0
 */
class MailtoHelper
{
	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @return  JObject
	 * @since   1.6
	 */
	public static function getActions()
	{
		$user	= JFactory::getUser();
		$result	= new JObject;

		$actions = array(
			'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.state', 'core.delete'
		);

		foreach ($actions as $action)
		{
			$result->set($action, $user->authorise($action, 'mailto'));
		}

		return $result;
	}

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string  $vName  The name of the active view.
	 *
	 * @return  void
	 * @since   8.0
	 */
	public static function addSubmenu($vName)
	{
		JSubMenuHelper::addEntry(
			JText::_('COM_MAILTO_SUBMENU_GROUPS'),
			'index.php?option=com_mailto&view=groups',
			$vName == 'groups'
		);
		JSubMenuHelper::addEntry(
			JText::_('COM_MAILTO_SUBMENU_LOGS'),
			'index.php?option=com_mailto&view=logs',
			$vName == 'logs'
		);
	}

}