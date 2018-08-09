<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Core\Traits;

use Phproberto\Joomla\Entity\Tests\Unit\Core\Traits\Stubs\EntityWithUrls;

/**
 * HasUrls trait tests.
 *
 * @since   1.1.0
 */
class HasUrlsTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		EntityWithUrls::clearAll();

		parent::tearDown();
	}

	/**
	 * getUrls gets correct data.
	 *
	 * @return  void
	 */
	public function testGetUrlsGetsCorrectData()
	{
		$entity = new EntityWithUrls(999);

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, array('id' => 999, 'urls' => ''));

		$this->assertEquals(array(), $entity->getUrls(true));

		$rowProperty->setValue($entity, array('id' => 999, 'urls' => '{}'));

		$this->assertEquals(array(), $entity->getUrls(true));

		$rowProperty->setValue($entity, array('id' => 999, 'urls' => '{"urla":"","urlatext":"","targeta":"","urlb":"","urlbtext":"","targetb":"","urlc":"","urlctext":"","targetc":""}'));

		$this->assertEquals(array(), $entity->getUrls(true));

		$rowProperty->setValue($entity, array('id' => 999, 'urls' => '{"urla":"http://google.com","urlatext":"Google","targeta":"0"}'));

		// With no reload returns old data
		$this->assertEquals(array(), $entity->getUrls());

		$expected = array(
			'a' => array(
				'url'    => 'http://google.com',
				'text'   => 'Google',
				'target' => '0'
			)
		);

		$this->assertEquals($expected, $entity->getUrls(true));

		$rowProperty->setValue($entity, array('id' => 999, 'urls' => '{"urla":"http:\/\/google.es","urlatext":"Google","targeta":"1","urlb":"http:\/\/yahoo.com","urlbtext":"Yahoo","targetb":"0","urlc":"http://www.phproberto.com","urlctext":"Phproberto","targetc":""}'));

		$expected = array(
			'a' => array(
				'url'    => 'http://google.es',
				'text'   => 'Google',
				'target' => '1'
			),
			'b' => array(
				'url'    => 'http://yahoo.com',
				'text'   => 'Yahoo',
				'target' => '0'
			),
			'c' => array(
				'url'    => 'http://www.phproberto.com',
				'text'   => 'Phproberto'
			)
		);

		$this->assertEquals($expected, $entity->getUrls(true));
	}

	/**
	 * getUrls works with custom column.
	 *
	 * @return  void
	 */
	public function testGetUrlsWorksWithCustomColumn()
	{
		$entity = $this->getMockBuilder(EntityWithUrls::class)
			->setMethods(array('getColumnUrls'))
			->getMock();

		$entity->method('getColumnUrls')
			->willReturn('links');

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, array('id' => 999, 'links' => ''));

		$this->assertEquals(array(), $entity->getUrls());

		$rowProperty->setValue($entity, array('id' => 999, 'links' => '{"urla":"http:\/\/google.es","urlatext":"Google","targeta":"1","urlb":"http:\/\/yahoo.com","urlbtext":"Yahoo","targetb":"0","urlc":"http://www.phproberto.com","urlctext":"Phproberto","targetc":""}'));

		$this->assertEquals(array(), $entity->getUrls());

		$expected = array(
			'a' => array(
				'url'    => 'http://google.es',
				'text'   => 'Google',
				'target' => '1'
			),
			'b' => array(
				'url'    => 'http://yahoo.com',
				'text'   => 'Yahoo',
				'target' => '0'
			),
			'c' => array(
				'url'    => 'http://www.phproberto.com',
				'text'   => 'Phproberto'
			)
		);

		$this->assertEquals($expected, $entity->getUrls(true));
	}
}
