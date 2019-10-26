<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Contracts;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Contracts\EntityInterface;

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

	/**
	 * Retrieve an instance of the associated entity.
	 *
	 * @param   integer  $id  Identifier
	 *
	 * @return  EntityInterface
	 */
	public function entityInstance(int $id = null);
}
