<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users\Search;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Searcher\DatabaseSearcher;
use Phproberto\Joomla\Entity\Searcher\SearcherInterface;

/**
 * User search
 *
 * @since  1.6.0
 */
class UserSearch extends DatabaseSearcher implements SearcherInterface
{
	/**
	 * Default options for this finder.
	 *
	 * @return  array
	 */
	public function defaultOptions()
	{
		return array_merge(
			parent::defaultOptions(),
			[
				'list.ordering'    => 'u.name',
				'list.direction'   => 'ASC'
			]
		);
	}

	/**
	 * Retrieve the search query.
	 *
	 * @return  \JDatabaseQuery
	 */
	public function searchQuery()
	{
		$db = $this->db;

		$query = $db->getQuery(true)
			->select('u.*')
			->from($db->qn('#__users', 'u'));

		// Filter: blocked
		if (null !== $this->options->get('filter.blocked'))
		{
			$statuses = ArrayHelper::toInteger((array) $this->options->get('filter.blocked'));

			$query->where($db->qn('u.block') . ' IN(' . implode(',', $statuses) . ')');
		}

		// Filter: group
		if (null !== $this->options->get('filter.group'))
		{
			$ids = ArrayHelper::toInteger((array) $this->options->get('filter.group'));

			$query->leftJoin(
				$db->quoteName('#__user_usergroup_map', 'uum')
				. ' ON ' . $db->quoteName('uum.user_id') . ' = ' . $db->quoteName('u.id')
			)->where($db->qn('uum.group_id') . ' IN(' . implode(',', $ids) . ')');
		}

		// Filter: not group
		if (null !== $this->options->get('filter.not_group'))
		{
			$ids = ArrayHelper::toInteger((array) $this->options->get('filter.not_group'));

			$query->leftJoin(
				$db->quoteName('#__user_usergroup_map', 'uum2')
				. ' ON ' . $db->quoteName('uum2.user_id') . ' = ' . $db->quoteName('u.id')
			)->where($db->qn('uum2.group_id') . ' NOT IN(' . implode(',', $ids) . ')');
		}

		// Filter: search
		if (null !== $this->options->get('filter.search'))
		{
			$search = $this->options->get('filter.search');
			$search = $db->quote(
				'%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%')
			);

			$query->where(
				'(u.name LIKE ' . $search
				. ' OR u.username LIKE ' . $search
				. ' OR u.email LIKE ' . $search
				. ')'
			);
		}

		$query->order($db->escape($this->options->get('list.ordering')) . ' ' . $db->escape($this->options->get('list.direction')));

		return $query;
	}
}
