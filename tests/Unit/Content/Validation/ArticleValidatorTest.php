<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Content\Validation;

use Phproberto\Joomla\Entity\Content\Article;
use Phproberto\Joomla\Entity\Tests\Unit\Stubs\Entity;
use Phproberto\Joomla\Entity\Content\Validation\ArticleValidator;

/**
 * ArticleValidator tests.
 *
 * @since   1.1.0
 */
class ArticleValidatorTest extends \TestCase
{
	/**
	 * constructor adds rules.
	 *
	 * @return  void
	 */
	public function testConstructorAddsRules()
	{
		$entity = new Article;

		$validator = new ArticleValidator($entity);

		$reflection = new \ReflectionClass($validator);

		$rulesProperty = $reflection->getProperty('rules');
		$rulesProperty->setAccessible(true);

		$this->assertTrue(count($rulesProperty->getValue($validator)) > 0);
	}
}
