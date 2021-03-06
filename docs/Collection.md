# Collection..

`Phproberto\Joomla\Entity\Collection`

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
    * [intersect(Collection $collection)](#intersect)
    * [isEmpty()](#isEmpty)
    * [key()](#key)
    * [krsort()](#krsort)
    * [ksort()](#ksort)
    * [map(callable $function)](#map)
    * [merge(Collection $collection)](#merge)
    * [next()](#next)
    * [remove($id)](#remove)
    * [rewind()](#rewind)
    * [sort(callable $function)](#sort)
    * [sortBy($property, $direction = self::DIRECTION_ASCENDING)](#sortBy)
    * [sortByDesc($property](#sortByDesc)
    * [toObjects()](#toObjects)
    * [valid()](#valid)
    * [write(EntityInterface $entity, $overwrite = true)](#write)

## Usage <a id="usage"></a>

A couple of fast examples:

```php
<?php
JLoader::import('phproberto_entity.library');

use Joomla\Registry\Registry;
use Phproberto\Joomla\Entity\Content\Article;
use Phproberto\Joomla\Entity\Collection;

$articles = new Collection(array(Article::instance(69), Article::instance(70), Article::instance(71)));

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
    echo $articles->get(69)->id() . '. ' . $articles->get(69)->get('title') . '<br />';
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
$collection = new Collection;

$collection->add(Article::instance(1));
```

### all() <a id="all"></a>

> Get all the entities in the collection as array. Most of the times you don't want to use this because collection already behaves like an array.

**Parameters:**

None

**Returns:**

`array`

**Examples:**

```php
$collection = new Collection(array(Article::instance(70), Article::instance(69)));

foreach ($collection->all() as $entity)
{
    echo $entity->id() . '. ' . $article->get('title') . '<br />';
}
```

### clear() <a id="clear"></a>

> Clears all the entities of the collection.

**Parameters:**

None

**Returns:**

`self`

**Examples:**

```php
$collection = new Collection;

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
$collection = new Collection;

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
$collection = new Collection(array(Article::instance(69), Article::instance(70), Article::instance(71)));

while ($collection->valid())
{
    $article = $collection->current();
    echo $article->id() . '. ' . $article->get('title') . '<br />';
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
$collection = new Collection(array(Article::instance(69), Article::instance(70), Article::instance(71)));

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
$articles = new Collection(array(Article::instance(70), Article::instance(69), Article::instance(71)));

// Will echo 69
var_dump($articles->get(69)->id());

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
var_dump($articles->get(69)->id());
```

### ids() <a id="ids"></a>

> Returns ids of the entities in this collection in the order they were added.

**Parameters:**

None

**Returns:**

`array`

**Examples:**

```php
$collection = new Collection(array(Article::instance(70), Article::instance(69), Article::instance(71)));

// Will return: [70, 69, 71]
var_dump($collection->ids());
```

### intersect(Collection $collection) <a id="intersect"></a>

> Get a new collection containing entities present in two collections.

**Parameters:**

* `Collection` *$collection (required):* Collection to intersect.

**Returns:**

`static`

**Examples:**

```php
$articles = new Collection(array(Article::instance(69), Article::instance(70)));
$articles2 = new Collection(array(Article::instance(69), Article::instance(72)));

// Will show [69]
var_dump($articles->intersect($articles2)->ids());
```

### isEmpty() <a id="isEmpty"></a>

> Check if the collection is empty.

**Parameters:**

None

**Returns:**

`boolean`

**Examples:**

```php
$collection = new Collection;

// Will return true
var_dump($collection->isEmpty());

$collection = new Collection(array(Article::instance(70), Article::instance(69), Article::instance(71)));

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
$collection = new Collection(array(Article::instance(69), Article::instance(70), Article::instance(71)));

while ($collection->valid())
{
    $article = $collection->current();
    echo $collection->key() . '. ' . $article->get('title') . '<br />';
    $collection->next();
}
```

### krsort() <a id="krsort"></a>

> Sort collection entities reversely by id.

**Parameters:**

None

**Returns:**

`static`  New collection with entities ordered

**Examples:**

```php
$articles = new Collection(array(Article::instance(69), Article::instance(70), Article::instance(71)));

$articles->krsort();

// Will print [71, 70, 69]
var_dump($articles->ids());

```

### ksort() <a id="ksort"></a>

> Sort collection entities by id.

**Parameters:**

None

**Returns:**

`static`  New collection with entities ordered

**Examples:**

```php
$articles = new Collection(array(Article::instance(70), Article::instance(60), Article::instance(71)));

$articles->ksort();

// Will print [60, 70, 71]
var_dump($articles->ids());

```

### map(callable $function) <a id="map"></a>

> Execute a function on all the items in the collection.

**Parameters:**

* `callable` *$function (required)   :* Function to execute. It will receive the entity as first parameter.

**Returns:**

`static`  New collection with modified entities

**Examples:**

```php
$articles = new Collection(array(Article::instance(70), Article::instance(60), Article::instance(71)));

// Change the title of all the articles
$articles->map(
    function($article)
    {
        $article->assign('title', $article->get('title') . ' edited');

        return $article;
    }
);
```

### merge(Collection $collection) <a id="merge"></a>

> Create a new collection containing elements from 2 collections.

**Parameters:**

* `Collection` *$collection (required):* Collection to merge.

**Returns:**

`static`

**Examples:**

```php
$articles = new Collection(array(Article::instance(69), Article::instance(70)));
$articles2 = new Collection(array(Article::instance(71), Article::instance(72)));

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
$collection = new Collection(array(Article::instance(69), Article::instance(70), Article::instance(71)));

$article = $collection->current();

// It will echo something like: 69. Quick Icons
echo $article->id() . '. ' . $article->get('title') . '<br />';

$article = $collection->next();

// It will echo something like: 70. Smart Search
echo $article->id() . '. ' . $article->get('title') . '<br />';

$article = $collection->next();

// It will echo something like: 71. Similar Tags
echo $article->id() . '. ' . $article->get('title') . '<br />';
```

### remove($id) <a id="remove"></a>

> Remove an entity from the collection.

**Parameters:**

* `integer` *$id (required):* Entity identifier.

**Returns:**

`boolean`

**Examples:**

```php
$collection = new Collection(array(Article::instance(70), Article::instance(69), Article::instance(71)));

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
$articles = new Collection(array(Article::instance(70), Article::instance(69), Article::instance(71)));

foreach ($articles as $article)
{
    echo $article->id() . '. ' . $article->get('title') . '<br />';
}

// Will echo 71
echo '<pre>'; print_r($article->id()); echo '</pre>';

$article = $articles->rewind();

// Will echo 70
echo '<pre>'; print_r($article->id()); echo '</pre>';
```

### sort(callable $function) <a id="sort"></a>

> Apply custom function to order collection entities.

**Parameters:**

* `callable` *$function (required)   :* Function to order entities.

**Returns:**

`static`  New collection with entities ordered

**Examples:**

```php
$articles = new Collection(array(Article::instance(70), Article::instance(60), Article::instance(71)));

$sortByTitle = function ($entity1, $entity2)
{
    return strcmp($entity1->get('title'), $entity2->get('title'));
};

$reverseSortByTitle = function ($entity1, $entity2)
{
    return strcmp($entity2->get('title'), $entity1->get('title'));
};

$articles->sort($sortByTitle);

echo '<h3>Ascending</h3>';

// This will print article titles ordered alphabetically by title
foreach ($articles as $article)
{
    echo '<pre>'; print_r($article->get('title')); echo '</pre>';
}

$articles->sort($reverseSortByTitle);

echo '<h3>Descending</h3>';

// This will print article titles ordered alphabetically by title in descending order
foreach ($articles as $article)
{
    echo '<pre>'; print_r($article->get('title')); echo '</pre>';
}
```

### sortBy($property) <a id="sortBy"></a>

> Sort entities by a property.

**Parameters:**

* `string` *$property (required):*  Property to use for ordering.
* `string` *$direction (optional):* Ordering direction: 'ASC' | 'DESC'.

**Returns:**

`static`  New collection with entities ordered

**Examples:**

```php
$tags = Article::instance(71)->getTags();

$tags->sortBy('id');

// This will show ids ordered ascendently
foreach ($tags as $tag)
{
    echo '<pre>'; print_r($tag->id()); echo '</pre>';
}

$tags->sortBy('id', $tags::DIRECTION_DESCENDING);

// This will show ids ordered descendently
foreach ($tags as $tag)
{
    echo '<pre>'; print_r($tag->id()); echo '</pre>';
}
```

### sortByDesc($property) <a id="sortByDesc"></a>

> Sort entities in descendent order by a property. This is a fast usage proxy sortBy with descendencing direction.

**Parameters:**

* `string` *$property (required):* Property to use for ordering.

**Returns:**

`static`  New collection with entities ordered

**Examples:**

```php
$tags = Article::instance(71)->getTags();

$tags->sortByDesc('title');

// This will show tags ordered by title descendently
foreach ($tags as $tag)
{
    echo '<pre>'; print_r($tag->get('title')); echo '</pre>';
}
```

### toObjects() <a id="toObjects"></a>

> Get all data from all the entities as objects.

**Parameters:**

None

**Returns:**

`array`

**Examples:**

```php

$articles = new Collection(array(Article::instance(69), Article::instance(70)));

// Will return an array of objects with articles data
var_dump($articles->toObjects());
```

### valid() <a id="valid"></a>

> Check if there are still entities in the entities array. Part of the iterator implementation.

**Parameters:**

None

**Returns:**

`boolean`

**Examples:**

```php
$collection = new Collection(array(Article::instance(69), Article::instance(70), Article::instance(71)));

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
$collection = new Collection;

$collection->add(Article::instance(1));
```
