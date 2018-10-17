<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
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
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;
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
	 * Retrieve the alias of content type associated with this entity.
	 *
	 * @return  string
	 *
	 * @since   1.6.0
	 */
	public static function contentTypeAlias()
	{
		return 'com_content.category';
	}

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

	/**
	 * Search within this entity tags.
	 *
	 * @param   array   $options  Search options
	 *
	 * @return  Collection
	 *
	 * @since   1.7.0
	 */
	public function searchArticles(array $options = [])
	{
		if (!$this->hasId())
		{
			return new Collection;
		}

		$options['filter.category_id'] = $this->id();

		return Collection::fromData(
			ArticleSearch::instance($options)->search(),
			Article::class
		);
	}
}
