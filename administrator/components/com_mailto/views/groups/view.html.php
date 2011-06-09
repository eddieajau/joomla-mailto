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
 * Groups view.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_mailto
 * @since       8.0
 */
class MailtoViewGroups extends JView
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
	 * Prepare and display the Groups view.
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
		$state	= $this->get('State');
		$canDo	= MailtoHelper::getActions();

		JToolBarHelper::title(JText::_('COM_Mailto_Groups_TITLE'));

		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew('group.add', 'JTOOLBAR_NEW');
		}

		if ($canDo->get('core.edit')) {
			JToolBarHelper::editList('group.edit', 'JTOOLBAR_EDIT');
		}

		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::publishList('groups.publish', 'JTOOLBAR_PUBLISH');
			JToolBarHelper::unpublishList('groups.unpublish', 'JTOOLBAR_UNPUBLISH');
		}

		if ($state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'groups.delete','JTOOLBAR_EMPTY_TRASH');
		}
		else if ($canDo->get('core.edit.state')) {
			JToolBarHelper::trash('groups.trash','JTOOLBAR_TRASH');
		}

		if ($canDo->get('core.admin')) {
			JToolBarHelper::preferences('com_mailto');
		}
	}
}