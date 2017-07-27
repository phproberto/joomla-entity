# HasTranslations trait

`Phproberto\Joomla\Entity\Traits\HasTranslations`

> Trait for entities with translations support.

## Index  

* [Usage](#usage)
* [Methods](#methods)
    * [hasTranslation($langTag)](#hasTranslation)
    * [hasTranslations()](#hasTranslations)
    * [translation($langTag)](#translation)
    * [translations($reload = false)](#translations)
    * [translationsByTag()](#translationsByTag)

## Usage <a id="usage"></a>

To start using this trait you have to include in your class the line:

```php
use Phproberto\Joomla\Entity\Traits\HasTranslations;
```

And then include the `use` statement inside the class like:

```php
class Article extends Entity
{
	use HasTranslations;
}
```

This trait also requires that your entity implements the `loadTranslations()` method. Here is an example of how it does it for com_content article entity through the articles model:  

```php
	/**
	 * Load associated translations from DB.
	 *
	 * @return  Collection
	 */
	protected function loadTranslations()
	{
		$ids = $this->associationsIds();

		if (empty($ids))
		{
			return new Collection;
		}

		$state = array(
			'filter.article_id' => array_values($ids)
		);

		$articles = array_map(
			function ($item)
			{
				return static::instance($item->id)->bind($item);
			},
			$this->getArticlesModel($state)->getItems() ?: array()
		);

		return new Collection($articles);
	}
``` 

## Methods <a id="methods"></a>

When implementing this trait you can start using following methods in your entity:

### hasTranslation($langTag) <a id="hasTranslation"></a>

> Check if this entity has an associated translation.

**Parameters:**

* `string` *$langTag (required):* Language tag. Examples: es-ES, en-GB, etc.

**Returns:**

`boolean`

**Examples:**

```php
$article = Article::instance(74);

if ($article->hasTranslation('es-ES'))
{
	echo 'Article ' . $article->get('title') . ' is translated to spanish!';
}
```

### hasTranslations() <a id="hasTranslations"></a>

> Check if this entity has associated translations.

**Parameters:**

None

**Returns:**

`boolean`

**Examples:**

```php
$article = Article::instance(74);

if ($article->hasTranslations())
{
	echo 'Article ' . $article->get('title') . ' has ' . $article->translations()->count() . ' translations';
}
```

### translation($langTag) <a id="translation"></a>

> Get a specific translation.

**Parameters:**

* `string` *$langTag (required):* Language tag. Examples: es-ES, en-GB, etc.

**Returns:**

`boolean`

**Throws:**

`\InvalidArgumentException` if translation does not exist. ALWAYS use `hasTranslation()` to check if a translation exists before accessing to it.

**Examples:**

```php
$article = Article::instance(74);

if ($article->hasTranslation('es-ES'))
{
	echo 'Article `' . $article->get('title') . '` has `' . $article->translation('es-ES')->get('title') . '` in spanish';
}
```

### translations($reload = false) <a id="translations"></a>

> Get the associated translations.

**Parameters:**

* `boolean` *$reload (optional):* Force data reloading.

**Returns:**

`Phproberto\Joomla\Entity\Collection`

**Examples:**

```php
$article = Article::instance(74);

if ($article->hasTranslations())
{
	echo 'Article `' . $article->get('title') . '` has following translations: <br />';

	foreach ($article->translations() as $translation)
	{
		echo $translation->get('language') . ' -> ' . $translation->get('title') . '<br />';
	}
}
```

### translationsByTag() <a id="translationsByTag"></a>

> Retrieve an array with the available translations using language tag as key.

**Parameters:**

None

**Returns:**

`array`

**Examples:**

```php
$article = Article::instance(74);

if ($article->hasTranslations())
{
	echo 'Article `' . $article->get('title') . '` has following translations: <br />';

	foreach ($article->translationsByTag() as $langTag => $translation)
	{
		echo $langTag . ' -> ' . $translation->get('title') . '<br />';
	}
}
```
