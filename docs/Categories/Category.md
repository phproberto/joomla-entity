# Category entity 

`Phproberto\Joomla\Entity\Categories\Category`

> Represents a com_categories category.

## Index <a id="index"></a>

* [Usage](#usage)
* [Methods](#methods)
    * [Traits](#traits)
    * [searchAncestors(array $options = [])](#searchAncestors)
    * [searchChildren(array $options = [])](#searchChildren)
    * [searchDescendants(array $options = [])](#searchDescendants)
    * [table($name = '', $prefix = null, $options = array())](#table)
    * [validator()](#validator)

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
* [Phproberto\Joomla\Entity\Core\Traits\HasAncestors](../Core/Traits/HasAncestors.md)
* [Phproberto\Joomla\Entity\Core\Traits\HasAsset](../Core/Traits/HasAsset.md)
* [Phproberto\Joomla\Entity\Core\Traits\HasAssociations](../Core/Traits/HasAssociations.md)
* [Phproberto\Joomla\Entity\Core\Traits\HasChildren](../Core/Traits/HasChildren.md)
* [Phproberto\Joomla\Entity\Core\Traits\HasDescendants](../Core/Traits/HasDescendants.md)
* [Phproberto\Joomla\Entity\Core\Traits\HasLevel](../Core/Traits/HasLevel.md)
* [Phproberto\Joomla\Entity\Core\Traits\HasMetadata](../Core/Traits/HasMetadata.md)
* [Phproberto\Joomla\Entity\Core\Traits\HasParams](../Core/Traits/HasParams.md)
* [Phproberto\Joomla\Entity\Core\Traits\HasParent](../Core/Traits/HasParent.md)
* [Phproberto\Joomla\Entity\Core\Traits\HasState](../Core/Traits/HasState.md)
* [Phproberto\Joomla\Entity\Core\Traits\HasTranslations](../Core/Traits/HasTranslations.md)
* [Phproberto\Joomla\Entity\Users\Traits\HasAuthor](../Users/Traits/HasAuthor.md)
* [Phproberto\Joomla\Entity\Users\Traits\HasEditor](../Users/Traits/HasEditor.md)
* [Phproberto\Joomla\Entity\Validation\Traits\HasValidation](../Validation\Traits\HasValidation.md)

### searchAncestors(array $options = []) <a id="searchAncestors"></a>

> Search category ancestors. Parent category and parents of the parent category. 

**Parameters:**

* `array`  *$options (optional):* Search options. For filters, limit, ordering, etc.

**Returns:**

`\Phproberto\Joomla\Entity\Collection`

**Examples:**

```php
$category = Category::instance(34);

// Search the category ancestor of first level
$ancestors = $category->searchAncestors(['filter.level' => 1]);
```

### searchChildren(array $options = []) <a id="searchChildren"></a>

> Search category children. These are direct child categories. 

**Parameters:**

* `array`  *$options (optional):* Search options. For filters, limit, ordering, etc.

**Returns:**

`\Phproberto\Joomla\Entity\Collection`

**Examples:**

```php
$category = Category::instance(34);

// Get first 5 category children
$childCategories = $category->searchChildren(['list.limit' => 5]);
```

### searchDescendants(array $options = []) <a id="searchDescendants"></a>

> Search category descendants. These are direct child categories and their subcategories. 

**Parameters:**

* `array`  *$options (optional):* Search options. For filters, limit, ordering, etc.

**Returns:**

`\Phproberto\Joomla\Entity\Collection`

**Examples:**

```php
$category = Category::instance(34);

// Get category descendants of level 4
$childCategories = $category->searchDescendants(['filter.level' => 4]);
```

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

### validator() <a id="validator"></a>

> Get the category validator. This may be useful when you want to apply a specific validation rule to a category.

**Parameters:**

None

**Returns:**

`\Phproberto\Joomla\Entity\Categories\Validation\CategoryValidator`

**Examples:**

```php
$category = Category::instance(34);

$validator = $category->validator();

// Add a custom rule that checks that category level is greater than 2
$validator->addRule(
	new CustomRule(
		function ($value, 'Only level > 2 allowed')
		{
			return (int) $value > 2;
		}
	),
	'level'
);
$validator->validate();
```