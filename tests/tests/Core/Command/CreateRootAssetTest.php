<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Command;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Phproberto\Joomla\Entity\Core\Entity\Asset;
use Phproberto\Joomla\Entity\Core\Command\CreateRootAsset;

/**
 * CreateRootAsset tests.
 *
 * @since   __DEPOY_VERSION__
 */
class CreateRootAssetTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function categoryIsCreated()
	{
		$created = CreateRootAsset::instance()->execute();

		$this->assertInstanceOf(Asset::class, $created);

		$reloaded = Asset::load($created->id());

		$this->assertSame('0', $reloaded->get('level'));
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
		$this->restoreFactoryState();

		Asset::clearAll();

		parent::tearDown();
	}
}
