<?php
/**
 * @package     Joomla.Entity
 * @subpackage  Table
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */
namespace Phproberto\Joomla\Entity\Table\Traits;

defined('_JEXEC') || die;

use Joomla\CMS\Table\Table;

/**
 * Table with assets.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasAsset
{
	/**
	 * Get the asset prefix.
	 *
	 * @return  string
	 */
	protected function getAssetPrefix()
	{
		return '';
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 */
	protected function getAssetName()
	{
		$keys = array();

		foreach ($this->{'_tbl_keys'} as $k)
		{
			$keys[] = (int) $this->$k;
		}

		$prefix = $this->getAssetPrefix();

		return ($prefix ? $prefix . '.' : '') . strtolower($this->getInstanceName()) . '.' . implode('.', $keys);
	}
}
