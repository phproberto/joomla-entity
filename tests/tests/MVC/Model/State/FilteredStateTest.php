<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Tests\MVC\Model\State;

defined('_JEXEC') || die;

use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Phproberto\Joomla\Entity\MVC\Model\State\Filter;
use Phproberto\Joomla\Entity\MVC\Model\State\FilteredState;
use Phproberto\Joomla\Entity\MVC\Model\State\FilteredProperty;
use Phproberto\Joomla\Entity\MVC\Model\State\Property;
use Phproberto\Joomla\Entity\MVC\Model\State\PropertyInterface;

/**
 * FilteredState tests.
 *
 * @since   __DEPLOY_VERSION__
 */
class FilteredStateTest extends \TestCaseDatabase
{
	/**
	 * @test
	 *
	 * @return void
	 */
	public function getReturnsFilteredValue()
	{
		$model = $this->getMockBuilder(BaseDatabaseModel::class)
			->setMethods(array('getState'))
			->getMock();

		$model->expects($this->once())
			->method('getState')
			->with($this->equalTo('filter.sample'), $this->equalTo('defaultValue'))
			->willReturn('23, 45');

		$properties = [
			new FilteredProperty(
				new Property('filter.sample'),
				new Filter\Integer
			)
		];

		$state = new FilteredState($model, $properties);

		$this->assertSame([23, 45], $state->get('filter.sample', 'defaultValue'));
	}

	/**
	 * @test
	 *
	 * @return void
	 */
	public function getReturnsStringQuotedValueIfExceptionHappens()
	{
		$model = $this->getMockBuilder(BaseDatabaseModel::class)
			->setMethods(array('getState'))
			->getMock();

		$model->expects($this->once())
			->method('getState')
			->with($this->equalTo('filter.sample'), $this->equalTo('defaultValue'))
			->willReturn('23, 45');

		$state = new FilteredState($model);

		$this->assertSame(['\'23\'', '\'45\''], $state->get('filter.sample', 'defaultValue'));
	}
}
