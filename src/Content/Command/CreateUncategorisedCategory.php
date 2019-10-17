<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Content\Command;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Content\Entity\Category;
use Phproberto\Joomla\Entity\Command\BaseCommand;
use Phproberto\Joomla\Entity\Command\Contracts\CommandInterface;

/**
 * Create the uncategorised category.
 *
 * @since  __DEPLOY_VERSION__
 */
class CreateUncategorisedCategory extends BaseCommand implements CommandInterface
{
	/**
	 * Execute the command.
	 *
	 * @return  Category
	 */
	public function execute()
	{
		return Category::create(
			[
				'title'     => 'Uncategorised',
				'alias'     => 'uncategorised',
				'parent_id' => Category::root()->id()
			]
		);
	}
}
