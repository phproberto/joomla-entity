# HasAcl trait to integrate Acl with 

`Phproberto\Joomla\Entity\Acl\Traits\HasAcl`

> This trait provides everything you need to start using ACL with an entity.

## Index <a id="index"></a>

* [Requirements](#requirements)
* [Usage](#usage)
* [Methods](#methods)
    * [acl(User $user = null)](#acl)
    * [aclPrefix()](#aclPrefix)
    * [aclAssetName()](#aclAssetName)

## Requirements <a id="requirements"></a>

Entities with ACL checks need to implement the interface:  

`Phproberto\Joomla\Entity\Acl\Contracts\Aclable`

To use this trait with the default behavior your class has to provide a method to retrieve the component that contains the ACL settings this entity requires.   

Sample implementation:  

```php
	/**
	 * Retrieve the associated component.
	 *
	 * @return  Component
	 */
	public function component()
	{
		return Component::fromOption('com_phproberto');
	}
```

If your entity already extends:

`Phproberto\Joomla\Entity\ComponentEntity`

You don't need to do anything.  

Alternatively you can override the `aclAssetName()` method to provide the entity asset name which is the only place `component()` method is retrieved.  

Sample custom `aclAssetName()`:  

```php
	/**
	 * Get the identifier of the associated asset
	 *
	 * @return  string
	 */
	public function aclAssetName()
	{
		if ($this->hasId())
		{
			return 'com_phproberto.product.' . $this->id();
		}

		return 'com_phproberto';		
	}
```

## Usage <a id="usage"></a>

Once integrated with your entity you can use this trait's methods to perform permission checks like:  

```php
// Article already uses HasAcl trait
use Phproberto\Joomla\Entity\Content\Article;

$article = Article::find(1);

// If no user is specified it will check permissions of the active user
$permissions = $article->acl();

// You can also check permissions for a specific user
$permissions = $article->acl(User::find(23));

// Actions can be tested directly with can() method. This expects an action core.edit is defined in component access.xml file
if ($permissions->can('edit'))
{
	// Do something
}

// Common actions are already defined as methods for fast usage. It also contains complex tests for entities owned by an user. Global edit may be disabled by owner edit may be allowed.
if ($permissions->canEdit())
{
	// Do something
}
```

## Methods <a id="methods"></a>

This trait has these methods available:

### acl(User $user = null)<a id="acl"></a>

> Get ACL settings for a specific user to start checking permissions.

**Parameters:**

* `Phproberto\Joomla\Entity\Users\User` *$user (optional):* User whose permissions we want to check. Defaults to active user.

**Returns:**

`Phproberto\Joomla\Entity\Acl\Acl`

**Examples:**

```php
// Article already uses HasAcl trait
use Phproberto\Joomla\Entity\Content\Article;

// If no user is specified it will check permissions of the active user
$permissions = $article->acl();

// You can also check permissions for a specific user
$permissions = $article->acl(User::find(23));
```

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
