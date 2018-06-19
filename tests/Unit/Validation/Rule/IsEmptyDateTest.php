<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Validation\Rule;

use Phproberto\Joomla\Entity\Validation\Rule\IsEmptyDate;

/**
 * IsEmptyDate tests.
 *
 * @since   1.1.0
 */
class IsEmptyDateTest extends \TestCase
{
	/**
	 * passes returns correct value.
	 *
	 * @return  void
	 */
	public function testPassesReturnsCorrectValue()
	{
		$rule = $this->getMockBuilder(IsEmptyDate::class)
			->setMethods(array('nullDate'))
			->getMock();

		$rule->method('nullDate')
			->will($this->onConsecutiveCalls('0000-00-00 00:00:00', '1976-11-16 16:00:00', '0000-00-00 00:00:00', '1976-11-16 16:00:00'));

		$this->assertTrue($rule->passes(''));
		$this->assertTrue($rule->passes(null));
		$this->assertTrue($rule->passes('0000-00-00 00:00:00'));
		$this->assertTrue($rule->passes('1976-11-16 16:00:00'));
		$this->assertFalse($rule->passes('1976-11-16 16:00:00'));
		$this->assertFalse($rule->passes('0000-00-00 00:00:00'));
	}
}
