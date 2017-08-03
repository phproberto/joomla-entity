<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

use Phproberto\Joomla\Entity\Core\Asset;
use Phproberto\Joomla\Entity\Core\Column;

defined('JPATH_PLATFORM') || die;

/**
 * Trait for entities that have an asset. Based on asset_id column.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasAsset
{
	/**
	 * Associated asset.
	 *
	 * @var  Asset
	 */
	protected $asset;

	/**
	 * Get the alias for a specific DB column.
	 *
	 * @param   string  $column  Name of the DB column. Example: created_by
	 *
	 * @return  string
	 */
	abstract public function columnAlias($column);

	/**
	 * Get a property of this entity.
	 *
	 * @param   string  $property  Name of the property to get
	 * @param   mixed   $default   Value to use as default if property is not set or is null
	 *
	 * @return  mixed
	 */
	abstract public function get($property, $default = null);

	/**
	 * Get the associated asset.
	 *
	 * @param   boolean  $reload  Force asset reloading
	 *
	 * @return  Asset
	 */
	public function getAsset($reload = false)
	{
		if ($reload || null === $this->asset)
		{
			$this->asset = $this->loadAsset();
		}

		return $this->asset;
	}

	/**
	 * Load the asset from the database.
	 *
	 * @return  Asset
	 */
	protected function loadAsset()
	{
		try
		{
			$assetId = (int) $this->get($this->columnAlias(Column::ASSET));
		}
		catch (\Exception $e)
		{
			$assetId = 0;
		}

		return $assetId ? Asset::instance($assetId) : new Asset;
	}
}
