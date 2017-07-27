# Article entity

`Phproberto\Joomla\Entity\Content\Article`

> Represents a com_content article.

## Index <a id="index"></a>

* [Usage](#usage)
* [Methods](#methods)
    * [Traits](#traits)
        * [Phproberto\Joomla\Entity\Categories\Traits\HasCategory](../Categories/Traits/HasCategory.md)
        * [Phproberto\Joomla\Entity\Core\Traits\HasAsset](../Core/Traits/HasAsset.md)

## Usage <a id="usage"></a>

To start using this entity you have to load the `phproberto_library` and add the use statement like:

```php
\JLoader::import('phproberto_entity.library');

use Phproberto\Joomla\Entity\Content\Article;

// Load the article with id = 1
$article = Article::instance(1);
```

## Methods <a id="methods"></a>

This entity has these methods available:

### Traits <a id="traits"></a>

This class allows you to use methods defined in these traits:

* [Phproberto\Joomla\Entity\Categories\Traits\HasCategory](../Categories/Traits/HasCategory.md)
* [Phproberto\Joomla\Entity\Core\Traits\HasAsset](../Core/Traits/HasAsset.md)
