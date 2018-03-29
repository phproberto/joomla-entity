<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Content;

defined('_JEXEC') || die;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Tags\Tag;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Content\Article;
use Phproberto\Joomla\Entity\Acl\Traits\HasAcl;
use Phproberto\Joomla\Entity\Core\Traits\HasLink;
use Phproberto\Joomla\Entity\Tags\Traits\HasTags;
use Phproberto\Joomla\Entity\Acl\Contracts\Aclable;
use Phproberto\Joomla\Entity\Core\Traits as CoreTraits;
use Phproberto\Joomla\Entity\Content\Traits\HasArticles;
use Phproberto\Joomla\Entity\Categories\Category as BaseCategory;

/**
 * Content category entity.
 *
 * @since   1.0.0
 */
class Category extends BaseCategory implements Aclable
{
	use HasArticles, HasAcl, HasLink, HasTags;

	/**
	 * Load associated articles from DB.
	 *
	 * @return  Collection
	 */
	protected function loadArticles()
	{
		if (!$this->hasId())
		{
			return new Collection;
		}

		$articles = array_map(
			function ($item)
			{
				return Article::find($item->id)->bind($item);
			},
			$this->getArticlesModel()->getItems() ?: array()
		);

		return new Collection($articles);
	}

	/**
	 * Load the link to this entity.
	 *
	 * @return  string
	 *
	 * @codeCoverageIgnore
	 */
	protected function loadLink()
	{
		$slug = $this->slug();

		if (!$slug)
		{
			return null;
		}

		\JLoader::register('ContentHelperRoute', JPATH_SITE . '/components/com_content/helpers/route.php');

		return \JRoute::_(\ContentHelperRoute::getCategoryRoute($slug));
	}

	/**
	 * Load associated tags from DB.
	 *
	 * @return  Collection
	 */
	protected function loadTags()
	{
		if (!$this->hasId())
		{
			return new Collection;
		}

		$items = $this->getTagsHelperInstance()->getItemTags('com_content.category', $this->id()) ?: array();

		$tags = array_map(
			function ($tag)
			{
				return Tag::find($tag->id)->bind($tag);
			},
			$items
		);

		return new Collection($tags);
	}

	/**
	 * Get an instance of the articles model.
	 *
	 * @return  \JModelList
	 */
	protected function getArticlesModel()
	{
		\JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');

		$model = \JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

		$model->setState('params', new Registry);

		if ($this->hasId())
		{
			$model->setState('filter.category_id', $this->id());
		}

		return $model;
	}
}
