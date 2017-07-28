# Category entity

`Phproberto\Joomla\Entity\Content\Category`

extends:

`Phproberto\Joomla\Entity\Categories\Category`

> Represents a com_content category.

## Index <a id="index"></a>

* [Usage](#usage)
* [Methods](#methods)
	* [Extends](#extends)
	    * [Phproberto\Joomla\Entity\Categories\Category](../Categories/Category.md)
    * [Traits](#traits)
        * [Phproberto\Joomla\Entity\Content\Traits\HasArticles](./Traits/HasArticles.md)

## Usage <a id="usage"></a>

To start using this entity you have to load the `phproberto_entity` library and add the use statement like:

```php
\JLoader::import('phproberto_entity.library');

use Phproberto\Joomla\Entity\Content\Category;

// Load the category with id = 1
$category = Category::instance(1);

// Show category stuff
echo $category->get('title') . '<br />';

// Retrieve articles in this category
foreach ($category->getArticles() as $article)
{
	echo $article->id() . '. ' . $article->get('title');
}
```

## Methods <a id="methods"></a>

### Extends <a id="extends"></a>
This class extends another class so you can also use methods defined there:

[Phproberto\Joomla\Entity\Categories\Category](../Categories/Category.md)

### Traits <a id="traits"></a>
This class allows you to use methods defined in these traits:

* [Phproberto\Joomla\Entity\Content\Traits\HasArticles](./Traits/HasArticles.md)
