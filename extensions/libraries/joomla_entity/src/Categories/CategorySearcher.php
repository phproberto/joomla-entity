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
 * @since  1.4.0
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

		// Filter: descendant
		if (null !== $this->options->get('filter.descendant_id'))
		{
			$ids = ArrayHelper::toInteger((array) $this->options->get('filter.descendant_id'));

			$query->innerJoin($db->qn('#__categories', 'dsc2') . ' ON ' . $db->qn('dsc2.id') . ' = ' . $db->qn('c.id'))
				->innerJoin(
					$db->qn('#__categories', 'dsc1') . ' ON ' . $db->qn('dsc1.rgt') . ' < ' . $db->qn('dsc2.rgt') .
					' AND ' . $db->qn('dsc1.lft') . ' > ' . $db->qn('dsc2.lft')
				);

			$query->where($db->qn('dsc1.id') . ' IN(' . implode(',', $ids) . ')');
		}

		// Filter: ancestor
		if (null !== $this->options->get('filter.ancestor_id'))
		{
			$ids = ArrayHelper::toInteger((array) $this->options->get('filter.ancestor_id'));

			$query->innerJoin($db->qn('#__categories', 'anc2') . ' ON ' . $db->qn('anc2.id') . ' = ' . $db->qn('c.id'))
				->innerJoin(
					$db->qn('#__categories', 'anc1') . ' ON ' . $db->qn('anc1.lft') . ' < ' . $db->qn('anc2.lft') .
					' AND ' . $db->qn('anc1.rgt') . ' > ' . $db->qn('anc2.rgt')
				);

			$query->where($db->qn('anc1.id') . ' IN(' . implode(',', $ids) . ')');
		}

		// Filter: search
		if (null !== $this->options->get('filter.search'))
		{
			$search = $this->options->get('filter.search');
			$search = $db->quote(
				'%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%')
			);

			$query->where(
				'(c.title LIKE ' . $search
				. ' OR c.alias LIKE ' . $search
				. ' OR c.path LIKE ' . $search
				. ' OR c.extension LIKE ' . $search
				. ')'
			);
		}

		$query->order($db->escape($this->options->get('list.ordering')) . ' ' . $db->escape($this->options->get('list.direction')));

		return $query;
	}
}
