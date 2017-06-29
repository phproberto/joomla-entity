<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity;

defined('_JEXEC') or die;

/**
 * Describes methods required by entities.
 *
 * @since  __DEPLOY_VERSION__
 */
interface EntityInterface
{
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
	public function getRow();

	/**
	 * Check if this entity has an identifier.
	 *
	 * @return  boolean
	 */
	public function hasId();
}
