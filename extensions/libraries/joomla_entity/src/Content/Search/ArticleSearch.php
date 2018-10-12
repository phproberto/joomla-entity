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
use Phproberto\Joomla\Entity\Content\Article;
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

		// Filter: author_id
		if (null !== $this->options->get('filter.author_id'))
		{
			$ids = ArrayHelper::toInteger((array) $this->options->get('filter.author_id'));

			$query->where($db->qn('a.created_by') . ' IN(' . implode(',', $ids) . ')');
		}

		// Filter: editor_id
		if (null !== $this->options->get('filter.editor_id'))
		{
			$ids = ArrayHelper::toInteger((array) $this->options->get('filter.editor_id'));

			$query->where($db->qn('a.modified_by') . ' IN(' . implode(',', $ids) . ')');
		}

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

		// Filter: category
		if (null !== $this->options->get('filter.category_id'))
		{
			$viewLevels = ArrayHelper::toInteger((array) $this->options->get('filter.category_id'));

			$query->where($db->qn('a.catid') . ' IN(' . implode(',', $viewLevels) . ')');
		}

		// Filter: featured
		if (null !== $this->options->get('filter.featured'))
		{
			$statuses = ArrayHelper::toInteger((array) $this->options->get('filter.featured'));

			$query->where($db->qn('a.featured') . ' IN(' . implode(',', $statuses) . ')');
		}

		// Filter: language
		if (null !== $this->options->get('filter.language'))
		{
			$languages = array_map([$db, 'quote'], (array) $this->options->get('filter.language'));

			$query->where($db->qn('a.language') . ' IN(' . implode(',', $languages) . ')');
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

		// Filter: tag
		if (null !== $this->options->get('filter.tag'))
		{
			$tagIds = ArrayHelper::toInteger((array) $this->options->get('filter.tag'));

			$query->leftJoin(
				$db->quoteName('#__contentitem_tag_map', 'tagmap')
				. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('a.id')
				. ' AND ' . $db->quoteName('tagmap.type_alias') . ' = ' . $db->quote(Article::contentTypeAlias())
			)->where($db->qn('tagmap.tag_id') . ' IN(' . implode(',', $tagIds) . ')');
		}

		$query->order($db->escape($this->options->get('list.ordering')) . ' ' . $db->escape($this->options->get('list.direction')));

		return $query;
	}
}
