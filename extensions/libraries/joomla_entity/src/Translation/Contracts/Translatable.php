<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Translation\Contracts;

use Phproberto\Joomla\Entity\Contracts\EntityInterface;

defined('_JEXEC') || die;

/**
 * Describes methods required by translatable entities.
 *
 * @since  __DEPLOY_VERSION__
 */
interface Translatable extends EntityInterface
{
	/**
	 * Get a translation.
	 *
	 * @param   string  $langTag  Language string. Example: es-ES
	 *
	 * @return  static
	 *
	 * @throws  \InvalidArgumentException
	 */
	public function translation($langTag);
}
