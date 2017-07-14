<?php
/**
 * Joomla! common library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    GNU/GPL 2, http://www.gnu.org/licenses/gpl-2.0.htm
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

use Phproberto\Joomla\Entity\Core\Asset;
use Phproberto\Joomla\Client\Client;
use Phproberto\Joomla\Client\ClientInterface;

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
	 * Get the name of the column that stores category.
	 *
	 * @return  string
	 */
	protected function columnClient()
	{
		return 'client_id';
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
		$column = $this->columnClient();
		$data = $this->all();

		if (!array_key_exists($column, $data))
		{
			throw new \RuntimeException(__CLASS__ . ": Cannot load entity client");
		}

		return (int) $data[$column] ? Client::admin() : Client::site();
	}
}
