<?php
/**
 * Joomla! common library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    GNU/GPL 2, http://www.gnu.org/licenses/gpl-2.0.htm
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

use Phproberto\Joomla\Entity\Core\Asset;

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
	 * Get the attached database row.
	 *
	 * @return  array
	 */
	abstract public function all();

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
		$data = $this->all();

		if (empty($data['asset_id']))
		{
			return new Asset;
		}

		return Asset::instance($data['asset_id']);
	}
}
