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
	 * Gets a list of the email groups that the user can send to.
	 *
	 * @return  array  An array of objects with properties of 'value' and 'text' suitable for a select list.
	 *
	 * @since   8.0
	 * @throws  DatabaseException
	 */
	public function getEmailGroups()
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
			MailtoHelper::validateHash(
				urldecode(JRequest::getVar('link', '', 'method', 'base64'))
			)
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
}