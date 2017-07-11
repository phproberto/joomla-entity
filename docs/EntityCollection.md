# Entity collection class

`Phproberto\Joomla\Entity\EntityCollection`

> Class to perform common actions on groups of entities.

## Index <a id="index"></a>

* [Usage](#usage)
* [Methods](#methods)
    * [add(EntityInterface $entity)](#add)
    * [clear()](#clear)
    * [count()](#count)
    * [current()](#current)
    * [has($id)](#has)
    * [get($id)](#get)
    * [ids()](#ids)
    * [isEmpty()](#isEmpty)
    * [key()](#key)
    * [merge(EntityCollection $collection)](#merge)
    * [next()](#next)
    * [remove($id)](#remove)
    * [rewind()](#rewind)
    * [valid()](#valid)
    * [write(EntityInterface $entity, $overwrite = true)](#write)

## Usage <a id="usage"></a>

A couple of fast examples:

```php
<?php
JLoader::import('phproberto_entity.library');

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Content\Article;
use Phproberto\Joomla\Entity\EntityCollection;

$articles = new EntityCollection(array(Article::instance(69), Article::instance(70), Article::instance(71)));

// Collection implements Iterator so you can traverse it like
foreach ($articles as $articleId => $article)
{
    echo $articleId . '. ' . $article->get('title') . '<br />';
}

// It also implements Countable
echo 'Collecton has ' . $articles->count() . ' entities <br />';

// Other entities can return collections
$article = Article::instance(71);

foreach ($article->getTags() as $tagId => $tag)
{
    echo 'Tag ' . $tagId . '. ' . $tag->get('title') . '<br />';
}

// Check if an article is in the collection
if ($articles->has(69))
{
    echo $articles->get(69)->getId() . '. ' . $articles->get(69)->get('title') . '<br />';
}

// Add entities
$articles->add(Article::instance(72));

// Remove entities
$articles->remove(69);

// Get the ids of the entities in the collection
var_dump($articles->ids());

// Clear the collection
$articles->clear();

// Check if collection is empty
if ($articles->isEmpty())
{
    echo 'Collection is empty <br />';
}

```

### add(EntityInterface $entity) <a id="add"></a>

> Add an entity to the collection.

**Parameters:**

* `EntityInterface` *$entity (required):* Entity to add.

**Returns:**

`boolean`

**Examples:**

```php
$collection = new EntityCollection;

$collection->add(Article::instance(1));
```

### clear() <a id="clear"></a>

> Clears all the entities of the collection.

**Parameters:**

None

**Returns:**

`self`

**Examples:**

```php
$collection = new EntityCollection;

$collection->add(Article::instance(1));
$collection->add(Article::instance(2));

// Will echo 2
echo $collection->count();

$collection->clear();

// Will echo 0
echo $collection->count();
```

### count() <a id="count"></a>

> Get the count of entities in this collection.

**Parameters:**

None

**Returns:**

`integer`

**Examples:**

```php
$collection = new EntityCollection;

$collection->add(Article::instance(1));
$collection->add(Article::instance(2));

// Will echo 2
echo $collection->count();
```

### current() <a id="current"></a>

> Get the active entity for iterations. Part of the iterator implementation.

**Parameters:**

None

**Returns:**

`mixed` EntityInterface | FALSE for no entities

**Examples:**

```php
$collection = new EntityCollection(array(Article::instance(69), Article::instance(70), Article::instance(71)));

while ($collection->valid())
{
    $article = $collection->current();
    echo $article->getId() . '. ' . $article->get('title') . '<br />';
    $collection->next();
}
```

### has($id) <a id="has"></a>

> Check if an entity is present in this collection.

**Parameters:**

* `integer` *$id (required):* Entity identifier.

**Returns:**

`boolean`

**Examples:**

```php
$collection = new EntityCollection(array(Article::instance(69), Article::instance(70), Article::instance(71)));

// Will return true
var_dump($collection->has(69));

// Will return false
var_dump($collection->has(13));
```

### get($id) <a id="get"></a>

> Get an entity by its identifier.

**Parameters:**

* `integer` *$id (required):* Entity identifier.

**Returns:**

`EntityInterface`

**Examples:**

```php
$articles = new EntityCollection(array(Article::instance(70), Article::instance(69), Article::instance(71)));

// Will echo 69
var_dump($articles->get(69)->getId());

// Trying to retrieve an unexisting entity will throw an exception
try
{
    $article = $articles->get(999);
}
catch (\InvalidArgumentException $e)
{
    $article = $articles->get(69);
}

// Will echo 69 because retrieving 999 threw an exception
var_dump($articles->get(69)->getId());
```

### ids() <a id="ids"></a>

> Returns ids of the entities in this collection in the order they were added.

**Parameters:**

None

**Returns:**

`array`

**Examples:**

```php
$collection = new EntityCollection(array(Article::instance(70), Article::instance(69), Article::instance(71)));

// Will return: [70, 69, 71]
var_dump($collection->ids());
```

### isEmpty() <a id="isEmpty"></a>

> Check if the collection is empty.

**Parameters:**

None

**Returns:**

`boolean`

**Examples:**

```php
$collection = new EntityCollection;

// Will return true
var_dump($collection->isEmpty());

$collection = new EntityCollection(array(Article::instance(70), Article::instance(69), Article::instance(71)));

// Will return: false
var_dump($collection->isEmpty());
```

### key() <a id="key"></a>

> Return the id of the active entity.

**Parameters:**

None

**Returns:**

`integer`

**Examples:**

```php
$collection = new EntityCollection(array(Article::instance(69), Article::instance(70), Article::instance(71)));

while ($collection->valid())
{
    $article = $collection->current();
    echo $collection->key() . '. ' . $article->get('title') . '<br />';
    $collection->next();
}
```

### merge(EntityCollection $collection) <a id="merge"></a>

> Merge another collection into this collection.

**Parameters:**

* `EntityCollection` *$collection (required):* Collection to merge.

**Returns:**

`self`

**Examples:**

```php
$articles = new EntityCollection(array(Article::instance(69), Article::instance(70)));
$articles2 = new EntityCollection(array(Article::instance(71), Article::instance(72)));

// Will show [69, 70, 71, 72]
var_dump($articles->merge($articles2)->ids());
```

### next() <a id="next"></a>

> Gets the next entity. Part of the iterator implementation. 

**Parameters:**

None

**Returns:**

`mixed` EntityInterface | FALSE if no entities

**Examples:**

```php
$collection = new EntityCollection(array(Article::instance(69), Article::instance(70), Article::instance(71)));

$article = $collection->current();

// It will echo something like: 69. Quick Icons
echo $article->getId() . '. ' . $article->get('title') . '<br />';

$article = $collection->next();

// It will echo something like: 70. Smart Search
echo $article->getId() . '. ' . $article->get('title') . '<br />';

$article = $collection->next();

// It will echo something like: 71. Similar Tags
echo $article->getId() . '. ' . $article->get('title') . '<br />';
```

### remove($id) <a id="remove"></a>

> Remove an entity from the collection.

**Parameters:**

* `integer` *$id (required):* Entity identifier.

**Returns:**

`boolean`

**Examples:**

```php
$collection = new EntityCollection(array(Article::instance(70), Article::instance(69), Article::instance(71)));

// Will print [70, 69, 71]
var_dump($collection->ids());

$collection->remove(69);

// Will print [70, 71]
var_dump($collection->ids());
```

### rewind() <a id="rewind"></a>

> Get the first entity in the collection. Part of the iterator implementation.

**Parameters:**

None

**Returns:**

`mixed` EntityInterface | FALSE if no entities

**Examples:**

```php
$articles = new EntityCollection(array(Article::instance(70), Article::instance(69), Article::instance(71)));

foreach ($articles as $article)
{
    echo $article->getId() . '. ' . $article->get('title') . '<br />';
}

// Will echo 71
echo '<pre>'; print_r($article->getId()); echo '</pre>';

$article = $articles->rewind();

// Will echo 70
echo '<pre>'; print_r($article->getId()); echo '</pre>';
```

### valid() <a id="valid"></a>

> Check if there are still entities in the entities array. Part of the iterator implementation.

**Parameters:**

None

**Returns:**

`boolean`

**Examples:**

```php
$collection = new EntityCollection(array(Article::instance(69), Article::instance(70), Article::instance(71)));

while ($collection->valid())
{
    $article = $collection->current();
    echo $collection->key() . '. ' . $article->get('title') . '<br />';
    $collection->next();
}
```

### write(EntityInterface $entity, $overwrite = true) <a id="write"></a>

> Add an entity to the collection.

**Parameters:**

* `EntityInterface` *$entity (required)   :* Entity to write.
* `boolean`         *$overwrite (optional):* Force writing the entity if it already exists

**Returns:**

`boolean`

**Examples:**

```php
$collection = new EntityCollection;

$collection->add(Article::instance(1));
```
