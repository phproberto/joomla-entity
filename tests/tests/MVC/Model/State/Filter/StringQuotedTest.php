<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\MVC\Model\State\Filter;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\MVC\Model\State\Filter\StringQuoted;

/**
 * StringQuoted tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class StringQuotedTest extends \TestCaseDatabase
{
	/**
	 * Data provider for data.
	 *
	 * @return  array
	 */
	public function filterTestData() : array
	{
		return [
			[1, ['\'1\'']],
			[[null, 'sample', '', ' another string'], ['\'sample\'', '\'another string\'']],
			[[0], ['\'0\'']],
			[[false, true], []],
			[['', ' ', null], []],
			[[], []],
			[['false', '', 'true'], ['\'false\'', '\'true\'']]
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
		$filterer = new StringQuoted;

		$this->assertSame($filterer->filter($value), $expected);
	}
}
