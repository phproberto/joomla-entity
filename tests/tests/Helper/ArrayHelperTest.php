<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Helper;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Helper\ArrayHelper;

/**
 * ArrayHelper tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class ArrayHelperTest extends \TestCase
{
	/**
	 * Data provider for invalidValuesAreRemoved() test.
	 *
	 * @return  array
	 */
	public function invalidValuesProvider()
	{
		return [
			[[null, '', '1', 1, ' ', 'aa'],[1]],
			[[2, '2', ' 2'],[2]],
			[[0, ' 3'],[3]]
		];
	}

	/**
	 * @test
	 *
	 * @dataProvider  invalidValuesProvider
	 *
	 * @param   array  $input     Input data for the function
	 * @param   array  $expected  Expected result returned by the function
	 *
	 * @return void
	 */
	public function toPositiveIntegersReturnsExpectedValues(array $input, array $expected)
	{
		$this->assertSame($expected, ArrayHelper::toPositiveIntegers($input));
	}
}
