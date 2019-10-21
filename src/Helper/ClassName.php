<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Helper;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Contracts\EntityInterface;
use Phproberto\Joomla\Entity\Contracts\ExceptionInterface;

/**
 * Helper to deal with class name/namespaces things.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class ClassName
{
	/**
	 * Check if a class uses namespace.
	 *
	 * @param   object  $class  Class instance
	 *
	 * @return  boolean
	 */
	public static function inNamespace($class): bool
	{
		return (new \ReflectionClass($class))->inNamespace();
	}

	/**
	 * Retrieve class namespace.
	 *
	 * @param   object  $class  Class instance
	 *
	 * @return  string
	 */
	public static function namespace($class): string
	{
		return (new \ReflectionClass($class))->getNamespaceName();
	}

	/**
	 * Get the namespace parts.
	 *
	 * @param   object  $class  Class instance
	 *
	 * @return  string[]
	 */
	public static function namespaceParts($class): array
	{
		return explode('\\', self::namespace($class));
	}

	/**
	 * Guess the entity class
	 *
	 * @param   object  $class  Class instance
	 *
	 * @return  string
	 */
	public static function parentNamespace($class): string
	{
		$namespace = self::namespace($class);

		return substr($namespace, 0, strrpos($namespace, '\\'));
	}

	/**
	 * Retrieve class name without namespace.
	 *
	 * @param   object  $class  Class instance
	 *
	 * @return  string
	 */
	public static function withoutNamespace($class): string
	{
		return (new \ReflectionClass($class))->getShortName();
	}
}
