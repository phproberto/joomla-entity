<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Validation;

use Phproberto\Joomla\Entity\Validation\Validator;
use Phproberto\Joomla\Entity\Validation\Rule;
use Phproberto\Joomla\Entity\Tests\Unit\Stubs\Entity;

/**
 * Base rule tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class RuleTest extends \TestCase
{
	/**
	 * fails returns true when passes returns false.
	 *
	 * @return  void
	 */
	public function testFailsReturnsTruenWhenPassesReturnsFalse()
	{
		$rule = $this->getMockBuilder(Rule::class)
			->setMethods(array('passes'))
			->getMockForAbstractClass();

		$rule->expects($this->exactly(2))
			->method('passes')
			->with('value')
			->will($this->onConsecutiveCalls(false, true));

		$this->assertTrue($rule->fails('value'));
		$this->assertFalse($rule->fails('value'));
	}
}
