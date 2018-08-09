<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tags\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Tags\Tag;
use Phproberto\Joomla\Entity\Collection;

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
	abstract protected function loadTags();
}
