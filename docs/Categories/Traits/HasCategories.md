# HasCategories trait

`Phproberto\Joomla\Entity\Categories\Traits\HasCategories`

> Trait for entities that have associated categories.

## Index

* [Requirements](#requirements)
* [Usage](#usage)
* [Methods](#methods)
    * [categories($reload = false)](#categories)
    * [clearCategories()](#clearCategories)
    * [hasCategory($id)](#hasCategory)
    * [hasCategories()](#hasCategories)

## Requirements <a id="requirements"></a>

Using this trait requires that your entity implements the loadCategories() method like:

```php
	/**
	 * Load associated categories from DB.
	 *
	 * @return  Collection
	 */
	protected function loadCategories()
	{
		if (!$this->hasId())
		{
			return new Collection;
		}

		$categories = array_map(
			function ($item)
			{
				return Category::instance($item->id)->bind($item);
			},
			$this->getCategoriesModel()->getItems() ?: array()
		);

		return new Collection($categories);
	}
```

### categories($reload = false) <a id="categories"></a>

> Get the associated categories.

**Parameters:**

* `boolean` *$reload (optional):* Force data reloading.

**Returns:**

`\Phproberto\Joomla\Entity\Collection`

**Examples:**

```php
$class = new ClassWithCategories;

// This loads categories first time is called
$categories = $class->categories();

// This loads cached categories
$categories = $class->categories();

// This forces data reloading
$categories = $class->categories(true);
```

### clearCategories() <a id="clearCategories"></a>

> Clear already loaded categories. Loaded categories are statically cached to avoid duplicated queries. This method allows you to force categories loading.

**Parameters:**

None

**Returns:**

`self`

**Examples:**

```php
$class = new ClassWithCategories;

// This loads categories
$categories = $class->categories();

// But if you modify them somewhere
$category = Category::instance(23);
$category->assign('title', 'Edited title');

// Category inside categories will still contain the old title
$categories->get(23)->get('title');

// So you can do something like:
$categories = $class->clearCategories()->categories();

// Or directly use reload option
$categories = $class->categories(true);
```

### hasCategory($id) <a id="hasCategory"></a>

> Check if this entity has an associated category.

**Parameters:**

* `integer` *$id (required):* Category identifier.

**Returns:**

`boolean`

**Examples:**

```php
$class = new ClassWithCategories;

if ($class->hasCategory(23))
{
	echo $class->categories()->get(23)->get('title');
}
```

### hasCategories() <a id="hasCategories"></a>

> Check if this entity has associated categories.

**Parameters:**

None

**Returns:**

`boolean`

**Examples:**

```php
$class = new ClassWithCategories;

if (!$class->hasCategories())
{
	echo 'Nothing to show';
}
```
