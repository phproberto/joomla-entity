<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Controller;

defined('_JEXEC') || die;

use Joomla\CMS\MVC\Controller\AdminController as BaseAdminController;

/**
 * Base Admin Controller.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class AdminController extends BaseAdminController
{
	/**
	 * The prefix of the models
	 *
	 * @var    string
	 */
	protected $modelPrefix = null;

	/**
	 * Constructor.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     \JControllerLegacy
	 *
	 * @throws  \Exception
	 */
	public function __construct($config = array())
	{
		if (null !== $this->modelPrefix)
		{
			$this->{'model_prefix'} = $this->modelPrefix;
		}

		parent::__construct($config);
	}

	/**
	 * Gets the name of the latest extending class.
	 * For a class named ContentControllerArticles will return Articles
	 *
	 * @return  string
	 */
	public function instanceName()
	{
		$class = get_class($this);

		if (false !== strpos($class, '\\'))
		{
			return (new \ReflectionClass($this))->getShortName();
		}

		$name = strstr($class, 'Controller');
		$name = str_replace('Controller', '', $name);

		return strtolower($name);
	}
}
