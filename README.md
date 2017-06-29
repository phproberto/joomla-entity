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

// Retrieve article category
echo $article->getCategory()->get('title');

// Retrieve article asset
$asset = $article->getAsset();
```

## Requirements

* **PHP 5.5+** 
* **Joomla! CMS v3.7+**

## License

This library is licensed under [GNU LESSER GENERAL PUBLIC LICENSE](./LICENSE).  

Copyright (C) 2017 [Roberto Segura López](http://phproberto.com) - All rights reserved.  
