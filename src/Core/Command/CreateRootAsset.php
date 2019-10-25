<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core\Command;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Core\Entity\Asset;
use Phproberto\Joomla\Entity\Command\BaseCommand;
use Phproberto\Joomla\Entity\Command\Contracts\CommandInterface;

/**
 * Create the root asset.
 *
 * @since  __DEPLOY_VERSION__
 */
class CreateRootAsset extends BaseCommand implements CommandInterface
{
	/**
	 * Execute the command.
	 *
	 * @return  Folder
	 */
	public function execute()
	{
		$db = Factory::getDbo();

		$data = [
			'lft'   => 0,
			'rgt'   => 1,
			'level' => 0,
			'name'  => $db->q('root.1'),
			'title' => $db->q('Root Asset'),
			'rules' => $db->q(
				'{'
				. '"core.login.site":{"6":1,"2":1},"core.login.admin":{"6":1},"core.login.offline":{"6":1},'
				. '"core.admin":{"8":1},"core.manage":{"7":1},"core.create":{"6":1,"3":1},"core.delete":{"6":1},'
				. '"core.edit":{"6":1,"4":1},"core.edit.state":{"6":1,"5":1},"core.edit.own":{"6":1,"3":1}'
				. '}'
			)
		];

		$query = $db->getQuery(true)
			->insert($db->qn('#__assets'))
			->columns(array_keys($data))
			->values(implode(',', array_values($data)));

		$db->setQuery($query);

		if (!$db->execute())
		{
			throw new \RuntimeException("Error creating root asset: " . $db->stderr(true));
		}

		return Asset::find($db->insertid());
	}
}
