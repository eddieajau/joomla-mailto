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
	 * Get a list of date ranges for filtering.
	 *
	 * @return  array
	 *
	 * @since   8.0
	 */
	public static function getDateRangeOptions()
	{
		$options = array(
			JHtml::_('select.option', 'today', JText::_('COM_MAILTO_OPTION_RANGE_TODAY')),
			JHtml::_('select.option', 'past_week', JText::_('COM_MAILTO_OPTION_RANGE_PAST_WEEK')),
			JHtml::_('select.option', 'past_1month', JText::_('COM_MAILTO_OPTION_RANGE_PAST_1MONTH')),
			JHtml::_('select.option', 'past_3month', JText::_('COM_MAILTO_OPTION_RANGE_PAST_3MONTH')),
			JHtml::_('select.option', 'past_6month', JText::_('COM_MAILTO_OPTION_RANGE_PAST_6MONTH')),
			JHtml::_('select.option', 'past_year', JText::_('COM_MAILTO_OPTION_RANGE_PAST_YEAR')),
			JHtml::_('select.option', 'post_year', JText::_('COM_MAILTO_OPTION_RANGE_POST_YEAR')),
		);
		return $options;
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