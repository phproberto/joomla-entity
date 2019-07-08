<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Core\Traits;

use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Tests\Core\Traits\Stubs\EntityWithAssociations;

/**
 * HasAssociations trait tests.
 *
 * @since   1.1.0
 */
class HasAssociationsTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		EntityWithAssociations::clearAll();

		parent::tearDown();
	}

	/**
	 * Association throws exception when association does not exist.
	 *
	 * @return  void
	 *
	 * @expectedException  \InvalidArgumentException
	 */
	public function testAssociationThrowsExceptionWhenAssociationDoesNotExist()
	{
		$entity = new EntityWithAssociations;

		$reflection = new \ReflectionClass($entity);

		$associationsProperty = $reflection->getProperty('associations');
		$associationsProperty->setAccessible(true);

		$associationsProperty->setValue($entity, array());

		$entity->association('es-ES');
	}

	/**
	 * association returns correct association.
	 *
	 * @return  void
	 */
	public function testAssociationReturnsCorrectAssociation()
	{
		$entity = new EntityWithAssociations;

		$cachedAssociations = array(
			'es-ES' => new EntityWithAssociations(666),
			'pt-BR' => new EntityWithAssociations(999)
		);

		$reflection = new \ReflectionClass($entity);

		$associationsProperty = $reflection->getProperty('associations');
		$associationsProperty->setAccessible(true);

		$associationsProperty->setValue($entity, $cachedAssociations);

		$this->assertSame($cachedAssociations['es-ES'], $entity->association('es-ES'));
		$this->assertSame($cachedAssociations['pt-BR'], $entity->association('pt-BR'));
	}

	/**
	 * Associations returns empty array for no associations.
	 *
	 * @return  void
	 */
	public function testAssociationsReturnsEmptyArrayForNoAssociations()
	{
		$entity = new EntityWithAssociations;

		$this->assertSame(array(), $entity->associations());
	}

	/**
	 * Associations returns cached data.
	 *
	 * @return  void
	 */
	public function testAssociationsReturnsCachedData()
	{
		$entity = new EntityWithAssociations;

		$cachedAssociations = array(
			'es-ES' => new EntityWithAssociations(666),
			'pt-BR' => new EntityWithAssociations(999)
		);

		$reflection = new \ReflectionClass($entity);

		$associationsProperty = $reflection->getProperty('associations');
		$associationsProperty->setAccessible(true);

		$associationsProperty->setValue($entity, $cachedAssociations);

		$this->assertSame($cachedAssociations, $entity->associations());
	}

	/**
	 * associations calls loadAssociations when no cached associations.
	 *
	 * @return  void
	 */
	public function testAssociationsCallsLoadAssociationsWhenNoCachedAssociations()
	{
		$associations = array(
			'es-ES' => new EntityWithAssociations(666),
			'pt-BR' => new EntityWithAssociations(999)
		);

		$entity = $this->getMockBuilder(EntityWithAssociations::class)
			->setMethods(array('loadAssociations'))
			->getMock();

		$entity->expects($this->once())
			->method('loadAssociations')
			->willReturn($associations);

		$this->assertSame($associations, $entity->associations());
	}

	/**
	 * Associations reloads data when reload is true.
	 *
	 * @return  void
	 */
	public function testAssociationsReloadsDataWhenReloadIsTrue()
	{
		$associations = array(
			'es-ES' => new EntityWithAssociations(666),
			'pt-BR' => new EntityWithAssociations(999)
		);

		$entity = $this->getMockBuilder(EntityWithAssociations::class)
			->setMethods(array('loadAssociations'))
			->getMock();

		$entity->expects($this->once())
			->method('loadAssociations')
			->willReturn($associations);

		$reflection = new \ReflectionClass($entity);

		$associationsProperty = $reflection->getProperty('associations');
		$associationsProperty->setAccessible(true);

		$associationsProperty->setValue($entity, array());

		$this->assertSame(array(), $entity->associations());
		$this->assertSame($associations, $entity->associations(true));
	}

	/**
	 * associationsIds returns empty array for no associations.
	 *
	 * @return  void
	 */
	public function testAssociationsIdsReturnsEmptyArrayForNoAssociations()
	{
		$entity = new EntityWithAssociations;

		$this->assertSame(array(), $entity->associationsIds());
	}

	/**
	 * associationsIdsReturnsCorrectData.
	 *
	 * @return  void
	 */
	public function testAssociationsIdsReturnsCorrectData()
	{
		$entity = new EntityWithAssociations;

		$cachedAssociations = array(
			'es-ES' => new EntityWithAssociations(666),
			'pt-BR' => new EntityWithAssociations(999)
		);

		$reflection = new \ReflectionClass($entity);

		$associationsProperty = $reflection->getProperty('associations');
		$associationsProperty->setAccessible(true);

		$associationsProperty->setValue($entity, $cachedAssociations);

		$this->assertSame(array('es-ES' => 666, 'pt-BR' => 999), $entity->associationsIds());
	}

	/**
	 * hasAssociation returns correct value.
	 *
	 * @return  void
	 */
	public function testHasAssociationReturnsCorrectValue()
	{
		$entity = new EntityWithAssociations;

		$reflection = new \ReflectionClass($entity);

		$associationsProperty = $reflection->getProperty('associations');
		$associationsProperty->setAccessible(true);

		$associationsProperty->setValue($entity, array());

		$this->assertSame(false, $entity->hasAssociation('es-ES'));
		$this->assertSame(false, $entity->hasAssociation('pt-BR'));
		$this->assertSame(false, $entity->hasAssociation('es-AR'));

		$associations = array(
			'es-ES' => new EntityWithAssociations(666),
			'pt-BR' => new EntityWithAssociations(999)
		);

		$associationsProperty->setValue($entity, $associations);

		$this->assertSame(true, $entity->hasAssociation('es-ES'));
		$this->assertSame(true, $entity->hasAssociation('pt-BR'));
		$this->assertSame(false, $entity->hasAssociation('es-AR'));
	}

	/**
	 * hasAssociationById returns correct value.
	 *
	 * @return  void
	 */
	public function testHasAssociationByIdReturnsCorrectValue()
	{
		$entity = new EntityWithAssociations;

		$reflection = new \ReflectionClass($entity);

		$associationsProperty = $reflection->getProperty('associations');
		$associationsProperty->setAccessible(true);

		$associationsProperty->setValue($entity, array());

		$this->assertSame(false, $entity->hasAssociationById(333));
		$this->assertSame(false, $entity->hasAssociationById(666));
		$this->assertSame(false, $entity->hasAssociationById(999));

		$associations = array(
			'es-ES' => new EntityWithAssociations(666),
			'pt-BR' => new EntityWithAssociations(999)
		);

		$associationsProperty->setValue($entity, $associations);

		$this->assertSame(false, $entity->hasAssociationById(333));
		$this->assertSame(true, $entity->hasAssociationById(666));
		$this->assertSame(true, $entity->hasAssociationById(999));
	}

	/**
	 * hasAssociations returns correct value.
	 *
	 * @return  void
	 */
	public function testHasAssociationsReturnsCorrectValue()
	{
		$entity = new EntityWithAssociations;

		$reflection = new \ReflectionClass($entity);

		$associationsProperty = $reflection->getProperty('associations');
		$associationsProperty->setAccessible(true);

		$associationsProperty->setValue($entity, array());

		$this->assertSame(false, $entity->hasAssociations());

		$associations = array(
			'es-ES' => new EntityWithAssociations(666),
			'pt-BR' => new EntityWithAssociations(999)
		);

		$associationsProperty->setValue($entity, $associations);

		$this->assertSame(true, $entity->hasAssociations());
	}
}
