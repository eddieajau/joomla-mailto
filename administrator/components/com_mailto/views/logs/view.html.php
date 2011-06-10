<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_mailto
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
jimport('joomla.application.component.view');

/**
 * Mailto view.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_mailto
 * @since       8.0
 */
class MailtoViewLogs extends JView
{
	/**
	 * @var    array  The array of records to display in the list.
	 * @since  8.0
	 */
	protected $items;

	/**
	 * @var    JPagination  The pagination object for the list.
	 * @since  8.0
	 */
	protected $pagination;

	/**
	 * @var    JObject	The model state.
	 * @since  8.0
	 */
	protected $state;

	/**
	 * Prepare and display the Logs view.
	 *
	 * @return  void
	 * @since   8.0
	 */
	public function display()
	{
		// Initialise variables.
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		// Add the toolbar and display the view layout.
		$this->addToolbar();
		parent::display();
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 * @since   8.0
	 */
	protected function addToolbar()
	{
		// Initialise variables.
		$canDo	= MailtoHelper::getActions();

		JToolBarHelper::title(JText::_('COM_MAILTO_LOGS_TITLE'));

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_mailto');
		}
	}
}