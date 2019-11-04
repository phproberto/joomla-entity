<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Core\CoreColumn;

/**
 * Trait for entities with state.
 *
 * @since   1.0.0
 */
trait HasState
{
	/**
	 * Entity state.
	 *
	 * @var  integer
	 */
	protected $state;

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
	 * Get a list of available states.
	 *
	 * @return  string
	 */
	public function availableStates()
	{
		return array(
			self::STATE_PUBLISHED   => \JText::_('JPUBLISHED'),
			self::STATE_UNPUBLISHED => \JText::_('JUNPUBLISHED'),
			self::STATE_ARCHIVED    => \JText::_('JARCHIVEDSTATE_ARCHIVED'),
			self::STATE_TRASHED     => \JText::_('JTRASHED')
		);
	}

	/**
	 * Check if this entity is archived.
	 *
	 * @return  boolean
	 */
	public function isArchived()
	{
		return $this->isOnState(self::STATE_ARCHIVED);
	}

	/**
	 * Check if this entity is disabled.
	 * This is just a proxy for entities that use enabled/disabled instead of published/unpublished.
	 *
	 * @return  boolean
	 */
	public function isDisabled()
	{
		return $this->isUnpublished();
	}

	/**
	 * Check if this entity is enabled.
	 * This is just a proxy for entities that use enabled/disabled instead of published/unpublished.
	 *
	 * @return  boolean
	 */
	public function isEnabled()
	{
		return $this->isPublished();
	}

	/**
	 * Check if this entity is on a specific state.
	 *
	 * @param   integer   $state  State to check.
	 *
	 * @return  boolean
	 */
	public function isOnState($state)
	{
		return $this->state() === (int) $state;
	}

	/**
	 * Check if this entity is published.
	 *
	 * @return  boolean
	 */
	public function isPublished()
	{
		return $this->isOnState(self::STATE_PUBLISHED);
	}

	/**
	 * Check if this entity is trashed.
	 *
	 * @return  boolean
	 */
	public function isTrashed()
	{
		return $this->isOnState(self::STATE_TRASHED);
	}

	/**
	 * Check if this entity is unpublished.
	 *
	 * @return  boolean
	 */
	public function isUnpublished()
	{
		return $this->isOnState(self::STATE_UNPUBLISHED);
	}

	/**
	 * Get state of this entity.
	 *
	 * @return  integer
	 */
	public function state()
	{
		return (int) $this->get($this->columnAlias(CoreColumn::STATE));
	}

	/**
	 * Get the name of the entity state.
	 *
	 * @return  string
	 */
	public function stateName()
	{
		$state = $this->state();
		$availableStates = $this->availableStates();

		return isset($availableStates[$state]) ? $availableStates[$state] : '';
	}
}
