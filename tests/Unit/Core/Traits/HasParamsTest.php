<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Unit\Core\Traits;

use Phproberto\Joomla\Entity\Tests\Unit\Core\Traits\Stubs\EntityWithParams;
use Joomla\Registry\Registry;

/**
 * HasParams trait tests.
 *
 * @since   1.1.0
 */
class HasParamsTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * clearParmas sets params property to null.
	 *
	 * @return  void
	 */
	public function testClearParamsSetsParamsPropertyToNull()
	{
		$entity = $this->getEntity(array('id' => 999, 'params' => '{"foo":"bar"}'));

		$reflection = new \ReflectionClass($entity);

		$paramsProperty = $reflection->getProperty('params');
		$paramsProperty->setAccessible(true);

		$this->assertSame(null, $paramsProperty->getValue($entity));

		$loadedParams = new Registry('{"foo":"bar-modified"}');
		$paramsProperty->setValue($entity, $loadedParams);

		$this->assertSame($loadedParams, $paramsProperty->getValue($entity));

		$entity->clearParams();

		$this->assertSame(null, $paramsProperty->getValue($entity));
	}

	/**
	 * loadParams returns row params if they are already there as string.
	 *
	 * @return  void
	 */
	public function testLoadParamsReturnsRowParamsIfTheyAreAlreadyThereAsString()
	{
		$row = array('id' => 999, 'title' => 'test entity', 'attribs' => '{"foo":"var"}', 'params' => '{"bar":"foo"}');

		$entity = $this->getMockBuilder(EntityWithParams::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn('attribs');

		$entity->bind($row);

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('loadParams');
		$method->setAccessible(true);

		$this->assertSame('foo', $method->invoke($entity)->get('bar'));
	}

	/**
	 * loadParams returns row params if they are already there as Registry.
	 *
	 * @return  void
	 */
	public function testLoadParamsReturnsRowParamsIfTheyAreAlreadyThereAsRegistry()
	{
		$params = new Registry('{"bar":"foo"}');

		$row = array('id' => 999, 'title' => 'test entity', 'attribs' => '{"foo":"var"}', 'params' => $params);

		$entity = $this->getMockBuilder(EntityWithParams::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn('attribs');

		$entity->bind($row);

		$reflection = new \ReflectionClass($entity);
		$method = $reflection->getMethod('loadParams');
		$method->setAccessible(true);

		$this->assertSame($params, $method->invoke($entity));
	}

	/**
	 * param returns correct value.
	 *
	 * @return  void
	 */
	public function testParamReturnsCorrectValue()
	{
		$entity = $this->getEntity(array('id' => 999, 'params' => '{"foo":"var"}'));

		$this->assertSame('var', $entity->param('foo'));
		$this->assertSame(null, $entity->param('unknown'));
		$this->assertSame('default', $entity->param('use-default', 'default'));
	}

	/**
	 * params works with attribs column.
	 *
	 * @return  void
	 */
	public function testParamsWorksWithAttribsColumn()
	{
		$entity = $this->getMockBuilder(EntityWithParams::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->expects($this->once())
			->method('columnAlias')
			->willReturn('attribs');

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, array('id' => 999, 'attribs' => '{"foo":"var"}'));

		$this->assertEquals(new Registry(array('foo' => 'var')), $entity->params());
	}

	/**
	 * params works for unset params.
	 *
	 * @return  void
	 */
	public function testParamsWorksForUnsetParams()
	{
		$entity = $this->getEntity(array('id' => 999, 'name' => 'Roberto Segura', 'params' => ''));

		$this->assertEquals(new Registry, $entity->params());
	}

	/**
	 * params works with params column.
	 *
	 * @return  void
	 */
	public function testParamsWorksWithParamsColumn()
	{
		$entity = $this->getEntity(array('id' => 999, 'params' => '{"foo":"bar"}'));

		$this->assertEquals(new Registry(array('foo' => 'bar')), $entity->params());
	}

	/**
	 * saveParams throws an exception when column is not present in database row.
	 *
	 * @return  void
	 *
	 * @expectedException \InvalidArgumentException
	 */
	public function testSaveParamsThrowsExceptionIfParamsColumnIsNotPresentInRow()
	{
		$entity = $this->getMockBuilder(EntityWithParams::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn('attribs');

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, array('id' => 999, 'params' => '{"test":"var"}'));

		$entity->saveParams();
	}

	/**
	 * saveParams stores correct value.
	 *
	 * @return  void
	 *
	 * @expectedException \RuntimeException
	 */
	public function testSaveParamsThrowsExceptionIfTableSaveFails()
	{
		$tableMock = $this->getMockBuilder(\JTable::class)
			->disableOriginalConstructor()
			->setMethods(array('save', 'getError'))
			->getMock();

		$tableMock->expects($this->at(0))
			->method('save')
			->willReturn(false);

		$tableMock->expects($this->at(1))
			->method('getError')
			->willReturn('En un lugar de La Mancha de cuyo nombre no quiero acordarme');

		$entity = $this->getMockBuilder(EntityWithParams::class)
			->setMethods(array('table'))
			->getMock();

		$entity->method('table')
			->willReturn($tableMock);

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, array('id' => 999, 'params' => '{"test":"var"}'));

		$entity->saveParams();
	}

	/**
	 * saveParams returns true when table saves data.
	 *
	 * @return  void
	 */
	public function testSaveParamsReturnsTrueWhenTableSavesData()
	{
		$tableMock = $this->getMockBuilder(\JTable::class)
			->disableOriginalConstructor()
			->setMethods(array('save', 'load'))
			->getMock();

		$tableMock
			->method('load')
			->willReturn(true);

		$tableMock
			->method('save')
			->willReturn(true);

		$entity = $this->getMockBuilder(EntityWithParams::class)
			->setMethods(array('table'))
			->getMock();

		$entity->method('table')
			->willReturn($tableMock);

		$reflection = new \ReflectionClass($entity);
		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($entity, 999);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, array('id' => 999, 'params' => '{"test":"var"}'));

		$this->assertTrue($entity->saveParams());
	}

	/**
	 * setParam sets the correct param value.
	 *
	 * @return  void
	 */
	public function testSetParamSetsCorrectParamValue()
	{
		$entity = $this->getEntity(array('id' => 999, 'params' => '{"test":"var"}'));

		$reflection = new \ReflectionClass($entity);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$paramsProperty = $reflection->getProperty('params');
		$paramsProperty->setAccessible(true);

		$this->assertSame(null, $paramsProperty->getValue($entity));

		$entity->setParam('foo', 'foobar');

		$this->assertEquals(new Registry(array('test' => 'var', 'foo' => 'foobar')), $entity->params());

		$entity->setParam('test', 'modified-var');

		$expectedParams = new Registry(array('test' => 'modified-var', 'foo' => 'foobar'));

		$this->assertEquals($expectedParams, $entity->params());
		$this->assertEquals($expectedParams->toString(), $rowProperty->getValue($entity)['params']);
	}

	/**
	 * setParam updates row parameters.
	 *
	 * @return  void
	 */
	public function testSetParamSetsCorrectParamValueWithCustomParamsColumn()
	{
		$entity = $this->getMockBuilder(EntityWithParams::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn('attribs');

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, array('id' => 999, 'attribs' => '{"test":"var"}'));

		$reflection = new \ReflectionClass($entity);
		$paramsProperty = $reflection->getProperty('params');
		$paramsProperty->setAccessible(true);

		$this->assertSame(null, $paramsProperty->getValue($entity));

		$entity->setParam('foo', 'foobar');

		$this->assertEquals(new Registry(array('test' => 'var', 'foo' => 'foobar')), $paramsProperty->getValue($entity));

		$entity->setParam('test', 'modified-var');

		$expectedParams = new Registry(array('test' => 'modified-var', 'foo' => 'foobar'));

		$this->assertEquals($expectedParams, $paramsProperty->getValue($entity));
		$this->assertEquals($expectedParams->toString(), $rowProperty->getValue($entity)['attribs']);
	}

	/**
	 * setParam sets the correct param value.
	 *
	 * @return  void
	 */
	public function testSetParamsSetsCorrectValue()
	{
		$entity = $this->getEntity(array('id' => 999, 'params' => '{"test":"var"}'));

		$reflection = new \ReflectionClass($entity);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$paramsProperty = $reflection->getProperty('params');
		$paramsProperty->setAccessible(true);

		$this->assertSame(null, $paramsProperty->getValue($entity));

		$expectedParams = new Registry(array('test' => 'modified-var', 'foo' => 'foobar'));
		$entity->setParams($expectedParams);

		$this->assertEquals($expectedParams, $paramsProperty->getValue($entity));
		$this->assertEquals($expectedParams->toString(), $rowProperty->getValue($entity)['params']);
	}

	/**
	 * setParam updates row parameters.
	 *
	 * @return  void
	 */
	public function testSetParamsSetsCorrectValueWithCustomParamsColumn()
	{
		$entity = $this->getMockBuilder(EntityWithParams::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn('attribs');

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, array('id' => 999, 'attribs' => '{"test":"var"}'));

		$reflection = new \ReflectionClass($entity);
		$paramsProperty = $reflection->getProperty('params');
		$paramsProperty->setAccessible(true);

		$this->assertSame(null, $paramsProperty->getValue($entity));

		$expectedParams = new Registry(array('test' => 'modified-var', 'foo' => 'foobar'));

		$entity->setParams($expectedParams);

		$this->assertEquals($expectedParams, $paramsProperty->getValue($entity));
		$this->assertEquals($expectedParams->toString(), $rowProperty->getValue($entity)['attribs']);
	}

	/**
	 * Get a mocked entity.
	 *
	 * @param   array  $row  Row returned by the entity as data
	 *
	 * @return  \PHPUnit_Framework_MockObject_MockObject
	 */
	private function getEntity($row = array())
	{
		$entity = $this->getMockBuilder(EntityWithParams::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn('params');

		$entity->bind($row);

		return $entity;
	}
}
