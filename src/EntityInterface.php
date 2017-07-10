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
	 * Get the entity identifier.
	 *
	 * @return  integer
	 */
	public function getId();

	/**
	 * Get entity primary key column.
	 *
	 * @return  string
	 */
	public function getPrimaryKey();

	/**
	 * Get the attached database row.
	 *
	 * @return  array
	 */
	public function getAll();

	/**
	 * Check if this entity has an identifier.
	 *
	 * @return  boolean
	 */
	public function hasId();
}
