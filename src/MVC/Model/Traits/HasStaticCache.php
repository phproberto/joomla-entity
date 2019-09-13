<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Model\Traits;

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
	 * Gets static cache for this class
	 *
	 * @return  array
	 */
	protected function &getStaticCache(): array
	{
		$className = get_class($this);

		if (!isset(static::$staticCache[$className]))
		{
			static::$staticCache[$className] = [];
		}

		return static::$staticCache[$className];
	}
}
