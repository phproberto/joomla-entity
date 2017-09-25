<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Fields\Traits;

use Phproberto\Joomla\Entity\Collection;
use Phproberto\Joomla\Entity\Fields\Field;
use Phproberto\Joomla\Entity\Core\Extension\Component;
use Phproberto\Joomla\Entity\Tests\Fields\Traits\Stubs\EntityWithFields;

/**
 * HasTags trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasFieldsTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * field returns correct field.
	 *
	 * @return  void
	 */
	public function testFieldReturnsCorrectField()
	{
		$fields = new Collection(array(Field::find(999), Field::find(1000)));

		$entity = $this->getMockBuilder(EntityWithFields::class)
			->setMethods(array('fields'))
			->getMock();

		$entity
			->method('fields')
			->willReturn($fields);

		$this->assertInstanceOf(Field::class, $entity->field(1000));
		$this->assertInstanceOf(Field::class, $entity->field(999));
	}

	/**
	 * field throws exception for missing field.
	 *
	 * @return  void
	 *
	 * @expectedException  \InvalidArgumentException
	 */
	public function testFieldThrowsExceptionForMissingField()
	{
		$fields = new Collection(array(Field::find(999), Field::find(1000)));

		$entity = $this->getMockBuilder(EntityWithFields::class)
			->setMethods(array('fields'))
			->getMock();

		$entity
			->method('fields')
			->willReturn($fields);

		$entity->field(767);
	}

	/**
	 * fieldsContext returns correct value.
	 *
	 * @return  void
	 */
	public function testFieldsContextReturnsCorrectValue()
	{
		$component = $this->getMockBuilder(Component::class)
			->setMethods(array('option'))
			->getMock();

		$component->expects($this->once())
			->method('option')
			->willReturn('com_phproberto');

		$entity = $this->getMockBuilder(EntityWithFields::class)
			->setMethods(array('component', 'name'))
			->getMock();

		$entity->expects($this->once())
			->method('component')
			->willReturn($component);

		$entity->expects($this->once())
			->method('name')
			->willReturn('sample');

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('fieldsContext');
		$method->setAccessible(true);

		$this->assertSame('com_phproberto.sample', $method->invoke($entity));
	}

	/**
	 * fields loadFields.
	 *
	 * @return  void
	 */
	public function testFieldsLoadFields()
	{
		$reloadCollection = new Collection(array(Field::find(999), Field::find(1000)));

		$entity = $this->getMockBuilder(EntityWithFields::class)
			->setMethods(array('loadFields'))
			->getMock();

		$entity->expects($this->at(0))
			->method('loadFields')
			->willReturn(new Collection);

		$entity->expects($this->at(1))
			->method('loadFields')
			->willReturn($reloadCollection);

		$this->assertEquals(new Collection, $entity->fields());
		$this->assertEquals(new Collection,  $entity->fields());
		$this->assertEquals($reloadCollection,  $entity->fields(true));
	}

	/**
	 * fieldValue returns correct value.
	 *
	 * @return  void
	 */
	public function testFieldValueReturnsCorrectValue()
	{
		$field = new Field(666);
		$field->bind(
			array(
				'id'       => 666,
				'title'    => 'Sample field',
				'value'    => 'Sample field value',
				'rawvalue' => 'Sample field raw value'
			)
		);

		$field2 = new Field(999);
		$field2->bind(
			array(
				'id'       => 999,
				'title'    => 'Sample field 2',
				'value'    => 'Sample field 2 value',
				'rawvalue' => 'Sample field 2 raw value'
			)
		);

		$field3 = new Field(1002);
		$field3->bind(
			array(
				'id'       => 1002,
				'title'    => 'Sample field 3',
				'value'    => null,
				'rawvalue' => null
			)
		);

		$fields = new Collection(array($field, $field2, $field3));

		$entity = $this->getMockBuilder(EntityWithFields::class)
			->setMethods(array('fields'))
			->getMock();

		$entity
			->method('fields')
			->willReturn($fields);

		$this->assertSame('default value', $entity->fieldValue(1002, 'default value'));
		$this->assertSame('Sample field value', $entity->fieldValue(666));
		$this->assertSame('Sample field 2 value', $entity->fieldValue(999));
	}

	/**
	 * fieldValue throws exception for missing field.
	 *
	 * @return  void
	 *
	 * @expectedException  \InvalidArgumentException
	 */
	public function testFieldValueThrowsExceptionForMissingField()
	{
		$entity = $this->getMockBuilder(EntityWithFields::class)
			->setMethods(array('fields'))
			->getMock();

		$entity->expects($this->once())
			->method('fields')
			->willReturn(new Collection);

		$entity->fieldValue(999);
	}

	/**
	 * fieldValues returns an array with values.
	 *
	 * @return  void
	 */
	public function testFieldValuesReturnsAnArrayWithValues()
	{
		$field = new Field(666);
		$field->bind(
			array(
				'id'       => 666,
				'title'    => 'Sample field',
				'value'    => 'Sample field value',
				'rawvalue' => 'Sample field raw value'
			)
		);

		$field2 = new Field(999);
		$field2->bind(
			array(
				'id'       => 999,
				'title'    => 'Sample field 2',
				'value'    => 'Sample field 2 value',
				'rawvalue' => 'Sample field 2 raw value'
			)
		);

		$field3 = new Field(1002);
		$field3->bind(
			array(
				'id'       => 1002,
				'title'    => 'Sample field 3',
				'value'    => 'Sample field 3 value',
				'rawvalue' => 'Sample field 3 raw value'
			)
		);

		$fields = new Collection(array($field, $field2, $field3));

		$entity = $this->getMockBuilder(EntityWithFields::class)
			->setMethods(array('fields'))
			->getMock();

		$entity->expects($this->at(0))
			->method('fields')
			->willReturn(new Collection);

		$entity->expects($this->at(1))
			->method('fields')
			->willReturn($fields);

		$entity->expects($this->at(2))
			->method('fields')
			->willReturn($fields);

		$this->assertSame(array(), $entity->fieldValues());

		$expectedValues = array(
			666  => 'Sample field value',
			999  => 'Sample field 2 value',
			1002 => 'Sample field 3 value'
		);

		$this->assertSame($expectedValues, $entity->fieldValues());

		$expectedRawValues = array(
			666  => 'Sample field raw value',
			999  => 'Sample field 2 raw value',
			1002 => 'Sample field 3 raw value'
		);

		$this->assertSame($expectedRawValues, $entity->fieldValues(true));
	}

	/**
	 * hasField returns correct value.
	 *
	 * @return  void
	 */
	public function testHasFieldReturnsCorrectValue()
	{
		$fields = new Collection(array(Field::find(999), Field::find(1000)));

		$entity = $this->getMockBuilder(EntityWithFields::class)
			->setMethods(array('fields'))
			->getMock();

		$entity
			->method('fields')
			->willReturn($fields);

		$this->assertTrue($entity->hasField(1000));
		$this->assertFalse($entity->hasField(998));
		$this->assertTrue($entity->hasField(1000));
	}

	/**
	 * loadFields returns correct value.
	 *
	 * @return  void
	 */
	public function testLoadFieldsReturnsCorrectValue()
	{
		$helperData = array(
			(object) array(
				'id'       => 666,
				'title'    => 'Sample field',
				'value'    => 'Sample field value',
				'rawvalue' => 'Sample raw field value'
			)
		);

		$entity = $this->getMockBuilder(EntityWithFields::class)
			->setMethods(array('component', 'fieldsContext', 'getFieldsThroughHelper'))
			->getMock();

		$entity->expects($this->once())
			->method('fieldsContext')
			->willReturn('com_phproberto.sample');

		$entity->expects($this->once())
			->method('getFieldsThroughHelper')
			->with($this->equalTo('com_phproberto.sample'))
			->willReturn($helperData);

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('loadFields');
		$method->setAccessible(true);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($entity, 444);

		$this->assertEquals(new Collection(array(Field::find(666))), $method->invoke($entity));
	}
}
