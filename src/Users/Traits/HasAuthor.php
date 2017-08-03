<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users\Traits;

use Phproberto\Joomla\Entity\Users\User;

defined('JPATH_PLATFORM') || die;

/**
 * Trait for entities that have an associated author.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasAuthor
{
	/**
	 * Entity author.
	 *
	 * @var  User
	 */
	protected $author;

	/**
	 * Get the alias for a specific DB column.
	 *
	 * @param   string  $column  Name of the DB column. Example: created_by
	 *
	 * @return  string
	 */
	abstract public function columnAlias($column);

	/**
	 * Get this entity author.
	 *
	 * @param   boolean  $reload  Force data reloading
	 *
	 * @return  User
	 */
	public function author($reload = false)
	{
		if ($reload || null === $this->author)
		{
			$this->author = $this->loadAuthor();
		}

		return $this->author;
	}

	/**
	 * Check if this entity has an associated author.
	 *
	 * @return  boolean
	 */
	public function hasAuthor()
	{
		$authorId = (int) $this->get($this->columnAlias('created_by'));

		return !empty($authorId);
	}

	/**
	 * Load entity's author.
	 *
	 * @return  User
	 *
	 * @throws  \InvalidArgumentException  Author property not found
	 */
	protected function loadAuthor()
	{
		$authorId = (int) $this->get($this->columnAlias('created_by'));

		return User::instance($authorId);
	}
}
