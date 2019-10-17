<?php
/**
 * @package     Joomla.Entity
 * @subpackage  Table
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Table;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Table\Nested;
use Phproberto\Joomla\Entity\Table\Traits;

/**
 * Base table class.
 *
 * @since  __DEPLOY_VERSION__
 */
abstract class AbstractNestedTable extends Nested implements TableInterface
{
	use Traits\HasCommonChecks, Traits\HasAutoEvents, Traits\HasFixedGetProperties, Traits\HasInstanceName;
	use Traits\HasInstancePrefix, Traits\HasKeysComparator, Traits\HasLanguageStrings;

	/**
	 * The table name without the prefix. Ex: cursos_courses
	 *
	 * @var  string
	 */
	protected $tableName = null;

	/**
	 * The table key column. Usually: id
	 *
	 * @var  string
	 */
	protected $tableKey = 'id';

	/**
	 * Name of the primary key fields in the table.
	 *
	 * @var  array
	 */
	protected $tableKeys = [];

	/**
	 * Array with alias for "special" columns such as ordering, hits etc etc
	 *
	 * @var    array
	 */
	protected $columnAlias = array(
		'published' => 'state'
	);

	/**
	 * Constructor
	 *
	 * @param   \JDatabase  $db  A database connector object
	 *
	 * @throws  \UnexpectedValueException
	 */
	public function __construct(&$db)
	{
		if (!empty($this->tableName))
		{
			$this->{'_tbl'} = $this->tableName;
		}

		if (!empty($this->columnAlias))
		{
			$this->{'_columnAlias'} = array_merge($this->{'_columnAlias'}, $this->columnAlias);
		}

		if (!empty($this->tableKeys))
		{
			$this->{'_tbl_keys'} = $this->tableKeys;
		}

		$key = $this->{'_tbl_key'};

		if (empty($key) && !empty($this->{'_tbl_keys'}))
		{
			$key = $this->{'_tbl_keys'};
		}

		// Keep checking _tbl_key for standard defined tables
		if (empty($key) && !empty($this->tableKey))
		{
			$this->{'_tbl_key'} = $this->tableKey;
			$key = $this->{'_tbl_key'};
		}

		if (empty($this->{'_tbl'}) || empty($key))
		{
			throw new \UnexpectedValueException(sprintf('Missing data to initialize %s table | id: %s', $this->{'_tbl'}, $key));
		}

		if ($this->autoEvents)
		{
			$this->generateEventsConfig();
		}

		parent::__construct($this->{'_tbl'}, $key, $db);
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
		if (!$this->checkCommonValidations())
		{
			return false;
		}

		if (!parent::check())
		{
			return false;
		}

		return true;
	}

	/**
	 * Delete one or more registers.
	 * Overriden to avoid errors having assets on a different database.
	 *
	 * @param   string/array  $pk  Array of ids or ids comma separated
	 *
	 * @return  boolean  Deleted successfuly?
	 */
	protected function doDelete($pk = null)
	{
		$previousTrackAssets = $this->_trackAssets;

		// If tracking assets, remove the asset first.
		if ($this->_trackAssets)
		{
			$name = $this->_getAssetName();
			$asset = AbstractTable::getInstance('Asset', 'JTable', array('dbo' => $this->getDbo()));

			// Lock the table for writing.
			if (!$asset->_lock())
			{
				// Error message set in lock method.
				return false;
			}

			if ($asset->loadByName($name))
			{
				// Delete the node in assets table.
				if (!$asset->delete(null, $children))
				{
					$this->setError($asset->getError());
					$asset->_unlock();

					return false;
				}

				$asset->_unlock();
			}

			$this->_trackAssets = false;
		}

		$result = parent::delete($pk);

		$this->_trackAssets = $previousTrackAssets;

		return $result;
	}

	/**
	 * Returns an asset id for the given name.
	 *
	 * @param   string  $name  The asset name
	 *
	 * @return  integer
	 */
	protected function getAssetId($name)
	{
		$db = Factory::getDbo();

		$query = $db->getQuery(true)
			->select($db->quoteName('id'))
			->from($db->quoteName('#__assets'))
			->where($db->quoteName('name') . ' = ' . $db->quote($name));

		$db->setQuery($query);

		return (int) $db->loadResult();
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 */
	protected function getAssetName()
	{
		return parent::_getAssetName();
	}

	/**
	 * Method to get the parent asset under which to register this one.
	 * By default, all assets are registered to the ROOT node with ID,
	 * which will default to 1 if none exists.
	 * The extended class can define a table and id to lookup.  If the
	 * asset does not exist it will be created.
	 *
	 * @param   Table    $table  A Table object for the asset parent.
	 * @param   integer  $id     Id to look up
	 *
	 * @return  integer
	 */
	protected function getAssetParentId(Table $table = null, $id = null)
	{
		return parent::_getAssetParentId($table, $id);
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 */
	protected function _getAssetName()
	{
		return $this->getAssetName();
	}

	/**
	 * Method to get the parent asset under which to register this one.
	 * By default, all assets are registered to the ROOT node with ID,
	 * which will default to 1 if none exists.
	 * The extended class can define a table and id to lookup.  If the
	 * asset does not exist it will be created.
	 *
	 * @param   Table    $table  A Table object for the asset parent.
	 * @param   integer  $id     Id to look up
	 *
	 * @return  integer
	 */
	protected function _getAssetParentId(Table $table = null, $id = null)
	{
		return $this->getAssetParentId($table, $id);
	}

	/**
	 * Static method to get an instance of a Table class if it can be found in the table include paths.
	 *
	 * To add include paths for searching for Table classes see Table::addIncludePath().
	 *
	 * @param   string  $type    The type (name) of the Table class to get an instance of.
	 * @param   string  $prefix  An optional prefix for the table class name.
	 * @param   array   $config  An optional array of configuration values for the Table object.
	 *
	 * @return  Table|boolean   A Table object if found or boolean false on failure.
	 */
	public static function getInstance($type, $prefix = 'JTable', $config = array())
	{
		// Ensure that Joomla DB is used for joomla tables
		if ('JTable' === $prefix)
		{
			$config['dbo'] = Factory::getDbo();
		}

		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to store a row in the database from the Table instance properties.
	 *
	 * If a primary key value is set the row with that primary key value will be updated with the instance property values.
	 * If no primary key value is set a new row will be inserted into the database with the properties from the Table instance.
	 *
	 * @param   boolean  $updateNulls  True to update fields even if they are null.
	 *
	 * @return  boolean  True on success.
	 */
	public function parentStore($updateNulls = false)
	{
		$k = $this->{'_tbl_keys'};

		// Implement JObservableInterface: Pre-processing by observers
		$this->_observers->update('onBeforeStore', array($updateNulls, $k));

		$currentAssetId = 0;

		if (!empty($this->{'asset_id'}))
		{
			$currentAssetId = $this->{'asset_id'};
		}

		// The asset id field is managed privately by this class.
		if ($this->_trackAssets)
		{
			unset($this->{'asset_id'});
		}

		// If a primary key exists update the object, otherwise insert it.
		if ($this->hasPrimaryKey())
		{
			$result = $this->{'_db'}->updateObject($this->{'_tbl'}, $this, $this->{'_tbl_keys'}, $updateNulls);
		}
		else
		{
			$result = $this->{'_db'}->insertObject($this->{'_tbl'}, $this, $this->{'_tbl_keys'}[0]);
		}

		// If the table is not set to track assets return true.
		if ($this->_trackAssets)
		{
			if ($this->_locked)
			{
				$this->_unlock();
			}

			/*
			 * Asset Tracking
			 */
			$parentId = $this->_getAssetParentId();
			$name     = $this->_getAssetName();
			$title    = $this->_getAssetTitle();

			$asset = self::getInstance('Asset', 'JTable', array('dbo' => $this->getDbo()));
			$asset->loadByName($name);

			// Re-inject the asset id.
			$this->{'asset_id'} = $asset->id;

			// Check for an error.
			$error = $asset->getError();

			if ($error)
			{
				$this->setError($error);

				return false;
			}
			else
			{
				// Specify how a new or moved node asset is inserted into the tree.
				if (empty($this->{'asset_id'}) || $asset->{'parent_id'} != $parentId)
				{
					$asset->setLocation($parentId, 'last-child');
				}

				// Prepare the asset to be stored.
				$asset->{'parent_id'} = $parentId;
				$asset->name      = $name;
				$asset->title     = $title;

				if ($this->_rules instanceof \JAccessRules)
				{
					$asset->rules = (string) $this->_rules;
				}

				if (!$asset->check() || !$asset->store())
				{
					$this->setError($asset->getError());

					return false;
				}
				else
				{
					// Create an asset_id or heal one that is corrupted.
					if (empty($this->{'asset_id'}) || ($currentAssetId != $this->{'asset_id'} && !empty($this->{'asset_id'})))
					{
						// Update the asset_id field in this table.
						$this->{'asset_id'} = (int) $asset->id;

						$query = $this->{'_db'}->getQuery(true)
							->update($this->{'_db'}->quoteName($this->{'_tbl'}))
							->set('asset_id = ' . (int) $this->{'asset_id'});
						$this->appendPrimaryKeys($query);
						$this->{'_db'}->setQuery($query)->execute();
					}
				}
			}
		}

		// Implement JObservableInterface: Post-processing by observers
		$this->_observers->update('onAfterStore', array(&$result));

		return $result;
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
		$k = $this->_tbl_key;

		// Implement \JObservableInterface: Pre-processing by observers
		// 2.5 upgrade issue - check if property_exists before executing
		if (property_exists($this, '_observers'))
		{
			$this->_observers->update('onBeforeStore', array($updateNulls, $k));
		}

		if ($this->_debug)
		{
			echo "\n" . get_class($this) . "::store\n";
			$this->_logtable(true, false);
		}

		/*
		 * If the primary key is empty, then we assume we are inserting a new node into the
		 * tree.  From this point we would need to determine where in the tree to insert it.
		 */
		if (empty($this->$k))
		{
			/*
			 * We are inserting a node somewhere in the tree with a known reference
			 * node.  We have to make room for the new node and set the left and right
			 * values before we insert the row.
			 */
			if ($this->_location_id >= 0)
			{
				// Lock the table for writing.
				if (!$this->_lock())
				{
					// Error message set in lock method.
					return false;
				}

				// We are inserting a node relative to the last root node.
				if ($this->_location_id == 0)
				{
					// Get the last root node as the reference node.
					$query = $this->_db->getQuery(true)
						->select($this->_tbl_key . ', parent_id, level, lft, rgt')
						->from($this->_tbl)
						->where('parent_id IS NULL')
						->order('lft DESC');
					$this->_db->setQuery($query, 0, 1);
					$reference = $this->_db->loadObject();

					if ($this->_debug)
					{
						$this->_logtable(false);
					}
				}
				// We have a real node set as a location reference.
				else
				{
					// Get the reference node by primary key.
					if (!$reference = $this->_getNode($this->_location_id))
					{
						// Error message set in getNode method.
						$this->_unlock();

						return false;
					}
				}

				// Get the reposition data for shifting the tree and re-inserting the node.
				if (!($repositionData = $this->_getTreeRepositionData($reference, 2, $this->_location)))
				{
					// Error message set in getNode method.
					$this->_unlock();

					return false;
				}

				// Create space in the tree at the new location for the new node in left ids.
				$query = $this->_db->getQuery(true)
					->update($this->_tbl)
					->set('lft = lft + 2')
					->where($repositionData->left_where);
				$this->_runQuery($query, 'JLIB_DATABASE_ERROR_STORE_FAILED');

				// Create space in the tree at the new location for the new node in right ids.
				$query->clear()
					->update($this->_tbl)
					->set('rgt = rgt + 2')
					->where($repositionData->right_where);
				$this->_runQuery($query, 'JLIB_DATABASE_ERROR_STORE_FAILED');

				// Set the object values.
				$this->parent_id = $repositionData->new_parent_id;
				$this->level = $repositionData->new_level;
				$this->lft = $repositionData->new_lft;
				$this->rgt = $repositionData->new_rgt;
			}
			else
			{
				// Negative parent ids are invalid
				$e = new \UnexpectedValueException(sprintf('%s::store() used a negative _location_id', get_class($this)));
				$this->setError($e);

				return false;
			}
		}
		/*
		 * If we have a given primary key then we assume we are simply updating this
		 * node in the tree.  We should assess whether or not we are moving the node
		 * or just updating its data fields.
		 */
		else
		{
			// If the location has been set, move the node to its new location.
			if ($this->_location_id > 0)
			{
				// Skip recursiveUpdatePublishedColumn method, it will be called later.
				if (!$this->moveByReference($this->_location_id, $this->_location, $this->$k, false))
				{
					// Error message set in move method.
					return false;
				}
			}

			// Lock the table for writing.
			if (!$this->_lock())
			{
				// Error message set in lock method.
				return false;
			}
		}

		// Implement \JObservableInterface: We do not want parent::store to update observers,
		// since tables are locked and we are updating it from this level of store():

		// 2.5 upgrade issue - check if property_exists before executing
		if (property_exists($this, '_observers'))
		{
			$oldCallObservers = $this->_observers->doCallObservers(false);
		}

		$result = $this->parentStore($updateNulls);

		// Implement \JObservableInterface: Restore previous callable observers state:
		// 2.5 upgrade issue - check if property_exists before executing
		if (property_exists($this, '_observers'))
		{
			$this->_observers->doCallObservers($oldCallObservers);
		}

		if ($result)
		{
			if ($this->_debug)
			{
				$this->_logtable();
			}
		}

		// Unlock the table for writing.
		$this->_unlock();

		if (property_exists($this, 'published'))
		{
			$this->recursiveUpdatePublishedColumn($this->$k);
		}

		// Implement \JObservableInterface: Post-processing by observers
		// 2.5 upgrade issue - check if property_exists before executing
		if (property_exists($this, '_observers'))
		{
			$this->_observers->update('onAfterStore', array(&$result));
		}

		return $result;
	}
}
