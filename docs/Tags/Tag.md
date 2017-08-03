# Tag entity 

`Phproberto\Joomla\Entity\Tags\Tag`

> Represents a com_tags tag.

## Index <a id="index"></a>

* [Usage](#usage)
* [Methods](#methods)
    * [Traits](#traits)

## Usage <a id="usage"></a>

To start using this entity you have to load the `phproberto_library` and add the use statement like:

```php
\JLoader::import('phproberto_entity.library');

use Phproberto\Joomla\Entity\Tags\Tag;

// Load the tag with id = 1
$tag = Tag::instance(1);
```

## Methods <a id="methods"></a>

This entity has these methods available:

### Traits <a id="traits"></a>

This class allows you to use methods defined in these traits:

* [Phproberto\Joomla\Entity\Core\Traits\HasImages](../Core/Traits/HasImages.md)
* [Phproberto\Joomla\Entity\Core\Traits\HasLink](../Core/Traits/HasLink.md)
* [Phproberto\Joomla\Entity\Core\Traits\HasMetadata](../Core/Traits/HasMetadata.md)
* [Phproberto\Joomla\Entity\Core\Traits\HasParams](../Core/Traits/HasParams.md)
* [Phproberto\Joomla\Entity\Traits\HasState](../Traits/HasState.md)
