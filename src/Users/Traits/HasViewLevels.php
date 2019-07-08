<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users\Traits;

defined('_JEXEC') || die;

/**
 * Trait for entities that have associated view levels.
 *
 * @since  1.2.0
 */
trait HasViewLevels
{
	/**
	 * Associated view levels.
	 *
	 * @var  Collection
	 */
	protected $viewLevels;

	/**
	 * Clear already loaded view levels.
	 *
	 * @return  self
	 */
	public function clearViewLevels()
	{
		$this->viewLevels = null;

		return $this;
	}

	/**
	 * Get the associated view levels.
	 *
	 * @return  Collection
	 */
	public function viewLevels()
	{
		if (null === $this->viewLevels)
		{
			$this->viewLevels = $this->loadViewLevels();
		}

		return $this->viewLevels;
	}

	/**
	 * Check if this entity has an associated view level.
	 *
	 * @param   integer   $id  View level identifier
	 *
	 * @return  boolean
	 */
	public function hasViewLevel($id)
	{
		return $this->viewLevels()->has($id);
	}

	/**
	 * Check if this entity has associated view levels.
	 *
	 * @return  boolean
	 */
	public function hasViewLevels()
	{
		return !$this->viewLevels()->isEmpty();
	}

	/**
	 * Load associated view levels.
	 *
	 * @return  Collection
	 */
	abstract protected function loadViewLevels();
}
