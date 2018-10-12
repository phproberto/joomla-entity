<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Content\Search;

defined('_JEXEC') || die;

use Joomla\Utilities\ArrayHelper;
use Phproberto\Joomla\Entity\Content\Category;
use Phproberto\Joomla\Entity\Categories\Search\CategorySearch as BaseCategorySearc;

/**
 * Category search.
 *
 * @since  __DEPLOY_VERSION__
 */
class CategorySearch extends BaseCategorySearc
{
	/**
	 * Retrieve the search query.
	 *
	 * @return  \JDatabaseQuery
	 */
	public function searchQuery()
	{
		$db = $this->db;

		$query = parent::searchQuery()
			->where($db->qn('c.extension') . ' = ' . $db->q('com_content'));

		// Filter: tag
		if (null !== $this->options->get('filter.tag'))
		{
			$tagIds = ArrayHelper::toInteger((array) $this->options->get('filter.tag'));

			$query->leftJoin(
				$db->quoteName('#__contentitem_tag_map', 'tagmap')
				. ' ON ' . $db->quoteName('tagmap.content_item_id') . ' = ' . $db->quoteName('c.id')
				. ' AND ' . $db->quoteName('tagmap.type_alias') . ' = ' . $db->quote(Category::contentTypeAlias())
			)->where($db->qn('tagmap.tag_id') . ' IN(' . implode(',', $tagIds) . ')');
		}

		return $query;
	}
}
