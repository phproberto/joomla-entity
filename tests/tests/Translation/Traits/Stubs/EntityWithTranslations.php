<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Translation\Traits\Stubs;

use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Translation\Traits\HasTranslations;

/**
 * Sample entity to test HasTranslations trait.
 *
 * @since  1.1.0
 */
class EntityWithTranslations extends Entity
{
	use HasTranslations;

	/**
	 * Expected translations ids for testing.
	 *
	 * @var  array
	 */
	public $translationsIds = array();

	/**
	 * Load associated translations from DB.
	 *
	 * @return  Collection
	 */
	protected function loadTranslations()
	{
		$collection = new Collection;

		foreach ($this->translationsIds as $id)
		{
			$collection->add(new static($id));
		}

		return $collection;
	}
}
