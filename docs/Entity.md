# Base entity

`Phproberto\Joomla\Entity\Entity`

> This is the base entity that you can extend to easily connect your custom entities.

## Index <a id="index"></a>

* [Usage](#usage)
* [Methods](#methods)
    * [all()](#all)
    * [assign($property, $value)](#assign)
    * [bind(array $data)](#bind)
    * [date($property, $tz = true)](#date)
    * [fetch($id)](#fetch)
    * [fetchRow($id)](#fetchRow)
    * [get($property, $default = null)](#get)
    * [has($property)](#has)
    * [hasId()](#hasId)
    * [id()](#id)
    * [isLoaded()](#isLoaded)
    * [json($property)](#json)
    * [load()](#load)
    * [primaryKey()](#primaryKey)
    * [registry($property)](#registry)
    * [save()](#save)
    * [showDate($property, array $options = [])](#showDate)
    * [table($name = '', $prefix = null, $options = array())](#table)
    * [unassign($property)](#unassign)

## Usage <a id="usage"></a>

This is an abstract class. You cannot use it directly but extend it to inherit all its logic. 

How to use it as base class for your entity:

```php
<?php
namespace My\Namespace;

use Phproberto\Joomla\Entity\Entity;

/**
 * Sample entity declaration.
 *
 * @since   __DEPLOY_VERSION__
 */
class Sample extends Entity
{
}
```

## Methods <a id="methods"></a>

Extending this class your entity will have this methods available.  

### all() <a id="all"></a>

> Get all the entity properties.

**Parameters:**

None

**Returns:**

`array`

**Examples:**

```php
$article = Article::instance(74);

foreach ($article->all() as $property => $value)
{
    echo '<h3> ' . $property . '</h3>';
    echo '<pre>'; print_r($value); echo '</pre>';
}
```

### assign($property, $value) <a id="assign"></a>

> Assign a value to an entity property.

**Parameters:**

* `string` *$property (required):* Property to set the value.
* `mixed` *$value (required):* Value to assign.

**Returns:**

`self`

**Examples:**

```php
$article->assign('title', 'My new title');

// This will return the new title
$article->get('title');
```

### bind($data) <a id="bind"></a>

> Bind data to the entity.

**Parameters:**

* `mixed` *$data (required):* Data to bind. Array or stdClass object

**Returns:**

`self`

**Throws:**

`\InvalidArgumentException` Invalid data received.

**Examples:**

```php

```

### date($property, $tz = true) <a id="date"></a>

> Get an \JDate object from an entity date property. Useful for operations with dates.  

**Parameters:**

* `string` *$property (required):* Property where date is stored.
* `mixed`  *$tz (optional):* Time zone to be used for the date. Special cases: 
    * boolean true for user setting
    * boolean false for server setting.

**Returns:**

`JDate`

**Throws:**

`\InvalidArgumentException` If date property is empty.

**Examples:**

```php
$article = Article::instance(74);

// Use \DateTime::format() function
echo $article->date('modified')->format('Y-m-d H:i:s');

// Use date objects for comparisons
$today = new \JDate;
$modified = $article->date('modified');

// Displays `Article was modified +2 days ago`
if ($modified < $today)
{
    $ago = $article->date('modified')->diff($today);

    echo "Article was modified " . $ago->format('%R%a days ago');
}
elseif ($modified == $today)
{
    echo "Article hasn't been  modified ";
}
```

### loadFromData(array $data) <a id="loadFromData"></a>

> Tries to load an entity with columns matching passed data. Quite similar to Table::load() method.

**Parameters:**

* `array` *$data (required):* Data to search the entity.

**Returns:**

`static`  Returns a loaded entity if found or an unloaded entity if not.

**Examples:**

```php
// Searching by an existing title will return an Article instance. Ensure you test if it's loaded.
$article = Article::loadFromData(['title' => 'Existing title']);

if ($article->isLoaded())
{
    echo 'We have an article with title `' . $article->get('title') . '` in the `' . $article->category()->get('title') . '` category';
}

// Searching by an unexisting title will still return an article. Ensure you test if it's loaded.
$article = Article::loadFromData(['title' => 'Unexisting title']);

if (!$article->isLoaded())
{
    echo 'Article not found!';
}
```

### registry($property) <a id="registry"></a>

> Get a Registry object from a property of the entity.

**Parameters:**

* `string` *$property (required):* Property with the Registry dat source.

**Returns:**

`Joomla\Registry\Registry`

**Throws:**

`\InvalidArgumentException` Property does not exist. If you are not sure if a property exist use has($property) before accessing to it.

**Examples:**

```php
// Defaults for article metadata
echo $article->registry('metadata')->get('author', 'Roberto Segura');

// Defaults for article images
echo '<img src="' . $article->registry('images')->get('image_intro', 'images/joomla_black.png') . '" />';
```

### showDate($property, $format = 'DATE_FORMAT_LC1', array $options = array()) <a id="showDate"></a>

> Get an entity date field formatted.

**Parameters:**

* `string` *$property (required):* Property where date is stored.
* `string` *$format (required):* PHP date format or language string containing it. Defaults to `DATE_FORMAT_LC1`
* `array`  *$options (optional):* Supported options:
    * `gregorian`: True to use Gregorian calendar.
    * `tz`: Time zone to be used for the date.  Special cases:
        * boolean true for user setting
        * boolean false for server setting.

**Returns:**

`string`

**Throws:**

`\InvalidArgumentException` If date property is empty.

**Examples:**

```php
// Shows Friday, 28 July 2017
echo $article->showDate('modified');

// Shows 2017-07-28 08:31:36
echo $article->showDate('modified', 'Y-m-d H:i:s');

// Shows Friday, 28 July 2017 08:31
echo $article->showDate('modified', 'DATE_FORMAT_LC2');
```
