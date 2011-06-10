<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_mailto
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

/**
 * Logs model.
 *
 * @package     Joomla.Administrator
 * @subpackage  com_mailto
 * @since       8.0
 */
class MailtoModelLogs extends JModelList
{
	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.

	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 * @since   8.0
	 */
	protected function populateState($ordering = 'a.date', $direction = 'desc')
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		$range = $app->getUserStateFromRequest($this->context.'.range', 'filter_range');
		$this->setState('filter.range', $range);

		// Set list state ordering defaults.
		parent::populateState($ordering, $direction);
	}

	/**
	 * Overrides the getItems method to attach additional metrics to the list.
	 *
	 * @return	mixed	An array of data items on success, false on failure.
	 * @since	1.6.1
	 */
	public function getItems()
	{
		// Get a storage key.
		$store = $this->getStoreId('getItems');

		// Try to load the data from internal storage.
		if (!empty($this->cache[$store])) {
			return $this->cache[$store];
		}

		// Load the list items.
		$items = parent::getItems();

		// If emtpy or an error, just return.
		if (empty($items)) {
			return array();
		}

		// Post-process the JSON message field.
		foreach ($items as $item)
		{
			$item->message = new JObject(json_decode($item->message));
		}

		// Add the items to the internal cache.
		$this->cache[$store] = $items;

		return $this->cache[$store];
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return  JDatabaseQuery
	 * @since   8.0
	 */
	protected function getListQuery()
	{
		// Initialise variables.
		$db		= $this->getDbo();
		$query	= $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from('#__log_entries AS a');

			// Apply the range filter.
		if ($range = $this->getState('filter.range')) {

			// Get UTC for now.
			$dNow = new JDate;
			$dStart = clone $dNow;

			switch ($range)
			{
				case 'past_week':
					$dStart->modify('-7 day');
					break;
				case 'past_1month':
					$dStart->modify('-1 month');
					break;
				case 'past_3month':
					$dStart->modify('-3 month');
					break;
				case 'past_6month':
					$dStart->modify('-6 month');
					break;
				case 'post_year':
				case 'past_year':
					$dStart->modify('-1 year');
					break;

				case 'today':
					// Ranges that need to align with local 'days' need special treatment.
					$app	= JFactory::getApplication();
					$offset	= $app->getCfg('offset');

					// Reset the start time to be the beginning of today, local time.
					$dStart	= new JDate('now', $offset);
					$dStart->setTime(0,0,0);

					// Now change the timezone back to UTC.
					$dStart->setOffset(0);
					break;
			}

			if ($range == 'post_year') {
				$query->where(
					'a.date < '.$db->quote($dStart->format('Y-m-d H:i:s'))
				);
			}
			else {
				$query->where(
					'a.date >= '.$db->quote($dStart->format('Y-m-d H:i:s')).
					' AND a.date <='.$db->quote($dNow->format('Y-m-d H:i:s'))
				);
			}
		}

		// Add the list ordering clause.
		$orderCol	= $this->state->get('list.ordering');
		$orderDirn	= $this->state->get('list.direction');
		if ($orderCol == 'a.ordering' || $orderCol == 'category_title') {
			$orderCol = 'category_title '.$orderDirn.', a.ordering';
		}
		$query->order($db->getEscaped($orderCol.' '.$orderDirn));

		//echo nl2br(str_replace('#__','jos_',$query));
		return $query;
	}
}