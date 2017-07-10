# Entity collection class

`Phproberto\Joomla\Entity\EntityCollection`

> Class to perform common actions for groups of entities.

## Index <a id="index"></a>

* [Usage](#usage)
* [Methods](#methods)
    * [add(EntityInterface $entity)](#add)
    * [clear()](#clear)
    * [count()](#count)
    * [current()](#current)
    * [has($id)](#has)
    * [ids()](#ids)
    * [isEmpty()](#isEmpty)
    * [key()](#key)
    * [next()](#next)
    * [remove($id)](#remove)
    * [rewind()](#rewind)
    * [valid()](#valid)
    * [write(EntityInterface $entity, $overwrite = true)](#write)

## Usage <a id="usage"></a>

This is a fast example:

```php
<?php
JLoader::import('phproberto_entity.library');

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Content\Article;
use Phproberto\Joomla\Entity\EntityCollection;

\JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');

$model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

$model->setState('params', new Registry);

$model->setState('list.start', 0);
$model->setState('list.limit', 5);
$model->setState('filter.published', 1);

$articles = array_map(
	function($item) {
		$article = Article::instance($item->id);

		return $article;
	}
	,
	$model->getItems() ?: array()
);


$collection = new EntityCollection($articles);

foreach ($collection as $id => $article)
{
	if (!$article->canAccess())
	{
		continue;
	}
	echo '<pre>'; print_r('You have access to ' . $id . '. ' . $article->get('title')); echo '</pre>';
}
```
