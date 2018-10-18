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
 * Trait for entities with access column.
 *
 * @since   1.0.0
 */
trait HasAccess
{
	/**
	 * Can current user access this entity?
	 *
	 * @var  boolean
	 */
	protected $access;

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
	 * Check if this entity has an id.
	 *
	 * @return  boolean
	 */
	abstract public function hasId();

	/**
	 * Get access level required for this entity.
	 *
	 * @return  integer
	 */
	public function access()
	{
		return (int) $this->get($this->columnAlias(Column::ACCESS));
	}

	/**
	 * Can current user access this entity?
	 *
	 * @param   boolean  $reload  Force reloading
	 *
	 * @return  boolean
	 */
	public function canAccess($reload = false)
	{
		if ($reload || null === $this->access)
		{
			$this->access = $this->checkAccess();
		}

		return $this->access;
	}

	/**
	 * Check access to this entity.
	 *
	 * @return  boolean
	 *
	 * @codeCoverageIgnore
	 */
	protected function checkAccess()
	{
		if (!$this->hasId())
		{
			return false;
		}

		$authorised = \JAccess::getAuthorisedViewLevels(\JFactory::getUser()->get('id'));

		return in_array($this->access(), $authorised);
	}
}
