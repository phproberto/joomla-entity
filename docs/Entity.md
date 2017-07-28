# Base entity

`Phproberto\Joomla\Entity\Entity`

> This is the base entity that you can extend to easily connect your custom entities.

## Index <a id="index"></a>

* [Usage](#usage)
* [Methods](#methods)
    * [all()](#all)
    * [assign($property, $value)](#assign)
    * [bind(array $data)](#bind)
    * [date($property, array $options = [])](#date)
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

### date($property, array $options = array()) <a id="date"></a>

> Get an entity date field formatted.

**Parameters:**

* `string` *$property (required):* Property where date is stored.
* `mixed`  *$tz (optional):* Time zone to be used for the date. Special cases: 
    * boolean true for user setting
    * boolean false for server setting.

**Returns:**

`JDate`

**Examples:**

```php
$article = Article::instance(74);

echo $article->date('modified')->format('Y-m-d H:i:s');
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
$article = Article::instance(74);

if ($article->has('images'))
{
    // Display intro image and fallback to `joomla_black.png` if no image is et
    echo '<img src="' . $article->registry('images')->get('image_intro', 'images/joomla_black.png') . '" />';
}
```
