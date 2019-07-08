<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Validation\Rule;

use Phproberto\Joomla\Entity\Validation\Rule\IsInteger;

/**
 * IsInteger tests.
 *
 * @since   1.1.0
 */
class IsIntegerTest extends \TestCase
{
	/**
	 * passes returns correct value.
	 *
	 * @return  void
	 */
	public function testPassesReturnsCorrectValue()
	{
		$rule = new IsInteger;

		$this->assertFalse($rule->passes('mytest'));
		$this->assertFalse($rule->passes(''));
		$this->assertFalse($rule->passes('  my string'));
		$this->assertTrue($rule->passes(0));
		$this->assertFalse($rule->passes(null));
		$this->assertTrue($rule->passes('0'));
		$this->assertTrue($rule->passes('1111'));
		$this->assertTrue($rule->passes('1111'));
		$this->assertFalse($rule->passes(' '));
	}
}
