<?php
/**
 * @package     Joomla.Entity
 * @subpackage  Table
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */
namespace Phproberto\Joomla\Entity\Table\Traits;

defined('_JEXEC') || die;

/**
 * Tables with instance prefix.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasInstancePrefix
{
	/**
	 * Instance prefix used for autoloading + events.
	 *
	 * @var    string
	 */
	protected $instancePrefix;

	/**
	 * Get the class prefix
	 *
	 * @return  string
	 */
	public function getInstancePrefix()
	{
		if (null === $this->instancePrefix)
		{
			$this->instancePrefix = strtolower(strstr(get_class($this), 'Table', true));
		}

		return $this->instancePrefix;
	}
}
