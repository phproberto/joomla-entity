<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\MVC\Model\State\Filter;

defined('_JEXEC') || die;

use Phproberto\Joomla\Entity\MVC\Model\State\Filter\Escaped;

/**
 * Escaped filter tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class EscapedTest extends \TestCaseDatabase
{
	/**
	 * Data provider for data.
	 *
	 * @return  array
	 */
	public function filterTestData() : array
	{
		return [
			["'s Hertogenbosch", ["''s Hertogenbosch"]],
			['\sample string', ['\\sample string']],
			[[0], ['0']],
			[[false, true], []],
			[['', ' ', null], []],
			[[], []],
			[['false', '', 'true'], ['false', 'true']]
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
		$filterer = new Escaped;

		$this->assertSame($filterer->filter($value), $expected);
	}
}
