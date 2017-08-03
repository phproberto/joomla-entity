<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits;

use Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs\EntityWithMetadata;

/**
 * HasMetadata trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasMetadataTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * getMetadata returns correct value.
	 *
	 * @return  void
	 */
	public function testGetMetadataReturnsCorrectValue()
	{
		$article = new EntityWithMetadata(999);

		$reflection = new \ReflectionClass($article);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($article, array('id' => 999));

		$this->assertEquals(array(), $article->getMetadata(true));

		$rowProperty->setValue($article, array('id' => 999, 'metadata' => ''));

		$this->assertEquals(array(), $article->getMetadata(true));

		$rowProperty->setValue($article, array('id' => 999, 'metadata' => '{}'));

		$this->assertEquals(array(), $article->getMetadata(true));

		$rowProperty->setValue($article, array('id' => 999, 'metadata' => '{"robots":"","author":"","rights":"","xreference":""}'));

		$this->assertEquals(array(), $article->getMetadata(true));

		$rowProperty->setValue($article, array('id' => 999, 'metadata' => '{"robots":"noindex, follow","author":"Roberto Segura","rights":"Creative Commons","xreference":"http:\/\/phproberto.com"}'));

		// Without reload = old data
		$this->assertEquals(array(), $article->getMetadata());

		$expected = array(
			'robots'     => 'noindex, follow',
			'author'     => 'Roberto Segura',
			'rights'     => 'Creative Commons',
			'xreference' => 'http://phproberto.com'
		);

		$this->assertEquals($expected, $article->getMetadata(true));

		$rowProperty->setValue($article, array('id' => 999, 'metadata' => '{"robots":"noindex, follow","author":"Roberto Segura","xreference":"http:\/\/phproberto.com"}'));

		$expected = array(
			'robots'     => 'noindex, follow',
			'author'     => 'Roberto Segura',
			'xreference' => 'http://phproberto.com'
		);

		$this->assertEquals($expected, $article->getMetadata(true));

		$rowProperty->setValue($article, array('id' => 999, 'metadata' => '{"author":"Roberto Segura","xreference":"http:\/\/phproberto.com"}'));

		$expected = array(
			'author' => 'Roberto Segura',
			'xreference' => 'http://phproberto.com'
		);

		$this->assertEquals($expected, $article->getMetadata(true));

		$rowProperty->setValue($article, array('id' => 999, 'metadata' => '{"author":"Roberto Segura"}'));

		$expected = array(
			'author' => 'Roberto Segura'
		);

		$this->assertEquals($expected, $article->getMetadata(true));
	}
}
