<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Traits;

/**
 * Trait for entities with featured column.
 *
 * @since   __DEPLOY_VERSION__
 */
trait HasFeatured
{
	/**
	 * Is this entity featured.
	 *
	 * @var  boolean
	 */
	private $featured;

	/**
	 * Get the name of the column that stores featured.
	 *
	 * @return  string
	 */
	protected function getColumnFeatured()
	{
		return 'featured';
	}

	/**
	 * Get the attached database row.
	 *
	 * @return  array
	 */
	abstract public function all();

	/**
	 * Is this article featured?
	 *
	 * @param   boolean  $reload  Force reloading
	 *
	 * @return  boolean
	 */
	public function isFeatured($reload = false)
	{
		if ($reload || null === $this->featured)
		{
			$this->featured = $this->loadFeatured();
		}

		return $this->featured;
	}

	/**
	 * Check if this entity is featured.
	 *
	 * @return  boolean
	 */
	private function loadFeatured()
	{
		$column = $this->getColumnFeatured();
		$data    = $this->all();

		if (empty($data[$column]))
		{
			return false;
		}

		return 1 === (int) $data[$column];
	}
}
