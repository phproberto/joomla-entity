<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Traits;

use Phproberto\Joomla\Entity\Tests\Traits\Stubs\EntityWithImages;

/**
 * HasImages trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasImagesTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * getImages returns intro image if exists.
	 *
	 * @return  void
	 */
	public function testGetImagesReturnsFullImageIfExists()
	{
		$_SERVER['HTTP_HOST'] = 'joomla-entity.test.com';
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		$entity = new EntityWithImages(999);

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'images' => '{"image_fulltext":"images\/joomla_black.png"}']);

		$images = $entity->getImages(true);

		$this->assertEquals("images/joomla_black.png", $images['full']['url']);
		$this->assertFalse(isset($images['full']['float']));
		$this->assertFalse(isset($images['full']['alt']));
		$this->assertFalse(isset($images['full']['caption']));

		$rowProperty->setValue($entity, ['id' => 999, 'images' => '{"image_fulltext":"images\/joomla_black.png","float_fulltext":"left"}']);

		$images = $entity->getImages(true);

		$this->assertEquals("images/joomla_black.png", $images['full']['url']);
		$this->assertEquals("left", $images['full']['float']);
		$this->assertFalse(isset($images['full']['alt']));
		$this->assertFalse(isset($images['full']['caption']));

		$rowProperty->setValue($entity, ['id' => 999, 'images' => '{"image_fulltext":"images\/joomla_black.png","float_fulltext":"left","image_fulltext_alt":"Alt text"}']);

		$images = $entity->getImages(true);

		$this->assertEquals("images/joomla_black.png", $images['full']['url']);
		$this->assertEquals("left", $images['full']['float']);
		$this->assertEquals("Alt text", $images['full']['alt']);
		$this->assertFalse(isset($images['full']['caption']));

		$rowProperty->setValue($entity, ['id' => 999, 'images' => '{"image_fulltext":"images\/joomla_black.png","float_fulltext":"left","image_fulltext_alt":"Alt text","image_fulltext_caption":"Caption text"}']);

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

		$entity = new EntityWithImages(999);

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'images' => '{"image_intro":"images\/joomla_black.png"}']);

		$images = $entity->getImages(true);

		$this->assertTrue(isset($entity->getImages()['intro']));
		$this->assertEquals("images/joomla_black.png", $images['intro']['url']);
		$this->assertFalse(isset($images['intro']['float']));
		$this->assertFalse(isset($images['intro']['alt']));
		$this->assertFalse(isset($images['intro']['caption']));

		$rowProperty->setValue($entity, ['id' => 999, 'images' => '{"image_intro":"images\/joomla_black.png","float_intro":"left"}']);

		$images = $entity->getImages(true);

		$this->assertTrue(isset($images['intro']));
		$this->assertEquals("images/joomla_black.png", $images['intro']['url']);
		$this->assertEquals("left", $images['intro']['float']);
		$this->assertFalse(isset($images['intro']['alt']));
		$this->assertFalse(isset($images['intro']['caption']));

		$rowProperty->setValue($entity, ['id' => 999, 'images' => '{"image_intro":"images\/joomla_black.png","float_intro":"left","image_intro_alt":"Alt text"}']);

		$images = $entity->getImages(true);

		$this->assertTrue(isset($images['intro']));
		$this->assertEquals("images/joomla_black.png", $images['intro']['url']);
		$this->assertEquals("left", $images['intro']['float']);
		$this->assertEquals("Alt text", $images['intro']['alt']);
		$this->assertFalse(isset($images['intro']['caption']));

		$rowProperty->setValue($entity, ['id' => 999, 'images' => '{"image_intro":"images\/joomla_black.png","float_intro":"left","image_intro_alt":"Alt text","image_intro_caption":"Caption text"}']);

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
		$article = new EntityWithImages(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, ['id' => 999, 'images' => '']);

		$this->assertEquals([], $article->getIntroImage());

		$article = EntityWithImages::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'images' => '{"image_intro":"images\/joomla_black.png"}']);

		$this->assertEquals(['url' => 'images/joomla_black.png'], $article->getIntroImage());

		$article = EntityWithImages::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999]);

		$this->assertEquals([], $article->getIntroImage());

		$article = EntityWithImages::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'images' => '{"image_intro":"","float_intro":"","image_intro_alt":"","image_intro_caption":"","image_fulltext":"","float_fulltext":"","image_fulltext_alt":"","image_fulltext_caption":""}']);

		$this->assertEquals([], $article->getIntroImage());
	}

	/**
	 * getImages works with custom column.
	 *
	 * @return  void
	 */
	public function testGetImagesWorksWithCustomColumn()
	{
		$mock = $this->getMockBuilder(EntityWithImages::class)
			->setMethods(array('getColumnImages'))
			->getMock();

		$mock->expects($this->once())
			->method('getColumnImages')
			->willReturn('img');

		$reflection = new \ReflectionClass($mock);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($mock, ['id' => 999, 'img' => '{"image_intro":"images\/joomla_black.png","float_intro":"left","image_intro_alt":"Alt text","image_intro_caption":"Caption text"}']);

		$expected = [
			'intro' => [
				'url'     => 'images/joomla_black.png',
				'float'   => 'left',
				'alt'     => 'Alt text',
				'caption' => 'Caption text'
			]
		];
		$this->assertEquals($expected, $mock->getImages());
	}
}
