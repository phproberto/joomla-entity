<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Model\State;

defined('_JEXEC') || die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Phproberto\Joomla\Entity\MVC\Model\State\StateInterface;
use Phproberto\Joomla\Entity\MVC\Model\State\PropertyInterface;

/**
 * Represents model state.
 *
 * @since  __DEPLOY_VERSION__
 */
class State implements StateInterface
{
	/**
	 * Model whose state we are working with.
	 *
	 * @var  BaseDatabaseModel
	 */
	protected $model;

	/**
	 * Properties supported by this state.
	 *
	 * @var  PropertyInterface
	 */
	protected $properties = [];

	/**
	 * Constructor.
	 *
	 * @param   BaseDatabaseModel    $model       Model whose state we will deal with
	 * @param   PropertyInterface[]  $properties  State properties
	 */
	public function __construct(BaseDatabaseModel $model, array $properties = [])
	{
		$this->model = $model;
		$this->addProperties($properties);
	}

	/**
	 * Add a property to the model state.
	 *
	 * @param   PropertyInterface  $property  Property
	 *
	 * @return  self
	 */
	public function addProperty(PropertyInterface $property)
	{
		$this->properties[$property->key()] = $property;

		return $this;
	}

	/**
	 * Add an array of properties to the model state.
	 *
	 * @param   PropertyInterface[]  $properties  Properties to add
	 *
	 * @return  self
	 */
	public function addProperties(array $properties)
	{
		foreach ($properties as $property)
		{
			$this->addProperty($property);
		}

		return $this;
	}

	/**
	 * Get a value from the state.
	 *
	 * @param   string  $key      State property key
	 * @param   mixed   $default  Default value
	 *
	 * @return  mixed
	 */
	public function get($key, $default = null)
	{
		return $this->model->getState($key, $default);
	}

	/**
	 * Check if this state has a property.
	 *
	 * @param   string  $key  Property key
	 *
	 * @return  boolean
	 */
	public function hasProperty($key)
	{
		return array_key_exists($key, $this->properties);
	}

	/**
	 * Retrieve a property.
	 *
	 * @param   string  $key  Property key
	 *
	 * @return  PropertyInterface
	 */
	public function property($key)
	{
		if (!$this->hasProperty($key))
		{
			throw new \RuntimeException("The property $key does not exist");
		}

		return $this->properties[$key];
	}

	/**
	 * Properties supported by this state.
	 *
	 * @return  PropertyInterface[]
	 */
	public function properties()
	{
		return $this->properties;
	}

	/**
	 * Return the properties that can be automatically populated buy the model.
	 *
	 * @return  array
	 */
	public function populableProperties()
	{
		$properties = [];

		foreach ($this->properties() as $key => $property)
		{
			if ($property->isPopulable())
			{
				$properties[$key] = $property;
			}
		}

		return $properties;
	}

	/**
	 * Set a value of a state property.
	 *
	 * @param   string  $key    Key of the property whose value we want to set
	 * @param   mixed   $value  Value to assign
	 *
	 * @return  mixed  The previous value of the property or null if not set.
	 */
	public function set($key, $value)
	{
		return $this->model->setState($key, $value);
	}
}
