<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Categories;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Phproberto\Joomla\Entity\Searcher\DatabaseSearcher;
use Phproberto\Joomla\Entity\Searcher\SearcherInterface;

/**
 * Category searcher.
 *
 * @since  __DEPLOY_VERSION__
 */
class CategorySearcher extends DatabaseSearcher implements SearcherInterface
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
				'filter.published' => 1,
				'filter.access'    => true,
				'list.ordering'    => 'c.lft',
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
			->select('c.*')
			->from($db->qn('#__categories', 'c'));

		// Filter: id
		if (null !== $this->options->get('filter.id'))
		{
			$ids = ArrayHelper::toInteger((array) $this->options->get('filter.id'));

			$query->where($db->qn('c.id') . ' IN(' . implode(',', $ids) . ')');
		}

		// Filter: not id
		if (null !== $this->options->get('filter.not_id'))
		{
			$ids = ArrayHelper::toInteger((array) $this->options->get('filter.not_id'));

			$query->where($db->qn('c.id') . ' NOT IN(' . implode(',', $ids) . ')');
		}

		// Filter: parent_id
		if (null !== $this->options->get('filter.parent_id'))
		{
			$parentsIds = ArrayHelper::toInteger((array) $this->options->get('filter.parent_id'));

			$query->where($db->qn('c.parent_id') . ' IN(' . implode(',', $parentsIds) . ')');
		}

		// Filter: access
		if (null !== $this->options->get('filter.access'))
		{
			if (true === $this->options->get('filter.access'))
			{
				$viewLevels = ArrayHelper::toInteger(Factory::getUser()->getAuthorisedViewLevels());
			}
			else
			{
				$viewLevels = ArrayHelper::toInteger((array) $this->options->get('filter.access'));
			}

			$query->where($db->qn('c.access') . ' IN(' . implode(',', $viewLevels) . ')');
		}

		// Filter: published
		if (null !== $this->options->get('filter.published'))
		{
			$statuses = ArrayHelper::toInteger((array) $this->options->get('filter.published'));

			$query->where($db->qn('c.published') . ' IN(' . implode(',', $statuses) . ')');
		}

		// Filter: extension
		if (null !== $this->options->get('filter.extension'))
		{
			$extensions = array_map([$db, 'quote'], (array) $this->options->get('filter.extension'));

			$query->where($db->qn('c.extension') . ' IN(' . implode(',', $extensions) . ')');
		}

		$query->order($db->escape($this->options->get('list.ordering')) . ' ' . $db->escape($this->options->get('list.direction')));

		return $query;
	}
}
