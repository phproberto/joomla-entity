# Joomla! Entity library

[![Build Status](https://travis-ci.org/phproberto/joomla-entity.svg?branch=master)](https://travis-ci.org/phproberto/joomla-entity)
[![Code Coverage](https://scrutinizer-ci.com/g/phproberto/joomla-entity/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/phproberto/joomla-entity/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phproberto/joomla-entity/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phproberto/joomla-entity/?branch=master)

> Semantical entities for Joomla!

This library is intended to help developers to use Joomla! core classes with a logical/semantical entity structure. The main goal is DX (Developer Experience) to reduce the learning curve for novice developers and save time for experienced ones.

It provides an API for core extensions but its structure can be used and extended by any third party extension in minutes.  

Benefits:
* Entities aren't `stdClass` objects. They are entities that expose everything you need from them in a semantical way.
* Creates an API between core classes and extensions. 
* Includes static caching to ensure that objects are only loaded once per page load.
* Traits that can be reused by any entity.
* Extendable. You can extend the entity system to connect any custom logic you need.

## Index <a id="index"></a>

* [Quickstart](#quickstart)
* [Installation](#installation)
* [Documentation](#documentation)
* [Requirements](#requirements)
* [Copyright & License](#license)

## Quickstart <a id="quickstart"></a>

Let's use a fast example. This is how you actually can load an article by id in Joomla:

```php
\JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');

$model = JModelLegacy::getInstance('Article', 'ContentModel');
$article = $model->getItem(1);
```

Where `$article` is a `stdClass` object. You can access its properties but it's really a dummy thing. An end point.

This library allows you to use something like:

```php
use Phproberto\Joomla\Entity\Content\Article;

$article = Article::instance(1);
```

Here `$article` is an entity. An object that exposes its logic for you to use it. Not an end point anymore but a tool that exposes its available resources to you.

Some examples of what you can do with that article entity:

```php
// Use article as entity
echo $article->get('title');

// Use params transparently
if ($article->param('show_title', '1') === '1')
{
	echo $article->get('title');
}

// Check if article is featured
if ($article->isFeatured())
{
	// Do something
}

// Check if article has an intro image
if ($article->hasIntroImage())
{
	$image = $article->getIntroImage();
	echo '<img src="' . JUri::root(true) . '/' . $image['url'] . '" />';
}

// Check article state
if ($article->isPublished())
{
	echo 'Article published!';
}

// Retrieve article category
echo $article->category()->get('title');

// You can modify article properties
$article->set('title', 'My modified title');

// And save it
try	
{
	$article->save();
}
catch (\RuntimeException $e)
{
	echo 'There was an error saving article: ' . $e->getMessage();
}
```

## Installation <a id="installation"></a>

**This is still a work in progress.** There is still no official release and you use it at your own risk. 

This repository contains exclusively the classes you need to use the entity system. You can use include it with composer by adding to your `composer.json` require section something like:

```
	"require": {
		"php": ">=5.5.0",
		"phproberto/joomla-entity": "@dev"
	}
```

which will use the current code in development mode.

With the first stable version I will publish a repository that will contain a library extension that can be installed on any Joomla! site and included in third party extension packages.

## Documentation <a id="documentation"></a>

See [documentation](./docs/README.md) for detailed documentation.

## Requirements <a id="requirements"></a>

* **PHP 5.5+** 
* **Joomla! CMS v3.7+**

## Copyright & License <a id="license"></a>

This library is licensed under [GNU LESSER GENERAL PUBLIC LICENSE](./LICENSE).  

Copyright (C) 2017 [Roberto Segura López](http://phproberto.com) - All rights reserved.  
