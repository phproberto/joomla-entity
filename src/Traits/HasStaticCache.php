<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Collection;

/**
 * For list models with search functions.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasStaticCache
{
	/**
	 * Static cache for items
	 *
	 * @var  array
	 */
	protected static $staticCache = [];

	/**
	 * Clear the static cache.
	 *
	 * @return  void
	 */
	public static function clearStaticCache()
	{
		static::$staticCache[get_called_class()] = [];
	}

	/**
	 * Delete data from the static cache.
	 *
	 * @param   string  $key  Key of the staticCache array
	 *
	 * @return  self
	 */
	protected function deleteFromStaticCache(string $key)
	{
		unset(static::$staticCache[get_called_class()][$key]);

		return $this;
	}

	/**
	 * Get data from the static cache.
	 *
	 * @param   string  $key  Key of the staticCache array
	 *
	 * @return  mixed
	 *
	 * @throws  \InvalidArgumentException
	 */
	protected function getFromStaticCache(string $key)
	{
		if (!$this->hasInStaticCache($key))
		{
			throw new \InvalidArgumentException(sprintf('`%s` does not exist in static cache'));
		}

		return static::$staticCache[get_called_class()][$key];
	}

	/**
	 * Store data in static cache.
	 *
	 * @param   string  $key    Key of the staticCache array
	 * @param   mixed   $value  Value to store
	 *
	 * @return  self
	 */
	protected function storeInStaticCache(string $key, $value)
	{
		$key = trim($key);

		if (array_key_exists(get_called_class(), static::$staticCache))
		{
			static::$staticCache[get_called_class()] = [];
		}

		static::$staticCache[get_called_class()][$key] = $value;

		return $this;
	}

	/**
	 * Check if static cache is empty.
	 *
	 * @return  boolean
	 */
	protected function hasEmptyStaticCache(): bool
	{
		return !array_key_exists(get_called_class(), static::$staticCache) || !count(static::$staticCache[get_called_class()]);
	}

	/**
	 * Check if a key exists in the static cache.
	 *
	 * @param   string  $key    Key of the staticCache array
	 *
	 * @return  boolean
	 */
	protected function hasInStaticCache(string $key): bool
	{
		return !$this->hasEmptyStaticCache() && array_key_exists($key, static::$staticCache[get_called_class()]);
	}

	/**
	 * Retrieve a value from the static cache or generate it and store it.
	 *
	 * @param   string    $key            Key of the staticCache array
	 * @param   callable  $generateValue  Function to generate value if not present in the cache.
	 *
	 * @return  mixed
	 */
	protected function getFromStaticCacheOrStore($key, callable $generateValue)
	{
		if (!$this->hasInStaticCache($key))
		{
			$this->storeInStaticCache($key, call_user_func($generateValue));
		}

		return $this->getFromStaticCache($key);
	}
}
