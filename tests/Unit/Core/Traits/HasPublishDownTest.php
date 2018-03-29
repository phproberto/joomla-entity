<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Core\Traits;

use Phproberto\Joomla\Entity\Tests\Unit\Core\Traits\Stubs\EntityWithPublishDown;

/**
 * HasPublishDown trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasPublishDownTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Column to use to load/store publish down date.
	 *
	 * @const
	 */
	const COLUMN_PUBLISH_DOWN = 'publish_down';

	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		EntityWithPublishDown::clearAll();

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
		$entity = $this->getMockBuilder(EntityWithPublishDown::class)
			->setMethods(array('columnAlias', 'nullDate'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn(static::COLUMN_PUBLISH_DOWN);

		$entity->method('nullDate')
			->willReturn('0000-00-00 00:00:00');

		$entity->bind($row);

		return $entity;
	}

	/**
	 * getPublishDown returns expected value.
	 *
	 * @return  void
	 */
	public function testGetPublishDownReturnsExpectedValue()
	{
		$entity = $this->getEntity(array(self::COLUMN_PUBLISH_DOWN => '2017-09-23 16:49:00'));

		$this->assertSame('2017-09-23 16:49:00', $entity->getPublishDown());
	}

	/**
	 * hasPublishDownReturnsExpectedValue.
	 *
	 * @return  void
	 */
	public function testHasPublishDownReturnsExpectedValue()
	{
		$entity = $this->getEntity(array(self::COLUMN_PUBLISH_DOWN => null));

		$this->assertFalse($entity->hasPublishDown());

		$entity = $this->getEntity(array(self::COLUMN_PUBLISH_DOWN => '2017-09-23 16:49:00'));

		$this->assertTrue($entity->hasPublishDown());

		$entity = $this->getEntity(array(self::COLUMN_PUBLISH_DOWN => '0000-00-00 00:00:00'));

		$this->assertFalse($entity->hasPublishDown());
	}

	/**
	 * isPublishedDown returns correct value.
	 *
	 * @return  void
	 */
	public function testIsPublishedDownReturnsCorrectValue()
	{
		$entity = $this->getEntity(array(self::COLUMN_PUBLISH_DOWN => null));

		$this->assertFalse($entity->isPublishedDown());

		// Remove 1h to current time to force past date
		$date = new \DateTime;
		$date->sub(new \DateInterval('PT1H'));

		$entity = $this->getEntity(array(self::COLUMN_PUBLISH_DOWN => $date->format('Y-m-d H:i:s')));

		$this->assertTrue($entity->isPublishedDown());

		$entity = $this->getEntity(array(self::COLUMN_PUBLISH_DOWN => '0000-00-00 00:00:00'));

		$this->assertFalse($entity->isPublishedDown());

		// Add 1h to current time to force future date
		$date = new \DateTime;
		$date->add(new \DateInterval('PT1H'));

		$entity = $this->getEntity(array(self::COLUMN_PUBLISH_DOWN => $date->format('Y-m-d H:i:s')));

		$this->assertFalse($entity->isPublishedDown());
	}
}
