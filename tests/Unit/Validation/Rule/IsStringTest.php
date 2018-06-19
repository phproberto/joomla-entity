<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Validation\Rule;

use Phproberto\Joomla\Entity\Validation\Rule\IsString;

/**
 * IsString tests.
 *
 * @since   1.1.0
 */
class IsStringTest extends \TestCase
{
	/**
	 * passes returns correct value.
	 *
	 * @return  void
	 */
	public function testPassesReturnsCorrectValue()
	{
		$rule = new IsString;

		$this->assertTrue($rule->passes('mytest'));
		$this->assertTrue($rule->passes(''));
		$this->assertTrue($rule->passes('  my string'));
		$this->assertFalse($rule->passes(0));
		$this->assertFalse($rule->passes(0.11));
		$this->assertFalse($rule->passes(null));
		$this->assertTrue($rule->passes(' '));
	}
}
