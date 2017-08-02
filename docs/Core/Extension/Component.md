# Component entity 

`Phproberto\Joomla\Entity\Core\Extension\Component`

> Represents a joomla component.

## Index <a id="index"></a>

* [Usage](#usage)
* [Methods](#methods)
	* [Extends](#extends)
	    * [Phproberto\Joomla\Entity\Core\Extension](../Extension.md)

## Usage <a id="usage"></a>

To start using this entity you have to load the `phproberto_entity` library and add the use statement like:

```php
\JLoader::import('phproberto_entity.library');

use Phproberto\Joomla\Entity\Core\Extension\Component;

// Load the component by id
$component = Component::instance(22);

// Load active component
echo 'active component is: ' . Component::active()->option();

// Load component by its option
echo 'com_contact id is: ' . Component::fromOption('com_contact')->id();

// Components are entities so you can retrieve params, etc.
$comContact = Component::fromOption('com_contact');

echo 'Show tags option is ' . ($comContact->param('show_tags', '1') === '1'  ? 'enabled' : 'disabled');

// Find a model from a component's backend
$backendModel = Component::fromOption('com_categories')->model('Categories');

// Find a model from a component's frontend
$frontendModel = Component::fromOption('com_content')->site()->model('Articles');
```

## Methods <a id="methods"></a>

### Extends <a id="extends"></a>
This class extends another class so you can also use methods defined there:

[Phproberto\Joomla\Entity\\Core\Extension](../Extension.md)
