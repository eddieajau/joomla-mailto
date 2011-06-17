<?php
/**
 * @version     $Id: mailto.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @package     Joomla.Site
 * @subpackage  com_mailto
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

/**
 * Mailto model.
 *
 * @package     Joomla.Site
 * @subpackage  com_mailto
 * @since       8.0
 */
class MailtoModelMailto extends JModel
{
	/**
	 * Checks that the headers have not been tampered with.
	 *
	 * @return  void
	 *
	 * @since   8.0
	 * @throws  Exception
	 */
	public function checkHeaders()
	{
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
					throw new Exception('', 403);
				}
			}
		}

		// Free up memory
		unset($headers, $fields);
	}

	/**
	 * Checks that the content link is acceptable.
	 *
	 * @param   string  $value   The link to email.
	 *
	 * @return  void
	 *
	 * @since   8.0
	 * @throws  Exception
	 */
	public function checkContentLink($value)
	{
		// Verify that this is a local link.
		if (empty($value) || !JURI::isInternal($value)) {
			// Non-local url...
			throw new Exception('COM_MAILTO_EMAIL_NOT_SENT');
		}
	}

	/**
	 * Checks that an email address is acceptable.
	 *
	 * @param   mixed  $value  The a string or array of email addresses.
	 *
	 * @return  void
	 *
	 * @since   8.0
	 * @throws  Exception
	 */
	public function checkEmailAddress($value)
	{
		// JMailHelper::isEmailAddress doesn't support arrays.
		settype($value, 'array');

		foreach ($value as $e)
		{
			if (!JMailHelper::isEmailAddress($e)) {
				throw new TextException(array('COM_MAILTO_EMAIL_INVALID', $e));
			}
		}
	}

	/**
	 * Gets the content to be sent.
	 *
	 * @return  object
	 *
	 * @since   8.0
	 */
	public function getContent()
	{
		// TODO Allow for adapters to get other types of content.

	}

	/**
	 * Get the data for the email.
	 *
	 * @return  void
	 *
	 * @since   8.0
	 * @throws  Exception
	 */
	function &getData()
	{
		$user = JFactory::getUser();
		$data = new JObject;

		$data->link = $this->getState('email.link');
		$data->clearLink = MailtoHelper::validateHash($data->link);

		if ($data->link == '') {
			throw new Exception('COM_MAILTO_LINK_IS_MISSING', 403);
		}

		if ($user->get('id') > 0) {
			$data->sender	= $user->get('name');
			$data->from		= $user->get('email');
		}
		else {
			$data->sender	= $this->getState('email.sender');
			$data->from		= $this->getState('email.from');
		}

		$data->subject	= $this->getState('email.subject');
		$data->mailto	= $this->getState('email.mailto');

		return $data;
	}

	/**
	 * Prepares the email to send.
	 *
	 * @return  JMail
	 *
	 * @since   8.0
	 * @throws  Exception
	 */
	protected function getMail()
	{
		// Include dependancies.
		jimport('joomla.mail.helper');

		// Initialise variables.
		$user	= JFactory::getUser();
		$config	= JFactory::getConfig();
		$params	= JComponentHelper::getParams('com_mailto');

		$link	= MailtoHelper::validateHash($this->getState('email.link'));

		$this->checkContentLink($link);

		$toMail		= $this->getState('email.mailto');
		$groupId	= (int) $this->getState('email.group_id');
		$groupTitle	= '';
		$fromName	= $this->getState('email.sender');
		$fromMail	= $this->getState('email.from');
		$subject	= $this->getState('email.subject', JText::sprintf('COM_MAILTO_SENT_BY', $fromName));

		// Assemble the email data...the sexy way!
		$mail = JFactory::getMailer();

		// If we are using an email group, add the extra emails.
		if ($groupId) {
			$emailGroups	= $this->getEmailGroups(true);
			$groupSend		= $params->get('group_send');

			if (!isset($emailGroups[$groupId])) {
				throw new Exception('COM_MAILTO_ERROR_INVALID_EMAIL_GROUP', 403);
			}

			$groupTitle = $emailGroups[$groupId]->text;

			// Split the emails on comma or newline and add in the other emails from the form.
			$toMail = preg_split('#(\s*,\s*)|(\s*\n\s*)#i', trim($emailGroups[$groupId]->emails).','.$toMail);

			// Clean out any empty strings and validate.
			$toMail = array_filter($toMail);
			$this->checkEmailAddress($toMail);

			// Configure the way we are sending the mail.
			// Note all email addresses are treated the same way.
			if ($groupSend == 'bcc') {
				$mail->addBCC($toMail);
			}
			else if ($groupSend == 'cc') {
				$mail->addBCC($toMail);
			}
			else {
				$mail->recipient($toMail);
			}

			// Check if we are overriding the sender details (good idea if using bcc).
			$fromMail = $params->get('group_from_mail', $fromMail);
			$fromName = $params->get('group_from_name', $fromName);
		}
		else {
			// Not use an email group, so just validate and add a single email recipient.
			$this->checkEmailAddress($toMail);
			$mail->recipient($toMail);
		}

		// Validate and add the sender information.
		$this->checkEmailAddress($fromMail);
		$mail->setSender(array(
			$fromMail,
			JMailHelper::cleanAddress($fromName))
		);

		// Build the message to send.
		$body	= JText::sprintf('COM_MAILTO_EMAIL_MSG', $config->get('sitename'), $fromName, $fromMail, $link);

		// Assemble the email data...the sexy way!
		$mail->setSubject(
				JMailHelper::cleanSubject($subject)
			)
			->setBody(
				JMailHelper::cleanBody($body)
			);

		// Prepare the log message.
		$this->setState(
			'email.log',
			array(
				'userId'			=> $user->get('id'),
				'userName'			=> $user->get('name'),
				'pageRoute'			=> $link,
				'pageTitle'			=> $subject,
				'emailGroupId'		=> $groupId,
				'emailGroupTitle'	=> $groupTitle,
			)
		);

		return $mail;
	}

	/**
	 * Gets a list of the email groups that the user can send to.
	 *
	 * @param   bool  $withEmails  Optionally include the emails in the list.
	 *
	 * @return  array  An array of objects with properties of 'value' and 'text' suitable for a select list.
	 *
	 * @since   8.0
	 * @throws  DatabaseException
	 */
	public function getEmailGroups($withEmails = false)
	{
		// Initialise variables.
		$db		= $this->getDbo();
		$user	= JFactory::getUser();
		$groups	= implode(',', $user->getAuthorisedViewLevels());

		$query	= $db->getQuery(true)
			->select('a.id AS '.$db->qn('value'))
			->select('a.title AS '.$db->qn('text'))
			->from('#__mailto_email_groups AS a')
			->where('a.published = 1')
			->where('a.access IN ('.$groups.')')
			->order('a.ordering, a.title')
			;

		if ($withEmails) {
			$query->select('a.emails');
		}

		$db->setQuery($query);

		$result = $db->loadObjectList('value');

		// @deprecated  Gracefully handle legacy error handling.
		if ($n = $db->getErrorNum()) {
			throw new DatabaseException($db->getErrorMsg(), $n);
		}

		return $result;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.

	 * @return  void
	 * @since   1.0
	 */
	protected function populateState()
	{
		$this->setState(
			'email.link',
			JRequest::getVar('link', '', 'method', 'base64')
		);

		$this->setState(
			'email.group_id',
			JRequest::getInt('email_group_id', 0, 'method')
		);

		$this->setState(
			'email.mailto',
			JRequest::getString('mailto', '', 'post')
		);

		$this->setState(
			'email.sender',
			JRequest::getString('sender', '', 'post')
		);

		$this->setState(
			'email.from',
			JRequest::getString('from', '', 'post')
		);

		$this->setState(
			'email.subject',
			JRequest::getString('subject', '', 'post')
		);
	}

	/**
	 * Method to send the email.
	 *
	 * @return  void
	 *
	 * @throws  Exception
	 * @throws  DatabaseException
	 * @throws  TextException
	 */
	public function sendMail()
	{
		// Basic checks
		$this->checkHeaders();

		// Get the mail object.
		$mail = $this->getMail();

		// Get the log message.
		$log = $this->getState('email.log');

		if (!$mail->Send()) {
			JLog::add(json_encode($log), JLog::ERROR, 'mailto');

			throw new TextException('COM_MAILTO_EMAIL_NOT_SENT');
		}

		// Log the mail.
		JLog::add(json_encode($log), JLog::INFO, 'mailto');
	}
}