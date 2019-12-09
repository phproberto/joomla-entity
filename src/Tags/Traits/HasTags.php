<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tags\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Tags\Tag;
use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Tags\Search\TagSearch;

/**
 * Trait for entities that have associated tags.
 *
 * @since  1.0.0
 */
trait HasTags
{
	/**
	 * Associated tags.
	 *
	 * @var  Collection
	 */
	protected $tags;

	/**
	 * Clear preloaded tags.
	 *
	 * @return  self
	 */
	public function clearTags()
	{
		$this->tags = null;

		return $this;
	}

	/**
	 * Retrieve the alias of content type associated with this entity.
	 *
	 * @return  string
	 *
	 * @since   1.6.0
	 */
	public static function contentTypeAlias()
	{
		return '';
	}

	/**
	 * Get the associated tags.
	 *
	 * @param   boolean  $reload  Force data reloading
	 *
	 * @return  Collection
	 */
	public function tags($reload = false)
	{
		if ($reload || null === $this->tags)
		{
			$this->tags = $this->loadTags();
		}

		return $this->tags;
	}

	/**
	 * Get an instance of the tags helper.
	 * Here mainly for tests.
	 *
	 * @return  \JHelperTags
	 */
	protected function getTagsHelperInstance()
	{
		return new \JHelperTags;
	}

	/**
	 * Check if this entity has an associated tag.
	 *
	 * @param   integer   $id  Tag identifier
	 *
	 * @return  boolean
	 */
	public function hasTag($id)
	{
		return $this->tags()->has($id);
	}

	/**
	 * Check if this entity has associated tags.
	 *
	 * @return  boolean
	 */
	public function hasTags()
	{
		return !$this->tags()->isEmpty();
	}

	/**
	 * Load associated tags from DB.
	 *
	 * @return  Collection
	 */
	protected function loadTags()
	{
		$contentTypeAlias = self::contentTypeAlias();

		if (!$this->hasId() || !$contentTypeAlias)
		{
			return new Collection;
		}

		$items = $this->getTagsHelperInstance()->getItemTags($contentTypeAlias, $this->id()) ?: array();

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
	 * Remove all tags assigned to this entity.
	 *
	 * @return  void
	 *
	 * @since   1.7.0
	 */
	public function removeAllTags()
	{
		$contentTypeAlias = self::contentTypeAlias();

		if (!$this->hasId())
		{
			throw new \RuntimeException("Trying to remove tags assigned to unsaved entiy", 500);
		}

		$db = $this->getDbo();

		$query = $db->getQuery(true)
			->delete('#__contentitem_tag_map')
			->where($db->qn('type_alias') . ' = ' . $db->q($contentTypeAlias))
			->where($db->qn('content_item_id') . ' = ' . (int) $this->id());

		$db->setQuery($query);
		$db->execute();
		TagSearch::clearStaticCache();
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
	public function searchTags(array $options = [])
	{
		$contentTypeAlias = self::contentTypeAlias();

		if (!$this->hasId() || !$contentTypeAlias)
		{
			return new Collection;
		}

		$options['filter.content_type_alias'] = $contentTypeAlias;
		$options['filter.content_item_id'] = $this->id();

		return Collection::fromData(
			TagSearch::instance($options)->search(),
			Tag::class
		);
	}
}
