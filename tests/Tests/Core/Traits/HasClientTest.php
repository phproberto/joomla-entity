<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits;

use Phproberto\Joomla\Client\Administrator;
use Phproberto\Joomla\Client\Site;
use Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs\ClassWithClient;

/**
 * HasClient trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasClientTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Column storing the client identifier.
	 *
	 * @const
	 */
	const CLIENT_COLUMN = 'client_id';

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		ClassWithClient::clearAllInstances();

		parent::tearDown();
	}

	/**
	 * admin changes active client.
	 *
	 * @return  void
	 */
	public function testAdminChangesActiveClient()
	{
		$class = new ClassWithClient(999);

		$reflection = new \ReflectionClass($class);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($class, array('id' => 999, 'client_id' => '0'));

		$this->assertInstanceOf(Site::class, $class->client());

		$class->admin();

		$this->assertInstanceOf(Administrator::class, $class->client());
	}

	/**
	 * client returns correct data.
	 *
	 * @return  void
	 */
	public function testClientReturnsCorrectData()
	{
		$class = new ClassWithClient;

		$reflection = new \ReflectionClass($class);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($class, array('id' => 999, self::CLIENT_COLUMN => 0));

		$this->assertInstanceOf(Site::class, $class->client());

		$rowProperty->setValue($class, array('id' => 999, self::CLIENT_COLUMN => 1));

		// Without force it should return cached client
		$this->assertInstanceOf(Site::class, $class->client());
		$this->assertInstanceOf(Administrator::class, $class->client(true));
	}

	/**
	 * client throws an exception when client column is not found.
	 *
	 * @return  void
	 *
	 * @expectedException  \RuntimeException
	 */
	public function testLoadClientThrowsExceptionWhenClientColumnIsNotFound()
	{
		$class = new ClassWithClient;

		$reflection = new \ReflectionClass($class);
		$method = $reflection->getMethod('loadClient');
		$method->setAccessible(true);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($class, array('id' => 999));

		$method->invoke($class);
	}

	/**
	 * loadClient returns correct value.
	 *
	 * @return  void
	 */
	public function testLoadClientReturnsCorrectValue()
	{
		$class = new ClassWithClient(999);

		$reflection = new \ReflectionClass($class);
		$method = $reflection->getMethod('loadClient');
		$method->setAccessible(true);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($class, array('id' => 999, 'client_id' => 0));

		$this->assertInstanceOf(Site::class, $method->invoke($class));

		$rowProperty->setValue($class, array('id' => 999, 'client_id' => '0'));

		$this->assertInstanceOf(Site::class, $method->invoke($class));

		$rowProperty->setValue($class, array('id' => 999, 'client_id' => 'thiswillreturn0'));

		$this->assertInstanceOf(Site::class, $method->invoke($class));

		$rowProperty->setValue($class, array('id' => 999, 'client_id' => 1));

		$this->assertInstanceOf(Administrator::class, $method->invoke($class));

		$rowProperty->setValue($class, array('id' => 999, 'client_id' => '1'));

		$this->assertInstanceOf(Administrator::class, $method->invoke($class));
	}

	/**
	 * columnClient returns correct value.
	 *
	 * @return  void
	 */
	public function testColumnClientReturnsCorrectValue()
	{
		$class = new ClassWithClient;

		$reflection = new \ReflectionClass($class);
		$method = $reflection->getMethod('columnClient');
		$method->setAccessible(true);

		$this->assertEquals(self::CLIENT_COLUMN, $method->invoke($class));
	}

	/**
	 * site changes active client.
	 *
	 * @return  void
	 */
	public function testSiteChangesActiveClient()
	{
		$class = new ClassWithClient(999);

		$reflection = new \ReflectionClass($class);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($class, array('id' => 999, 'client_id' => '1'));

		$this->assertInstanceOf(Administrator::class, $class->client());

		$class->site();

		$this->assertInstanceOf(Site::class, $class->client());
	}
}
