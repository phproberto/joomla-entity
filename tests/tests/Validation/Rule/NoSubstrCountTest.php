<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Validation\Rule;

use Phproberto\Joomla\Entity\Validation\Rule\NoSubstrCount;

/**
 * NoSubstrCount tests.
 *
 * @since   1.1.0
 */
class NoSubstrCountTest extends \TestCase
{
	/**
	 * passes returns correct value.
	 *
	 * @return  void
	 */
	public function testPassesReturnsCorrectValue()
	{
		$rule = new NoSubstrCount('test');

		$this->assertFalse($rule->passes('mytest'));
		$this->assertTrue($rule->passes(''));
		$this->assertTrue($rule->passes('my string'));
		$this->assertTrue($rule->passes(0));
		$this->assertTrue($rule->passes(null));
		$this->assertFalse($rule->passes('testing substr_count'));
	}
}
