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

/**
 * Article entity.
 *
 * @since   __DEPLOY_VERSION__
 */
class Article extends Entity
{
	use CategoriesTraits\HasCategory, CoreTraits\HasAsset;
	use TagsTraits\HasTags;
	use EntityTraits\HasAccess, EntityTraits\HasAssociations, EntityTraits\HasFeatured, EntityTraits\HasLink, EntityTraits\HasImages;
	use EntityTraits\HasMetadata, EntityTraits\HasParams, EntityTraits\HasState, EntityTraits\HasTranslations, EntityTraits\HasUrls;

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

		$model->setState('params', new Registry);

		foreach ($state as $key => $value)
		{
			$model->setState($key, $value);
		}

		return $model;
	}

	/**
	 * Get the name of the column that stores category.
	 *
	 * @return  string
	 */
	protected function getColumnCategory()
	{
		return 'catid';
	}

	/**
	 * Get the name of the column that stores params.
	 *
	 * @return  string
	 */
	protected function columnParams()
	{
		return 'attribs';
	}

	/**
	 * Get a table.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  \JTable
	 *
	 * @codeCoverageIgnore
	 */
	public function table($name = '', $prefix = null, $options = array())
	{
		$name   = $name ?: 'Content';
		$prefix = $prefix ?: 'JTable';

		return parent::table($name, $prefix, $options);
	}

	/**
	 * Load associations from DB.
	 *
	 * @return  array
	 *
	 * @codeCoverageIgnore
	 */
	protected function loadAssociations()
	{
		if (!$this->hasId())
		{
			return array();
		}

		$associations = \JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $this->id());

		$result = array();

		foreach ($associations as $langTag => $association)
		{
			$result[$langTag] = static::instance($association->id)->bind($association);
		}

		return $result;
	}

	/**
	 * Load the category from the database.
	 *
	 * @return  Category
	 */
	protected function loadCategory()
	{
		$column = $this->getColumnCategory();
		$data    = $this->all();

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

		if (!$ids)
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
}
