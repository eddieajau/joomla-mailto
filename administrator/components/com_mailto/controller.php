<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_mailto
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 *  Component Controller
 *
 * @package		Joomla.Administrator
 * @subpackage	com_mailto
 * @since       8.0
 */
class MailtoController extends JController
{
	/**
	 * @var    string  The default view.
	 * @since  8.0
	 */
	protected $default_view = 'groups';

	/**
	 * Override the display method for the controller.
	 *
	 * @return  void
	 *
	 * @since   8.0
	 */
	function display()
	{
		// Load the component helper.
		require_once JPATH_COMPONENT.'/helpers/mailto.php';

		// Load the submenu.
		$view = JRequest::getCmd('view', 'groups');
		MailtoHelper::addSubmenu($view);

		// Display the view.
		parent::display();
	}
}