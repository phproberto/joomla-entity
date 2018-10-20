# Aclable interface 

`Phproberto\Joomla\Entity\Acl\Contracts\Aclable`

> This interface defines methods required by entities that want to use the permission system provided by this library.  

## Index <a id="index"></a>

* [Methods](#methods)
    * [aclPrefix()](#aclPrefix)
    * [aclAssetName()](#aclAssetName)

## Methods <a id="methods"></a>

Entities implementing this interface require these 2 methods defined. If default conventions work for you you can just integrate the trait:  

`Phproberto\Joomla\Entity\Acl\Traits\HasAcl`

### aclPrefix()<a id="aclPrefix"></a>

> This defines the prefix that will be used to check this entity permissions. It defaults to `core` because it comes already defined by Joomla and used by core but you can use your custom prefix if you have complex ACL settings. Example: If your entity has actions defined like `product.edit` in `access.xml` you have to use `product` as prefix. You will rarely use this method outside your entity.

**Parameters:**

None

**Returns:**

`string`

**Examples:**

```php
// Article already uses HasAcl trait
use Phproberto\Joomla\Entity\Content\Article;

// This will echo `core`
echo Article::find(1)->aclPrefix();
```

### aclAssetName()<a id="aclAssetName"></a>

> This returns the associated asset name to check entity permissions. Default naming rule is:

`{component}.{entity_name}.{id}`

> For an entity named product with id 23 in component com_phproberto the asset returned will be:

`com_phproberto.product.23`

**Parameters:**

None

**Returns:**

`string`

**Examples:**

```php
// Article already uses HasAcl trait
use Phproberto\Joomla\Entity\Content\Article;

// This will echo `com_content.article.1`
echo Article::find(1)->aclAssetName();
```
