<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Users\Command;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Users\UserGroup;
use Phproberto\Joomla\Entity\Command\BaseCommand;
use Phproberto\Joomla\Entity\Users\GuestUserGroup;
use Phproberto\Joomla\Entity\Users\PublicUserGroup;
use Phproberto\Joomla\Entity\Users\RegisteredUserGroup;
use Phproberto\Joomla\Entity\Users\SuperUsersUserGroup;
use Phproberto\Joomla\Entity\Command\Contracts\CommandInterface;

/**
 * Create the predefined user groups.
 *
 * @since  __DEPLOY_VERSION__
 */
class CreatePredefinedUserGroups extends BaseCommand implements CommandInterface
{
	/**
	 * Execute the command.
	 *
	 * @return  UserGroup[]
	 */
	public function execute()
	{
		return [
			'public'      => PublicUserGroup::create(),
			'guest'       => GuestUserGroup::create(),
			'registered'  => RegisteredUserGroup::create(),
			'super-users' => SuperUsersUserGroup::create()
		];
	}
}
