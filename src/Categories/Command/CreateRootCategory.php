<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Categories\Command;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Categories\Category;
use Phproberto\Joomla\Entity\Command\BaseCommand;
use Phproberto\Joomla\Entity\Command\Contracts\CommandInterface;

/**
 * Create the root category.
 *
 * @since  __DEPLOY_VERSION__
 */
class CreateRootCategory extends BaseCommand implements CommandInterface
{
	/**
	 * Execute the command.
	 *
	 * @return  Category
	 */
	public function execute()
	{
		$db = Factory::getDbo();

		$data = [
			'lft'       => 0,
			'rgt'       => 1,
			'level'     => 0,
			'extension' => $db->q('system'),
			'title'     => $db->q('ROOT'),
			'alias'     => $db->q('root'),
			'published' => 1,
			'access'    => 1
		];

		$query = $db->getQuery(true)
			->insert($db->qn('#__categories'))
			->columns(array_keys($data))
			->values(implode(',', array_values($data)));

		$db->setQuery($query);

		if (!$db->execute())
		{
			throw new \RuntimeException("Error creating root category: " . $db->stderr(true));
		}

		return Category::find($db->insertid());
	}
}
