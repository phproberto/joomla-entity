# HasClient trait

`Phproberto\Joomla\Entity\Core\Traits\HasClient`

> Trait for entities that have an associated client.

## Index  

* [Requirements](#requirements)
* [Usage](#usage)
* [Methods](#methods)

## Requirements <a id="requirements"></a>

Class expects that the entity database row includes an `client_id` column. If your entity uses a different column to store the client identifier you can include override the `columnClient()` method like:

```php
/**
 * Get the name of the column that stores category.
 *
 * @return  string
 */
protected function columnClient()
{
	return 'custom_client_id';
}
```

## Usage <a id="usage"></a>

To start using this trait you have to include in your class the line:

```php
use Phproberto\Joomla\Entity\Core\Traits\HasClient;
```

And then include the `use` statement inside the class like:

```php
class Extension extends Entity
{
	use HasClient;
}
```

## Methods <a id="methods"></a>

When implementing this trait you can start using following methods in your entity:

### client($reload = false) <a id="client"></a>

> Get the associated client.

**Parameters:**

* `boolean` *$reload (optional):* Force to reload client.

**Returns:**

`Phproberto\Joomla\Client\ClientInterface` Site | Administrator client

**Throws:**

`\RuntimeException` if client column is not present

**Examples:**

```php
$class = new ClassWithClient;

if ($class->client()->isAdmin())
{
// Do something
}
```

