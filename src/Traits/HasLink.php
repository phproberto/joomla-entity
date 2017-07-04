<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Traits;

/**
 * Trait for linkable ntities.
 *
 * @since   __DEPLOY_VERSION__
 */
trait HasLink
{
	/**
	 * Link to this entity.
	 *
	 * @var  string
	 */
	protected $link;

	/**
	 * Get the link to this entity.
	 *
	 * @param   boolean  $reload  Force reloading
	 *
	 * @return  string
	 */
	public function getLink($reload = false)
	{
		if ($reload || null === $this->link)
		{
			$this->link = $this->loadLink();
		}

		return $this->link;
	}

	/**
	 * Get the URL slug.
	 *
	 * @return  string
	 */
	public function getSlug()
	{
		$slug = $this->getId();

		if (!$slug)
		{
			return null;
		}

		$row = $this->getRow();

		if ($this->has('alias'))
		{
			$slug .= ':' . $this->get('alias');
		}

		return $slug;
	}

	/**
	 * Load the link to this entity.
	 *
	 * @return  atring
	 */
	abstract protected function loadLink();
}
