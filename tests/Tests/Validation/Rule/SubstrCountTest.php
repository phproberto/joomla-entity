<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Validation\Rule;

use Phproberto\Joomla\Entity\Validation\Rule\SubstrCount;

/**
 * SubstrCount tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class SubstrCountTest extends \TestCase
{
	/**
	 * passes returns correct value.
	 *
	 * @return  void
	 */
	public function testPassesReturnsCorrectValue()
	{
		$rule = new SubstrCount('test');

		$this->assertTrue($rule->passes('mytest'));
		$this->assertFalse($rule->passes(''));
		$this->assertFalse($rule->passes('my string'));
		$this->assertFalse($rule->passes(0));
		$this->assertFalse($rule->passes(null));
		$this->assertTrue($rule->passes('testing substr_count'));
	}
}
