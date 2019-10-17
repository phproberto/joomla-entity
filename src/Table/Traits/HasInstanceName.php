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
 * Tables with instance name.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasInstanceName
{
	/**
	 * Name of the instance.
	 *
	 * @var    string
	 */
	protected $instanceName;

	/**
	 * Gets the name of the latest extending class.
	 * For a class named ContentTableArticles will return Articles
	 *
	 * @return  string
	 */
	public function getInstanceName()
	{
		if (null === $this->instanceName)
		{
			$this->instanceName = strtolower(str_replace('Table', '', strstr(get_class($this), 'Table')));
		}

		return $this->instanceName;
	}
}
