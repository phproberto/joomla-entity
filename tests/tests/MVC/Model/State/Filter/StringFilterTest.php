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
use Phproberto\Joomla\Entity\MVC\Model\State\Filter\StringFilter;

/**
 * StringFilter tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class StringFilterTest extends TestCase
{
	/**
	 * Data provider for data.
	 *
	 * @return  array
	 */
	public function filterTestData() : array
	{
		return [
			['test', ['test']],
			[' test2', ['test2']],
			[2016, ['2016']],
			['2016', ['2016']],
			[[false, true], []],
			['', []],
			[[' '], []],
			[['', null], []]
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
		$filterer = new StringFilter;

		$this->assertSame($filterer->filter($value), $expected);
	}
}
