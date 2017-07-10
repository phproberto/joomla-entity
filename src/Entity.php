<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity;

use Phproberto\Joomla\Traits\HasInstances;
use Phproberto\Joomla\Entity\Exception\InvalidEntityData;
use Phproberto\Joomla\Entity\Exception\LoadEntityDataError;

/**
 * Entity class.
 *
 * @since   __DEPLOY_VERSION__
 */
abstract class Entity implements EntityInterface
{
	use HasInstances;

	/**
	 * Identifier.
	 *
	 * @var  integer
	 */
	protected $id;

	/**
	 * Database row data.
	 *
	 * @var  array
	 */
	protected $row;

	/**
	 * Constructor.
	 *
	 * @param   integer  $id  Identifier
	 */
	public function __construct($id = null)
	{
		$this->id = (int) $id;
	}

	/**
	 * Assign a value to entity property.
	 *
	 * @param   string  $property  Name of the property to set
	 * @param   mixed   $value     Value to assign
	 *
	 * @return  self
	 */
	public function assign($property, $value)
	{
		if (null === $this->row)
		{
			$this->row = array();
		}

		$this->row[$property] = $value;

		if ($property === $this->getPrimaryKey())
		{
			$this->id = (int) $value;
		}

		return $this;
	}

	/**
	 * Bind data to the entity.
	 *
	 * @param   array  $data  Data to bind
	 *
	 * @return  self
	 */
	public function bind($data)
	{
		if (null === $this->row)
		{
			$this->row = array();
		}

		$primaryKey = $this->getPrimaryKey();

		foreach ((array) $data as $property => $value)
		{
			$this->row[$property] = $value;

			if ($property === $primaryKey)
			{
				$this->id = (int) $data[$primaryKey];
			}
		}

		return $this;
	}

	/**
	 * Get a row date field formatted.
	 *
	 * @param   string   $property   Name of the property to use as source date
	 * @param   array    $options    Optional settings:
	 *                               format =>The date format specification string (see {@link PHP_MANUAL#date}).
	 * 	                             tz => Time zone to be used for the date.  Special cases: boolean true for user
	 *                               	setting, boolean false for server setting.
	 * 	                             gregorian => True to use Gregorian calendar.
	 *
	 * @return  string
	 */
	public function date($property, array $options = array())
	{
		$format    = isset($options['format']) ? $options['format'] : 'DATE_FORMAT_LC1';
		$tz        = isset($options['tz']) ? $options['tz'] : true;
		$gregorian = isset($options['gregorian']) ? $options['gregorian'] : false;

		$data = $this->getAll();

		if (empty($data[$property]))
		{
			return null;
		}

		return \JHtml::_('date', $data[$property], $format, $tz, $gregorian);
	}

	/**
	 * Get a property of this entity.
	 *
	 * @param   string  $property  Name of the property to get
	 * @param   mixed   $default   Value to use as default if property is not set or is null
	 *
	 * @return  mixed
	 */
	public function get($property, $default = null)
	{
		$data = $this->getAll();

		if (!$data || !isset($data[$property]))
		{
			return $default;
		}

		return $data[$property];
	}

	/**
	 * Gets the Identifier.
	 *
	 * @return  integer
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * Get entity primary key column.
	 *
	 * @return  string
	 */
	public function getPrimaryKey()
	{
		return 'id';
	}

	/**
	 * Get the attached database row.
	 *
	 * @return  array
	 */
	public function getAll()
	{
		if (empty($this->row[$this->getPrimaryKey()]))
		{
			$this->load();
		}

		return $this->row;
	}

	/**
	 * Get a table.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for table. Optional.
	 *
	 * @return  \JTable
	 *
	 * @codeCoverageIgnore
	 */
	public function getTable($name = '', $prefix = null, $options = array())
	{
		$table = \JTable::getInstance($name, $prefix);

		if (!$table instanceof \JTable)
		{
			throw new \InvalidArgumentException(
				sprintf("Cannot find the table `%s`.", $prefix . $name)
			);
		}

		return $table;
	}

	/**
	 * Check if entity has a property.
	 *
	 * @param   string   $property  Entity property name
	 *
	 * @return  boolean
	 */
	public function has($property)
	{
		$row = $this->getAll();

		return $row && array_key_exists($property, $row);
	}

	/**
	 * Check if this entity has an id.
	 *
	 * @return  boolean
	 */
	public function hasId()
	{
		return !empty($this->id);
	}

	/**
	 * Fetch an instance.
	 *
	 * @param   integer  $id  Instance identifier
	 *
	 * @return  static
	 */
	public static function fetch($id)
	{
		return static::instance($id)->load();
	}

	/**
	 * Get the content of a column with data stored in JSON.
	 *
	 * @param   string  $property  Name of the column storing data
	 *
	 * @return  array
	 */
	public function json($property)
	{
		$data = array();
		$row  = $this->getAll();

		if (empty($row[$property]))
		{
			return $data;
		}

		foreach ((array) json_decode($row[$property]) as $property => $value)
		{
			if ($value === '')
			{
				continue;
			}

			$data[$property] = $value;
		}

		return $data;
	}

	/**
	 * Load row data.
	 *
	 * @return  self
	 */
	public function load()
	{
		$this->row = $this->fetchRow();

		return $this;
	}

	/**
	 * Check if entity has been loaded.
	 *
	 * @return  boolean
	 */
	public function isLoaded()
	{
		return $this->hasId() && !empty($this->row);
	}

	/**
	 * Load the entity from the database.
	 *
	 * @return  array
	 *
	 * @throws  LoadEntityDataError  Table error loading row
	 * @throws  InvalidEntityData    Incorrect data received
	 */
	protected function fetchRow()
	{
		$table = $this->getTable();

		if (!$table->load($this->id))
		{
			throw LoadEntityDataError::tableError($this, $table->getError());
		}

		$data = $table->getProperties(true);

		if (empty($data))
		{
			throw InvalidEntityData::emptyData($this);
		}

		if (!array_key_exists($this->getPrimaryKey(), $data))
		{
			throw InvalidEntityData::missingPrimaryKey($this);
		}

		$this->id = (int) $data[$this->getPrimaryKey()];

		return $data;
	}

	/**
	 * Save entity to the database.
	 *
	 * @return  boolean
	 */
	public function save()
	{
		$table = $this->getTable();

		if (!$table->save($this->row))
		{
			throw new \RuntimeException($table->getError());
		}

		return true;
	}

	/**
	 * Unassigns a row property.
	 *
	 * @param   string  $property  Name of the property to set
	 *
	 * @return  self
	 */
	public function unassign($property)
	{
		unset($this->row[$property]);

		return $this;
	}
}
