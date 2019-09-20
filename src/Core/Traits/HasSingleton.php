<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

defined('_JEXEC') || die;

/**
 * Classes that provide singleton access.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasSingleton
{
	/**
	 * The instance.
	 *
	 * @var  $this
	 */
	protected static $instance;

	/**
	 * Clear singleton instance.
	 *
	 * @return  void
	 */
	public static function clearInstance()
	{
		static::$instance = null;
	}

	/**
	 * Gets an instance or create it.
	 *
	 * @return  static
	 */
	public static function instance()
	{
		if (null === static::$instance)
		{
			static::$instance = new static;
		}

		return static::$instance;
	}
}
