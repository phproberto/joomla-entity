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
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Router\Route;

/**
 * Trait for classes with redirections.
 *
 * @since  1.0.1
 */
trait HasRedirect
{
	/**
	 * URL to redirect.
	 *
	 * @var   string
	 */
	protected $redirectUrl;

	/**
	 * Get the default redirect url.
	 *
	 * @param   boolean  $route  Process URL with Route?
	 *
	 * @return  string
	 */
	protected function defaultRedirectUrl($route = true)
	{
		return Uri::root();
	}

	/**
	 * Check if there is a redirection set.
	 *
	 * @return  boolean
	 *
	 * @since   1.2.0
	 */
	public function hasRedirect()
	{
		return !empty($this->redirect);
	}

	/**
	 * Redirect to the URL.
	 *
	 * @return  void
	 *
	 * @since   1.2.0
	 */
	public function redirect()
	{
		Factory::getApplication()->redirect($this->redirectUrl);
	}

	/**
	 * Retrieve the active redirect url.
	 *
	 * @param   boolean  $route  Process URL with Route?
	 *
	 * @return  string
	 */
	protected function redirectUrl($route = true)
	{
		if (null === $this->redirectUrl)
		{
			$this->redirectUrl = $this->defaultRedirectUrl();
		}

		return $route ? Route::_($this->redirectUrl, false) : $this->redirectUrl;
	}

	/**
	 * Set the active redirect URL.
	 *
	 * @param   string  $url  URL to redirect the user
	 *
	 * @return  self
	 */
	public function setRedirectUrl($url)
	{
		$this->redirectUrl = $url;

		return $this;
	}
}
