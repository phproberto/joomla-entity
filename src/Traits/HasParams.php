<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Traits;

use Phproberto\Joomla\Traits\HasParams as CommonHasParams;
use Joomla\Registry\Registry;

/**
 * Trait for entities with params. Based on params | attribs columns.
 *
 * @since   __DEPLOY_VERSION__
 */
trait HasParams
{
	use CommonHasParams {
		setParam as protected commonSetParam;
		setParams as protected commonSetParams;
	}

	/**
	 * Get the name of the column that stores params.
	 *
	 * @return  string
	 */
	protected function getParamsColumn()
	{
		return 'params';
	}

	/**
	 * Load parameters from database.
	 *
	 * @return  Registry
	 */
	protected function loadParams()
	{
		$column = $this->getParamsColumn();
		$row = $this->getRow();

		if (array_key_exists($column, $row))
		{
			return new Registry($row[$column]);
		}

		return new Registry;
	}

	/**
	 * Save parameters to database.
	 *
	 * @return  boolean
	 */
	public function saveParams()
	{
		$column = $this->getParamsColumn();
		$row = $this->getRow();

		if (!array_key_exists($column, $row))
		{
			throw new \RuntimeException("Cannot find entity parameters column", 500);
		}

		$table = $this->getTable();

		$data = [
			$this->primaryKey => $this->getId(),
			$column => $this->getParams()->toString()
		];

		return $table->save($data);
	}

	/**
	 * Set the value of a parameter.
	 *
	 * @param   string  $name   Parameter name
	 * @param   mixed   $value  Value to assign to selected parameter
	 *
	 * @return  self
	 */
	public function setParam($name, $value)
	{
		$this->commonSetParam($name, $value);

		$this->assign($this->getParamsColumn(), $this->getParams()->toString());

		return $this;
	}

	/**
	 * Set the module parameters.
	 *
	 * @param   Registry  $params  Parameters to apply
	 *
	 * @return  self
	 */
	public function setParams(Registry $params)
	{
		$this->commonSetParams($params);

		$this->assign($this->getParamsColumn(), $this->getParams()->toString());

		return $this;
	}
}
