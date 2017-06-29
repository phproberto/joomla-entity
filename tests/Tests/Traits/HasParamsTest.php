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
	 * getParam returns correct value.
	 *
	 * @return  void
	 */
	public function testGetParamReturnsCorrectValue()
	{
		$entity = new EntityWithParams;

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'params' => '{"foo":"var"}']);

		$this->assertSame('var', $entity->getParam('foo'));
		$this->assertSame(null, $entity->getParam('unknown'));
		$this->assertSame('default', $entity->getParam('use-default', 'default'));
	}

	/**
	 * getParams works with attribs column.
	 *
	 * @return  void
	 */
	public function testGetParamsWorksWithAttribsColumn()
	{
		$mock = $this->getMockBuilder(EntityWithParams::class)
			->setMethods(array('getParamsColumn'))
			->getMock();

		$mock->expects($this->once())
			->method('getParamsColumn')
			->willReturn('attribs');

		$reflection = new \ReflectionClass($mock);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($mock, ['id' => 999, 'attribs' => '{"foo":"var"}']);

		$this->assertEquals(new Registry(['foo' => 'var']), $mock->getParams());
	}

	/**
	 * getParams works with params column.
	 *
	 * @return  void
	 */
	public function testGetParamsWorksWithParamsColumn()
	{
		$entity = new EntityWithParams;

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'params' => '{"foo":"bar"}']);

		$this->assertEquals(new Registry(['foo' => 'bar']), $entity->getParams());
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
			->setMethods(array('getParamsColumn'))
			->getMock();

		$entity->method('getParamsColumn')
			->willReturn('attribs');

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'params' => '{"test":"var"}']);

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
			->setMethods(array('getTable'))
			->getMock();

		$entity->method('getTable')
			->willReturn($tableMock);

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'params' => '{"test":"var"}']);

		$entity->save();
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
			->setMethods(array('save'))
			->getMock();

		$tableMock->expects($this->at(0))
			->method('save')
			->willReturn(true);

		$entity = $this->getMockBuilder(EntityWithParams::class)
			->setMethods(array('getTable'))
			->getMock();

		$entity->method('getTable')
			->willReturn($tableMock);

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'params' => '{"test":"var"}']);

		$this->assertTrue($entity->save());
	}

	/**
	 * setParam sets the correct param value.
	 *
	 * @return  void
	 */
	public function testSetParamSetsCorrectParamValue()
	{
		$entity = new EntityWithParams(999);

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'params' => '{"test":"var"}']);

		$reflection = new \ReflectionClass($entity);
		$paramsProperty = $reflection->getProperty('params');
		$paramsProperty->setAccessible(true);

		$this->assertSame(null, $paramsProperty->getValue($entity));

		$entity->setParam('foo', 'foobar');

		$this->assertEquals(new Registry(['test' => 'var', 'foo' => 'foobar']), $entity->getParams());

		$entity->setParam('test', 'modified-var');

		$expectedParams = new Registry(['test' => 'modified-var', 'foo' => 'foobar']);

		$this->assertEquals($expectedParams, $entity->getParams());
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
			->setMethods(array('getParamsColumn'))
			->getMock();

		$entity->method('getParamsColumn')
			->willReturn('attribs');

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'attribs' => '{"test":"var"}']);

		$reflection = new \ReflectionClass($entity);
		$paramsProperty = $reflection->getProperty('params');
		$paramsProperty->setAccessible(true);

		$this->assertSame(null, $paramsProperty->getValue($entity));

		$entity->setParam('foo', 'foobar');

		$this->assertEquals(new Registry(['test' => 'var', 'foo' => 'foobar']), $paramsProperty->getValue($entity));

		$entity->setParam('test', 'modified-var');

		$expectedParams = new Registry(['test' => 'modified-var', 'foo' => 'foobar']);

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
		$entity = new EntityWithParams(999);

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'params' => '{"test":"var"}']);

		$reflection = new \ReflectionClass($entity);
		$paramsProperty = $reflection->getProperty('params');
		$paramsProperty->setAccessible(true);

		$this->assertSame(null, $paramsProperty->getValue($entity));

		$expectedParams = new Registry(['test' => 'modified-var', 'foo' => 'foobar']);
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
			->setMethods(array('getParamsColumn'))
			->getMock();

		$entity->method('getParamsColumn')
			->willReturn('attribs');

		$reflection = new \ReflectionClass($entity);
		$rowProperty = $reflection->getProperty('row');
		$rowProperty->setAccessible(true);

		$rowProperty->setValue($entity, ['id' => 999, 'attribs' => '{"test":"var"}']);

		$reflection = new \ReflectionClass($entity);
		$paramsProperty = $reflection->getProperty('params');
		$paramsProperty->setAccessible(true);

		$this->assertSame(null, $paramsProperty->getValue($entity));

		$expectedParams = new Registry(['test' => 'modified-var', 'foo' => 'foobar']);

		$entity->setParams($expectedParams);

		$this->assertEquals($expectedParams, $paramsProperty->getValue($entity));
		$this->assertEquals($expectedParams->toString(), $rowProperty->getValue($entity)['attribs']);
	}
}
