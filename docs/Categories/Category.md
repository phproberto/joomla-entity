# Category entity 

`Phproberto\Joomla\Entity\Categories\Category`

> Represents a com_categories category.

## Index <a id="index"></a>

* [Usage](#usage)
* [Methods](#methods)
    * [Traits](#traits)
    * [table($name = '', $prefix = null, $options = array())](#table)

## Usage <a id="usage"></a>

To start using this entity you have to load the `phproberto_library` and add the use statement like:

```php
\JLoader::import('phproberto_entity.library');

use Phproberto\Joomla\Entity\Categories\Category;

// Load the category with id = 1
$category = Category::instance(1);
```

## Methods <a id="methods"></a>

This entity has these methods available:

### Traits <a id="traits"></a>

This class allows you to use methods defined in these traits:

* [Phproberto\Joomla\Entity\Core\Traits\HasAccess](../Core/Traits/HasAccess.md)
* [Phproberto\Joomla\Entity\Core\Traits\HasAsset](../Core/Traits/HasAsset.md)
* [Phproberto\Joomla\Entity\Core\Traits\HasMetadata](../Core/Traits/HasMetadata.md)
* [Phproberto\Joomla\Entity\Traits\HasAssociations](../Traits/HasAssociations.md)
* [Phproberto\Joomla\Entity\Traits\HasParams](../Traits/HasParams.md)
* [Phproberto\Joomla\Entity\Traits\HasTranslations](../Traits/HasTranslations.md)
* [Phproberto\Joomla\Entity\Users\Traits\HasAuthor](../Users/Traits/HasAuthor.md)
* [Phproberto\Joomla\Entity\Users\Traits\HasEditor](../Users/Traits/HasEditor.md)

### table($name = '', $prefix = null, $options = array()) <a id="table"></a>

> Get a table instance. Defauts to \CategoriesTableCategory. Most of the times you don't want to access this because entity handles most stuff that requires table access.

**Parameters:**

* `string` *$name (optional):* Table name. Defaults to `Content`.
* `string` *$prefix (optional):* Class prefix. Defaults to `JTable`.
* `array`  *$options (optional):* Configuration array for the table.

**Returns:**

`\JTable`

**Throws:**

`\InvalidArgumentException` if table cannot be loaded.

**Examples:**

```php
$category = Category::instance(34);

// Load another category through \CategoriesTableCategory
$table = $category->table();
$table->load(35);
```