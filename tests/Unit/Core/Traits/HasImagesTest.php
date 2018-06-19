<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Core\Traits;

use Phproberto\Joomla\Entity\Tests\Unit\Core\Traits\Stubs\EntityWithImages;

/**
 * HasImages trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasImagesTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * getFullTextImage returns correct value
	 *
	 * @return  void
	 */
	public function testGetFullTextImageReturnsCorrectValue()
	{
		$entity = $this->getEntity(array('id' => 999, 'images' => ''));

		$this->assertEquals(array(), $entity->getFullTextImage());

		$entity = $this->getEntity(array('id' => 999, 'images' => '{"image_fulltext":"images\/joomla_black.png"}'));

		$this->assertEquals(array('url' => 'images/joomla_black.png'), $entity->getFullTextImage());

		$entity = $this->getEntity(array('id' => 999, 'images' => '{"image_intro":"","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}'));

		$this->assertEquals(array(), $entity->getFullTextImage());
	}

	/**
	 * getImages returns intro image if exists.
	 *
	 * @return  void
	 */
	public function testGetImagesReturnsFullImageIfExists()
	{
		$_SERVER['HTTP_HOST'] = 'joomla-entity.test.com';
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		$entity = $this->getEntity(array('id' => 999, 'images' => '{"image_fulltext":"images\/joomla_black.png"}'));

		$images = $entity->getImages(true);

		$this->assertEquals("images/joomla_black.png", $images['full']['url']);
		$this->assertFalse(isset($images['full']['float']));
		$this->assertFalse(isset($images['full']['alt']));
		$this->assertFalse(isset($images['full']['caption']));

		$entity = $this->getEntity(array('id' => 999, 'images' => '{"image_fulltext":"images\/joomla_black.png","float_fulltext":"left"}'));

		$images = $entity->getImages(true);

		$this->assertEquals("images/joomla_black.png", $images['full']['url']);
		$this->assertEquals("left", $images['full']['float']);
		$this->assertFalse(isset($images['full']['alt']));
		$this->assertFalse(isset($images['full']['caption']));

		$entity = $this->getEntity(array('id' => 999, 'images' => '{"image_fulltext":"images\/joomla_black.png","float_fulltext":"left","image_fulltext_alt":"Alt text"}'));

		$images = $entity->getImages(true);

		$this->assertEquals("images/joomla_black.png", $images['full']['url']);
		$this->assertEquals("left", $images['full']['float']);
		$this->assertEquals("Alt text", $images['full']['alt']);
		$this->assertFalse(isset($images['full']['caption']));

		$entity = $this->getEntity(array('id' => 999, 'images' => '{"image_fulltext":"images\/joomla_black.png","float_fulltext":"left","image_fulltext_alt":"Alt text","image_fulltext_caption":"Caption text"}'));

		$images = $entity->getImages(true);

		$this->assertEquals("images/joomla_black.png", $images['full']['url']);
		$this->assertEquals("left", $images['full']['float']);
		$this->assertEquals("Alt text", $images['full']['alt']);
		$this->assertEquals("Caption text", $images['full']['caption']);
	}

	/**
	 * getImages returns intro image if exists.
	 *
	 * @return  void
	 */
	public function testGetImagesReturnsIntroImageIfExists()
	{
		$_SERVER['HTTP_HOST'] = 'joomla-entity.test.com';
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		$entity = $this->getEntity(array('id' => 999, 'images' => '{"image_intro":"images\/joomla_black.png"}'));

		$images = $entity->getImages(true);

		$this->assertTrue(isset($entity->getImages()['intro']));
		$this->assertEquals("images/joomla_black.png", $images['intro']['url']);
		$this->assertFalse(isset($images['intro']['float']));
		$this->assertFalse(isset($images['intro']['alt']));
		$this->assertFalse(isset($images['intro']['caption']));

		$entity = $this->getEntity(array('id' => 999, 'images' => '{"image_intro":"images\/joomla_black.png","float_intro":"left"}'));

		$images = $entity->getImages(true);

		$this->assertTrue(isset($images['intro']));
		$this->assertEquals("images/joomla_black.png", $images['intro']['url']);
		$this->assertEquals("left", $images['intro']['float']);
		$this->assertFalse(isset($images['intro']['alt']));
		$this->assertFalse(isset($images['intro']['caption']));

		$entity = $this->getEntity(array('id' => 999, 'images' => '{"image_intro":"images\/joomla_black.png","float_intro":"left","image_intro_alt":"Alt text"}'));

		$images = $entity->getImages(true);

		$this->assertTrue(isset($images['intro']));
		$this->assertEquals("images/joomla_black.png", $images['intro']['url']);
		$this->assertEquals("left", $images['intro']['float']);
		$this->assertEquals("Alt text", $images['intro']['alt']);
		$this->assertFalse(isset($images['intro']['caption']));

		$entity = $this->getEntity(array('id' => 999, 'images' => '{"image_intro":"images\/joomla_black.png","float_intro":"left","image_intro_alt":"Alt text","image_intro_caption":"Caption text"}'));

		$images = $entity->getImages(true);

		$this->assertTrue(isset($images['intro']));
		$this->assertEquals("images/joomla_black.png", $images['intro']['url']);
		$this->assertEquals("left", $images['intro']['float']);
		$this->assertEquals("Alt text", $images['intro']['alt']);
		$this->assertEquals("Caption text", $images['intro']['caption']);
	}

	/**
	 * getIntroImage returns correct value.
	 *
	 * @return  void
	 */
	public function testGetIntroImageReturnsCorrectValue()
	{
		$entity = $this->getEntity(array('id' => 999, 'images' => ''));

		$this->assertEquals(array(), $entity->getIntroImage());

		$entity = $this->getEntity(array('id' => 999, 'images' => '{"image_intro":"images\/joomla_black.png"}'));

		$this->assertEquals(array('url' => 'images/joomla_black.png'), $entity->getIntroImage());

		$entity = $this->getEntity(array('id' => 999, 'images' => '{"image_intro":"","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}'));

		$this->assertEquals(array(), $entity->getIntroImage());
	}

	/**
	 * getImages works with custom column.
	 *
	 * @return  void
	 */
	public function testGetImagesWorksWithCustomColumn()
	{
		$mock = $this->getMockBuilder(EntityWithImages::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$mock->expects($this->once())
			->method('columnAlias')
			->willReturn('img');

		$reflection = new \ReflectionClass($mock);
		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($mock, 999);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($mock, array('id' => 999, 'img' => '{"image_intro":"images\/joomla_black.png","float_intro":"left","image_intro_alt":"Alt text","image_intro_caption":"Caption text"}'));

		$expected = array(
			'intro' => array(
				'url'     => 'images/joomla_black.png',
				'float'   => 'left',
				'alt'     => 'Alt text',
				'caption' => 'Caption text'
			)
		);

		$this->assertEquals($expected, $mock->getImages());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function loadImagesReturnsEmptyArrayForNotLoadedEntity()
	{
		$entity = new EntityWithImages;

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('loadImages');
		$method->setAccessible(true);

		$this->assertSame([], $method->invoke($entity));
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
		$entity = $this->getMockBuilder(EntityWithImages::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn('images');

		$entity->bind($row);

		return $entity;
	}
}
