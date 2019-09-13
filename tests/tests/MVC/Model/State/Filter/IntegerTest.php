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
use Phproberto\Joomla\Entity\MVC\Model\State\Filter\Integer;

/**
 * Integer tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class IntegerTest extends TestCase
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
			[0, [0]],
			['1', [1]],
			['0', [0]],
			['', []],
			[null, []],
			[[null, ''], []],
			[[null, '', 0], [0]]
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
		$filterer = new Integer;

		$this->assertSame($filterer->filter($value), $expected);
	}
}
