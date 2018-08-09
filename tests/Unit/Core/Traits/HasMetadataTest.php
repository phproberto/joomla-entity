<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2018 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Core\Traits;

use Phproberto\Joomla\Entity\Tests\Unit\Core\Traits\Stubs\EntityWithMetadata;
use Phproberto\Joomla\Entity\Core\Column;

/**
 * HasMetadata trait tests.
 *
 * @since   1.1.0
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
		$entity = $this->getMockBuilder(EntityWithMetadata::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn(Column::METADATA);

		$reflection = new \ReflectionClass($entity);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($entity, 999);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($entity, array('id' => 999));

		$rowProperty->setValue($entity, array('id' => 999, Column::METADATA => ''));

		$this->assertEquals(array(), $entity->metadata(true));

		$rowProperty->setValue($entity, array('id' => 999, Column::METADATA => '{}'));

		$this->assertEquals(array(), $entity->metadata(true));

		$rowProperty->setValue($entity, array('id' => 999, Column::METADATA => '{"robots":"","author":"","rights":"","xreference":""}'));

		$this->assertEquals(array(), $entity->metadata(true));

		$rowProperty->setValue($entity, array('id' => 999, Column::METADATA => '{"robots":"noindex, follow","author":"Roberto Segura","rights":"Creative Commons","xreference":"http:\/\/phproberto.com"}'));

		// Without reload = old data
		$this->assertEquals(array(), $entity->metadata());

		$expected = array(
			'robots'     => 'noindex, follow',
			'author'     => 'Roberto Segura',
			'rights'     => 'Creative Commons',
			'xreference' => 'http://phproberto.com'
		);

		$this->assertEquals($expected, $entity->metadata(true));

		$rowProperty->setValue($entity, array('id' => 999, Column::METADATA => '{"robots":"noindex, follow","author":"Roberto Segura","xreference":"http:\/\/phproberto.com"}'));

		$expected = array(
			'robots'     => 'noindex, follow',
			'author'     => 'Roberto Segura',
			'xreference' => 'http://phproberto.com'
		);

		$this->assertEquals($expected, $entity->metadata(true));

		$rowProperty->setValue($entity, array('id' => 999, Column::METADATA => '{"author":"Roberto Segura","xreference":"http:\/\/phproberto.com"}'));

		$expected = array(
			'author' => 'Roberto Segura',
			'xreference' => 'http://phproberto.com'
		);

		$this->assertEquals($expected, $entity->metadata(true));

		$rowProperty->setValue($entity, array('id' => 999, Column::METADATA => '{"author":"Roberto Segura"}'));

		$expected = array(
			'author' => 'Roberto Segura'
		);

		$this->assertEquals($expected, $entity->metadata(true));
	}
}
