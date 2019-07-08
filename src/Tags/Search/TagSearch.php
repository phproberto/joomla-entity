<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tags\Search;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\Utilities\ArrayHelper;
use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Tags\Tag;
use Phproberto\Joomla\Entity\Searcher\DatabaseSearcher;
use Phproberto\Joomla\Entity\Searcher\SearcherInterface;

/**
 * Tag search
 *
 * @since  1.7.0
 */
class TagSearch extends DatabaseSearcher implements SearcherInterface
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
				'list.ordering'  => 't.lft',
				'list.direction' => 'ASC'
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
			->select('t.*')
			->from($db->qn('#__tags', 't'));

		// Filter: access
		if (null !== $this->options->get('filter.access'))
		{
			$viewLevels = ArrayHelper::toInteger((array) $this->options->get('filter.access'));

			$query->where($db->qn('t.access') . ' IN(' . implode(',', $viewLevels) . ')');
		}

		// Filter: active_language
		if (true === $this->options->get('filter.active_language'))
		{
			$tag = Factory::getLanguage()->getTag();
			$query->where($db->qn('t.language') . ' = ' . $db->q($tag));
		}

		// Filter: active user access
		if (true === $this->options->get('filter.active_user_access'))
		{
			$viewLevels = ArrayHelper::toInteger(User::active()->getAuthorisedViewLevels());

			$query->where($db->qn('t.access') . ' IN(' . implode(',', $viewLevels) . ')');
		}

		// Filter: ancestor
		if (null !== $this->options->get('filter.ancestor_id'))
		{
			$ids = ArrayHelper::toInteger((array) $this->options->get('filter.ancestor_id'));

			$query->innerJoin($db->qn('#__tags', 'anc2') . ' ON ' . $db->qn('anc2.id') . ' = ' . $db->qn('t.id'))
				->innerJoin(
					$db->qn('#__tags', 'anc1') . ' ON ' . $db->qn('anc1.lft') . ' < ' . $db->qn('anc2.lft') .
					' AND ' . $db->qn('anc1.rgt') . ' > ' . $db->qn('anc2.rgt')
				);

			$query->where($db->qn('anc1.id') . ' IN(' . implode(',', $ids) . ')');
		}

		// Filter: content_item_id
		if (null !== $this->options->get('filter.content_item_id'))
		{
			$ids = ArrayHelper::toInteger((array) $this->options->get('filter.content_item_id'));

			$query->innerJoin(
				$db->qn('#__contentitem_tag_map', 'ctm')
				. ' ON ' . $db->qn('ctm.tag_id') . ' = ' . $db->qn('t.id')
			)->where($db->qn('ctm.content_item_id') . ' IN(' . implode(',', $ids) . ')');
		}

		// Filter: content_type_alias
		if (null !== $this->options->get('filter.content_type_alias'))
		{
			$types = array_map([$db, 'quote'], (array) $this->options->get('filter.content_type_alias'));

			$query->innerJoin(
				$db->qn('#__contentitem_tag_map', 'ctm1')
				. ' ON ' . $db->qn('ctm1.tag_id') . ' = ' . $db->qn('t.id')
			)->where($db->qn('ctm1.type_alias') . ' IN(' . implode(',', $types) . ')');
		}

		// Filter: descendant
		if (null !== $this->options->get('filter.descendant_id'))
		{
			$ids = ArrayHelper::toInteger((array) $this->options->get('filter.descendant_id'));

			$query->innerJoin($db->qn('#__tags', 'dsc2') . ' ON ' . $db->qn('dsc2.id') . ' = ' . $db->qn('t.id'))
				->innerJoin(
					$db->qn('#__tags', 'dsc1') . ' ON ' . $db->qn('dsc1.rgt') . ' < ' . $db->qn('dsc2.rgt') .
					' AND ' . $db->qn('dsc1.lft') . ' > ' . $db->qn('dsc2.lft')
				);

			$query->where($db->qn('dsc1.id') . ' IN(' . implode(',', $ids) . ')');
		}

		// Filter: id
		if (null !== $this->options->get('filter.id'))
		{
			$ids = ArrayHelper::toInteger((array) $this->options->get('filter.id'));

			$query->where($db->qn('t.id') . ' IN(' . implode(',', $ids) . ')');
		}

		// Filter: language
		if (null !== $this->options->get('filter.language'))
		{
			$languages = array_map([$db, 'quote'], (array) $this->options->get('filter.language'));

			$query->where($db->qn('t.language') . ' IN(' . implode(',', $languages) . ')');
		}

		// Filter: level
		if (null !== $this->options->get('filter.level'))
		{
			$levels = ArrayHelper::toInteger((array) $this->options->get('filter.level'));

			$query->where($db->qn('t.level') . ' IN(' . implode(',', $levels) . ')');
		}

		// Filter: not id
		if (null !== $this->options->get('filter.not_id'))
		{
			$ids = ArrayHelper::toInteger((array) $this->options->get('filter.not_id'));

			$query->where($db->qn('t.id') . ' NOT IN(' . implode(',', $ids) . ')');
		}

		// Filter: parent_id
		if (null !== $this->options->get('filter.parent_id'))
		{
			$parentsIds = ArrayHelper::toInteger((array) $this->options->get('filter.parent_id'));

			$query->where($db->qn('t.parent_id') . ' IN(' . implode(',', $parentsIds) . ')');
		}

		// Filter: published
		if (null !== $this->options->get('filter.published'))
		{
			$statuses = ArrayHelper::toInteger((array) $this->options->get('filter.published'));

			$query->where($db->qn('t.published') . ' IN(' . implode(',', $statuses) . ')');
		}

		// Filter: search
		if (null !== $this->options->get('filter.search'))
		{
			$search = $this->options->get('filter.search');
			$search = $db->quote(
				'%' . str_replace(' ', '%', $db->escape(trim($search), true) . '%')
			);

			$query->where(
				'(t.title LIKE ' . $search
				. ' OR t.alias LIKE ' . $search
				. ' OR t.path LIKE ' . $search
				. ')'
			);
		}

		$query->order($db->escape($this->options->get('list.ordering')) . ' ' . $db->escape($this->options->get('list.direction')));
		$query->group('t.id');

		return $query;
	}
}
