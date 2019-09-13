<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Model\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Collection;

/**
 * For list models with search functions.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasSearch
{
	/**
	 * Method to get an array of data items. Overriden to add static cache support.
	 *
	 * @return  array
	 */
	abstract public function getItems();

	/**
	 * Method to set model state variables
	 *
	 * @param   string  $property  The name of the property.
	 * @param   mixed   $value     The value of the property to set or null.
	 *
	 * @return  mixed  The previous value of the property or null if not set.
	 */
	abstract public function setState($property, $value = null);

	/**
	 * Method to search items based on a state.
	 *
	 * Note: This method clears the model state.
	 *
	 * @param   array  $state  Array with filters + list options
	 *
	 * @return  array
	 */
	public function search($state = array())
	{
		// Clear current state and avoid populateState
		$this->state = new \JObject;
		$this->{'__state_set'} = true;

		foreach ($state as $key => $value)
		{
			$this->setState($key, $value);
		}

		return $this->getItems();
	}

	/**
	 * Search items and return a collection of entities with the results of ->search()
	 *
	 * @param   string  $entityClass  Entity to bind item data
	 * @param   array   $state        Model state
	 *
	 * @return  Collection
	 */
	public function searchCollection(string $entityClass, array $state = [])
	{
		return new Collection(
			array_map(
				function ($entityData) use ($entityClass)
				{
					$entity = new $entityClass;
					$id = $entityData->{$entity->primaryKey()};

					return $entityClass::find($id)->bind($entityData);
				},
				$this->search($state)
			)
		);
	}
}
