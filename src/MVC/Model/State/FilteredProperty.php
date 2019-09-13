<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Model\State;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\MVC\Model\State\PropertyInterface;
use Phproberto\Joomla\Entity\MVC\Model\State\Filter\StringQuoted;
use Phproberto\Joomla\Entity\MVC\Model\State\Filter\FilterInterface;

/**
 * Represents a state property.
 *
 * @since  __DEPLOY_VERSION__
 */
class FilteredProperty implements PropertyInterface
{
	/**
	 * Property value filter.
	 *
	 * @var  FilterInterface
	 */
	protected $filter;

	/**
	 * Property being filtered.
	 *
	 * @var  PropertyInterface
	 */
	protected $property;

	/**
	 * Constructor
	 *
	 * @param   PropertyInterface  $property  Property
	 * @param   FilterInterface    $filter    Filter used to retrieve values
	 */
	public function __construct(PropertyInterface $property, FilterInterface $filter = null)
	{
		$this->property = $property;
		$this->filter = $filter ?: new StringQuoted;
	}

	/**
	 * Redirect any missing method call to decorated property.
	 *
	 * @param   string  $method  Method name
	 * @param   array   $params  Method parameters
	 *
	 * @return  mixed
	 */
	public function __call($method, $params)
	{
		return call_user_func_array([$this->property, $method], $params);
	}

	/**
	 * Filter a value for this property.
	 *
	 * @param   mixed  $value  Value to filter
	 *
	 * @return  mixed
	 */
	public function filter($value)
	{
		return $this->filter->filter($value);
	}

	/**
	 * Can this property be populated from request?
	 *
	 * @return  boolean
	 */
	public function isPopulable()
	{
		return $this->property->isPopulable();
	}

	/**
	 * Retrieve the property identifier.
	 *
	 * @return  string
	 */
	public function key()
	{
		return $this->property->key();
	}
}
