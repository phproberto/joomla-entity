<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Validation\Rule;

use Phproberto\Joomla\Entity\Validation\Rule\IsNotEmptyString;

/**
 * IsNotEmptyString tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class IsNotEmptyStringTest extends \TestCase
{
	/**
	 * passes returns correct value.
	 *
	 * @return  void
	 */
	public function testPassesReturnsCorrectValue()
	{
		$rule = new IsNotEmptyString;

		$this->assertTrue($rule->passes('mytest'));
		$this->assertFalse($rule->passes(''));
		$this->assertTrue($rule->passes('  my string'));
		$this->assertTrue($rule->passes(0));
		$this->assertTrue($rule->passes(null));
		$this->assertTrue($rule->passes(' '));
	}
}
