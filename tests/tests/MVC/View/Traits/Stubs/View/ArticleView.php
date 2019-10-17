<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\MVC\View\Traits\Stubs\View;

defined('_JEXEC') || die;

use Joomla\CMS\MVC\View\HtmlView;
use Phproberto\Joomla\Entity\Content\Entity\Article;
use Phproberto\Joomla\Entity\MVC\View\Traits\HasAssociatedEntity;

/**
 * Sample class to test HasAssociatedEntity trait.
 *
 * @since  __DEPLOY_VERSION__
 */
class ArticleView extends HtmlView
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
