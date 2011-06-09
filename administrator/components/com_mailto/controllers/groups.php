<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_mailto
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controlleradmin');

/**
 * Groups Subcontroller.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_mailto
 * @since       8.0
 */
class MailtoControllerGroups extends JControllerAdmin
{
	/**
	 * @var    string  The prefix to use with controller messages.
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_MAILTO_GROUPS';

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  The prefix for the model class name.
	 * @param   string  $config  The model configuration array.
	 *
	 * @return  MailtoModelGroups	The model for the controller set to ignore the request.
	 * @since   1.6
	 */
	public function getModel($name = 'Group', $prefix = 'MailtoModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}
}