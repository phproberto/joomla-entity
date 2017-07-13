<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Traits;

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
	 * Get a table.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  \JTable
	 *
	 * @codeCoverageIgnore
	 */
	abstract public function table($name = '', $prefix = null, $options = array());

	/**
	 * Get a list of available states.
	 *
	 * @return  string
	 */
	public function getAvailableStates()
	{
		return [
			self::STATE_PUBLISHED   => \JText::_('JPUBLISHED'),
			self::STATE_UNPUBLISHED => \JText::_('JUNPUBLISHED'),
			self::STATE_ARCHIVED    => \JText::_('JARCHIVEDSTATE_ARCHIVED'),
			self::STATE_TRASHED     => \JText::_('JTRASHED')
		];
	}

	/**
	 * Get the name of the column that stores state.
	 *
	 * @return  string
	 */
	protected function getColumnState()
	{
		return $this->table()->getColumnAlias('published');
	}

	/**
	 * Get state of this entity.
	 *
	 * @param   boolean  $reload  Force state loading
	 *
	 * @return  integer
	 */
	public function getState($reload = false)
	{
		if ($reload || null === $this->state)
		{
			$this->state = $this->loadState();
		}

		return $this->state;
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
	 * Check if this entity is on a specific state.
	 *
	 * @param   integer   $state  State to check.
	 *
	 * @return  boolean
	 */
	public function isOnState($state)
	{
		return $this->getState() === (int) $state;
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
	 * Calculates the entity state.
	 *
	 * @return  integer
	 *
	 * @throws  \RuntimeException
	 */
	protected function loadState()
	{
		$column = $this->getColumnState();
		$data    = $this->all();

		if (!array_key_exists($column, $data))
		{
			throw new \RuntimeException("Entity (" . get_class($this) . ") does not have a state column", 500);
		}

		return (int) $data[$column];
	}
}
