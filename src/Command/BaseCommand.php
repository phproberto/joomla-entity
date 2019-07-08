<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Command;

defined('_JEXEC') || die;

use Joomla\Registry\Registry;

/**
 * Base Command.
 *
 * @since  1.8
 */
abstract class BaseCommand
{
	/**
	 * Command extra configuration.
	 *
	 * @var  Registry
	 */
	protected $config;

	/**
	 * Constructor.
	 *
	 * @param   array  $options  Array with command options
	 */
	public function __construct(array $options = [])
	{
		$this->config = new Registry($options);
	}

	/**
	 * Factory method.
	 *
	 * @param   array   $arguments  Arguments for the instance.
	 *
	 * @return  static
	 */
	public static function instance(array $arguments = [])
	{
		return (new \ReflectionClass(get_called_class()))->newInstanceArgs($arguments);
	}
}
