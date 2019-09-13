<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\MVC\Model\QueryModifier;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\MVC\Model\QueryModifier\NotEmptyColumn;

/**
 * NotEmptyColumn tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class NotEmptyColumnTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function modifierIsApplied()
	{
		$db = Factory::getDbo();

		$query = $db->getQuery(true)
			->select('*')
			->from('#__test');

		$modifier = new NotEmptyColumn($query, 'a.test');
		$modifier->apply();

		$this->assertSame(
			1,
			substr_count(
				(string) $query,
				"WHERE `a`.`test` IS NOT NULL AND `a`.`test` NOT IN ('', '0', '0000-00-00', '0000-00-00 00:00:00')"
			)
		);
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function callbackIsCalled()
	{
		$db = Factory::getDbo();

		$query = $db->getQuery(true)
			->select('*')
			->from('#__test');

		$executed = false;

		$callback = function () use (&$executed) {
			$executed = true;
		};

		$this->assertFalse($executed);

		$this->modifier = new NotEmptyColumn($query, 'a.test', $callback);
		$this->modifier->apply();

		$this->assertTrue($executed);
	}
}
