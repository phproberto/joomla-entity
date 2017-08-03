<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Content;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Content\Category;
use Phproberto\Joomla\Entity\Tags\Tag;
use Phproberto\Joomla\Entity\Categories\Traits as CategoriesTraits;
use Phproberto\Joomla\Entity\Core\Traits as CoreTraits;
use Phproberto\Joomla\Entity\Tags\Traits as TagsTraits;
use Phproberto\Joomla\Entity\Traits as EntityTraits;
use Phproberto\Joomla\Entity\Users\Traits as UsersTraits;

/**
 * Article entity.
 *
 * @since   __DEPLOY_VERSION__
 */
class Article extends Entity
{
	use CategoriesTraits\HasCategory;
	use CoreTraits\HasAccess, CoreTraits\HasAsset, CoreTraits\HasAssociations, CoreTraits\HasComponent, CoreTraits\HasFeatured, CoreTraits\HasMetadata;
	use CoreTraits\HasImages, CoreTraits\HasLink, CoreTraits\HasParams, CoreTraits\HasState, CoreTraits\HasTranslations;
	use TagsTraits\HasTags;
	use EntityTraits\HasUrls;
	use UsersTraits\HasAuthor, UsersTraits\HasEditor;

	/**
	 * Get the list of column aliases.
	 *
	 * @return  array
	 */
	public function columnAliases()
	{
		return array(
			'category_id' => 'catid',
			'params'      => 'attribs'
		);
	}

	/**
	 * Get an instance of the articles model.
	 *
	 * @param   array  $state  State to populate in the model
	 *
	 * @return  \JModelList
	 */
	protected function getArticlesModel(array $state = array())
	{
		\JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');

		$model = \JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

		$params = isset($state['params']) ? $state['params'] : new Registry;

		$model->setState('params', $params);

		foreach ($state as $key => $value)
		{
			$model->setState($key, $value);
		}

		return $model;
	}

	/**
	 * Load associations from DB.
	 *
	 * @return  \stdClass[]
	 *
	 * @codeCoverageIgnore
	 */
	protected function loadAssociations()
	{
		if (!$this->hasId())
		{
			return array();
		}

		return \JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $this->id());
	}

	/**
	 * Load the category from the database.
	 *
	 * @return  Category
	 */
	protected function loadCategory()
	{
		$column = $this->columnAlias('category_id');
		$data   = $this->all();

		if (array_key_exists($column, $data))
		{
			return Category::instance($data[$column]);
		}

		return new Category;
	}

	/**
	 * Load the link to this entity.
	 *
	 * @return  atring
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

		return \JRoute::_(\ContentHelperRoute::getArticleRoute($slug, (int) $this->get('catid'), $this->get('language')));
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

		$items = $this->getTagsHelperInstance()->getItemTags('com_content.article', $this->id()) ?: array();

		$tags = array_map(
			function ($tag)
			{
				return Tag::instance($tag->id)->bind($tag);
			},
			$items
		);

		return new Collection($tags);
	}

	/**
	 * Load associated translations from DB.
	 *
	 * @return  Collection
	 */
	protected function loadTranslations()
	{
		$ids = $this->associationsIds();

		if (empty($ids))
		{
			return new Collection;
		}

		$state = array(
			'filter.article_id' => array_values($ids)
		);

		$articles = array_map(
			function ($item)
			{
				return static::instance($item->id)->bind($item);
			},
			$this->getArticlesModel($state)->getItems() ?: array()
		);

		return new Collection($articles);
	}

	/**
	 * Get a table instance. Defauts to \JTableContent.
	 *
	 * @param   string  $name     Table name. Optional.
	 * @param   string  $prefix   Class prefix. Optional.
	 * @param   array   $options  Configuration array for the table. Optional.
	 *
	 * @return  \JTable
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function table($name = '', $prefix = null, $options = array())
	{
		$name   = $name ?: 'Content';
		$prefix = $prefix ?: 'JTable';

		return parent::table($name, $prefix, $options);
	}
}
