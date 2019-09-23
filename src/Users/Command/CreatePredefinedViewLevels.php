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
use Phproberto\Joomla\Entity\Command\BaseCommand;
use Phproberto\Joomla\Entity\Users\GuestViewLevel;
use Phproberto\Joomla\Entity\Users\PublicViewLevel;
use Phproberto\Joomla\Entity\Users\SpecialViewLevel;
use Phproberto\Joomla\Entity\Users\RegisteredViewLevel;
use Phproberto\Joomla\Entity\Users\SuperUsersViewLevel;
use Phproberto\Joomla\Entity\Command\Contracts\CommandInterface;

/**
 * Create the predefined user groups.
 *
 * @since  __DEPLOY_VERSION__
 */
class CreatePredefinedViewLevels extends BaseCommand implements CommandInterface
{
	/**
	 * Execute the command.
	 *
	 * @return  UserGroup[]
	 */
	public function execute()
	{
		return [
			'public'      => PublicViewLevel::create(),
			'registered'  => RegisteredViewLevel::create(),
			'special'     => SpecialViewLevel::create(),
			'guest'       => GuestViewLevel::create(),
			'super-users' => SuperUsersViewLevel::create()
		];
	}
}
