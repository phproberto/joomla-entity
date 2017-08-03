<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\Traits;

use Phproberto\Joomla\Entity\Tests\Traits\Stubs\EntityWithParams;
use Joomla\Registry\Registry;

/**
 * HasParams trait tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class HasParamsTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * param returns correct value.
	 *
	 * @return  void
	 */
	public function testParamReturnsCorrectValue()
	{
		$entity = $this->getMockBuilder(EntityWithParams::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->expects($this->once())
			->method('columnAlias')
			->willReturn('params');

		$reflection = new \ReflectionClass($entity);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($entity, 999);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($entity, array('id' => 999, 'params' => '{"foo":"var"}'));

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
		$entity = $this->getMockBuilder(EntityWithParams::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->expects($this->once())
			->method('columnAlias')
			->willReturn('params');

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, array('id' => 999, 'name' => 'Roberto Segura'));

		$this->assertEquals(new Registry, $entity->params());
	}

	/**
	 * params works with params column.
	 *
	 * @return  void
	 */
	public function testParamsWorksWithParamsColumn()
	{
		$entity = $this->getMockBuilder(EntityWithParams::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn('params');

		$reflection = new \ReflectionClass($entity);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($entity, 999);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($entity, array('id' => 999, 'params' => '{"foo":"bar"}'));

		$this->assertEquals(new Registry(array('foo' => 'bar')), $entity->params());
	}

	/**
	 * params reload works.
	 *
	 * @return  void
	 */
	public function testParamsReloadWorks()
	{
		$entity = $this->getMockBuilder(EntityWithParams::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn('params');

		$reflection = new \ReflectionClass($entity);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($entity, 999);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($entity, array('id' => 999, 'params' => '{"foo":"bar"}'));

		$this->assertEquals(new Registry(array('foo' => 'bar')), $entity->params());

		$rowProperty->setValue($entity, array('id' => 999, 'params' => '{"foo":"bar-modified"}'));

		$this->assertEquals(new Registry(array('foo' => 'bar')), $entity->params());
		$this->assertEquals(new Registry(array('foo' => 'bar-modified')), $entity->params(true));
	}

	/**
	 * saveParams throws an exception when column is not present in database row.
	 *
	 * @return  void
	 *
	 * @expectedException \RuntimeException
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
		$entity = $this->getMockBuilder(EntityWithParams::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->method('columnAlias')
			->willReturn('params');

		$reflection = new \ReflectionClass($entity);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($entity, 999);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($entity, array('id' => 999, 'params' => '{"test":"var"}'));

		$reflection = new \ReflectionClass($entity);
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
		$entity = $this->getMockBuilder(EntityWithParams::class)
			->setMethods(array('columnAlias'))
			->getMock();

		$entity->expects($this->once())
			->method('columnAlias')
			->willReturn('params');

		$reflection = new \ReflectionClass($entity);

		$idProperty = $reflection->getProperty('id');
		$idProperty->setAccessible(true);
		$idProperty->setValue($entity, 999);

		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);
		$rowProperty->setValue($entity, array('id' => 999, 'params' => '{"test":"var"}'));

		$reflection = new \ReflectionClass($entity);
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
}
