# Article entity

> Represents a com_content article.

* [Usage](#usage)
* [Methods](#methods)

## Usage <a id="usage"></a>

To start using this entity you have to load the `phproberto_library` and add the use statement like:

```php
\JLoader::import('phproberto_entity.library');

use Phproberto\Joomla\Entity\Content\Article;

// Load the article with id = 1
$article = Article::instance(1);
```

## Methods <a id="methods"></a>

