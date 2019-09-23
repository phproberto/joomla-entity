<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Users\Command;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Users\ViewLevel;
use Phproberto\Joomla\Entity\Users\GuestViewLevel;
use Phproberto\Joomla\Entity\Users\PublicViewLevel;
use Phproberto\Joomla\Entity\Users\SpecialViewLevel;
use Phproberto\Joomla\Entity\Users\RegisteredViewLevel;
use Phproberto\Joomla\Entity\Users\SuperUsersViewLevel;
use Phproberto\Joomla\Entity\Command\Database\EmptyTable;
use Phproberto\Joomla\Entity\Users\Command\CreatePredefinedViewLevels;

/**
 * CreatePredefinedViewLevels tests.
 *
 * @since   __DEPOY_VERSION__
 */
class CreatePredefinedViewLevelsTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function viewLevelsAreCreated()
	{
		$viewLevels = CreatePredefinedViewLevels::instance()->execute();

		$this->assertTrue(count($viewLevels) > 0);

		foreach ($viewLevels as $key => $viewLevel)
		{
			$this->assertInstanceOf(ViewLevel::class, $viewLevel);
		}
	}

	/**
	 * Sets up the fixture, for example, opens a network connection.
	 * This method is called before a test is executed.
	 *
	 * @return  void
	 */
	protected function setUp()
	{
		parent::setUp();

		$this->saveFactoryState();

		Factory::$config      = $this->getMockConfig();
		Factory::$session     = $this->getMockSession();
		Factory::$application = $this->getMockCmsApp();
	}

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		EmptyTable::instance(['#__viewLevels'])->execute();

		$this->restoreFactoryState();

		PublicViewLevel::clearAll();
		RegisteredViewLevel::clearAll();
		SpecialViewLevel::clearAll();
		GuestViewLevel::clearAll();
		SuperUsersViewLevel::clearAll();

		parent::tearDown();
	}
}
