<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Traits;

use Phproberto\Joomla\Entity\Tests\Traits\Stubs\EntityWithMetadata;

/**
 * HasMetadata trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasMetadataTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * getParam returns correct value.
	 *
	 * @return  void
	 */
	public function testGetParamReturnsCorrectValue()
	{
		$article = new EntityWithMetadata(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, ['id' => 999]);

		$this->assertEquals([], $article->getMetadata());

		$article = EntityWithMetadata::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'metadata' => '']);

		$this->assertEquals([], $article->getMetadata());

		$article = EntityWithMetadata::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'metadata' => '{}']);

		$this->assertEquals([], $article->getMetadata());

		$article = EntityWithMetadata::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'metadata' => '{"robots":"","author":"","rights":"","xreference":""}']);

		$this->assertEquals([], $article->getMetadata());

		$article = EntityWithMetadata::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'metadata' => '{"robots":"noindex, follow","author":"Roberto Segura","rights":"Creative Commons","xreference":"http:\/\/phproberto.com"}']);

		$expected = [
			'robots' => 'noindex, follow',
			'author' => 'Roberto Segura',
			'rights' => 'Creative Commons',
			'xreference' => 'http://phproberto.com'
		];

		$this->assertEquals($expected, $article->getMetadata());

		$article = EntityWithMetadata::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'metadata' => '{"robots":"noindex, follow","author":"Roberto Segura","xreference":"http:\/\/phproberto.com"}']);

		$expected = [
			'robots' => 'noindex, follow',
			'author' => 'Roberto Segura',
			'xreference' => 'http://phproberto.com'
		];

		$this->assertEquals($expected, $article->getMetadata());

		$article = EntityWithMetadata::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'metadata' => '{"author":"Roberto Segura","xreference":"http:\/\/phproberto.com"}']);

		$expected = [
			'author' => 'Roberto Segura',
			'xreference' => 'http://phproberto.com'
		];

		$this->assertEquals($expected, $article->getMetadata());

		$article = EntityWithMetadata::freshInstance(999);
		$rowProperty->setValue($article, ['id' => 999, 'metadata' => '{"author":"Roberto Segura"}']);

		$expected = [
			'author' => 'Roberto Segura'
		];

		$this->assertEquals($expected, $article->getMetadata());
	}
}
