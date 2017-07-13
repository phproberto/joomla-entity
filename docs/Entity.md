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
