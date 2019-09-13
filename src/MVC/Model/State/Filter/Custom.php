<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Model\State\Filter;

defined('_JEXEC') || die;

/**
 * Integer values filterer.
 *
 * @since  __DEPLOY_VERSION__
 */
class Custom extends BaseFilter
{
	/**
	 * Filter value function.
	 *
	 * @var  callable
	 */
	private $filterValueFunction;

	/**
	 * Prepare value function.
	 *
	 * @var  callable
	 */
	private $prepareValueFunction;

	/**
	 * Constructorl
	 *
	 * @param   callable       $filterValueFunction   Filtering function
	 * @param   callable|null  $prepareValueFunction  Prepare function
	 * @param   array          $options               Additional options
	 */
	public function __construct(callable $filterValueFunction, callable $prepareValueFunction = null, array $options = [])
	{
		parent::__construct($options);

		$this->filterValueFunction = $filterValueFunction;
		$this->prepareValueFunction = $prepareValueFunction;
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
		return call_user_func($this->filterValueFunction, $value);
	}

	/**
	 * Prepare value.
	 *
	 * @param   mixed  $value  Value to prepare
	 *
	 * @return  mixed
	 */
	public function prepareValue($value)
	{
		$value = parent::prepareValue($value);

		if (!$this->prepareValueFunction)
		{
			return $value;
		}

		return call_user_func($this->prepareValueFunction, $value);
	}
}
