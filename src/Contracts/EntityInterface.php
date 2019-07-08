<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Contracts;

defined('_JEXEC') || die;

/**
 * Describes methods required by entities.
 *
 * @since  1.0.0
 */
interface EntityInterface
{
	/**
	 * Value for published state
	 *
	 * @const
	 */
	const STATE_PUBLISHED = 1;

	/**
	 * Value for unpublished state
	 *
	 * @const
	 */
	const STATE_UNPUBLISHED = 0;

	/**
	 * Value for archived state
	 *
	 * @const
	 */
	const STATE_ARCHIVED = 2;

	/**
	 * Value for trashed state
	 *
	 * @const
	 */
	const STATE_TRASHED = -2;

	/**
	 * Get the attached database row.
	 *
	 * @return  array
	 */
	public function all();

	/**
	 * Assign a value to entity property.
	 *
	 * @param   string  $property  Name of the property to set
	 * @param   mixed   $value     Value to assign
	 *
	 * @return  self
	 */
	public function assign($property, $value);

	/**
	 * Get the alias for a specific DB column.
	 *
	 * @param   string  $column  Name of the DB column. Example: created_by
	 *
	 * @return  string
	 */
	public function columnAlias($column);

	/**
	 * Get a property of this entity.
	 *
	 * @param   string  $property  Name of the property to get
	 * @param   mixed   $default   Value to use as default if property is not set or is null
	 *
	 * @return  mixed
	 */
	public function get($property, $default = null);

	/**
	 * Check if this entity has an identifier.
	 *
	 * @return  boolean
	 */
	public function hasId();

	/**
	 * Get the entity identifier.
	 *
	 * @return  integer
	 */
	public function id();

	/**
	 * Get this entity name.
	 *
	 * @return  string
	 */
	public function name();

	/**
	 * Get entity primary key column.
	 *
	 * @return  string
	 */
	public function primaryKey();
}
