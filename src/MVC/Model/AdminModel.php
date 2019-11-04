<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\MVC\Model;

defined('_JEXEC') || die;

use Joomla\CMS\Factory;
use Joomla\CMS\Date\Date;
use Joomla\Registry\Registry;
use Joomla\CMS\MVC\Model\AdminModel as BaseAdminModel;
use Phproberto\Joomla\Entity\Helper\ClassName;

/**
 * Base admin model.
 *
 * @since  __DEPLOY_VERSION__
 */
class AdminModel extends BaseAdminModel
{
	/**
	 * Minutes that need to pass before an item is automatically available to be edited by other users.
	 *
	 * @var    integer
	 */
	protected $autoCheckinMinutes = 15;

	/**
	 * Method to check-out a row for editing. Overriden for autocheckin.
	 *
	 * @param   integer  $pk  The numeric id of the primary key.
	 *
	 * @return  boolean  False on failure or error, true otherwise.
	 */
	public function checkout($pk = null)
	{
		if (!$pk)
		{
			return true;
		}

		$user = \JFactory::getUser();

		// Get an instance of the row to checkin.
		$table = $this->getTable();

		if (!$table->load($pk))
		{
			$this->setError($table->getError());

			return false;
		}

		$checkedOutField = $table->getColumnAlias('checked_out');
		$checkedOutTimeField = $table->getColumnAlias('checked_out_time');

		// If there is no checked_out or checked_out_time field, just return true.
		if (!property_exists($table, $checkedOutField) || !property_exists($table, $checkedOutTimeField))
		{
			return true;
		}

		// Automatically free items checked out for more than 15 minutes
		$isCheckinActive = false;

		if ($this->autoCheckinMinutes)
		{
			$now = new Date;
			$autoCheckinTime = new Date($table->{$checkedOutTimeField});
			$autoCheckinTime->add(new \DateInterval('PT' . $this->autoCheckinMinutes . 'M'));

			$isCheckinActive = $now < $autoCheckinTime;
		}

		// Check if this is the user having previously checked out the row.
		if ($isCheckinActive
			&& $table->{$checkedOutField} > 0
			&& $table->{$checkedOutField} != $user->get('id')
			&& !$user->authorise('core.admin', 'com_checkin')
		)
		{
			$this->setError(\JText::_('JLIB_APPLICATION_ERROR_CHECKIN_USER_MISMATCH'));

			return false;
		}

		// Attempt to check the row in.
		if (!$table->checkIn($pk))
		{
			$this->setError($table->getError());

			return false;
		}

		return true;
	}

	/**
	 * Get the form control.
	 *
	 * @return  string
	 */
	protected function formControl()
	{
		return 'jform';
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm|boolean  A JForm object on success, false on failure
	 */
	public function getForm($data = array(), $loadData = true)
	{
		$form = $this->loadForm(
			$this->option . '.edit.' . $this->getName(),
			$this->getName(),
			[
				'control'   => $this->formControl(),
				'load_data' => $loadData
			]
		);

		if (empty($form))
		{
			return false;
		}

		// Force re-preprocess with data so custom fields are saved
		if (!$loadData && $data)
		{
			$this->preprocessForm($form, $data);
		}

		return $form;
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  \JTable  A \JTable object
	 *
	 * @throws  \Exception
	 */
	public function getTable($name = '', $prefix = '', $options = array())
	{
		$name   = empty($name) ? $this->getName() : $name;
		$prefix = empty($prefix) ? $this->tablePrefix() : $prefix;

		if ($table = $this->_createTable($name, $prefix, $options))
		{
			return $table;
		}

		throw new \Exception(\JText::sprintf('JLIB_APPLICATION_ERROR_TABLE_NAME_NOT_SUPPORTED', $name), 0);
	}

	/**
	 * Default prefix used to access tables.
	 *
	 * @return  string
	 */
	public function tablePrefix()
	{
		$class = get_class($this);

		if (!ClassName::inNamespace($this))
		{
			$parts = explode('Model', $class, 2);

			return $parts ? $parts[0] . 'Table' : '';
		}

		$namespaceParts = ClassName::namespaceParts($class);
		$lastNamespacePart = end($namespaceParts);

		// Asume namespace contains Entity folder with entities. Example: Content/Model/Article -> Should return Content
		if ('Model' === $lastNamespacePart)
		{
			return isset($namespaceParts[count($namespaceParts) - 2]) ? $namespaceParts[count($namespaceParts) - 2] . 'Table' : '';
		}

		return $lastNamespacePart . 'Table';
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  mixed  The data for the form.
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$app  = Factory::getApplication();
		$data = $app->getUserState($this->option . '.edit.' . $this->getName() . '.data', array());

		if (empty($data))
		{
			$data = $this->getItem();
		}

		// If there are params fieldsets in the form it will fail with a registry object
		if (isset($data->params) && $data->params instanceof Registry)
		{
			$data->params = $data->params->toArray();
		}

		$this->preprocessData($this->option . '.edit.' . $this->getName(), $data);

		return $data;
	}
}
