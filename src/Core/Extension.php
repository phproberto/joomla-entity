<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Core;

use Phproberto\Joomla\Entity\Entity;
use Phproberto\Joomla\Entity\Core\Traits as CoreTraits;
use Phproberto\Joomla\Entity\Traits;

/**
 * Extension entity.
 *
 * @since   __DEPLOY_VERSION__
 */
class Extension extends Entity
{
	use CoreTraits\HasClient;
	use Traits\HasParams;

	/**
	 * Component extension type
	 *
	 * @const
	 */
	const TYPE_COMPONENT = 'component';

	/**
	 * File extension type
	 *
	 * @const
	 */
	const TYPE_FILE = 'file';

	/**
	 * Language extension type
	 *
	 * @const
	 */
	const TYPE_LANGUAGE = 'language';

	/**
	 * Library extension type
	 *
	 * @const
	 */
	const TYPE_LIBRARY = 'library';

	/**
	 * Component extension type
	 *
	 * @const
	 */
	const TYPE_MODULE = 'module';

	/**
	 * Package extension type
	 *
	 * @const
	 */
	const TYPE_PACKAGE = 'package';

	/**
	 * Plugin extension type
	 *
	 * @const
	 */
	const TYPE_PLUGIN = 'plugin';

	/**
	 * Template extension type
	 *
	 * @const
	 */
	const TYPE_TEMPLATE = 'template';

	/**
	 * Check if this extension is a component.
	 *
	 * @return  boolean
	 */
	public function isComponent()
	{
		return $this->isType(self::TYPE_COMPONENT);
	}

	/**
	 * Check if this extension is a file.
	 *
	 * @return  boolean
	 */
	public function isFile()
	{
		return $this->isType(self::TYPE_FILE);
	}

	/**
	 * Check if this extension is a language.
	 *
	 * @return  boolean
	 */
	public function isLanguage()
	{
		return $this->isType(self::TYPE_LANGUAGE);
	}

	/**
	 * Check if this extension is a library.
	 *
	 * @return  boolean
	 */
	public function isLibrary()
	{
		return $this->isType(self::TYPE_LIBRARY);
	}

	/**
	 * Check if this extension is a module.
	 *
	 * @return  boolean
	 */
	public function isModule()
	{
		return $this->isType(self::TYPE_MODULE);
	}

	/**
	 * Check if this extension is a package.
	 *
	 * @return  boolean
	 */
	public function isPackage()
	{
		return $this->isType(self::TYPE_PACKAGE);
	}

	/**
	 * Check if this extension is a plugin.
	 *
	 * @return  boolean
	 */
	public function isPlugin()
	{
		return $this->isType(self::TYPE_PLUGIN);
	}

	/**
	 * Check if this extension is a template.
	 *
	 * @return  boolean
	 */
	public function isTemplate()
	{
		return $this->isType(self::TYPE_TEMPLATE);
	}

	/**
	 * Check if this extension is of a specific type.
	 *
	 * @param   string   $type  Type to check
	 *
	 * @return  boolean
	 */
	public function isType($type)
	{
		return strtolower($this->get('type')) === strtolower($type);
	}

	/**
	 * Get entity primary key column.
	 *
	 * @return  string
	 */
	public function primaryKey()
	{
		return 'extension_id';
	}

	/**
	 * Get a table.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return  \JTable
	 *
	 * @codeCoverageIgnore
	 */
	public function table($name = '', $prefix = null, $options = array())
	{
		$name = $name ?: 'extension';
		$prefix = $prefix ?: 'JTable';

		return parent::table($name, $prefix, $options);
	}
}