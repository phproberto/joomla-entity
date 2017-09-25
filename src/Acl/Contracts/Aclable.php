<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Acl\Contracts;

use Phproberto\Joomla\Entity\Contracts\EntityInterface;

defined('_JEXEC') || die;

/**
 * Describes methods required by entities with ACL support.
 *
 * @since  __DEPLOY_VERSION__
 */
interface Aclable extends EntityInterface
{
	/**
	 * Get the ACL prefix applied to this entity
	 *
	 * @return  string
	 */
	public function aclPrefix();

	/**
	 * Get the identifier of the associated asset
	 *
	 * @return  string
	 */
	public function aclAssetName();
}
