# HasAccess trait

`Phproberto\Joomla\Entity\Traits\HasAccess`

> Trait for classes that have an `access` column to specify the entity access view level.

## Index  

* [Requirements](#requirements)
* [Usage](#usage)
* [Methods](#methods)
    * [canAccess($reload = false)](#canAccess)
    * [getAccess()](#getAccess)

## Requirements <a id="requirements"></a>

Class expects that the entity database row includes an `access` column. If your entity uses a different column to store the access you can include override the `getColumnAccess()` method like:

```php
/**
 * Get the name of the column that stores access.
 *
 * @return  string
 */
protected function getColumnAccess()
{
	return 'access_level';
}

```

## Usage <a id="usage"></a>

To start using this trait you have to include in your class the line:

```php
use Phproberto\Joomla\Entity\Traits\HasAccess;
```

And then include the `use` statement inside the class like:

```php
class Article extends Entity
{
	use HasAccess;
}
```

## Methods <a id="methods"></a>

When implementing this trait you can start using following methods in your entity:

### canAccess($reload = false) <a id="canAccess"></a>

> Check if current/active user can access this entity.

**Parameters:**

* `boolean` *$reload (optional):* Force to recheck access for current user. Useful in case you have done some changes to the entity.

**Returns:**

`boolean`

**Examples:**

```php
// Use a different link based on access
$link = $article->canAccess() ? $article->getLink() : JRoute::_('index.php?option=com_users&view=login');

// Do something based on the access
if ($article->canAccess())
{
	// Show/do something
}
```

### getAccess() <a id="getAccess"></a>

> Get access level required for this entity.

**Parameters:**

None

**Returns:**

`integer`

**Examples:**

```php
if (0 === $article->getAccess())
{
	// 0 is usually public access level but this is a shit check
}
```