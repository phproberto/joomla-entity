<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Core\Client\Administrator;
use Phproberto\Joomla\Entity\Core\Client\Client;
use Phproberto\Joomla\Entity\Core\Client\ClientInterface;
use Phproberto\Joomla\Entity\Core\Client\Site;
use Phproberto\Joomla\Entity\Core\CoreColumn;

/**
 * Trait for entities that have an associated client.
 *
 * @since  1.0.0
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
		$clientId = (int) $this->get($this->columnAlias(CoreColumn::CLIENT));

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
