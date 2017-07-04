<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Traits;

/**
 * Trait for entities with access column.
 *
 * @since   __DEPLOY_VERSION__
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
	 * Get the name of the column that stores access.
	 *
	 * @return  string
	 */
	protected function getColumnAccess()
	{
		return 'access';
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

		return in_array($this->getAccess(), $authorised);
	}

	/**
	 * Get access level required for this entity.
	 *
	 * @return  integer
	 */
	public function getAccess()
	{
		return (int) $this->get($this->getColumnAccess());
	}
}
