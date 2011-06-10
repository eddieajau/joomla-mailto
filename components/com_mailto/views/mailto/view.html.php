<?php
/**
 * @version     $Id: view.html.php 21097 2011-04-07 15:38:03Z dextercowley $
 * @package     Joomla.Site
 * @subpackage  com_mailto
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

/**
 * @package     Joomla.Site
 * @subpackage  com_mailto
 * @since       1.5
 */
class MailtoViewMailto extends JView
{
	/**
	 * Display the view.
	 *
	 * @return  void
	 *
	 * @since   1.5
	 */
	function display($tpl = null)
	{
		$data = $this->getData();

		if ($data === false) {
			return false;
		}

		$this->set('data'  , $data);

		parent::display($tpl);
	}

	/**
	 * Get the data for the link.
	 *
	 * @return  void
	 *
	 * @since   1.5
	 */
	function &getData()
	{
		$user = JFactory::getUser();
		$data = new stdClass();

		$data->link = urldecode(JRequest::getVar('link', '', 'method', 'base64'));

		if ($data->link == '') {
			JError::raiseError(403, JText::_('COM_MAILTO_LINK_IS_MISSING'));
			$false = false;
			return $false;
		}

		// Load with previous data, if it exists
		$mailto		= JRequest::getString('mailto', '', 'post');
		$sender		= JRequest::getString('sender', '', 'post');
		$from		= JRequest::getString('from', '', 'post');
		$subject	= JRequest::getString('subject', '', 'post');

		if ($user->get('id') > 0) {
			$data->sender	= $user->get('name');
			$data->from		= $user->get('email');
		}
		else {
			$data->sender	= $sender;
			$data->from		= $from;
		}

		$data->subject	= $subject;
		$data->mailto	= $mailto;

		return $data;
	}
}