<?php
/**
 * Joomla! common library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    GNU/GPL 2, http://www.gnu.org/licenses/gpl-2.0.htm
 */

namespace Phproberto\Joomla\Entity\Tags\Traits;

use Phproberto\Joomla\Entity\Tags\Tag;
use Phproberto\Joomla\Entity\Collection;

defined('JPATH_PLATFORM') || die;

/**
 * Trait for entities that have associated tags.
 *
 * @since  __DEPLOY_VERSION__
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
	public function getTags($reload = false)
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
		return $this->getTags()->has($id);
	}

	/**
	 * Check if this entity has associated tags.
	 *
	 * @return  boolean
	 */
	public function hasTags()
	{
		return !$this->getTags()->isEmpty();
	}

	/**
	 * Load associated tags from DB.
	 *
	 * @return  Collection
	 */
	abstract protected function loadTags();
}
