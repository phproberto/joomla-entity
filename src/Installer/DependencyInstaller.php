<?php
/**
 * Joomla! entity library.
 *
 * @copyright  Copyright (C) 2017-2019 Roberto Segura López, Inc. All rights reserved.
 * @license    See COPYING.txt
 */

namespace Phproberto\Joomla\Entity\Installer;

use Joomla\CMS\Factory;
use Joomla\Registry\Registry;
use Joomla\CMS\Installer\Installer;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerHelper;

defined('_JEXEC') || die;

/**
 * Extensions dependency installer.
 *
 * @since  __DEPLOY_VERSION__
 */
final class DependencyInstaller
{
	/**
	 * Installer instance
	 *
	 * @var  Installer
	 */
	public $installer;

	/**
	 * Extension manifest
	 *
	 * @var  \SimpleXMLElement
	 */
	protected $manifest;

	/**
	 * Parent installer.
	 *
	 * @var  InstallerAdapter
	 */
	protected $parent;

	/**
	 * Constructor.
	 *
	 * @param   \SimpleXMLElement  $manifest   Extension manifest
	 * @param   InstallerAdapter   $parent     Parent installer
	 * @param   Installer          $installer  Extension installer
	 */
	public function __construct(\SimpleXMLElement $manifest, InstallerAdapter $parent, Installer $installer = null)
	{
		$this->manifest = $manifest;
		$this->parent = $parent;
		$this->installer = $installer ?: new Installer;
	}

	/**
	 * Install dependencies.
	 *
	 * @return  void
	 */
	public function install()
	{
		$dependencies = $this->manifest->dependencies;

		if (empty($dependencies))
		{
			return;
		}

		foreach ($dependencies->dependency as $dependency)
		{
			$description = trim((string) $dependency);
			$type  = (string) $dependency->attributes()->type;
			$name  = (string) $dependency->attributes()->name;
			$group = (string) $dependency->attributes()->group;
			$file  = (string) $dependency->attributes()->file;
			$url   = (string) $dependency->attributes()->url;
			$size   = (int) (string) $dependency->attributes()->size;
			$hash   = trim((string) $dependency->attributes()->hash);
			$version   = trim((string) $dependency->attributes()->version);
			$upgradeableVersion   = trim((string) $dependency->attributes()->upgradeableVersion);

			$extension = $this->searchExtension($name, $type, null, $group);

			if ($extension)
			{
				$extensionManifest = new Registry($extension->{'manifest_cache'});

				$existingVersion = $extensionManifest->get('version');

				if (!$existingVersion)
				{
					$msg = sprintf(
						'Error installing dependency `%s`: unable to determine installed version.',
						$description
					);

					throw new \RuntimeException($msg);
				}

				$isOkVersion = preg_match('/^' . $version . '/', $existingVersion);

				if ($isOkVersion)
				{
					continue;
				}

				$isUpgradeable = $upgradeableVersion && preg_match('/^' . $upgradeableVersion . '/', $existingVersion);

				// There is a version that does not match requirements. Let the user to solve the issue.
				if (!$isUpgradeable)
				{
					$msg = sprintf(
						'Error installing dependency `%s`: unable to satisfy dependency version. Installed: %s. Requirements: %s.',
						$description,
						$existingVersion,
						$version
					);

					throw new \RuntimeException($msg);
				}
			}

			if (empty($size))
			{
				$msg = sprintf(
					'Error installing dependency `%s`: missing expected file size in manifest.',
					$description
				);

				throw new \RuntimeException($msg);
			}

			if (empty($hash))
			{
				$msg = sprintf(
					'Error installing dependency `%s`: missing expected file hash in manifest.',
					$description
				);

				throw new \RuntimeException($msg);
			}

			if (empty($file) && empty($url))
			{
				$msg = sprintf(
					'Error installing dependency `%s`: missing file/URL in manifest.',
					$description
				);

				throw new \RuntimeException($msg);
			}

			$folder = (string) $dependencies->folder;
			$source = $this->parent->getParent()->getPath('source');

			if ($folder)
			{
				$source .= '/' . $folder;
			}

			if (!empty($file))
			{
				$filePath = $source . '/' . (string) $file;
			}
			elseif (!empty($url))
			{
				$fileName = InstallerHelper::downloadPackage($url);

				if (false === $fileName)
				{
					$msg = sprintf(
						'Error installing dependency `%s`: failed to download file from `%s`.',
						$description,
						$url
					);

					throw new \RuntimeException($msg);
				}

				$filePath = Factory::getConfig()->get('tmp_path') . '/' . $fileName;
			}

			if (!is_file($filePath))
			{
				$msg = sprintf(
					'Error installing dependency `%s`: missing file `%s`.',
					$description,
					$filePath
				);

				throw new \RuntimeException($msg);
			}

			$fileSize = @filesize($filePath);

			if (false === $fileSize || $fileSize !== $size)
			{
				$msg = sprintf(
					'Error installing dependency `%s`: wrong dependency file size `%s`.',
					$description,
					$filePath
				);

				throw new \RuntimeException($msg);
			}

			$fileHash = @md5_file($filePath);

			if (false === $fileHash || $fileHash !== $hash)
			{
				$msg = sprintf(
					'Error installing dependency `%s`: wrong dependency file hash `%s`.',
					$description,
					$filePath
				);

				throw new \RuntimeException($msg);
			}

			$package = InstallerHelper::unpack($filePath, true);

			if (false === $package)
			{
				$msg = sprintf(
					'Error installing dependency `%s`: Error unpacking package `%s`.',
					$description,
					$filePath
				);

				throw new \RuntimeException($msg);
			}

			if (!$this->installer->install($package['dir']))
			{
				$msg = sprintf(
					'Error installing dependency `%s`: Could not install extracted package from `%s`.',
					$description,
					$package['dir']
				);

				throw new \RuntimeException($msg);
			}
		}
	}

	/**
	 * Search a extension in the database
	 *
	 * @param   string  $element  Extension technical name/alias
	 * @param   string  $type     Type of extension (component, file, language, library, module, plugin)
	 * @param   string  $state    State of the searched extension
	 * @param   string  $folder   Folder name used mainly in plugins
	 *
	 * @return  integer           Extension identifier
	 */
	protected function searchExtension($element, $type, $state = null, $folder = null)
	{
		$db = Factory::getDBO();
		$query = $db->getQuery(true)
			->select('*')
			->from($db->quoteName("#__extensions"))
			->where("type = " . $db->quote($type))
			->where("element = " . $db->quote($element));

		if (!is_null($state))
		{
			$query->where("state = " . (int) $state);
		}

		if (!is_null($folder))
		{
			$query->where("folder = " . $db->quote($folder));
		}

		$db->setQuery($query);

		return $db->loadObject();
	}
}
