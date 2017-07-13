<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Content;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\EntityCollection;
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
	use EntityTraits\HasAccess, EntityTraits\HasFeatured, EntityTraits\HasLink, EntityTraits\HasImages, EntityTraits\HasMetadata;
	use EntityTraits\HasParams, EntityTraits\HasState, EntityTraits\HasUrls;

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
	protected function getColumnParams()
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
	public function getTable($name = '', $prefix = null, $options = array())
	{
		$name   = $name ?: 'Content';
		$prefix = $prefix ?: 'JTable';

		return parent::getTable($name, $prefix, $options);
	}

	/**
	 * Load the category from the database.
	 *
	 * @return  Category
	 */
	protected function loadCategory()
	{
		$column = $this->getColumnCategory();
		$data    = $this->getAll();

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
		$slug = $this->getSlug();

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
	 * @return  EntityCollection
	 */
	protected function loadTags()
	{
		if (!$this->hasId())
		{
			return new EntityCollection;
		}

		$items = $this->getTagsHelperInstance()->getItemTags('com_content.article', $this->id()) ?: array();

		$tags = array_map(
			function ($tag)
			{
				return Tag::instance($tag->id)->bind($tag);
			},
			$items
		);

		return new EntityCollection($tags);
	}
}
