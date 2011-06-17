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

jimport('joomla.application.component.controller');
jimport('joomla.application.component.helper');

require_once JPATH_COMPONENT.'/helpers/mailto.php';
require_once JPATH_COMPONENT.'/controller.php';

/**
 * TextException class for handling translatable messaged.
 *
 *  @since  8.0
 */
class TextException extends Exception
{
	/**
	 * @var    array
	 * @since  8.0
	 */
	protected $args = array();

	/**
	 * Overrides the Exception constructor to interogate the message variable.
	 *
	 * @param   mxied  $message  The message can be a string or an array.
	 * @param   long   $code
	 *
	 * @return  TextException
	 *
	 * @since   8.0
	 */
	public function __construct($message = null, $code = 0)
	{
		if (is_array($message)) {
			$this->args = $message;
			$message = $message[0];
		}

		parent::__construct($message, $code);
	}

	/**
	 * Get the JText::sprintf arguments.
	 *
	 * @return  array
	 *
	 * @since   8.0
	 */
	public function getArgs()
	{
		return $this->args;
	}
}

//
// Some experiments with exception handling.
//

// TODO This try-catch should be wrapping the component as a whole.
try
{
	// Configure the logger.
	/*JLog::addLogger(
		array('logger' => 'database'),
		JLog::ALL,
		array('mailto')
	);*/

	$controller = JController::getInstance('Mailto');
	$controller->execute(JRequest::getCmd('task'));
	$controller->redirect();
}
// Specific database exception.
catch (DatabaseException $e)
{
	JError::raiseError(500, $e->getMessage());
}
// Special exception that allows for JText::sprintf handling.
catch (TextException $e)
{
	JError::raiseNotice($e->getCode(), call_user_func_array(array('JText', 'sprintf'), $e->getArgs()));
}
catch (phpmailerException $e)
{
	JError::raiseWarning(500, $e->getMessage());
}
catch (Exception $e)
{
	$code = $e->getCode();

	if ($code == 403) {
		JError::raiseError($code, JText::_($e->getMessage()));
	}
	else {
		JError::raiseNotice($code, JText::_($e->getMessage()));
	}
}
