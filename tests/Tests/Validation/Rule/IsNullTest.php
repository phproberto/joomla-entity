<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Validation\Rule;

use Phproberto\Joomla\Entity\Validation\Rule\IsNull;

/**
 * IsNull tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class IsNullTest extends \TestCase
{
	/**
	 * passes returns correct value.
	 *
	 * @return  void
	 */
	public function testPassesReturnsCorrectValue()
	{
		$rule = new IsNull;

		$this->assertFalse($rule->passes(''));
		$this->assertFalse($rule->passes('#aa'));
		$this->assertFalse($rule->passes('null'));
		$this->assertFalse($rule->passes(0));
		$this->assertFalse($rule->passes(0.1));
		$this->assertFalse($rule->passes(1.1));
		$this->assertFalse($rule->passes('1.1'));
		$this->assertFalse($rule->passes('12,000'));

		$this->assertTrue($rule->passes(null));
	}
}
