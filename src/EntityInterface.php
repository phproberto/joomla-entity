<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity;

defined('_JEXEC') || die;

/**
 * Describes methods required by entities.
 *
 * @since  __DEPLOY_VERSION__
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
	 * Assign a value to entity property.
	 *
	 * @param   string  $property  Name of the property to set
	 * @param   mixed   $value     Value to assign
	 *
	 * @return  self
	 */
	public function assign($property, $value);

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
	 * Get the entity identifier.
	 *
	 * @return  integer
	 */
	public function id();

	/**
	 * Get entity primary key column.
	 *
	 * @return  string
	 */
	public function primaryKey();

	/**
	 * Get the attached database row.
	 *
	 * @return  array
	 */
	public function all();

	/**
	 * Check if this entity has an identifier.
	 *
	 * @return  boolean
	 */
	public function hasId();
}
