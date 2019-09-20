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
	private static $instance;

	/**
	 * Clear singleton instance.
	 *
	 * @return  void
	 */
	public static function clearInstance()
	{
		self::$instance = null;
	}

	/**
	 * Gets an instance or create it.
	 *
	 * @return  self
	 */
	public static function instance()
	{
		if (null === self::$instance)
		{
			self::$instance = new self;
		}

		return self::$instance;
	}
}
