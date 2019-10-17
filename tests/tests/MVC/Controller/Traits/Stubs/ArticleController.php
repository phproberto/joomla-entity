<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\MVC\Controller\Traits\Stubs;

defined('_JEXEC') || die;

use Joomla\CMS\MVC\Controller\FormController;
use Phproberto\Joomla\Entity\Content\Entity\Article;
use Phproberto\Joomla\Entity\MVC\Controller\Traits\HasAssociatedEntity;

/**
 * Sample class to test HasAssociatedEntity trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class ArticleController extends FormController
{
	use HasAssociatedEntity;

	/**
	 * Retrieve the associated entity class.
	 *
	 * @return  string
	 */
	public function entityClass()
	{
		return Article::class;
	}
}
