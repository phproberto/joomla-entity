# Joomla! Entity library

[![Build Status](https://travis-ci.org/phproberto/joomla-entity.svg?branch=master)](https://travis-ci.org/phproberto/joomla-entity)
[![Code Coverage](https://scrutinizer-ci.com/g/phproberto/joomla-entity/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/phproberto/joomla-entity/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phproberto/joomla-entity/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phproberto/joomla-entity/?branch=master)

> Easy management of Joomla! entities.

This library is intended to help developers to use Joomla! core classes with a logical entity structure. It also exposes that entity structure so it can be used and extended by any third part extension.  

Benefits:
* Entities aren't `stdClass` objects. The expose everything you can get from them.
* Creates an API between core classes and extensions. 
* Includes static caching to ensure that objects are only loaded once per page load
* Traits that can be reused by any entity
* Extendable. You can extend the entity system to connect any custom logic you need to connect.

## Index <a id="index"></a>

* [Quickstart](#quickstart)
* [Requirements](#requirements)
* [Copyright & License](#license)

## Quickstart <a id="quickstart"></a>

Let's use a fast example. This is how you actually can load an article by id:

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
if ($article->getParam('show_title', '1') === '1')
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
echo $article->getCategory()->get('title');

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

## Requirements <a id="requirements"></a>

* **PHP 5.5+** 
* **Joomla! CMS v3.7+**

## Copyright & License <a id="license"></a>

This library is licensed under [GNU LESSER GENERAL PUBLIC LICENSE](./LICENSE).  

Copyright (C) 2017 [Roberto Segura López](http://phproberto.com) - All rights reserved.  
