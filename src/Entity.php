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
	 * Get the attached database row.
	 *
	 * @return  array
	 */
	public function all()
	{
		if (empty($this->row[$this->primaryKey()]))
		{
			$this->fetch();
		}

		return $this->row;
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

		if ($property === $this->primaryKey())
		{
			$this->id = (int) $value;
		}

		return $this;
	}

	/**
	 * Bind data to the entity.
	 *
	 * @param   mixed  $data  array | \stdClass Data to bind
	 *
	 * @return  self
	 */
	public function bind($data)
	{
		if (!is_array($data) && !$data instanceof \stdClass)
		{
			throw new \InvalidArgumentException(sprintf("Invalid data sent for %s::%s()", __CLASS__, __FUNCTION__));
		}

		$data = (array) $data;

		if (null === $this->row)
		{
			$this->row = array();
		}

		$primaryKey = $this->primaryKey();

		foreach ($data as $property => $value)
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

		$data = $this->all();

		if (empty($data[$property]))
		{
			return null;
		}

		return \JHtml::_('date', $data[$property], $format, $tz, $gregorian);
	}

	/**
	 * Fetch DB data.
	 *
	 * @return  self
	 */
	public function fetch()
	{
		$this->row = array_merge((array) $this->row, $this->fetchRow());

		return $this;
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
		$table = $this->table();

		if (!$table->load($this->id))
		{
			throw LoadEntityDataError::tableError($this, $table->getError());
		}

		$data = $table->getProperties(true);

		if (empty($data))
		{
			throw InvalidEntityData::emptyData($this);
		}

		if (!array_key_exists($this->primaryKey(), $data))
		{
			throw InvalidEntityData::missingPrimaryKey($this);
		}

		$this->id = (int) $data[$this->primaryKey()];

		return $data;
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
		$data = $this->all();

		if (!$data || !isset($data[$property]))
		{
			return $default;
		}

		return $data[$property];
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
		$row = $this->all();

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
	 * Gets the Identifier.
	 *
	 * @return  integer
	 */
	public function id()
	{
		return $this->id;
	}

	/**
	 * Load an instance.
	 *
	 * @param   integer  $id  Instance identifier
	 *
	 * @return  static
	 */
	public static function load($id)
	{
		return static::instance($id)->fetch();
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
	 * Get the content of a column with data stored in JSON.
	 *
	 * @param   string  $property  Name of the column storing data
	 *
	 * @return  array
	 */
	public function json($property)
	{
		$data = array();
		$row  = $this->all();

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
	 * Get entity primary key column.
	 *
	 * @return  string
	 */
	public function primaryKey()
	{
		return 'id';
	}

	/**
	 * Save entity to the database.
	 *
	 * @return  boolean
	 */
	public function save()
	{
		$table = $this->table();

		if (!$table->save($this->row))
		{
			throw new \RuntimeException($table->getError());
		}

		return true;
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
	public function table($name = '', $prefix = null, $options = array())
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
