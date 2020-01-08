<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Contracts;

defined('_JEXEC') || die;

/**
 * Publishable entities requirements.
 *
 * @since   1.0.0
 */
interface Assetable
{
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
	 * Get the associated asset.
	 *
	 * @param   boolean  $reload  Force asset reloading
	 *
	 * @return  Asset
	 */
	public function asset($reload = false);

	public function getContentExtension();
}
