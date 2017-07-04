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

		$rowProperty->setValue($article, ['id' => 999]);

		$this->assertSame([], $article->getImages(true));

		$rowProperty->setValue($article, ['id' => 999, 'images' => '{"image_intro":"images\/joomla_black.png","float_intro":"left","image_intro_alt":"Alt text","image_intro_caption":"Caption text","image_fulltext":"images\/fulltext.png","float_fulltext":"right","image_fulltext_alt":"Alt fulltext","image_fulltext_caption":"Caption fulltext"}']);

		$this->assertSame([], $article->getImages());

		$expected = [
			'intro' => [
				'url'     => 'images/joomla_black.png',
				'float'   => 'left',
				'alt'     => 'Alt text',
				'caption' => 'Caption text'
			],
			'full' => [
				'url'     => 'images/fulltext.png',
				'float'   => 'right',
				'alt'     => 'Alt fulltext',
				'caption' => 'Caption fulltext'
			]
		];
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

		$rowProperty->setValue($article, ['id' => 999, 'metadata' => '{"foo":"bar"}']);

		$expected = [
			'foo' => 'bar'
		];

		$this->assertEquals($expected, $article->getMetadata());
	}

	/**
	 * getParams returns parameters.
	 *
	 * @return  void
	 */
	public function testGetParamsReturnsParameters()
	{
		$article = new Tag(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, ['id' => 999, 'params' => '{"foo":"var"}']);

		$this->assertEquals(new Registry(['foo' => 'var']), $article->getParams());
	}
}
