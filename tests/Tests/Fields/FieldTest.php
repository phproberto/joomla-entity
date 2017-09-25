<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Fields;

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
	 * params returns parameters.
	 *
	 * @return  void
	 */
	public function testParamsReturnsParameters()
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
	 * getState returns correct value.
	 *
	 * @return  void
	 */
	public function testGetStateReturnsCorrectValue()
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
	 * table returns correct instance.
	 *
	 * @return  void
	 */
	public function testTableReturnsCorrectInstance()
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
	 * table returns a specific table instance.
	 *
	 * @return  void
	 */
	public function testTableReturnsSpecificTableInstance()
	{
		$field = new Field;

		$this->assertInstanceOf('JTableUser', $field->table('User', 'JTable'));
	}
}
