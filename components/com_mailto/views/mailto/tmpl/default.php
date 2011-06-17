<?php
/**
 * @version     $Id: default.php 21321 2011-05-11 01:05:59Z dextercowley $
 * @package     Joomla.Site
 * @subpackage  com_mailto
 * @copyright   Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');

// Prepare Javascript translation strings.
JText::script('COM_MAILTO_EMAIL_ERR_NOINFO');

?>
<script type="text/javascript">
	Joomla.submitbutton = function(pressbutton) {
		var form = document.getElementById('mailtoForm');

		// Do field validation.
		if (form.email_group_id) {
			if ((form.email_group_id.value == '' && form.mailto.value == '') || form.from.value == '') {
				alert(Joomla.JText._('COM_MAILTO_EMAIL_ERR_NOINFO'));
				return false;
			}
		}
		else if (form.mailto.value == '' || form.from.value == '') {
			alert(Joomla.JText._('COM_MAILTO_EMAIL_ERR_NOINFO'));
			return false;
		}
		form.submit();
	}
</script>

<div id="mailto-window">
	<h2>
		<?php echo JText::_('COM_MAILTO_EMAIL_TO_A_FRIEND'); ?>
	</h2>
	<div class="mailto-close">
		<a href="javascript: void window.close()" title="<?php echo JText::_('COM_MAILTO_CLOSE_WINDOW'); ?>">
		 <span><?php echo JText::_('COM_MAILTO_CLOSE_WINDOW'); ?> </span></a>
	</div>

	<form action="<?php echo JRoute::_('index.php?option=com_mailto&task=send&tmpl=component'); ?>" id="mailtoForm" method="post">
		<?php if (!empty($this->emailGroups)) : ?>
		<div class="formelm">
			<label for="emailGroup_field"><?php echo JText::_('COM_MAILTO_EMAIL_GROUP'); ?></label>
			<select name="email_group_id" id="emailGroup_field">
				<option value=""><?php echo JText::_('COM_MAILTO_SELECT_EMAIL_GROUP'); ?></option>
				<?php echo JHtml::_('select.options', $this->emailGroups); ?>
			</select>
		</div>
		<?php endif; ?>

		<div class="formelm">
			<label for="mailto_field"><?php echo JText::_('COM_MAILTO_EMAIL_TO'); ?></label>
			<input type="text" id="mailto_field" name="mailto" class="inputbox" size="25" value="<?php echo $this->data->mailto ?>"/>
		</div>
		<div class="formelm">
			<label for="sender_field">
			<?php echo JText::_('COM_MAILTO_SENDER'); ?></label>
			<input type="text" id="sender_field" name="sender" class="inputbox" value="<?php echo $this->data->sender ?>" size="25" />
		</div>
		<div class="formelm">
			<label for="from_field">
			<?php echo JText::_('COM_MAILTO_YOUR_EMAIL'); ?></label>
			<input type="text" id="from_field" name="from" class="inputbox" value="<?php echo $this->data->from ?>" size="25" />
		</div>
		<div class="formelm">
			<label for="subject_field">
			<?php echo JText::_('COM_MAILTO_SUBJECT'); ?></label>
			<input type="text" id="subject_field" name="subject" class="inputbox" value="<?php echo $this->data->subject ?>" size="25" />
		</div>
		<p>
			<button class="button" onclick="return Joomla.submitbutton('send');">
				<?php echo JText::_('COM_MAILTO_SEND'); ?>
			</button>
			<button class="button" onclick="window.close();return false;">
				<?php echo JText::_('COM_MAILTO_CANCEL'); ?>
			</button>
		</p>

		<input type="hidden" name="layout" value="<?php echo $this->getLayout();?>" />
		<input type="hidden" name="link" value="<?php echo urlencode($this->data->link); ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
