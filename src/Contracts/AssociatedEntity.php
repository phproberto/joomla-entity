<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Contracts;


defined('_JEXEC') || die;

/**
 * Describes methods required by classes with entity access.
 *
 * @since  __DEPLOY_VERSION__
 */
interface AssociatedEntity
{
	/**
	 * Retrieve the associated entity class.
	 *
	 * @return  string
	 */
	public function entityClass();
}
