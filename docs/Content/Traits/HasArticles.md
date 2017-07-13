# HasArticles trait

> Trait for entities that have associated articles.

## Index

* [Requirements](#requirements)
* [Usage](#usage)
* [Methods](#methods)
    * [clearArticles()](#clearArticles)
    * [getArticles($reload = false)](#getArticles)
    * [hasArticle($id)](#hasArticle)
    * [hasArticles()](#hasArticles)

## Requirements <a id="requirements"></a>

Using this trait requires that your entity implements the loadArticles() method like:

```php
	/**
	 * Load associated articles from DB.
	 *
	 * @return  Collection
	 */
	protected function loadArticles()
	{
		if (!$this->hasId())
		{
			return new Collection;
		}

		$articles = array_map(
			function ($item)
			{
				return Article::instance($item->id)->bind($item);
			},
			$this->getArticlesModel()->getItems() ?: array()
		);

		return new Collection($articles);
	}
```

### clearArticles() <a id="clearArticles"></a>

> Clear already loaded articles. Loaded articles are statically cached to avoid duplicated queries. This method allows you to force articles loading.

**Parameters:**

None

**Returns:**

`self`

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Category;

$category = Category::instance(1);

// This loads articles
$articles = $category->getArticles();

// But if you modify them somewhere
$article = Article::instance(23);
$article->assign('title', 'Edited title');

// Article inside articles will contain the old title
$articles->get(23)->get('title');

// So you can do something like:
$category->clearArticles()->getArticles();

// Or directly use reload option
$articles = $category->getArticles(true);
```

### getArticles($reload = false) <a id="getArticles"></a>

> Get the associated articles.

**Parameters:**

* `boolean` *$reload (optional):* Force data reloading.

**Returns:**

`\Phproberto\Joomla\Entity\Collection`

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Category;

$category = Category::instance(2);

// This loads articles first time is called
$articles = $category->getArticles();

// This loads cached articles
$articles = $category->getArticles();

// This also loads cached articles
$articles = Category::instance(2)->getArticles();

// This forces data reloading
$articles = $category->getArticles(true);
```

### hasArticle($id) <a id="hasArticle"></a>

> Check if this entity has an associated article.

**Parameters:**

* `integer` *$id (required):* Article identifier.

**Returns:**

`boolean`

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Category;

$category = Category::instance(2);

if ($category->hasArticle(23))
{
	echo $category->getArticles()->get(23)->get('title');
}
```

### hasArticles() <a id="hasArticles"></a>

> Check if this entity has associated articles.

**Parameters:**

None

**Returns:**

`boolean`

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Category;

$category = Category::instance(2);

if (!$category->hasArticles())
{
	echo 'Nothing to show';
}
```
