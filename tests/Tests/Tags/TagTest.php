<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Tags;

use Phproberto\Joomla\Entity\Tags\Tag;
use Joomla\Registry\Registry;

/**
 * Tag entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class TagTest extends \TestCaseDatabase
{
	/**
	 * getImages returns correct value.
	 *
	 * @return  void
	 */
	public function testGetImagesReturnsCorrectValue()
	{
		$_SERVER['HTTP_HOST'] = 'joomla-entity.test.com';
		$_SERVER['SCRIPT_NAME'] = '/index.php';

		$article = new Tag(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, array('id' => 999, 'images' => ''));

		$this->assertSame(array(), $article->getImages(true));

		$rowProperty->setValue($article, array('id' => 999, 'images' => '{"image_intro":"images\/joomla_black.png","float_intro":"left","image_intro_alt":"Alt text","image_intro_caption":"Caption text","image_fulltext":"images\/fulltext.png","float_fulltext":"right","image_fulltext_alt":"Alt fulltext","image_fulltext_caption":"Caption fulltext"}'));

		$this->assertSame(array(), $article->getImages());

		$expected = array(
			'intro' => array(
				'url'     => 'images/joomla_black.png',
				'float'   => 'left',
				'alt'     => 'Alt text',
				'caption' => 'Caption text'
			),
			'full' => array(
				'url'     => 'images/fulltext.png',
				'float'   => 'right',
				'alt'     => 'Alt fulltext',
				'caption' => 'Caption fulltext'
			)
		);
		$images = $article->getImages(true);

		$this->assertEquals($expected, $article->getImages(true));
	}

	/**
	 * getMetadata returns data.
	 *
	 * @return  void
	 */
	public function testGetMetadataReturnsData()
	{
		$article = new Tag(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, array('id' => 999, 'metadata' => '{"foo":"bar"}'));

		$expected = array(
			'foo' => 'bar'
		);

		$this->assertEquals($expected, $article->metadata());
	}

	/**
	 * params returns parameters.
	 *
	 * @return  void
	 */
	public function testParamsReturnsParameters()
	{
		$article = new Tag(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, array('id' => 999, 'params' => '{"foo":"var"}'));

		$this->assertEquals(new Registry(array('foo' => 'var')), $article->params());
	}

	/**
	 * getState returns correct value.
	 *
	 * @return  void
	 */
	public function testGetStateReturnsCorrectValue()
	{
		$tag = new Tag(999);

		$reflection = new \ReflectionClass($tag);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($tag, array('id' => 999, 'published' => '0'));

		$this->assertEquals(0, $tag->state());

		$rowProperty->setValue($tag, array('id' => 999, 'published' => '1'));

		$this->assertEquals(1, $tag->state());
	}
}
