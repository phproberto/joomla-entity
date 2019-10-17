<?php
/**
 * @package     Joomla.Entity
 * @subpackage  Table
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */
namespace Phproberto\Joomla\Entity\Table\Traits;

defined('_JEXEC') || die;

/**
 * Table with automatic events support.
 *
 * @since  __DEPLOY_VERSION__
 */
trait HasAutoEvents
{
	/**
	 * Events available in this class
	 *
	 * @var    array
	 */
	protected $availableEvents = array(
		'event_after_bind'     => 'onAfterBind',
		'event_after_check'    => 'onAfterCheck',
		'event_after_delete'   => 'onAfterDelete',
		'event_after_load'     => 'onAfterLoad',
		'event_after_publish'  => 'onAfterPublish',
		'event_after_store'    => 'onAfterStore',
		'event_before_bind'    => 'onBeforeBind',
		'event_before_check'   => 'onBeforeCheck',
		'event_before_delete'  => 'onBeforeDelete',
		'event_before_load'    => 'onBeforeLoad',
		'event_before_publish' => 'onBeforePublish',
		'event_before_store'   => 'onBeforeStore'
	);

	/**
	 * Use automatic events for this table.
	 *
	 * @var    boolean
	 */
	protected $autoEvents = true;

	/**
	 * An array of plugin types to import.
	 *
	 * @var  array
	 */
	protected $pluginTypesToImport = array('content');

	/**
	 * Columns that will be nulled if bind data is empty.
	 *
	 * @var  array
	 */
	protected $nullIfEmptyColumns = [];

	/**
	 * Called after bind().
	 *
	 * Method to bind an associative array or object to the JTable instance.This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 *
	 * @param   mixed  $src     An associative array or object to bind to the JTable instance.
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 */
	public function afterBind($src, $ignore = array())
	{
		return $this->triggerEvent('event_after_bind', array(&$src, $ignore));
	}

	/** Called after check().
	 *
	 * This method checks that the parent_id is non-zero and exists in the database.
	 * Note that the root node (parent_id = 0) cannot be manipulated with this class.
	 *
	 * @return  boolean  True if all checks pass.
	 */
	protected function afterCheck()
	{
		return $this->triggerEvent('event_after_check', func_get_args());
	}

	/**
	 * Called after delete().
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 */
	protected function afterDelete($pk = null)
	{
		return $this->triggerEvent('event_after_delete', func_get_args());
	}

	/**
	 * Called after load().
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If not
	 *                           set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found.
	 */
	protected function afterLoad($keys = null, $reset = true)
	{
		return $this->triggerEvent('event_after_load', func_get_args());
	}

	/**
	 * Called after publish().
	 *
	 * @param   mixed    $pks     An optional array of primary key values to update.
	 *                            If not set the instance property value is used.
	 * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param   integer  $userId  The user id of the user performing the operation.
	 *
	 * @return  boolean  True on success; false if $pks is empty.
	 */
	public function afterPublish($pks = null, $state = 1, $userId = 0)
	{
		return $this->triggerEvent('event_after_publish', func_get_args());
	}

	/**
	 * Called after store(). Overriden to send isNew to plugins.
	 *
	 * @param   boolean  $updateNulls  True to update null values as well.
	 * @param   boolean  $isNew        True if we are adding a new item.
	 * @param   mixed    $oldItem      null for new items | JTable otherwise
	 *
	 * @return  boolean  True on success.
	 */
	protected function afterStore($updateNulls = false, $isNew = false, $oldItem = null)
	{
		return $this->triggerEvent('event_after_store', func_get_args());
	}

	/**
	 * Called before bind().
	 *
	 * Method to bind an associative array or object to the JTable instance.This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 *
	 * @param   mixed  $src     An associative array or object to bind to the JTable instance.
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 */
	public function beforeBind(&$src, $ignore = array())
	{
		return $this->triggerEvent('event_before_bind', array(&$src, $ignore));
	}

	/**
	 * Called before check().
	 *
	 * This method checks that the parent_id is non-zero and exists in the database.
	 * Note that the root node (parent_id = 0) cannot be manipulated with this class.
	 *
	 * @return  boolean  True if all checks pass.
	 */
	protected function beforeCheck()
	{
		return $this->triggerEvent('event_before_check', func_get_args());
	}

	/**
	 * Called before delete().
	 *
	 * @param   mixed  $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 *
	 * @return  boolean  True on success.
	 */
	protected function beforeDelete($pk = null)
	{
		return $this->triggerEvent('event_before_delete', func_get_args());
	}

	/**
	 * Called before load().
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If not
	 *                           set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found.
	 */
	protected function beforeLoad($keys = null, $reset = true)
	{
		return $this->triggerEvent('event_before_load', func_get_args());
	}

	/**
	 * Called before publish().
	 *
	 * @param   mixed    $pks     An optional array of primary key values to update.
	 *                            If not set the instance property value is used.
	 * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param   integer  $userId  The user id of the user performing the operation.
	 *
	 * @return  boolean  True on success; false if $pks is empty.
	 */
	public function beforePublish($pks = null, $state = 1, $userId = 0)
	{
		return $this->triggerEvent('event_before_publish', func_get_args());
	}

	/**
	 * Called before store(). Overriden to send isNew to plugins.
	 *
	 * @param   boolean  $updateNulls  True to update null values as well.
	 * @param   boolean  $isNew        True if we are adding a new item.
	 * @param   mixed    $oldItem      null for new items | JTable otherwise
	 *
	 * @return  boolean  True on success.
	 */
	protected function beforeStore($updateNulls = false, $isNew = false, $oldItem = null)
	{
		return $this->triggerEvent('event_before_store', func_get_args());
	}

	/**
	 * Method to bind an associative array or object to the JTable instance.This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 *
	 * @param   mixed  $src     An associative array or object to bind to the JTable instance.
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function bind($src, $ignore = array())
	{
		if (!$this->beforeBind($src, $ignore))
		{
			return false;
		}

		if (!$this->doBind($src, $ignore))
		{
			return false;
		}

		if (!$this->afterBind($src, $ignore))
		{
			return false;
		}

		return true;
	}

	/**
	 * Checks that the object is valid and able to be stored.
	 *
	 * This method checks that the parent_id is non-zero and exists in the database.
	 * Note that the root node (parent_id = 0) cannot be manipulated with this class.
	 *
	 * @return  boolean  True if all checks pass.
	 */
	public function check()
	{
		if (!$this->beforeCheck())
		{
			return false;
		}

		if (!$this->doCheck())
		{
			return false;
		}

		if (!$this->afterCheck())
		{
			return false;
		}

		return true;
	}

	/**
	 * Deletes this row in database (or if provided, the row of key $pk)
	 *
	 * @param   mixed  $pk  		An optional primary key value to delete.  If not set the instance property value is used.
	 * @param   mixed  $children	'Decl. of HasAutoEvents::delete($pk = NULL) must be compatible with Nested::delete($pk = NULL, $children = true)'
	 *
	 * @return  boolean  True on success.
	 */
	public function delete($pk = null, $children = null)
	{
		// Before delete
		if (!$this->beforeDelete($pk))
		{
			return false;
		}

		// Delete
		if (!$this->doDelete($pk))
		{
			return false;
		}

		// After delete
		if (!$this->afterDelete($pk))
		{
			return false;
		}

		return true;
	}

	/**
	 * Method to bind an associative array or object to the JTable instance.This
	 * method only binds properties that are publicly accessible and optionally
	 * takes an array of properties to ignore when binding.
	 *
	 * @param   mixed  $src     An associative array or object to bind to the JTable instance.
	 * @param   mixed  $ignore  An optional array or space separated list of properties to ignore while binding.
	 *
	 * @return  boolean  True on success.
	 *
	 * @throws  \InvalidArgumentException
	 */
	protected function doBind(&$src, $ignore = array())
	{
		// Try to automatically detect foreign keys and set them to NULL if empty
		foreach ($src as $key => $value)
		{
			if ('_id' === substr($key, -3) && is_string($src[$key]) && '' === trim($src[$key]))
			{
				$this->{$key} = null;
				unset($src[$key]);
			}
		}

		// Nullable columns
		foreach ($this->nullIfEmptyColumns as $nullIfEmptyColumn)
		{
			if (!array_key_exists($nullIfEmptyColumn, $src))
			{
				continue;
			}

			$emptyValues = [null, '', '0', '0000-00-00', '0000-00-00 00:00:00', '1971-01-01', '1971-01-01 00:00:00'];

			if (in_array(trim($src[$nullIfEmptyColumn]), $emptyValues, true))
			{
				$this->{$nullIfEmptyColumn} = null;
				unset($src[$nullIfEmptyColumn]);
			}
		}

		// Autogenerate aliases
		if (property_exists($this, 'alias') && (empty($this->alias) || isset($src['alias'])) && empty($src['alias']))
		{
			if (!empty($src['name']))
			{
				$src['alias'] = $src['name'];
			}
			elseif (!empty($src['title']))
			{
				$src['alias'] = $src['title'];
			}

			if (!empty($src['alias']))
			{
				$src['alias'] = \JApplication::stringURLSafe($src['alias']);

				// Ensure that we don't automatically generate duplicated aliases
				$table = clone $this;

				while ($table->load(array('alias' => $src['alias'])) && $table->id != $this->id)
				{
					$src['alias'] = \JString::increment($src['alias'], 'dash');
				}
			}
		}

		// Autofill created_by and modified_by information
		$now = \JDate::getInstance();
		$nowFormatted = $now->toSql();
		$userId = \JFactory::getUser()->get('id');

		if (property_exists($this, 'created_by')
			&& empty($src['created_by']) && (is_null($this->{'created_by'}) || empty($this->{'created_by'})))
		{
			$src['created_by']   = $userId;
		}

		if (property_exists($this, 'created_user_id')
			&& empty($src['created_user_id']) && empty($this->{'created_user_id'}))
		{
			$src['created_user_id']   = $userId;
		}

		if (property_exists($this, 'created_date')
			&& (empty($src['created_date']) || $src['created_date'] === '0000-00-00 00:00:00')
			&& (empty($this->{'created_date'}) || $this->{'created_date'} === '0000-00-00 00:00:00'))
		{
			$src['created_date'] = $nowFormatted;
		}

		if (property_exists($this, 'created_time')
			&& (empty($src['created_time']) || $src['created_time'] === '0000-00-00 00:00:00')
			&& (empty($this->{'created_time'}) || $this->{'created_time'} === '0000-00-00 00:00:00'))
		{
			$src['created_time'] = $nowFormatted;
		}

		if (property_exists($this, 'modified_by') && empty($src['modified_by']))
		{
			$src['modified_by']   = $userId;
		}

		if (property_exists($this, 'modified_user_id') && empty($src['modified_user_id']))
		{
			$src['modified_user_id']   = $userId;
		}

		if (property_exists($this, 'modified_date')
			&& (empty($src['modified_date']) || $src['modified_date'] === '0000-00-00 00:00:00'))
		{
			$src['modified_date'] = $nowFormatted;
		}

		if (property_exists($this, 'modified_time')
			&& (empty($src['modified_time']) || $src['modified_time'] === '0000-00-00 00:00:00'))
		{
			$src['modified_time'] = $nowFormatted;
		}

		if (isset($src['params']) && is_array($src['params']))
		{
			$registry = new \JRegistry;
			$registry->loadArray($src['params']);
			$src['params'] = (string) $registry;
		}

		if (isset($src['metadata']) && is_array($src['metadata']))
		{
			$registry = new \JRegistry;
			$registry->loadArray($src['metadata']);
			$src['metadata'] = (string) $registry;
		}

		if (isset($src['rules']) && is_array($src['rules']))
		{
			$rules = new \JAccessRules($src['rules']);
			$this->setRules($rules);
		}

		// JSON encode any fields required
		if (!empty($this->_jsonEncode))
		{
			foreach ($this->_jsonEncode as $field)
			{
				if (isset($src[$field]) && is_array($src[$field]))
				{
					$src[$field] = json_encode($src[$field]);
				}
			}
		}

		// Check if the source value is an array or object
		if (!is_object($src) && !is_array($src))
		{
			throw new \InvalidArgumentException(
				sprintf(
					'Could not bind the data source in %1$s::bind(), the source must be an array or object but a "%2$s" was given.',
					get_class($this),
					gettype($src)
				)
			);
		}

		// If the source value is an object, get its accessible properties.
		if (is_object($src))
		{
			$src = get_object_vars($src);
		}

		// If the ignore value is a string, explode it over spaces.
		if (!is_array($ignore))
		{
			$ignore = explode(' ', $ignore);
		}

		// Bind the source value, excluding the ignored fields.
		foreach ($this->getProperties() as $k => $v)
		{
			// Only process fields not in the ignore array.
			if (!in_array($k, $ignore))
			{
				if (array_key_exists($k, $src))
				{
					$this->$k = $src[$k];
				}
			}
		}

		// Generate automatic ordering. After parent:bind() so getNextOrder is able to use complex conditions
		if (property_exists($this, 'ordering') && empty($this->ordering) && empty($src['ordering']))
		{
			$this->ordering = $this->getNextOrder();

			unset($src['ordering']);
		}

		return true;
	}

	/**
	 * Checks that the object is valid and able to be stored.
	 *
	 * This method checks that the parent_id is non-zero and exists in the database.
	 * Note that the root node (parent_id = 0) cannot be manipulated with this class.
	 *
	 * @return  boolean  True if all checks pass.
	 */
	protected function doCheck()
	{
		return parent::check();
	}

	/**
	 * Delete one or more registers
	 *
	 * @param   string/array  $pk  Array of ids or ids comma separated
	 *
	 * @return  boolean  Deleted successfuly?
	 */
	protected function doDelete($pk = null)
	{
		return parent::delete($pk);
	}

	/**
	 * Method to load a row from the database by primary key and bind the fields
	 * to the JTable instance properties.
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If not
	 *                           set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found.
	 */
	protected function doLoad($keys = null, $reset = true)
	{
		return parent::load($keys, $reset);
	}

	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table.  The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param   mixed    $pks     An optional array of primary key values to update.
	 *                            If not set the instance property value is used.
	 * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param   integer  $userId  The user id of the user performing the operation.
	 *
	 * @return  boolean  True on success; false if $pks is empty.
	 *
	 * @link    https://docs.joomla.org/JTable/publish
	 */
	protected function doPublish($pks = null, $state = 1, $userId = 0)
	{
		return parent::publish($pks, $state, $userId);
	}

	/**
	 * Do the database store.
	 *
	 * @param   boolean  $updateNulls  True to update null values as well.
	 *
	 * @return  boolean
	 */
	protected function doStore($updateNulls = false)
	{
		return parent::store($updateNulls);
	}

	/**
	 * Generate automatic events for this table.
	 *
	 * Example of generated events:
	 * Array
	 *	(
	 *	    [event_after_check] => onComponentTableAfterCheckField
	 *	    [event_after_delete] => onComponentTableAfterDeleteField
	 *	    [event_after_load] => onComponentTableAfterLoadField
	 *	    [event_after_publish] => onComponentTableAfterPublishField
	 *	    [event_after_store] => onComponentTableAfterStoreField
	 *	    [event_before_check] => onComponentTableBeforeCheckField
	 *	    [event_before_delete] => onComponentTableBeforeDeleteField
	 *	    [event_before_load] => onComponentTableBeforeLoadField
	 *	    [event_before_publish] => onComponentTableBeforePublishField
	 *	    [event_before_store] => onComponentTableBeforeStoreField
	 *	)
	 *
	 * @return  void
	 */
	protected function generateEventsConfig()
	{
		$instanceName   = strtolower($this->getInstanceName());
		$instancePrefix = strtolower($this->getInstancePrefix());

		$eventsPrefix = 'on' . ucfirst($instancePrefix) . 'Table';
		$eventsSuffix = ucfirst($instanceName);

		foreach ($this->availableEvents as $eventKey => &$event)
		{
			if (null === $event)
			{
				$eventParts = explode('_', str_replace('event_', '', $eventKey));
				$eventName = implode('', array_map("ucfirst", $eventParts));

				$event = $eventsPrefix . ucfirst($eventName) . $eventsSuffix;
			}
		}

		if (empty($this->pluginTypesToImport))
		{
			$this->pluginTypesToImport[] = $instancePrefix;
		}
	}

	/**
	 * Import the plugin types.
	 *
	 * @return  void
	 */
	private function importPluginTypes()
	{
		foreach ($this->pluginTypesToImport as $type)
		{
			\JPluginHelper::importPlugin($type);
		}
	}

	/**
	 * Method to load a row from the database by primary key and bind the fields
	 * to the JTable instance properties.
	 *
	 * @param   mixed    $keys   An optional primary key value to load the row by, or an array of fields to match.  If not
	 *                           set the instance property value is used.
	 * @param   boolean  $reset  True to reset the default values before loading the new row.
	 *
	 * @return  boolean  True if successful. False if row not found.
	 */
	public function load($keys = null, $reset = true)
	{
		if (!$this->beforeLoad($keys, $reset))
		{
			return false;
		}

		if (!$this->doLoad($keys, $reset))
		{
			return false;
		}

		if (!$this->afterLoad($keys, $reset))
		{
			return false;
		}

		return true;
	}

	/**
	 * Method to set the publishing state for a row or list of rows in the database
	 * table.  The method respects checked out rows by other users and will attempt
	 * to checkin rows that it can after adjustments are made.
	 *
	 * @param   mixed    $pks     An optional array of primary key values to update.
	 *                            If not set the instance property value is used.
	 * @param   integer  $state   The publishing state. eg. [0 = unpublished, 1 = published]
	 * @param   integer  $userId  The user id of the user performing the operation.
	 *
	 * @return  boolean  True on success; false if $pks is empty.
	 *
	 * @link    https://docs.joomla.org/JTable/publish
	 */
	public function publish($pks = null, $state = 1, $userId = 0)
	{
		if (!$this->beforePublish($pks, $state, $userId))
		{
			return false;
		}

		if (!$this->doPublish($pks, $state, $userId))
		{
			return false;
		}

		if (!$this->afterPublish($pks, $state, $userId))
		{
			return false;
		}

		return true;
	}

	/**
	 * Method to store a node in the database table.
	 *
	 * @param   boolean  $updateNulls  True to update null values as well.
	 *
	 * @return  boolean  True on success.
	 */
	public function store($updateNulls = false)
	{
		$k = $this->{'_tbl_key'};

		$isNew = !$this->hasPrimaryKey();

		$oldItem = null;

		if (!$isNew)
		{
			$oldItem = clone $this;

			$data = array();

			foreach ($this->{'_tbl_keys'} as $key)
			{
				$data[$key] = $this->$key;
			}

			if (!$oldItem->load($data))
			{
				$this->setError(\JText::sprintf($this->getTextPrefix() . '_ERROR_CANNOT_LOAD_ITEM', $this->$k));

				return false;
			}
		}

		if (!$this->beforeStore($updateNulls, $isNew, $oldItem))
		{
			return false;
		}

		// Store
		if (!$this->doStore($updateNulls))
		{
			return false;
		}

		if (!$this->afterStore($updateNulls, $isNew, $oldItem))
		{
			return false;
		}

		return true;
	}

	/**
	 * Trigger an event.
	 *
	 * @param   string  $eventkey  Key of the event in the availableEvents array
	 * @param   array   $params    Arguments for the event being triggered
	 *
	 * @return  boolean
	 */
	protected function triggerEvent($eventkey, $params = array())
	{
		$eventkey = trim($eventkey);

		if (!$eventkey)
		{
			return false;
		}

		if (!isset($this->availableEvents[$eventkey]))
		{
			return true;
		}

		$eventName = $this->availableEvents[$eventkey];

		// Import the plugin types
		$this->importPluginTypes();

		// First param will be always this table
		array_unshift($params, $this);

		// Trigger the event
		$results = \JEventDispatcher::getInstance()->trigger($eventName, $params);

		if (count($results) && in_array(false, $results, true))
		{
			return false;
		}

		return true;
	}
}
