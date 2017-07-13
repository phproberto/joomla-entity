<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Content;

use Phproberto\Joomla\Entity\Categories\Category as BaseCategory;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Content\Article;
use Joomla\Registry\Registry;

/**
 * Content category entity.
 *
 * @since   __DEPLOY_VERSION__
 */
class Category extends BaseCategory
{
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
			$model->setState('filter.catid', $this->id());
		}

		return $model;
	}
}
