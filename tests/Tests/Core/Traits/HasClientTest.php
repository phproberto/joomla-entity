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
		$entity = $this->getEntity(array('id' => 999, 'client_id' => '0'));

		$this->assertInstanceOf(Site::class, $entity->client());

		$entity->admin();

		$this->assertInstanceOf(Administrator::class, $entity->client());
	}

	/**
	 * client returns correct data.
	 *
	 * @return  void
	 */
	public function testClientReturnsCorrectData()
	{
		$entity = $this->getEntity(array('id' => 999, static::CLIENT_COLUMN => 0));

		$this->assertInstanceOf(Site::class, $entity->client());

		$entity = $this->getEntity(array('id' => 999, static::CLIENT_COLUMN => 1));

		$this->assertInstanceOf(Administrator::class, $entity->client(true));
	}

	/**
	 * client throws an exception when client column is not found.
	 *
	 * @return  void
	 *
	 * @expectedException  \InvalidArgumentException
	 */
	public function testLoadClientThrowsExceptionWhenClientColumnIsNotFound()
	{
		$entity = $this->getEntity(array('id' => 999));

		$reflection = new \ReflectionClass($entity);

		$method = $reflection->getMethod('loadClient');
		$method->setAccessible(true);

		$method->invoke($entity);
	}

	/**
	 * loadClient returns correct value.
	 *
	 * @return  void
	 */
	public function testLoadClientReturnsCorrectValue()
	{
		$entity = $this->getEntity(array('id' => 999, 'client_id' => 0));

		$reflection = new \ReflectionClass($entity);

		$method = $reflection->getMethod('loadClient');
		$method->setAccessible(true);

		$this->assertInstanceOf(Site::class, $method->invoke($entity));

		$entity = $this->getEntity(array('id' => 999, 'client_id' => '0'));

		$this->assertInstanceOf(Site::class, $method->invoke($entity));

		$entity = $this->getEntity(array('id' => 999, 'client_id' => 'thiswillreturn0'));

		$this->assertInstanceOf(Site::class, $method->invoke($entity));

		$entity = $this->getEntity(array('id' => 999, 'client_id' => 1));

		$this->assertInstanceOf(Administrator::class, $method->invoke($entity));

		$entity = $this->getEntity(array('id' => 999, 'client_id' => '1'));

		$this->assertInstanceOf(Administrator::class, $method->invoke($entity));
	}

	/**
	 * site changes active client.
	 *
	 * @return  void
	 */
	public function testSiteChangesActiveClient()
	{
		$entity = $this->getEntity(array('id' => 999, 'client_id' => '1'));

		$this->assertInstanceOf(Administrator::class, $entity->client());

		$entity->site();

		$this->assertInstanceOf(Site::class, $entity->client());
	}

	/**
	 * Get a mocked entity with client.
	 *
	 * @param   array  $row  Row returned by the entity as data
	 *
	 * @return  \PHPUnit_Framework_MockObject_MockObject
	 */
	private function getEntity($row = array())
	{
		$entity = $this->getMockBuilder(ClassWithClient::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn('client_id');

		$entity->bind($row);

		return $entity;
	}
}
