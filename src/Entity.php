<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity;

defined('_JEXEC') || die;

use Joomla\CMS\Table\Nested;
use Joomla\Registry\Registry;
use Joomla\Utilities\ArrayHelper;
use Phproberto\Joomla\Entity\Core\Column;
use Phproberto\Joomla\Entity\Exception\SaveException;
use Phproberto\Joomla\Entity\Contracts\EntityInterface;
use Phproberto\Joomla\Entity\Core\Traits as CoreTraits;
use Phproberto\Joomla\Entity\Exception\DeleteException;
use Phproberto\Joomla\Entity\Exception\InvalidEntityData;
use Phproberto\Joomla\Entity\Exception\LoadEntityDataError;
use Phproberto\Joomla\Entity\Validation\Contracts\Validable;
use Phproberto\Joomla\Entity\Validation\Exception\ValidationException;

/**
 * Entity class.
 *
 * @since   1.0.0
 */
abstract class Entity implements EntityInterface
{
	use CoreTraits\HasEvents, CoreTraits\HasInstances;

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
	 * Proxy entity properties
	 *
	 * @param   string  $property  Property tried to access
	 *
	 * @return  mixed   Property if it exists
	 *
	 * @throws  \InvalidArgumentException  Column does not exist
	 */
	public function __get($property)
	{
		return $this->get($property);
	}

	/**
	 * Get all the entity properties.
	 *
	 * @return  array
	 */
	public function all()
	{
		if ($this->hasId() && empty($this->row[$this->primaryKey()]))
		{
			$this->fetch();
		}

		return (array) $this->row;
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
	 * Get the alias for a specific DB column.
	 *
	 * @param   string  $column  Name of the DB column. Example: created_by
	 *
	 * @return  string
	 */
	public function columnAlias($column)
	{
		$entityAliases = $this->columnAliases();

		if (array_key_exists($column, $entityAliases))
		{
			return $entityAliases[$column];
		}

		return $this->table()->getColumnAlias($column);
	}

	/**
	 * Get the list of column aliases.
	 *
	 * @return  array
	 */
	public function columnAliases()
	{
		return array();
	}

	/**
	 * Fast creation method.
	 *
	 * @param   array|\stdClass  $data  Data to store
	 *
	 * @return  static
	 */
	public static function create($data)
	{
		$entity = new static;

		// Remove primary key if present to force a new row
		$data = (array) $data;

		unset($data[$entity->primaryKey()]);

		return static::fromData($data)->save();
	}

	/**
	 * Get an \JDate object from an entity date property.
	 *
	 * @param   string   $property   Name of the property to use as source date
	 * @param   mixed    $tz         Time zone to be used for the date. Special cases:
	 *                               	* boolean true for user setting
	 *                               	* boolean false for server setting.
	 *
	 * @return  \JDate
	 *
	 * @throws  \RuntimeException  If date property is empty
	 */
	public function date($property, $tz = true)
	{
		$dateString = $this->get($property);

		if (empty($dateString))
		{
			$msg = sprintf('Date property `%s` is empty', $property);

			throw new \RuntimeException($msg);
		}

		// UTC date converted to user time zone.
		if ($tz === true)
		{
			$date = \JFactory::getDate($dateString, 'GMT');
			$date->setTimezone($this->juser()->getTimezone());

			return $date;
		}

		// UTC date converted to server time zone.
		if ($tz === false)
		{
			$config = \JFactory::getConfig();

			$date = \JFactory::getDate($dateString, 'UTC');
			$date->setTimezone(new \DateTimeZone($config->get('offset')));

			return $date;
		}

		// No date conversion.
		if ($tz === null)
		{
			return \JFactory::getDate($dateString);
		}

		// Get a date object based on UTC.
		$date = \JFactory::getDate($dateString, 'UTC');
		$date->setTimezone(new \DateTimeZone($tz));

		return $date;
	}

	/**
	 * Delete one or more entities by their primary key.
	 *
	 * @param   integer|array  $ids  And identifier or array of identifiers
	 *
	 * @return  boolean
	 *
	 * @throws  DeleteException
	 */
	public static function delete($ids)
	{
		$ids = array_filter(
			ArrayHelper::toInteger((array) $ids),
			function ($value)
			{
				return (int) $value > 0;
			}
		);

		if (empty($ids))
		{
			return true;
		}

		$entity = new static;

		$table = $entity->table();

		foreach ($ids as $id)
		{
			if (!$table->delete($id))
			{
				$entity->bind([$entity->primaryKey() => $id]);

				throw DeleteException::fromTable($entity, $table);
			}
		}

		return true;
	}

	/**
	 * Fetch DB data.
	 *
	 * @return  self
	 *
	 * @throws  LoadEntityDataError  Table error loading row
	 * @throws  InvalidEntityData    Incorrect data received
	 */
	public function fetch()
	{
		$this->row = array_merge($this->fetchRow(), (array) $this->row);

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
		if (!$this->hasId())
		{
			throw InvalidEntityData::missingPrimaryKey($this);
		}

		$table = $this->table();

		if (!$table->load($this->id()))
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
	 * Fast method to create an instance from an array|object of data.
	 *
	 * @param   array|\stdClass  $data  Data to bind to the instance
	 *
	 * @return  static
	 */
	public static function fromData($data)
	{
		$entity = new static;

		return $entity->bind($data);
	}

	/**
	 * Get a property of this entity.
	 *
	 * @param   string  $property  Name of the property to get
	 * @param   mixed   $default   Value to use as default if property is null
	 *
	 * @return  mixed
	 *
	 * @throws  \InvalidArgumentException  Column does not exist
	 */
	public function get($property, $default = null)
	{
		$data = $this->all();

		if (!array_key_exists($property, $data))
		{
			$msg = sprintf('Column `%s` does not exist in entity `%s` (id `%s`)', $property, get_class($this), $this->id());

			throw new \InvalidArgumentException($msg);
		}

		if (null === $data[$property])
		{
			return $default;
		}

		return $data[$property];
	}

	/**
	 * Get the \JDatabaseDriver object.
	 *
	 * @return  \JDatabaseDriver  Internal database driver object.
	 */
	public function getDbo()
	{
		return $this->table()->getDbo();
	}

	/**
	 * Check if entity has a property.
	 *
	 * @param   string   $property  Entity property name
	 * @param   mixed    $callback  Callable to execute for further verifications
	 *
	 * @return  boolean
	 */
	public function has($property, callable $callback = null)
	{
		$row = $this->all();

		if (!array_key_exists($property, $row))
		{
			return false;
		}

		return $callback ? call_user_func($callback, $row[$property]) : true;
	}

	/**
	 * Check if a property exists and is empty.
	 *
	 * @param   string   $property  Entity property name
	 *
	 * @return  boolean
	 */
	public function hasEmpty($property)
	{
		return $this->has(
			$property,
			function ($value)
			{
				return empty($value);
			}
		);
	}

	/**
	 * Check if a property exists and is empty.
	 *
	 * @param   string   $property  Entity property name
	 *
	 * @return  boolean
	 *
	 * @since   1.8
	 */
	public function hasEmptyDate($property)
	{
		return $this->has(
			$property,
			function ($value)
			{
				$emptyValues = [null, '', '0', '0000-00-00', '0000-00-00 00:00:00', '1971-01-01', '1971-01-01 00:00:00'];

				return in_array(trim($value), $emptyValues, true);
			}
		);
	}

	/**
	 * Check if a property exists and is not empty.
	 *
	 * @param   string   $property  Entity property name
	 *
	 * @return  boolean
	 */
	public function hasNotEmpty($property)
	{
		return $this->has(
			$property,
			function ($value)
			{
				return !empty($value);
			}
		);
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
		return static::find($id)->fetch();
	}

	/**
	 * Load an entity from columns data.
	 *
	 * @param   array   $data  Data to load the entity
	 *
	 * @return  false|static
	 *
	 * @since   1.2.0
	 */
	public static function loadFromData(array $data)
	{
		$entity = new static;

		$table = $entity->table();

		if (empty($data) || !$table->load($data))
		{
			return $entity;
		}

		$entity->bind($table->getProperties(true));

		return $entity;
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
	 * \JFactory::getUser() proxy for testing purposes
	 *
	 * @param   integer  $id  The user to load - Can be an integer or string - If string, it is converted to ID automatically.
	 *
	 * @return  \JUser object
	 *
	 * @see     \JUser
	 *
	 * @codeCoverageIgnore
	 */
	protected function juser($id = null)
	{
		return \JFactory::getUser($id);
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
		$storedData = (array) json_decode($this->get($property));

		if (empty($storedData))
		{
			return $data;
		}

		foreach ($storedData as $property => $value)
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
	 * Get this entity name.
	 *
	 * @return  string
	 */
	public function name()
	{
		$class = get_class($this);

		if (false !== strpos($class, '\\'))
		{
			$suffix = rtrim(strstr($class, 'Entity'), '\\');
			$parts = explode("\\", $suffix);

			return $parts ? strtolower(end($parts)) : null;
		}

		$parts = explode('Entity', $class, 2);

		return $parts ? strtolower(end($parts)) : null;
	}

	/**
	 * Get the empty date for the active DB driver.
	 *
	 * @return  string
	 *
	 * @codeCoverageIgnore
	 */
	protected function nullDate()
	{
		return \JFactory::getDbo()->getNullDate();
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
	 * Get a Registry object from a property of the entity.
	 *
	 * @param   string  $property  Property containing the data to import
	 *
	 * @return  Registry
	 *
	 * @throws  \InvalidArgumentException  Property does not exist
	 */
	public function registry($property)
	{
		return new Registry($this->get($property));
	}

	/**
	 * Save entity to the database.
	 *
	 * @return  self
	 *
	 * @throws  SaveException
	 */
	public function save()
	{
		if ($this instanceof Validable)
		{
			try
			{
				$this->validate();
			}
			catch (ValidationException $e)
			{
				throw SaveException::validation($this, $e);
			}
		}

		$table = $this->table();

		if ($this->hasId())
		{
			$table->load($this->id());
		}

		$parentColumn = $this->columnAlias(Column::PARENT);

		if (!$this->hasId() && $table instanceof Nested && $this->has($parentColumn))
		{
			$table->setLocation($this->get($parentColumn), 'last-child');
		}

		if (!$table->save($this->all()))
		{
			throw SaveException::table($this, $table);
		}

		if ($table instanceof Nested && !$table->rebuildPath($table->id))
		{
			throw SaveException::table($this, $table);
		}

		$path = property_exists($table, 'path') ? $table->path : null;

		if ($table instanceof Nested && !$table->rebuild($table->id, $table->lft, $table->level, $path))
		{
			throw SaveException::table($this, $table);
		}

		$this->bind($table->getProperties(true));

		return $this;
	}

	/**
	 * Get an entity date field formatted.
	 *
	 * @param   string   $property   Name of the property to use as source date
	 * @param   strig    $format     Format to output the date. PHP format | language string
	 * @param   array    $options    Optional settings:
	 *                               gregorian => True to use Gregorian calendar.
	 * 	                             tz => Time zone to be used for the date.  Special cases:
	 * 	                             	* boolean true for user setting
	 * 	                              	* boolean false for server setting.
	 *
	 * @return  string
	 */
	public function showDate($property, $format = 'DATE_FORMAT_LC1', array $options = array())
	{
		$tz        = isset($options['tz']) ? $options['tz'] : true;
		$gregorian = isset($options['gregorian']) ? $options['gregorian'] : false;

		$date = $this->date($property, $tz);

		if (\JFactory::getLanguage()->hasKey($format))
		{
			$format = \JText::_($format);
		}

		return $gregorian ? $date->format($format, true) : $date->calendar($format, true);
	}

	/**
	 * Get an entity date field formatted.
	 *
	 * @param   string   $property   Name of the property to use as source date
	 * @param   strig    $format     Format to output the date. PHP format | language string
	 * @param   array    $options    Optional settings:
	 *                               gregorian => True to use Gregorian calendar.
	 * 	                             tz => Time zone to be used for the date.  Special cases:
	 * 	                             	* boolean true for user setting
	 * 	                              	* boolean false for server setting.
	 *
	 * @return  string
	 *
	 * @since   1.8
	 */
	public function showNotEmptyDate($property, $format = 'DATE_FORMAT_LC1', array $options = array())
	{
		if ($this->hasEmptyDate($property))
		{
			return isset($options['default']) ? $options['default'] : '';
		}

		return $this->showDate($property, $format, $options);
	}

	/**
	 * Get a table.
	 *
	 * @param   string  $name     Table name. Optional.
	 * @param   string  $prefix   Class prefix. Optional.
	 * @param   array   $options  Configuration array for the table. Optional.
	 *
	 * @return  \JTable
	 *
	 * @throws  \InvalidArgumentException
	 *
	 * @codeCoverageIgnore
	 */
	public function table($name = '', $prefix = null, $options = array())
	{
		$table = \JTable::getInstance($name, $prefix, $options);

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
