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

use Phproberto\Joomla\Entity\Table\TableInterface;

/**
 * Tables with keys comparisions.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasKeysComparator
{
	/**
	 * Compare this table with another one to see if they contain the same entity.
	 *
	 * @param   TableInterface  $table  Table to compare
	 *
	 * @return  boolean
	 */
	public function hasSameKeys(TableInterface $table)
	{
		foreach ($this->{'_tbl_keys'} as $k)
		{
			if (!property_exists($this, $k) || $this->{$k} !== $table->{$k})
			{
				return false;
			}
		}

		return true;
	}
}
