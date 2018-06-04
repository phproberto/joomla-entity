<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Fields;

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Core\Column;
use Phproberto\Joomla\Entity\Fields\Field;
use Phproberto\Joomla\Entity\Core\Extension\Component;

/**
 * Field entity tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class FieldTest extends \TestCaseDatabase
{
	/**
	 * Tears down the fixture, for example, closes a network connection.
	 * This method is called after a test is executed.
	 *
	 * @return  void
	 */
	protected function tearDown()
	{
		Field::clearAll();

		parent::tearDown();
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function hasRawValueReturnsCorrectValue()
	{
		$field = new Field(12);
		$field->bind(['id' => 12, 'title' => 'Test field']);

		$this->assertFalse($field->hasRawValue());

		$field = new Field(14);
		$field->bind(['id' => 14, 'title' => 'Another field', 'rawvalue' => 'My value']);

		$this->assertTrue($field->hasRawValue());
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function hasValueReturnsCorrectValue()
	{
		$field = new Field(12);
		$field->bind(['id' => 12, 'title' => 'Test field']);

		$this->assertFalse($field->hasValue());

		$field = new Field(14);
		$field->bind(['id' => 14, 'title' => 'Another field', 'value' => 'My value']);

		$this->assertTrue($field->hasValue());
	}

	/**
	 * @test
	 *
	 * @return  void
	 */
	public function paramsReturnsParameters()
	{
		$field = $this->getMockBuilder(Field::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$field->method('columnAlias')
			->willReturn(Column::PARAMS);

		$field->bind(array('id' => 999, 'params' => '{"foo":"var"}'));

		$this->assertEquals(new Registry(array('foo' => 'var')), $field->params());
	}

	/**
	 * @test
	 *
	 * @return  void
	 */
	public function stateReturnsCorrectValue()
	{
		$field = $this->getMockBuilder(Field::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$field->method('columnAlias')
			->willReturn(Column::STATE);

		$field->bind(array('id' => 999, 'published' => '0'));

		$this->assertEquals(0, $field->state());

		$field->bind(array('id' => 999, 'published' => '1'));

		$this->assertEquals(1, $field->state());
	}

	/**
	 * @test
	 *
	 * @return  void
	 */
	public function tableReturnsCorrectInstance()
	{
		$component = $this->getMockBuilder(Component::class)
			->setMethods(array('table'))
			->getMock();

		$component->expects($this->once())
			->method('table')
			->with($this->equalTo('Field'))
			->willReturn('componentTable');

		$field = $this->getMockBuilder(Field::class)
			->setMethods(array('component'))
			->getMock();

		$field->method('component')
			->willReturn($component);

		$this->assertSame('componentTable', $field->table());
	}

	/**
	 * @test
	 *
	 * @return  void
	 */
	public function tableReturnsSpecificTableInstance()
	{
		$field = new Field;

		$this->assertInstanceOf('JTableUser', $field->table('User', 'JTable'));
	}

	/**
	 * @test
	 *
	 * @return  void
	 */
	public function nameRetrieved()
	{
		$field = new Field(999);

		$reflection = new \ReflectionClass($field);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($field, array('id' => 999, 'name' => 'field_name'));

		$this->assertSame('field_name', $field->fieldName());
	}

	/**
	 * @test
	 *
	 * @return  void
	 */
	public function valueRetrieved()
	{
		$field = new Field(999);

		$reflection = new \ReflectionClass($field);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($field, array('id' => 999, 'value' => 100));

		$this->assertSame(100, $field->value());
	}

	/**
	 * @test
	 *
	 * @return  void
	 */
	public function rawValueRetrieved()
	{
		$field = new Field(999);

		$reflection = new \ReflectionClass($field);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($field, array('id' => 999, 'rawvalue' => array('x' => 'dummy')));

		$expected = array('x' => 'dummy');

		$this->assertEquals($expected, $field->rawValue());
	}
}
