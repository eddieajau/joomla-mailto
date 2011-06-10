<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_mailto
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

//JHtml::addIncludePath(JPATH_COMPONENT.'/helpers/html');
JHtml::_('behavior.tooltip');

$user		= JFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo JRoute::_('index.php?option=com_mailto&view=logs');?>" method="post" name="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-select fltrt">
			<select name="filter_range" id="filter_range" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo JText::_('COM_MAILTO_OPTION_FILTER_DATE');?></option>
				<?php echo JHtml::_('select.options', MailtoHelper::getDateRangeOptions(),
					'value', 'text', $this->state->get('filter.range'));?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>




	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<?php echo JHtml::_('grid.sort', 'COM_mailto_message_heading', 'a.message', $listDirn, $listOrder); ?>
				</th>
				<th width="15%">
					<?php echo JHtml::_('grid.sort', 'JDATE', 'a.date', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td>
					<?php echo JText::sprintf(
						'COM_MAILTO_GROUP_SEND_LOG',
						$item->message->get('userName', $item->message->get('userId', '???')),
						$item->message->get('pageRoute', '???'),
						$item->message->get('pageTitle', '???'),
						$item->message->get('emailGroupTitle', '???')
					); ?>
				</td>
				<td class="center">
					<?php echo JHTML::_('date',$item->date, 'Y-m-d H:i'); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
