<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\MVC\Model\State\Filter;

defined('_JEXEC') || die;

use PHPUnit\Framework\TestCase;
use Phproberto\Joomla\Entity\MVC\Model\State\Filter\Boolean;

/**
 * BooleanFilter tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class BooleanTest extends TestCase
{
	/**
	 * Data provider for data.
	 *
	 * @return  array
	 */
	public function filterTestData() : array
	{
		return [
			[1, [true]],
			[0, [false]],
			['1', [true]],
			['0', [false]],
			['', []],
			[null, []],
			[[null, ''], []],
			[[null, true, false], [true, false]],
			[['', false], [false]],
			[[true, false, '* '], []],
			[true, [true]],
			[false, [false]],
			['true', [true]],
			['false', [false]]
		];
	}

	/**
	 * @test
	 *
	 * @dataProvider  filterTestData
	 *
	 * @param   mixed  $value     Value to test
	 * @param   mixed  $expected  Expected result
	 *
	 * @return void
	 */
	public function filterReturnsCorrectValue($value, $expected)
	{
		$filterer = new Boolean;

		$this->assertSame($filterer->filter($value), $expected);
	}
}
