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
	 */
	public function send()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app	= JFactory::getApplication();
		$session = JFactory::getSession();
		$db	= JFactory::getDbo();

		$timeout = $session->get('com_mailto.formtime', 0);

		if ($timeout == 0 || time() - $timeout < 20) {
			JError::raiseNotice(500, JText:: _ ('COM_MAILTO_EMAIL_NOT_SENT'));

			return;
		}

		jimport('joomla.mail.helper');

		$SiteName	= $app->getCfg('sitename');
		$MailFrom	= $app->getCfg('mailfrom');
		$FromName	= $app->getCfg('fromname');
		$link		= MailtoHelper::validateHash(JRequest::getCMD('link', '', 'post'));

		// Verify that this is a local link
		if (!$link || !JURI::isInternal($link)) {
			//Non-local url...
			JError::raiseNotice(500, JText:: _ ('COM_MAILTO_EMAIL_NOT_SENT'));

			return;
		}

		// An array of email headers we do not want to allow as input
		$headers = array(
			'Content-Type:',
			'MIME-Version:',
			'Content-Transfer-Encoding:',
			'bcc:',
			'cc:'
		);

		// An array of the input fields to scan for injected headers
		$fields = array(
			'mailto',
			'sender',
			'from',
			'subject',
		);

		/*
		 * Here is the meat and potatoes of the header injection test.  We
		 * iterate over the array of form input and check for header strings.
		 * If we find one, send an unauthorized header and die.
		 */
		foreach ($fields as $field)
		{
			foreach ($headers as $header)
			{
				if (strpos($_POST[$field], $header) !== false) {
					JError::raiseError(403, '');
				}
			}
		}

		// Free up memory
		unset ($headers, $fields);

		$email				= JRequest::getString('mailto', '', 'post');
		$sender				= JRequest::getString('sender', '', 'post');
		$from				= JRequest::getString('from', '', 'post');
		$subject_default	= JText::sprintf('COM_MAILTO_SENT_BY', $sender);
		$subject			= JRequest::getString('subject', $subject_default, 'post');

		// Check for a valid to address
		$error	= false;

		if (!$email  || !JMailHelper::isEmailAddress($email)) {
			$error	= JText::sprintf('COM_MAILTO_EMAIL_INVALID', $email);
			JError::raiseWarning(0, $error);
		}

		// Check for a valid from address
		if (!$from || !JMailHelper::isEmailAddress($from)) {
			$error	= JText::sprintf('COM_MAILTO_EMAIL_INVALID', $from);
			JError::raiseWarning(0, $error);
		}

		if ($error) {
			return $this->mailto();
		}

		// Build the message to send
		$msg	= JText :: _('COM_MAILTO_EMAIL_MSG');
		$body	= sprintf($msg, $SiteName, $sender, $from, $link);

		// Clean the email data
		$subject = JMailHelper::cleanSubject($subject);
		$body	 = JMailHelper::cleanBody($body);
		$sender	 = JMailHelper::cleanAddress($sender);

		// Send the email
		if (JUtility::sendMail($from, $sender, $email, $subject, $body) !== true) {
			JError::raiseNotice(500, JText:: _ ('COM_MAILTO_EMAIL_NOT_SENT'));

			return $this->mailto();
		}

		JRequest::setVar('view', 'sent');

		$this->display();
	}
}
