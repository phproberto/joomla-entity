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
use PHPUnit\Framework\TestCase;
use Phproberto\Joomla\Entity\MVC\Model\QueryModifier\NullInColumn;

/**
 * NullInColumn tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class NullInColumnTest extends TestCase
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

		$modifier = new NullInColumn($query, 'a.test');
		$modifier->apply();

		$this->assertSame(1, substr_count((string) $query, "WHERE `a`.`test` IS NULL"));
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

		$this->modifier = new NullInColumn($query, 'a.test', $callback);
		$this->modifier->apply();

		$this->assertTrue($executed);
	}
}
