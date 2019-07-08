<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Command\Contracts;

defined('_JEXEC') || die;

/**
 * Describes methods required by commands.
 *
 * @since  __DEPLOY_VERSION__
 */
interface CommandInterface
{
	/**
	 * Execute the command.
	 *
	 * @return  mixed
	 */
	public function execute();
}
