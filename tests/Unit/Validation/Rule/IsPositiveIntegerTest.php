<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Validation\Rule;

use Phproberto\Joomla\Entity\Validation\Rule\IsPositiveInteger;

/**
 * IsPositiveInteger tests.
 *
 * @since   1.1.0
 */
class IsPositiveIntegerTest extends \TestCase
{
	/**
	 * passes returns correct value.
	 *
	 * @return  void
	 */
	public function testPassesReturnsCorrectValue()
	{
		$rule = new IsPositiveInteger;

		$this->assertFalse($rule->passes(''));
		$this->assertFalse($rule->passes('#aa'));
		$this->assertFalse($rule->passes(0));
		$this->assertFalse($rule->passes(-1));
		$this->assertFalse($rule->passes(0.1));
		$this->assertFalse($rule->passes(1.1));
		$this->assertFalse($rule->passes('1.1'));
		$this->assertFalse($rule->passes('12,000'));

		$this->assertTrue($rule->passes('12'));
		$this->assertTrue($rule->passes(1));
		$this->assertTrue($rule->passes(' 12'));
		$this->assertTrue($rule->passes(12));
		$this->assertTrue($rule->passes('12.000'));
		$this->assertTrue($rule->passes(12.000));
	}
}
