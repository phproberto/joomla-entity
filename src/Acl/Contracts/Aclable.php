<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Acl\Contracts;

defined('_JEXEC') || die;

/**
 * Describes methods required by entities with ACL support.
 *
 * @since  1.0.0
 */
interface Aclable
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
