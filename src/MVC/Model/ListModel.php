<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Model;

defined('_JEXEC') || die;

use Joomla\CMS\MVC\Model\ListModel as BaseListModel;
use Phproberto\Joomla\Entity\MVC\Model\Traits\HasSearch;
use Phproberto\Joomla\Entity\MVC\Model\Traits\HasContext;
use Phproberto\Joomla\Entity\MVC\Model\ModelWithStateInterface;
use Phproberto\Joomla\Entity\MVC\Model\Traits\HasFilteredState;
use Phproberto\Joomla\Entity\MVC\Model\Traits\HasQueryModifiers;
use Phproberto\Joomla\Entity\Traits\HasStaticCache;

/**
 * Base list model.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class ListModel extends BaseListModel implements ModelWithStateInterface
{
	use HasContext, HasFilteredState, HasQueryModifiers, HasSearch, HasStaticCache;

	/**
	 * Function to get the active filters
	 *
	 * @return  array  Associative array in the format: array('filter_published' => 0)
	 */
	public function getActiveFilters()
	{
		$activeFilters = [];

		foreach ($this->state()->populableProperties() as $property)
		{
			if ('filter.' !== substr($property->key(), 0, 7))
			{
				continue;
			}

			$value = $this->state()->get($property->key());

			if (!$value)
			{
				continue;
			}

			$activeFilters[$property->key()] = $value;
		}

		return $activeFilters;
	}

	/**
	 * Returns a record count for the query.
	 *
	 * @param   \JDatabaseQuery|string  $query  The query.
	 *
	 * @return  integer  Number of rows for query.
	 */
	protected function _getListCount($query)
	{
		return $this->getListCount($query);
	}

	/**
	 * Method to get an array of data items. Overriden to add static cache support.
	 *
	 * @return  array
	 */
	public function getItems(): array
	{
		$key = $this->getStateHash('getItems');

		if (!$this->hasInStaticCache($key))
		{
			$this->storeInStaticCache($key, parent::getItems());
		}

		return $this->getFromStaticCache($key);
	}

	/**
	 * Returns a record count for the query.
	 *
	 * @param   \JDatabaseQuery|string  $query  The query.
	 *
	 * @return  integer  Number of rows for query.
	 */
	protected function getListCount($query)
	{
		return parent::_getListCount($query);
	}

	/**
	 * Gets a unique hash based on a prefix + model state
	 *
	 * @param   string  $prefix  Prefix for the cache
	 *
	 * @return  string
	 */
	protected function getStateHash($prefix = null)
	{
		$prefix = $prefix ? $prefix : get_class($this);

		$state = $this->getState()->getProperties();

		ksort($state);

		return md5($this->context . ':' . $prefix . ':' . json_encode($state));
	}

	/**
	 * Override because core method doesn't use filters to generate the id
	 *
	 * @param   string  $id  An identifier string to generate the store id.
	 *
	 * @return  string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		return $this->getStateHash($id);
	}
}
