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
use Joomla\CMS\Application\CMSApplication;
use Phproberto\Joomla\Entity\MVC\ResponseStatus;

/**
 * Represents a JSON response.
 *
 * @since   __DEPLOY_VERSION__
 */
final class JSONResponse
{
	/**
	 * Array containing response data.
	 *
	 * @var  array
	 */
	protected $data;

	/**
	 * Status code.
	 *
	 * @var  integer
	 */
	protected $statusCode;

	/**
	 * Status text.
	 *
	 * @var  string
	 */
	protected $statusText;

	/**
	 * Constructor.
	 *
	 * @param   array           $data        Data array
	 * @param   integer         $statusCode  HTTP status code
	 * @param   string          $statusText  Response text
	 * @param   CMSApplication  $app         Active Joomla application
	 */
	public function __construct(array $data = [], int $statusCode = null, string $statusText = '', CMSApplication $app = null)
	{
		$this->app = $app ?: $this->activeApplication();
		$this->statusCode = $statusCode ?: ResponseStatus::OK;
		$this->statusText = $statusText;
	}

	/**
	 * Get the active application.
	 *
	 * @return  CMSApplication
	 */
	protected function activeApplication(): CMSApplication
	{
		return Factory::getApplication();
	}

	/**
	 * Get the response data.
	 *
	 * @return  array
	 */
	public function getData(): array
	{
		return $this->data;
	}

	/**
	 * Get the status code.
	 *
	 * @return  integer
	 */
	public function getStatusCode(): int
	{
		return $this->statusCode;
	}

	/**
	 * Get the status text.
	 *
	 * @return  integer
	 */
	public function getStatusText(): string
	{
		return $this->statusText;
	}

	/**
	 * Send the response.
	 *
	 * @return  void
	 */
	public function send(): void
	{
		$this->app->mimeType = 'application/json';
		$this->app->setHeader('Content-Type', $this->app->mimeType . '; charset=' . $this->app->charSet);
		$this->app->setHeader('status', $this->statusCode);
		$this->app->sendHeaders();

		echo ($this->data ? json_encode($this->data) : $this->statusText);

		$this->app->close();
	}

	/**
	 * Set response data.
	 *
	 * @param   array  $data  Array containing data
	 *
	 * @return  self
	 */
	public function setData(array $data)
	{
		$this->data = $data;

		return $this;
	}

	/**
	 * Set the status code.
	 *
	 * @param   int  $code  HTTP status code
	 *
	 * @return  self
	 */
	public function setStatusCode(int $code)
	{
		$this->statusCode = $code;

		return $this;
	}

	/**
	 * Set the status text.
	 *
	 * @param   string  $text  Status text
	 *
	 * @return  self
	 */
	public function setStatusText(string $text)
	{
		$this->statusText = $text;

		return $this;
	}
}
