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
class MailtoViewGroup extends JView
{
	/**
	 * @var    JObject	The data for the record being displayed.
	 * @since  8.0
	 */
	protected $item;

	/**
	 * @var    JForm  The form object for this record.
	 * @since  8.0
	 */
	protected $form;

	/**
	 * @var    JObject  The model state.
	 * @since  8.0
	 */
	protected $state;

	/**
	 * Prepare and display the Group view.
	 *
	 * @return  void
	 * @since   8.0
	 */
	public function display()
	{
		// Intialiase variables.
		$this->item		= $this->get('Item');
		$this->form		= $this->get('Form');
		$this->state	= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

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
		JRequest::setVar('hidemainmenu', true);

		$user		= JFactory::getUser();
		$isNew		= ($this->item->id == 0);
		$checkedOut	= !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		$canDo		= MailtoHelper::getActions();

		JToolBarHelper::title(JText::_('COM_Mailto_'.($checkedOut ? 'VIEW_Group' : ($isNew ? 'ADD_Group' : 'EDIT_Group')).'_TITLE'));

		// If not checked out, can save the item.
		if (!$checkedOut && $canDo->get('core.edit')) {
			JToolBarHelper::apply('group.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('group.save', 'JTOOLBAR_SAVE');
			JToolBarHelper::custom('group.save2new', 'save-new.png', null, 'JTOOLBAR_SAVE_AND_NEW', false);
		}

		// If an existing item, can save to a copy.
		if (!$isNew && $canDo->get('core.create')) {
			JToolBarHelper::custom('group.save2copy', 'save-copy.png', null, 'JTOOLBAR_SAVE_AS_COPY', false);
		}

		if (empty($this->item->id))  {
			JToolBarHelper::cancel('group.cancel', 'JTOOLBAR_CANCEL');
		}
		else {
			JToolBarHelper::cancel('group.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}