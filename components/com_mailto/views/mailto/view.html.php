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
	 * @var    object
	 * @since  8.0
	 */
	protected $data;

	/**
	 * @var    array
	 * @since  8.0
	 */
	protected $emailGroups;

	/**
	 * Display the view.
	 *
	 * @param   string  $tpl  The name of the template file to parse.
	 *
	 * @return  void
	 *
	 * @since   1.5
	 * @throws  Exception if an error is encountered in the models.
	 */
	function display($tpl = null)
	{
		// Initialise view data.
		$this->data			= $this->get('Data');
		$this->emailGroups	= $this->get('emailGroups');

		parent::display($tpl);
	}
}