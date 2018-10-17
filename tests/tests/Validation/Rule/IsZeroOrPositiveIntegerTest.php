<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Validation\Rule;

use Phproberto\Joomla\Entity\Validation\Rule\IsZeroOrPositiveInteger;

/**
 * IsZeroOrPositiveInteger tests.
 *
 * @since   1.7.0
 */
class IsZeroOrPositiveIntegerTest extends \TestCase
{
	/**
	 * Data provider for tests.
	 *
	 * @return  array
	 */
	public function dataProvider()
	{
		return [
			['', false],
			['0', true],
			['aa', false],
			[' 0', true],
			[null, false],
			[0, true],
			[0.5, false],
			[1.0, true],
			[1.1, false],
			['1.1', false],
			['1.0', true],
			['1,0', false]
		];
	}

	/**
	 * @test
	 *
	 * @dataProvider  dataProvider
	 *
	 * @param   mixed    $value           Value to test
	 * @param   boolean  $expectedResult  Expected reponse from validator
	 *
	 * @return  void
	 */
	public function passesReturnsCorrectValue($value, $expectedResult)
	{
		$rule = new IsZeroOrPositiveInteger;

		$this->assertSame($expectedResult, $rule->passes($value));
	}
}
