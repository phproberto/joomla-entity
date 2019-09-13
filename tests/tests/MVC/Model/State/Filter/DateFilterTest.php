<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\MVC\Model\State\Filter;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\MVC\Model\State\Filter\DateFilter;

/**
 * DateFilter tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class DateFilterTest extends \TestCaseDatabase
{
	/**
	 * Data provider for data.
	 *
	 * @return  array
	 */
	public function filterTestData() : array
	{
		return [
			['2017-12-25', ['\'2017-12-25\'']],
			['2016,2017-12-25', ['\'2017-12-25\'']],
			['2016-03-12 - 2017-12-25', []],
			[[false, true], []],
			[['0000-11-16', '1976-00-16', '\'1976-11-00', '1976-11-16'], ['\'1976-11-16\'']],
			['', []],
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
		$filterer = new DateFilter;

		$this->assertSame($filterer->filter($value), $expected);
	}
}
