<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Entity;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Entity;

/**
 * Asset entity.
 *
 * @since   1.0.0
 */
class Asset extends Entity
{
	/**
	 * Cached root instance.
	 *
	 * @var    array
	 *
	 * @since  __DEPLOY_VERSION__
	 */
	protected static $root;

	/**
	 * Clear all instances from cache
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function clearAll()
	{
		parent::clearAll();

		static::clearRoot();
	}

	/**
	 * Clear cached root category.
	 *
	 * @return  void
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function clearRoot()
	{
		static::$root = null;
	}

	/**
	 * Retrieve root folder.
	 *
	 * @return  static
	 */
	public static function root()
	{
		if (null !== static::$root)
		{
			return static::$root;
		}

		$root = self::loadFromData(['level' => 0]);

		static::$root = $root->isLoaded() ? $root : CreateRootAsset::instance()->execute();

		return static::$root;
	}

	/**
	 * Get a table.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  \JTable
	 *
	 * @codeCoverageIgnore
	 */
	public function table($name = '', $prefix = null, $options = array())
	{
		$name = $name ?: 'Asset';
		$prefix = $prefix ?: 'JTable';

		return parent::table($name, $prefix, $options);
	}
}
