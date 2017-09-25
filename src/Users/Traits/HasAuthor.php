<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Users\Column;

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
	 * Get a property of this entity.
	 *
	 * @param   string  $property  Name of the property to get
	 * @param   mixed   $default   Value to use as default if property is null
	 *
	 * @return  mixed
	 *
	 * @throws  \InvalidArgumentException  Property does not exist
	 */
	abstract public function get($property, $default = null);

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
		$authorId = (int) $this->get($this->columnAlias(Column::AUTHOR));

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
		$authorId = (int) $this->get($this->columnAlias(Column::AUTHOR));

		return User::instance($authorId);
	}
}
