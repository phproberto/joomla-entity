<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Users\ViewLevel;

/**
 * PredefinedViewLevel entity.
 *
 * @since   __DEPLOY_VERSION__
 */
abstract class PredefinedViewLevel extends ViewLevel
{
	/**
	 * Predefined data to load the group.
	 *
	 * @return  array
	 */
	abstract public static function predefinedData();

	/**
	 * Constructor
	 */
	public function __construct()
	{
		$data = static::predefinedData();

		$viewLevel = ViewLevel::loadFromData($data);

		if (!$viewLevel->isLoaded())
		{
			throw new \RuntimeException('Cannot find view level: ' . json_encode($data));
		}

		$this->id = $viewLevel->id();
		$this->bind($viewLevel->all());
	}

	/**
	 * Create the view level.
	 *
	 * @param   array|\stdClass  $data  Data to store
	 *
	 * @return  static
	 */
	public static function create($data = null)
	{
		ViewLevel::create(static::predefinedData());

		return static::instance();
	}

	/**
	 * Retrieve the cached instance.
	 *
	 * @return  static
	 */
	public static function instance()
	{
		$viewLevel = new static;

		return static::find($viewLevel->id());
	}

	/**
	 * Retrieve the view level if exists or create it on the fly.
	 *
	 * @return  static
	 *
	 * @throws  \RuntimeException
	 */
	public static function instanceOrCreate()
	{
		try
		{
			return static::instance();
		}
		catch (\Exception $e)
		{
			return static::create();
		}
	}
}
