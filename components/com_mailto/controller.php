<?php
/**
 * @version     $Id: controller.php 21321 2011-05-11 01:05:59Z dextercowley $
 * @package     Joomla.Site
 * @subpackage  com_mailto
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

/**
 * @package     Joomla.Site
 * @subpackage  com_mailto
 * @since       1.5
 */
class MailtoController extends JController
{
	/**
	 * Override the display method to set a timeout in the session.
	 *
	 * @return  void
	 *
	 * @since   8.0
	 */
	public function display()
	{
		$session = JFactory::getSession();
		$session->set('com_mailto.formtime', time());

		parent::display();
	}

	/**
	 * Send the message and display a notice
	 *
	 * @return  void
	 *
	 * @since   1.5
	 * @throws  DatabaseException
	 * @throws  Exception
	 * @throws  TextException
	 */
	public function send()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$tmpl		= JRequest::getCmd('tmpl');
		$session	= JFactory::getSession();
		$timeout	= $session->get('com_mailto.formtime', 0);

		if ($timeout == 0 || time() - $timeout < 20) {
			throw new Exception('COM_MAILTO_ERROR_EMAIL_FLOOD');
		}

		$this->getModel()->sendMail();
die('HERE');
		$this->setRedirect(
			JRoute::_('index.php?option=com_mailto&view=sent'.($tmpl ? 'tmpl='.$tmpl : ''))
		);
	}
}
