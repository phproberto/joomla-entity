<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

defined('_JEXEC') || die;

/**
 * Classes using multiple singleton instances.
 *
 * @since  1.0.0
 */
trait HasInstances
{
	/**
	 * Cached instances
	 *
	 * @var  array
	 */
	protected static $instances = array();

	/**
	 * Remove an instance from cache.
	 *
	 * @param   integer  $id  Class identifier
	 *
	 * @return  void
	 */
	public static function clear($id)
	{
		unset(static::$instances[get_called_class()][$id]);
	}

	/**
	 * Clear all instances from cache
	 *
	 * @return  void
	 */
	public static function clearAll()
	{
		unset(static::$instances[get_called_class()]);
	}

	/**
	 * Ensure that we retrieve a non-statically-cached instance.
	 *
	 * @param   integer  $id   Identifier of the instance
	 *
	 * @return  $this
	 */
	public static function fresh($id)
	{
		static::clear($id);

		return static::find($id);
	}

	/**
	 * Create and return a cached instance
	 *
	 * @param   integer  $id  Identifier of the instance
	 *
	 * @return  $this
	 */
	public static function find($id)
	{
		$class = get_called_class();

		if (empty(static::$instances[$class][$id]))
		{
			static::$instances[$class][$id] = new static($id);
		}

		return static::$instances[$class][$id];
	}
}
