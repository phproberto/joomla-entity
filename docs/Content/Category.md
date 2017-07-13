# Category entity

`Phproberto\Joomla\Entity\Content\Category`

extends:

`Phproberto\Joomla\Entity\Category`

> Represents a com_content category.

## Index <a id="index"></a>

* [Usage](#usage)
* [Methods](#methods)

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

