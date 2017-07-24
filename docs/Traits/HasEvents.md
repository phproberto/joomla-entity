# HasEvents trait

`Phproberto\Joomla\Entity\Traits\HasEvents`

> Trait for entities with events column.

## Index  

* [Requirements](#requirements)
* [Usage](#usage)
* [Methods](#methods)
    * [importPlugin($pluginType)](#importPlugin)
    * [trigger($event, $params = array())](#trigger)

## Requirements <a id="requirements"></a>

This trait is used by default by `Phproberto\Joomla\Entity\Entity` so if your entity is extending that class  and you want to use the standard event system (that uses `joomla_entity` plugins) you don't have to do anything.

## Usage <a id="usage"></a>

If you are extending `Phproberto\Joomla\Entity\Entity` you don't have to do anything. 

Examples:  

```php

\JLoader::import('phproberto_entity.library');

use Phproberto\Joomla\Entity\Content\Article;

// Retrieve article instance
$article = Article::instance(12);

// Trigger an event (by default it will use `joomla_entity` plugins)
$article->trigger('articleVisited');

// You can also manualy import a plugin. This will run the event in `content` + `joomla_entity` plugins.
$article->import('content')->trigger('articleVisited', array(\JFactory::getUser()));
```

The plugin could be something like:  


```php
<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.Sample
 *
 * @copyright   Copyright (C) 2005 - 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

JLoader::import('phproberto_entity.library');

use Phproberto\Joomla\Entity\Content\Article;

/**
 * Sample content plugin.
 *
 * @since  1.0.0
 */
class PlgContentSample extends JPlugin
{
	/**
	 * Article has been visited.
	 *
	 * @param   Article  $article  Article being visited
	 * @param   \JUser   $user     User visiting the article
	 *
	 * @return  void
	 */
	public function articleVisited(Article $article, \JUser $user = null)
	{
		$user = $user ?: \JFactory::getUser();

		if ($user->guest)
		{
			echo '<pre>A guest has visited the article ' . $article->id() . '</pre>';

			return;
		}

		echo '<pre>User ' . $user->get('email') . ' has visited the article ' . $article->id() . '</pre>';
	}
}
```

If your entity is not extending `Phproberto\Joomla\Entity\Entity` you can use the trait like:


```php
use Phproberto\Joomla\Entity\Traits\HasEvents;

class Article extends MyCustomEntity
{
	use HasEvents;
}
```

If you want to customise the plugins used for triggered events you can override the `getEventsPlugins()` method:

```php
use Phproberto\Joomla\Entity\Traits\HasEvents;

class Article extends MyCustomEntity
{
	use HasEvents;

	/**
	 * Get the plugin types that will be used by this entity.
	 *
	 * @return  array
	 */
	protected function getEventsPlugins()
	{
		return array('content', 'system');
	}
}
```

## Methods <a id="methods"></a>

When implementing this trait you can start using following methods in your entity:

### importPlugin($pluginType) <a id="importPlugin"></a>

> Import a plugin type for triggered events.

**Parameters:**

* `string` *$pluginType (required):* Plugin type/folder to import.

**Returns:**

`self`

**Examples:**

```php

\JLoader::import('phproberto_entity.library');

use Phproberto\Joomla\Entity\Content\Article;

// Trigger `articleVisited` event in `content` plugins.
Article::instance(12)
	->import('content')
	->trigger('articleVisited');
```

### trigger($event, $params = array()) <a id="trigger"></a>

> Trigger an entity event.

**Parameters:**

* `string` *$event (required):* Event to launch.
* `array`  *$params (optional):* Optional parameters for the event

**Returns:**

`array`

**Examples:**

```php

\JLoader::import('phproberto_entity.library');

use Phproberto\Joomla\Entity\Content\Article;

// Trigger `articleVisited` event in `joomla_entity` plugins.
Article::instance(12)->trigger('articleVisited');

// Trigger `articleVisited` event in `content` plugins sending a custom user.
Article::instance(12)
	->import('content')
	->trigger('articleVisited', array(\JUser::getInstance(128)));
```
