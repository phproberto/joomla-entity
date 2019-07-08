<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits;

use Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs\EntityWithPublishUp;

/**
 * HasPublishUp trait tests.
 *
 * @since   1.1.0
 */
class HasPublishUpTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Column to use to load/store publish up date.
	 *
	 * @const
	 */
	const COLUMN_PUBLISH_UP = 'publish_up';

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		EntityWithPublishUp::clearAll();

		parent::tearDown();
	}

	/**
	 * Get a mocked entity.
	 *
	 * @param   array  $row  Row returned by the entity as data
	 *
	 * @return  \PHPUnit_Framework_MockObject_MockObject
	 */
	private function getEntity($row = array())
	{
		$entity = $this->getMockBuilder(EntityWithPublishUp::class)
			->setMethods(array('columnAlias', 'nullDate'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn(static::COLUMN_PUBLISH_UP);

		$entity->method('nullDate')
			->willReturn('0000-00-00 00:00:00');

		$entity->bind($row);

		return $entity;
	}

	/**
	 * getPublishUp returns expected value.
	 *
	 * @return  void
	 */
	public function testGetPublishUpReturnsExpectedValue()
	{
		$entity = $this->getEntity(array(self::COLUMN_PUBLISH_UP => '2017-09-23 16:49:00'));

		$this->assertSame('2017-09-23 16:49:00', $entity->getPublishUp());
	}

	/**
	 * hasPublishUpReturnsExpectedValue.
	 *
	 * @return  void
	 */
	public function testHasPublishUpReturnsExpectedValue()
	{
		$entity = $this->getEntity(array(self::COLUMN_PUBLISH_UP => null));

		$this->assertFalse($entity->HasPublishUp());

		$entity = $this->getEntity(array(self::COLUMN_PUBLISH_UP => '2017-09-23 16:49:00'));

		$this->assertTrue($entity->HasPublishUp());

		$entity = $this->getEntity(array(self::COLUMN_PUBLISH_UP => '0000-00-00 00:00:00'));

		$this->assertFalse($entity->HasPublishUp());
	}

	/**
	 * isPublishedUp returns correct value.
	 *
	 * @return  void
	 */
	public function testIsPublishedUpReturnsCorrectValue()
	{
		$entity = $this->getEntity(array(self::COLUMN_PUBLISH_UP => null));

		$this->assertTrue($entity->isPublishedUp());

		// Remove 1h to current time to force past date
		$date = new \DateTime;
		$date->sub(new \DateInterval('PT1H'));

		$entity = $this->getEntity(array(self::COLUMN_PUBLISH_UP => $date->format('Y-m-d H:i:s')));

		$this->assertTrue($entity->isPublishedUp());

		$entity = $this->getEntity(array(self::COLUMN_PUBLISH_UP => '0000-00-00 00:00:00'));

		$this->assertTrue($entity->isPublishedUp());

		// Add 1h to current time to force future date
		$date = new \DateTime;
		$date->add(new \DateInterval('PT1H'));

		$entity = $this->getEntity(array(self::COLUMN_PUBLISH_UP => $date->format('Y-m-d H:i:s')));

		$this->assertFalse($entity->isPublishedUp());
	}
}
