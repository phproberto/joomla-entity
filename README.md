# Joomla! Entity library

> Easy management of Joomla! entities.

[![Build Status](https://travis-ci.org/phproberto/joomla-entity.svg?branch=master)](https://travis-ci.org/phproberto/joomla-entity)
[![Code Coverage](https://scrutinizer-ci.com/g/phproberto/joomla-entity/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/phproberto/joomla-entity/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/phproberto/joomla-entity/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/phproberto/joomla-entity/?branch=master)

## Quickstart

This is still a work in progress but...

```php
use Phproberto\Joomla\Entity\Content\Article;

$article = Article::instance(1);

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

// Check if has an use intro image
if ($article->hasIntroImage())
{
	$image = $article->getIntroImage();
	echo '<img src="' . JUri::root(true) . '/' . $image['url'] . '" />';
}

// Check if has an use full text image
if ($article->hasFullTextImage())
{
	$image = $article->getFullTextImage();
	echo '<img src="' . JUri::root(true) . '/' . $image['url'] . '" />';
}

// Check article state
if ($article->isPublished())
{
	echo 'Article published!';
}
elseif ($article->isTrashed())
{
	echo 'Article trashed!';
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

// Retrieve article asset
$asset = $article->getAsset();
```

## Requirements

* **PHP 5.5+** 
* **Joomla! CMS v3.7+**

## License

This library is licensed under [GNU LESSER GENERAL PUBLIC LICENSE](./LICENSE).  

Copyright (C) 2017 [Roberto Segura López](http://phproberto.com) - All rights reserved.  
