<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Traits;

/**
 * Trait for entities with state.
 *
 * @since   __DEPLOY_VERSION__
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
	 * Get the attached database row.
	 *
	 * @return  array
	 */
	abstract public function all();

	/**
	 * Get the alias for a specific DB column.
	 *
	 * @param   string  $column  Name of the DB column. Example: created_by
	 *
	 * @return  string
	 */
	abstract public function columnAlias($column);

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
		$column = $this->columnAlias('published');
		$data = $this->all();

		if (!array_key_exists($column, $data))
		{
			throw new \RuntimeException("Entity (" . get_class($this) . ") does not have a state column", 500);
		}

		return (int) $data[$column];
	}
}
