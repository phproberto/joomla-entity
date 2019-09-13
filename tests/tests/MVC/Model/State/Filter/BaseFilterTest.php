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
use Phproberto\Joomla\Entity\MVC\Model\State\Filter\BaseFilter;

/**
 * BaseFilter tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class BaseFilterTest extends TestCase
{
	/**
	 * Data provider for data.
	 *
	 * @return  array
	 */
	public function filterTestData() : array
	{
		return [
			['*', []],
			['test,two', ['test', 'two']],
			['test, two',['test', 'two']],
			[', ',[]],
			[['', ', '],[',']],
			[['', '* ', 12],[]],
			[12,[12]]
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
		$filterer = $this->getMockBuilder(BaseFilter::class)
			->getMockForAbstractClass();

		$this->assertSame($filterer->filter($value), $expected);
	}
}
