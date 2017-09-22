<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Content;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Content\Article;
use Phproberto\Joomla\Entity\Core\Traits as CoreTraits;
use Phproberto\Joomla\Entity\Tags\Traits as HasTraits;
use Phproberto\Joomla\Entity\Categories\Category as BaseCategory;

/**
 * Content category entity.
 *
 * @since   __DEPLOY_VERSION__
 */
class Category extends BaseCategory
{
	use CoreTraits\HasLink;
	use Traits\HasArticles;

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
				return Article::instance($item->id)->bind($item);
			},
			$this->getArticlesModel()->getItems() ?: array()
		);

		return new Collection($articles);
	}

	/**
	 * Load the link to this entity.
	 *
	 * @return  string
	 */
	protected function loadLink()
	{
		\JLoader::register('ContentHelperRoute', JPATH_SITE . '/components/com_content/helpers/route.php');

		return \JRoute::_(\ContentHelperRoute::getCategoryRoute($this->slug()));
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
