<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Validation\Rule;

use Phproberto\Joomla\Entity\Validation\Rule\NotEmptyString;

/**
 * NotEmptyString tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class NotEmptyStringTest extends \TestCase
{
	/**
	 * passes returns correct value.
	 *
	 * @return  void
	 */
	public function testPassesReturnsCorrectValue()
	{
		$rule = new NotEmptyString;

		$this->assertTrue($rule->passes('mytest'));
		$this->assertFalse($rule->passes(''));
		$this->assertTrue($rule->passes('  my string'));
		$this->assertTrue($rule->passes(0));
		$this->assertFalse($rule->passes(null));
		$this->assertFalse($rule->passes(' '));
	}
}
