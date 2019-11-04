<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Controller;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController as BaseFormController;
use Joomla\CMS\Uri\Uri;
use Phproberto\Joomla\Entity\MVC\Request;
use Phproberto\Joomla\Entity\Acl\Contracts\Aclable;
use Phproberto\Joomla\Entity\Contracts\EntityInterface;
use Phproberto\Joomla\Entity\Users\Contracts\Ownerable;
use Phproberto\Joomla\Entity\Users\Column as UsersColumn;
use Phproberto\Joomla\Entity\MVC\Controller\Traits\HasAssociatedEntity;

/**
 * Base Form Controller.
 *
 * @since  1.0.1
 */
abstract class FormController extends BaseFormController
{
	use HasAssociatedEntity;

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
