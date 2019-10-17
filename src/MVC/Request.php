<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Input\Input;
use Joomla\CMS\Session\Session;

/**
 * Represents a request.
 *
 * @since   __DEPLOY_VERSION__
 */
final class Request
{
	/**
	 * Retrieve the active request.
	 *
	 * @var  static
	 */
	protected static $active;

	/**
	 * Input data.
	 *
	 * @var  Input
	 */
	protected $input;

	/**
	 * Constructor.
	 *
	 * @param   Input|null  $input  Input object from joomla
	 */
	public function __construct(Input $input = null)
	{
		$this->input = $input ?: $this->activeInput();
	}

	/**
	 * Has this request a valid token?
	 *
	 * @param   string  $method  Request method where the token is expected
	 *
	 * @return  boolean
	 */
	public function hasToken(string $method = 'post')
	{
		try
		{
			$hasToken = $this->validateHasToken($method);
		}
		catch (\Exception $e)
		{
			$hasToken = false;
		}

		return $hasToken;
	}

	/**
	 * Retrieve the active instance.
	 *
	 * @return  static
	 */
	public static function active()
	{
		if (null === static::$active)
		{
			static::$active = new static;
		}

		return static::$active;
	}

	/**
	 * Retrieve the active application input.
	 *
	 * @return  Input
	 *
	 * @codeCoverageIgnore
	 */
	private function activeInput()
	{
		return Factory::getApplication()->input;
	}

	/**
	 * Clear cached active request.
	 *
	 * @return  void
	 */
	public static function clearActive()
	{
		static::$active = null;
	}

	/**
	 * Retrieve the input.
	 *
	 * @return  Input
	 */
	public function input()
	{
		return $this->input;
	}

	/**
	 * Check if this is an AJAX request.
	 *
	 * @return  boolean
	 */
	public function isAjax()
	{
		try
		{
			$isAjax = $this->validateIsAjax();
		}
		catch (\Exception $e)
		{
			$isAjax = false;
		}

		return $isAjax;
	}

	/**
	 * Is this an AJAX request that contains a token?
	 *
	 * @param   string  $method  Request method where the token is expected
	 *
	 * @return  boolean
	 */
	public function isAjaxWithToken(string $method = 'post')
	{
		return $this->isAjax() && $this->hasToken($method);
	}

	/**
	 * Validate that this request contains a valid token.
	 *
	 * @param   string  $method  Request method where the token is expected
	 *
	 * @return  boolean
	 *
	 * @throws  \RuntimeException
	 */
	public function validateHasToken(string $method = 'post')
	{
		if (!Session::checkToken($method))
		{
			throw new \RuntimeException("None or invalid token received in method `" . $method . '`');
		}

		return true;
	}

	/**
	 * Validate that this is an AJAX request.
	 *
	 * @return  boolean
	 *
	 * @throws  \RuntimeException
	 */
	public function validateIsAjax()
	{
		if ('xmlhttprequest' !== strtolower($this->input->server->get('HTTP_X_REQUESTED_WITH', '')))
		{
			throw new \RuntimeException("Invalid AJAX request");
		}

		return true;
	}

	/**
	 * Validate that this is an AJAX request that contains a valid token.
	 *
	 * @param   string  $method  Request method where the token is expected
	 *
	 * @return  boolean
	 *
	 * @throws  \RuntimeException
	 */
	public function validateIsAjaxWithToken(string $method = 'post')
	{
		return $this->validateIsAjax() && $this->validateHasToken($method);
	}

	/**
	 * Close the active application with an error message if this request is not AJAX or does not include a valid token.
	 *
	 * @param   string  $method  Request method where the token is expected
	 *
	 * @return  boolean
	 */
	public function validateAjaxWithTokenOrCloseApp(string $method = 'post')
	{
		try
		{
			$this->validateIsAjaxWithToken($method);
		}
		catch (\Exception $e)
		{
			$app = Factory::getApplication();
			$app->setHeader('status', 403);
			$app->sendHeaders();
			echo $e->getMessage();
			$app->close();

			return false;
		}

		return true;
	}
}
