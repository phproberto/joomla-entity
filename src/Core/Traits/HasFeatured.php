<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Core\Column;

/**
 * Trait for entities with featured column.
 *
 * @since   1.0.0
 */
trait HasFeatured
{
	/**
	 * Is this entity featured.
	 *
	 * @var  boolean
	 */
	protected $featured;

	/**
	 * Get the alias for a specific DB column.
	 *
	 * @param   string  $column  Name of the DB column. Example: created_by
	 *
	 * @return  string
	 */
	abstract public function columnAlias($column);

	/**
	 * Get a property of this entity.
	 *
	 * @param   string  $property  Name of the property to get
	 * @param   mixed   $default   Value to use as default if property is not set or is null
	 *
	 * @return  mixed
	 */
	abstract public function get($property, $default = null);

	/**
	 * Is this article featured?
	 *
	 * @return  boolean
	 */
	public function isFeatured()
	{
		$featured = (int) $this->get($this->columnAlias(Column::FEATURED));

		return $featured ? true : false;
	}
}
