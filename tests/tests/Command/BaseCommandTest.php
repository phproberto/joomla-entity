<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Command;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Decorator;
use Phproberto\Joomla\Entity\Tests\Command\Stubs\SampleCommand;

/**
 * Base command tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class BaseCommandTest extends \TestCase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function constructorSetsConfig()
	{
		$command = new SampleCommand(['my-setting' => 'value']);

		$reflection = new \ReflectionClass($command);
		$configProperty = $reflection->getProperty('config');
		$configProperty->setAccessible(true);

		$config = $configProperty->getValue($command);

		$this->assertInstanceOf(Registry::class, $config);
		$this->assertSame('value', $config->get('my-setting'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function instanceReturnsExpectedInstance()
	{
		$command = SampleCommand::instance([['test' => 'avalue']]);

		$this->assertInstanceOf(SampleCommand::class, $command);

		$reflection = new \ReflectionClass($command);
		$configProperty = $reflection->getProperty('config');
		$configProperty->setAccessible(true);

		$config = $configProperty->getValue($command);

		$this->assertInstanceOf(Registry::class, $config);
		$this->assertSame('avalue', $config->get('test'));
	}
}
