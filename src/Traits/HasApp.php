<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Traits;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Application\CMSApplication;

/**
 * For classes having a linked app.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasApp
{
	/**
	 * Active application.
	 *
	 * @var  CMSApplication
	 */
	protected $app;

	/**
	 * Retrieve the active application.
	 *
	 * @return  CMSApplication
	 */
	protected function activeApplication()
	{
		return Factory::getApplication();
	}

	/**
	 * Retrieve the active application.
	 *
	 * @return  CMSApplication
	 */
	protected function app()
	{
		if (null === $this->app)
		{
			$this->app = $this->activeApplication();
		}

		return $this->app;
	}
}
