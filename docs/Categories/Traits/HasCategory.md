# HasCategory trait

`Phproberto\Joomla\Entity\Categories\Traits\HasCategory`

> Trait for entities that have an associated category.

## Index

* [Requirements](#requirements)
* [Methods](#methods)
    * [category($reload = false)](#category)

## Requirements <a id="requirements"></a>

This link asumes that your class has a column containing the associated category identifier. The default column name is `category_id` but you can use a custom one overriding the protected method `getColumnCategory()` like:  

```php
	/**
	 * Get the name of the column that stores category identifier.
	 *
	 * @return  string
	 */
	protected function getColumnCategory()
	{
		return 'category_id';
	}
```

## Methods <a id="methods"></a>

This trait provides the following methods:  

### category($reload = false) <a id="category"></a>

> Get the associated category.

**Parameters:**

* `boolean` *$reload (optional):* Force data reloading.

**Returns:**

`\Phproberto\Joomla\Entity\Categories\Category`

**Examples:**

```php
// Article already uses HasCategory trait
use Phproberto\Joomla\Entity\Content\Article;

$category = Article::find(1)->category();
```
