<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\MVC\Model\State\Filter;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\MVC\Model\State\Filter\DateTimeFilter;

/**
 * DateTimeFilter tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class DateTimeFilterTest extends \TestCaseDatabase
{
	/**
	 * Data provider for data.
	 *
	 * @return  array
	 */
	public function filterTestData() : array
	{
		return [
			// Missing time
			['2017-12-25', []],
			// Valid
			[
				['2017-12-25 23:45:55', '2017-12-25 23:00:55', '2017-12-25 00:00:00'],
				['\'2017-12-25 23:45:55\'', '\'2017-12-25 23:00:55\'', '\'2017-12-25 00:00:00\'']
			],
			// Invalid hours
			[['2019-12-25 24:45:55', '2019-12-25 23:60:55', '2019-12-25 23:05:61'], []],
			// Invalid dates
			[['0000-12-25 23:45:55', '2010-00-01 22:15:15', '2010-01-00 11:30:10'], []],
			// More invalid dates
			[['2010-13-01 23:45:55', '2010-10-32 23:45:55'], []],
			// Empty values
			[[null, ''], []],
			[[false, true], []]
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
		$filterer = new DateTimeFilter;

		$this->assertSame($filterer->filter($value), $expected);
	}
}
