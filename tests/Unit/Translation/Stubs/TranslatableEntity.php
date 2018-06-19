<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Translation\Stubs;

use Phproberto\Joomla\Entity\ComponentEntity;
use Phproberto\Joomla\Entity\Translation\Contracts\Translatable;

/**
 * Entity to test Acl decorator.
 *
 * @since  1.1.0
 *
 * @codeCoverageIgnore
 */
class TranslatableEntity extends ComponentEntity implements Translatable
{
	/**
	 * Available translations.
	 *
	 * @var  array
	 */
	public $translations = array();

	/**
	 * Get a translation.
	 *
	 * @param   string  $langTag  Language string. Example: es-ES
	 *
	 * @return  static
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function translation($langTag)
	{
		if (!isset($this->translations[$langTag]))
		{
			$msg = sprintf('Article %d does not have %s language', $this->id(), $langTag);

			throw new \InvalidArgumentException($msg);
		}

		return $this->translations[$langTag];
	}
}
