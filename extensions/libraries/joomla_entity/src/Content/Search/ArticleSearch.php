<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Content\Search;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Searcher\DatabaseSearcher;
use Phproberto\Joomla\Entity\Searcher\SearcherInterface;

/**
 * Article search
 *
 * @since  1.4.0
 */
class ArticleSearch extends DatabaseSearcher implements SearcherInterface
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
				'list.ordering'    => 'a.ordering',
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
			->select('a.*')
			->from($db->qn('#__content', 'a'));

		// Filter: id
		if (null !== $this->options->get('filter.id'))
		{
			$ids = ArrayHelper::toInteger((array) $this->options->get('filter.id'));

			$query->where($db->qn('a.id') . ' IN(' . implode(',', $ids) . ')');
		}

		// Filter: not id
		if (null !== $this->options->get('filter.not_id'))
		{
			$ids = ArrayHelper::toInteger((array) $this->options->get('filter.not_id'));

			$query->where($db->qn('a.id') . ' NOT IN(' . implode(',', $ids) . ')');
		}

		// Filter: active user access
		if (true === $this->options->get('filter.active_user_access'))
		{
			$viewLevels = ArrayHelper::toInteger(User::active()->getAuthorisedViewLevels());

			$query->where($db->qn('a.access') . ' IN(' . implode(',', $viewLevels) . ')');
		}

		// Filter: access
		if (null !== $this->options->get('filter.access'))
		{
			$viewLevels = ArrayHelper::toInteger((array) $this->options->get('filter.access'));

			$query->where($db->qn('a.access') . ' IN(' . implode(',', $viewLevels) . ')');
		}

		// Filter: state
		if (null !== $this->options->get('filter.state'))
		{
			$statuses = ArrayHelper::toInteger((array) $this->options->get('filter.state'));

			$query->where($db->qn('a.state') . ' IN(' . implode(',', $statuses) . ')');
		}

		// Filter: search
		if (null !== $this->options->get('filter.search'))
		{
			$search = $this->options->get('filter.search');
			$search = $db->quote(
				'%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%')
			);

			$query->where(
				'(a.title LIKE ' . $search
				. ' OR a.alias LIKE ' . $search
				. ')'
			);
		}

		$query->order($db->escape($this->options->get('list.ordering')) . ' ' . $db->escape($this->options->get('list.direction')));

		return $query;
	}
}
