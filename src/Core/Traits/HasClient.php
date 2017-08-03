<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

use Phproberto\Joomla\Client\Administrator;
use Phproberto\Joomla\Client\Client;
use Phproberto\Joomla\Client\ClientInterface;
use Phproberto\Joomla\Client\Site;
use Phproberto\Joomla\Entity\Core\Column;

defined('JPATH_PLATFORM') || die;

/**
 * Trait for entities that have an associated client.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasClient
{
	/**
	 * Associated client.
	 *
	 * @var  ClientInterface
	 */
	protected $client;

	/**
	 * Switch to admin client.
	 *
	 * @return  self
	 */
	public function admin()
	{
		$this->client = new Administrator;

		return $this;
	}

	/**
	 * Get the associated client.
	 *
	 * @param   boolean  $reload  Force reloading
	 *
	 * @return  ClientInterface
	 */
	public function client($reload = false)
	{
		if ($reload || null === $this->client)
		{
			$this->client = $this->loadClient();
		}

		return $this->client;
	}

	/**
	 * Load the client from the database.
	 *
	 * @return  Category
	 */
	protected function loadClient()
	{
		$clientId = (int) $this->get($this->columnAlias(Column::CLIENT));

		return $clientId ? Client::admin() : Client::site();
	}

	/**
	 * Switch to site client.
	 *
	 * @return  self
	 */
	public function site()
	{
		$this->client = new Site;

		return $this;
	}
}
