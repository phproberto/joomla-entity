<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Users\User;
use Phproberto\Joomla\Entity\Users\Column;

/**
 * Trait for entities that have an associated user.
 *
 * @since  1.0.0
 */
trait HasUser
{
	/**
	 * Entity user.
	 *
	 * @var  User
	 */
	protected $user;

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
	 * Check if entity has a property.
	 *
	 * @param   string   $property  Entity property name
	 * @param   mixed    $callback  Callable to execute for further verifications
	 *
	 * @return  boolean
	 */
	abstract public function has($property, callable $callback = null);

	/**
	 * Get this entity author.
	 *
	 * @return  User
	 */
	public function user()
	{
		if (null === $this->user)
		{
			$this->user = $this->loadUser();
		}

		return $this->user;
	}

	/**
	 * Retrieve associated user id.
	 *
	 * @return  integer
	 *
	 * @since   1.3.0
	 */
	public function userId()
	{
		try
		{
			return (int) $this->get($this->columnAlias(Column::USER));
		}
		catch (\Exception $e)
		{
			return 0;
		}
	}

	/**
	 * Check if this entity has an associated editor.
	 *
	 * @return  boolean
	 */
	public function hasUser()
	{
		$column = $this->columnAlias(Column::USER);

		if (!$this->has($column))
		{
			return false;
		}

		$userId = (int) $this->get($column);

		return !empty($userId);
	}

	/**
	 * Load entity's user.
	 *
	 * @return  User
	 *
	 * @throws  \InvalidArgumentException  User property not found
	 */
	protected function loadUser()
	{
		$userId = (int) $this->get($this->columnAlias(Column::USER));

		return User::find($userId);
	}
}
