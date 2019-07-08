<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Validation\Rule;

use Phproberto\Joomla\Entity\Validation\Rule\IsEmptyString;

/**
 * IsEmptyString tests.
 *
 * @since   1.1.0
 */
class IsEmptyStringTest extends \TestCase
{
	/**
	 * passes returns correct value.
	 *
	 * @return  void
	 */
	public function testPassesReturnsCorrectValue()
	{
		$rule = new IsEmptyString;

		$this->assertFalse($rule->passes('mytest'));
		$this->assertTrue($rule->passes(''));
		$this->assertFalse($rule->passes('  my string'));
		$this->assertFalse($rule->passes(0));
		$this->assertTrue($rule->passes(null));
		$this->assertTrue($rule->passes(' '));
	}
}
