<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_mailto
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

//JHtml::addIncludePath(JPATH_COMPONENT.'helpers/html');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
?>
<script type="text/javascript">
	// Attach a behaviour to the submit button to check validation.
	Joomla.submitbutton = function(task)
	{
		var form = document.id('group-form');
		if (task == 'group.cancel' || document.formvalidator.isValid(form)) {
			Joomla.submitform(task, form);
		}
		else {
			<?php JText::script('COM_MAILTO_ERROR_N_INVALID_FIELDS'); ?>
			// Count the fields that are invalid.
			var elements = form.getElements('fieldset').concat(Array.from(form.elements));
			var invalid = 0;

			for (var i = 0; i < elements.length; i++) {
				if (document.formvalidator.validate(elements[i]) == false) {
					valid = false;
					invalid++;
				}
			}

			alert(Joomla.JText._('COM_MAILTO_ERROR_N_INVALID_FIELDS').replace('%d', invalid));
		}
	}
</script>

<form action="<?php echo JRoute::_('index.php?option=com_mailto&layout=edit&id='.(int) $this->item->id); ?>"
	method="post" name="adminForm" id="group-form" class="form-validate">
	<div class="width-60 fltlft">
		<fieldset class="adminform">
			<ul class="adminformlist">
				<li>
					<?php echo $this->form->getLabel('title'); ?>
					<?php echo $this->form->getInput('title'); ?>
				</li>

				<li>
					<?php echo $this->form->getLabel('emails'); ?>
					<?php echo $this->form->getInput('emails'); ?>
				</li>


				<li>
					<?php echo $this->form->getLabel('note'); ?>
					<?php echo $this->form->getInput('note'); ?>
				</li>

				<li>
					<?php echo $this->form->getLabel('published'); ?>
					<?php echo $this->form->getInput('published'); ?>
				</li>

				<li>
					<?php echo $this->form->getLabel('ordering'); ?>
					<?php echo $this->form->getInput('ordering'); ?>
				</li>

				<li>
					<?php echo $this->form->getLabel('access'); ?>
					<?php echo $this->form->getInput('access'); ?>
				</li>

			</ul>
		</fieldset>
	</div>

	<input type="hidden" name="task" value="" />
	<?php echo JHtml::_('form.token'); ?>
</form>