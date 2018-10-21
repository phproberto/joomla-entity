# Component entity

`Phproberto\Joomla\Entity\ComponentEntity`

> This base class represents an entity that is linked to a component. This is usually the case of entities that have permissions, or need access to models or tables form a component. 

## Index <a id="index"></a>

* [Usage](#usage)
* [Methods](#methods)
    * [Entity](#entity)
    * [Traits](#traits)

## Usage <a id="usage"></a>

```php
<?php
namespace My\Namespace;

use Phproberto\Joomla\Entity\ComponentEntity;

/**
 * Sample entity declaration.
 *
 * @since   __DEPLOY_VERSION__
 */
class Sample extends ComponentEntity
{
}
```

## Methods <a id="methods"></a>

This class is mainly a bridge between `Entity` and `HasComponent` trait:

### Entity <a id="entity"></a>

This class extends the base entity class so you can use methods defined there:

* [Phproberto\Joomla\Entity\Entity](./Entity.md)

### Traits <a id="traits"></a>

This class allows you to use methods defined in these traits:

* [Phproberto\Joomla\Entity\Core\Traits\HasComponent](../Core/Traits/HasComponent.md)
