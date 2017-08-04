<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Core\Column;
use Phproberto\Joomla\Traits\HasParams as CommonHasParams;

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
	 * Get the alias for a specific DB column.
	 *
	 * @param   string  $column  Name of the DB column. Example: created_by
	 *
	 * @return  string
	 */
	abstract public function columnAlias($column);

	/**
	 * Get a property of this entity.
	 *
	 * @param   string  $property  Name of the property to get
	 * @param   mixed   $default   Value to use as default if property is not set or is null
	 *
	 * @return  mixed
	 */
	abstract public function get($property, $default = null);

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
	 * Load parameters from database.
	 *
	 * @return  Registry
	 */
	protected function loadParams()
	{
		$params = trim($this->get($this->columnAlias(Column::PARAMS)));

		return empty($params) ? new Registry : new Registry($params);
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
		$table = $this->table();

		if ($this->id())
		{
			$table->load($this->id());
		}

		$saveData = array(
			$this->primaryKey() => $this->id(),
			$this->columnAlias(Column::PARAMS) => $this->params()->toString()
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

		$this->assign($this->columnAlias(Column::PARAMS), $this->params()->toString());

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

		$this->assign($this->columnAlias(Column::PARAMS), $this->params()->toString());

		return $this;
	}
}
