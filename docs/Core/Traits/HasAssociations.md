# HasAssociations trait

`Phproberto\Joomla\Entity\Core\Traits\HasAssociations`

> Trait for entities with associations.

## Index  

* [Usage](#usage)
* [Methods](#methods)
    * [association($langTag)](#association)
    * [associations($reload = false)](#associations)
    * [associationsIds()](#associationsIds)
    * [hasAssociation($langTag)](#hasAssociation)
    * [hasAssociationById($id)](#hasAssociationById)
    * [hasAssociations()](#hasAssociations)

## Usage <a id="usage"></a>

To start using this trait you have to include in your class the line:

```php
use Phproberto\Joomla\Entity\Core\Traits\HasAssociations;
```

And then include the `use` statement inside the class like:

```php
class Article extends Entity
{
	use HasAssociations;
}
```

This trait also requires that your entity implements the `loadAssociations()` method. Here is an example of how it does it for com_content article entity through `JLanguageAssociations`:  

```php
	/**
	 * Load associations from DB.
	 *
	 * @return  array
	 *
	 * @codeCoverageIgnore
	 */
	protected function loadAssociations()
	{
		if (!$this->hasId())
		{
			return array();
		}

		return \JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $this->id());
	}
``` 

## Methods <a id="methods"></a>

When implementing this trait you can start using following methods in your entity:

### association($langTag) <a id="association"></a>

> Get an association by its language tag.

**Parameters:**

* `string` *$langTag (required):* Language tag. Examples: es-ES, en-GB, etc.

**Returns:**

`stdClass`

**Throws:**

`\InvalidArgumentException` if association does not exist. ALWAYS use `hasAssociation()` to check if an association exists before accessing to it.

**Examples:**

```php
$article = Article::instance(74);

if ($article->hasAssociation('es-ES'))
{
	echo 'Article `' . $article->get('title') . '` spanish association is ' . $article->association('es-ES')->id;
}
```

### associations($reload = false) <a id="associations"></a>

> Get entity's language associations.

**Parameters:**

None

**Returns:**

`array`

**Examples:**

```php
$article = Article::instance(74);

if ($article->hasAssociations())
{
	echo 'Article `' . $article->get('title') . '` has ' . count($article->associations()) . ' associations';
}
```

### associationsIds() <a id="associationsIds"></a>

> Get the ids of the entity's language associations.

**Parameters:**

None

**Returns:**

`array`

**Examples:**

```php
$article = Article::instance(74);

if ($article->hasAssociations())
{
	echo 'Article `' . $article->get('title') . '` is associated with articles with ids: ' . implode(',', $article->associationsIds());
}
```

### hasAssociation($langTag) <a id="hasAssociation"></a>

> Check if this entity has a specific association.

**Parameters:**

* `string` *$langTag (required):* Language tag. Examples: es-ES, en-GB, etc.

**Returns:**

`boolean`

**Examples:**

```php
$article = Article::instance(74);

if ($article->hasAssociation('es-ES'))
{
	echo 'Article `' . $article->get('title') . '` has a spanish association';
}
```

### hasAssociationById($id) <a id="hasAssociationById"></a>

> Check if this entity has a specific association.

**Parameters:**

* `integer` *$id (required):* Entity identifier

**Returns:**

`boolean`

**Examples:**

```php
$article = Article::instance(74);

if ($article->hasAssociationById(75))
{
	echo 'Article `' . $article->get('title') . '` is associated with article 75';
}
```

### hasAssociations() <a id="hasAssociations"></a>

> Check if this entity has associations.

**Parameters:**

None

**Returns:**

`boolean`

**Examples:**

```php
$article = Article::instance(74);

if ($article->hasAssociations())
{
	echo 'Article `' . $article->get('title') . '` has associations';
}
```
