# Article entity

`Phproberto\Joomla\Entity\Content\Article`

> Represents a com_content article.

## Index <a id="index"></a>

* [Usage](#usage)
* [Methods](#methods)
    * [Traits](#traits)
    * [table($name = '', $prefix = null, $options = array())](#table)

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
* [Phproberto\Joomla\Entity\Core\Traits\HasAccess](../Core/Traits/HasAccess.md)
* [Phproberto\Joomla\Entity\Core\Traits\HasAsset](../Core/Traits/HasAsset.md)
* [Phproberto\Joomla\Entity\Tags\Traits\HasTags](../Tags/Traits/HasTags.md)
* [Phproberto\Joomla\Entity\Traits\HasAssociations](../Traits/HasAssociations.md)
* [Phproberto\Joomla\Entity\Traits\HasFeatured](../Traits/HasFeatured.md)
* [Phproberto\Joomla\Entity\Traits\HasLink](../Traits/HasLink.md)
* [Phproberto\Joomla\Entity\Traits\HasImages](../Traits/HasImages.md)
* [Phproberto\Joomla\Entity\Traits\HasMetadata](../Traits/HasMetadata.md)
* [Phproberto\Joomla\Entity\Traits\HasParams](../Traits/HasParams.md)
* [Phproberto\Joomla\Entity\Traits\HasState](../Traits/HasState.md)
* [Phproberto\Joomla\Entity\Traits\HasTranslations](../Traits/HasTranslations.md)
* [Phproberto\Joomla\Entity\Traits\HasUrls](../Traits/HasUrls.md)
* [Phproberto\Joomla\Entity\Users\Traits\HasAuthor](../Users/Traits/HasAuthor.md)
* [Phproberto\Joomla\Entity\Users\Traits\HasEditor](../Users/Traits/HasEditor.md)

### table($name = '', $prefix = null, $options = array()) <a id="table"></a>

> Get a table instance. Defauts to \JTableContent. Most of the times you don't want to access this because entity handles most stuff that requires table access.

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
$article = new Article(74);

echo 'Title before `' . $article->get('title') . '`<br />';
$article->bind(array('title' => 'My new title'));

// This will show `My new title`
echo 'Title after `' . $article->get('title') . '`<br />';
```
