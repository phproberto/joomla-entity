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
	 * Assign a value to entity property.
	 *
	 * @param   string  $property  Name of the property to set
	 * @param   mixed   $value     Value to assign
	 *
	 * @return  self
	 */
	abstract public function assign($property, $value);

	/**
	 * Get the entity identifier.
	 *
	 * @return  integer
	 */
	abstract public function id();

	/**
	 * Get entity primary key column.
	 *
	 * @return  string
	 */
	abstract public function primaryKey();

	/**
	 * Get the attached database row.
	 *
	 * @return  array
	 */
	abstract public function getAll();

	/**
	 * Get a table.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  \JTable
	 *
	 * @codeCoverageIgnore
	 */
	abstract public function table($name = '', $prefix = null, $options = array());

	/**
	 * Get the name of the column that stores params.
	 *
	 * @return  string
	 */
	protected function getColumnParams()
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
		$column = $this->getColumnParams();
		$data    = $this->getAll();

		if (array_key_exists($column, $data))
		{
			return new Registry($data[$column]);
		}

		return new Registry;
	}

	/**
	 * Save parameters to database.
	 *
	 * @return  boolean
	 *
	 * @throws  \RuntimeException
	 */
	public function saveParams()
	{
		$column = $this->getColumnParams();
		$data    = $this->getAll();

		if (!array_key_exists($column, $data))
		{
			throw new \RuntimeException("Error saving entity parameters: Cannot find entity parameters column", 500);
		}

		$table = $this->table();

		$saveData = array(
			$this->primaryKey() => $this->id(),
			$column                => $this->getParams()->toString()
		);

		if (!$table->save($saveData))
		{
			throw new \RuntimeException("Error saving entity parameters: " . $table->getError(), 500);
		}

		return true;
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

		$this->assign($this->getColumnParams(), $this->getParams()->toString());

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

		$this->assign($this->getColumnParams(), $this->getParams()->toString());

		return $this;
	}
}
