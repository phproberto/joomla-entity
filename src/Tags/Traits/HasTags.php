<?php
/**
 * Joomla! common library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    GNU/GPL 2, http://www.gnu.org/licenses/gpl-2.0.htm
 */

namespace Phproberto\Joomla\Entity\Tags\Traits;

use Phproberto\Joomla\Entity\Tags\Tag;
use Phproberto\Joomla\Entity\EntityCollection;

defined('JPATH_PLATFORM') || die;

/**
 * Trait for entities that have tags.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasTags
{
	/**
	 * Associated tags.
	 *
	 * @var  EntityCollection
	 */
	protected $tags;

	/**
	 * Get the associated tags.
	 *
	 * @return  EntityCollection
	 */
	public function getTags()
	{

		if (null === $this->tags)
		{
			$this->tags = $this->loadTags();
		}

		return $this->tags;
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
	 * @return  EntityCollection
	 */
	abstract protected function loadTags();
}
