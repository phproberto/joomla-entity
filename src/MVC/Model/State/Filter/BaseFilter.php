<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Model\State\Filter;

defined('_JEXEC') || die;

use Joomla\Registry\Registry;

/**
 * Base state filter.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class BaseFilter implements FilterInterface
{
	/**
	 * Filter params.
	 *
	 * @var  Registry
	 */
	protected $params;

	/**
	 * Constructor
	 *
	 * @param   array  $options  Additional options
	 */
	public function __construct(array $options = [])
	{
		$this->params = new Registry($options);
	}

	/**
	 * Filter one or more values received from the state.
	 *
	 * @param   mixed  $values  Values to filter
	 *
	 * @return  array
	 */
	public function filter($values)
	{
		return $this->returnValues($this->filterValues($this->prepareValues($values)));
	}

	/**
	 * Determine if a value will be used or not.
	 *
	 * @param   mixed  $value  Value to filter
	 *
	 * @return  boolean
	 */
	protected function filterValue($value)
	{
		return !in_array($value, $this->forbiddenValues(), true);
	}

	/**
	 * Filter values.
	 *
	 * @param   array   $values  Values to filter
	 *
	 * @return  array
	 */
	protected function filterValues(array $values)
	{
		return array_filter($values, [$this, 'filterValue']);
	}

	/**
	 * Values that are not recognised as valid.
	 *
	 * @return  array
	 */
	protected function forbiddenValues()
	{
		return (array) $this->params->get('forbiddenValues', [null, '']);
	}

	/**
	 * Prepare a value before applying filter.
	 *
	 * @param   mixed  $value  Value to prepare
	 *
	 * @return  mixed
	 */
	protected function prefilterValue($value)
	{
		return !in_array($value, ['', null], true);
	}

	/**
	 * Prefilter values after they are prepared.
	 *
	 * @param   array   $values  Values to prefilter
	 *
	 * @return  array
	 */
	protected function prefilterValues(array $values)
	{
		return array_filter($values, [$this, 'prefilterValue']);
	}

	/**
	 * Prepare a value before applying filter.
	 *
	 * @param   mixed  $value  Value to prepare
	 *
	 * @return  mixed
	 */
	protected function prepareValue($value)
	{
		return $value;
	}

	/**
	 * Prepare values before returining them.
	 *
	 * @param   mixed  $values  Values to prepare
	 *
	 * @return  array
	 */
	protected function prepareValues($values)
	{
		if (null === $values)
		{
			return [];
		}

		// Cast to array + convert comma separated values to arrays
		if (!is_array($values))
		{
			$values = is_string($values) ? explode(',', $values) : (array) $values;
		}

		$values = array_map(
			function ($value)
			{
				return is_string($value) ? trim($value) : $value;
			},
			$values
		);

		if (in_array('*', $values, true))
		{
			return [];
		}

		$values = $this->prefilterValues($values);

		$values = array_map([$this, 'prepareValue'], $values);

		return $this->prefilterValues($values);
	}

	/**
	 * Return filtered values.
	 *
	 * @param   array  $values  Values to return
	 *
	 * @return  array
	 */
	protected function returnValues(array $values)
	{
		return array_values($values);
	}
}
