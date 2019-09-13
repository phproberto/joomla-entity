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
use Phproberto\Joomla\Entity\MVC\Model\State\Filter\PositiveInteger;

/**
 * PositiveInteger tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class PositiveIntegerTest extends TestCase
{
	/**
	 * Data provider for data.
	 *
	 * @return  array
	 */
	public function filterTestData() : array
	{
		return [
			[1, [1]],
			[0, []],
			['1', [1]],
			['123, 0, 15', [123, 15]],
			['0', []],
			['-12,13,-14', [13]],
			[[-12,13,-14], [13]],
			['', []],
			[null, []],
			[[null, ''], []],
			[[null, '', '23', 0], [23]]
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
		$filterer = new PositiveInteger;

		$this->assertSame($filterer->filter($value), $expected);
	}
}
