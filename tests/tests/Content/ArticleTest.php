<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Content;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\Content\Article as DeprecatedArticle;
use Phproberto\Joomla\Entity\Content\Entity\Article;

/**
 * Article entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class ArticleTest extends \TestCase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function deprecatedClassExtendsNewClass()
	{
		$entity = new DeprecatedArticle;

		$this->assertInstanceOf(Article::class, $entity);
	}
}
