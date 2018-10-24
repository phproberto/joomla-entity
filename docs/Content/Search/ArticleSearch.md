# Article search class 

`Phproberto\Joomla\Entity\Content\Search\ArticleSearch`

> Allows to easily search articles.

## Index <a id="index"></a>

* [Usage](#usage)
* [Filters](#filters)
    * [filter.access](#filter.access)
    * [filter.active_language](#filter.active_language)
    * [filter.active_user_access](#filter.active_user_access)
    * [filter.author_id](#filter.author_id)
    * [filter.category_id](#filter.category_id)
    * [filter.editor_id](#filter.editor_id)
    * [filter.featured](#filter.featured)
    * [filter.id](#filter.id)
    * [filter.language](#filter.language)
    * [filter.not_author_id](#filter.not_author_id)
    * [filter.not_category_id](#filter.not_category_id)
    * [filter.not_id](#filter.not_id)
    * [filter.not_language](#filter.not_language)
    * [filter.not_state](#filter.not_state)
    * [filter.search](#filter.search)
    * [filter.state](#filter.state)
    * [filter.tag_id](#filter.tag_id)
* [Search modifiers](#list)
    * [list.direction](#list.direction)
    * [list.limit](#list.limit)
    * [list.ordering](#list.ordering)
    * [list.start](#list.start)

## Usage <a id="usage"></a>

To start using the article search you have to load the `phproberto_library` and add the use statement like:

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

// Start searching!
$searcher = new ArticleSearch(['filter.language' => 'es-ES', 'list.limit' => 5]);
$articles = $searcher->search();

// You can do it in one line!
$articles = ArticleSearch::instance(['filter.language' => 'es-ES', 'list.limit' => 5])->search();
```

## Filters <a id="filters"></a>

Filters are ways to fine tune your search to retrieve exactly the data that you want. 

### filter.access <a id="filter.access"></a>

Allows to search articles only available for specific view levels. 

**Expected Value:**

* `integer`: Single View level identifier.  
* `integer[]`: Array of view levels identifiers.

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

// Search only visible articles for Public view level (id: 1)
$articles = ArticleSearch::instance(['filter.access' => 1]);

// Search only visible articles for Public or Registered view levels
$articles = ArticleSearch::instance(['filter.access' => [1, 2]]);
``` 

### filter.active_language <a id="filter.active_language"></a>

When this filter is enabled only articles of the active language will be returned. 

**Expected Value:**

* `boolean`: True to to enable it. 

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

$articles = ArticleSearch::instance(['filter.active_language' => true]);
```

### filter.active_user_access <a id="filter.active_user_access"></a>

When this filter is enabled only articles that have a view level viewable by the active language will be returned. You will usually use this in all your frontend stuff.

**Expected Value:**

* `boolean`: True to to enable it. 

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

$articles = ArticleSearch::instance(['filter.active_user_access' => true]);
```

### filter.author_id <a id="filter.author_id"></a>

Return only articles created by a specific user.

**Expected Value:**

* `integer`: Single identifier.  
* `integer[]`: Array of identifiers.

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

// Return only articles created by user with ID 42
$articles = ArticleSearch::instance(['filter.author_id' => 42]);

// Return only articles created by user with ID 42 or 50
$articles = ArticleSearch::instance(['filter.author_id' => [42, 50]]);
```

### filter.category_id <a id="filter.category_id"></a>

Return only articles assigned to a specific category.

**Expected Value:**

* `integer`: Single identifier.  
* `integer[]`: Array of identifiers.

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

// Return only articles assigned to category with ID 8
$articles = ArticleSearch::instance(['filter.category_id' => 8]);

// Return only articles assigned to category with ID 8 or 14
$articles = ArticleSearch::instance(['filter.category_id' => [8, 14]]);
```

### filter.editor_id <a id="filter.editor_id"></a>

Return only articles edited by a specific user.

**Expected Value:**

* `integer`: Single identifier.  
* `integer[]`: Array of identifiers.

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

// Return only articles edited by user with ID 42
$articles = ArticleSearch::instance(['filter.editor_id' => 42]);

// Return only articles edited by user with ID 42 or 50
$articles = ArticleSearch::instance(['filter.editor_id' => [42, 50]]);
```

### filter.featured <a id="filter.featured"></a>

Return only articles in the specifiec featured status.

**Expected Value:**

* `integer`: Single status identifier.  
* `integer[]`: Array of status identifiers.

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

// Return only articles that are featured
$articles = ArticleSearch::instance(['filter.featured' => 1]);

// Return only articles that are not featured
$articles = ArticleSearch::instance(['filter.featured' => 0]);
```

### filter.id <a id="filter.id"></a>

Return only articles with one or more specific ids.

**Expected Value:**

* `integer`: Single identifier.  
* `integer[]`: Array of identifiers.

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

// Return only article with ID 11
$articles = ArticleSearch::instance(['filter.id' => 11]);

// Return only article with ID 11 or 12
$articles = ArticleSearch::instance(['filter.id' => [11, 12]]);
```

### filter.language <a id="filter.language"></a>

Return only articles from one or more specified languages.

**Expected Value:**

* `string`: Single language tag.  
* `string[]`: Array of language tags.

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

// Return only articles in english
$articles = ArticleSearch::instance(['filter.language' => 'en-GB']);

// Return only articles in english or spanish
$articles = ArticleSearch::instance(['filter.language' => ['en-GB', 'es-ES']]);
```

### filter.not_author_id <a id="filter.not_author_id"></a>

Return only articles created by users distinct than specified.

**Expected Value:**

* `integer`: Single identifier.  
* `integer[]`: Array of identifiers.

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

// Return only articles created by any user except user with ID 42
$articles = ArticleSearch::instance(['filter.not_author_id' => 42]);

// Return only articles created by any user except user with ID 42 or 50
$articles = ArticleSearch::instance(['filter.not_author_id' => [42, 50]]);
```

### filter.not_category_id <a id="filter.not_category_id"></a>

Return only articles assigned to a category different than the specified ones.

**Expected Value:**

* `integer`: Single identifier.  
* `integer[]`: Array of identifiers.

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

// Return only articles assigned to any category with ID not equal to 8
$articles = ArticleSearch::instance(['filter.not_category_id' => 8]);

// Return only articles assigned to any category with ID not equal to 8 or 14
$articles = ArticleSearch::instance(['filter.not_category_id' => [8, 14]]);
```

### filter.not_id <a id="filter.not_id"></a>

Return only articles with IDs distinct than the specified ones.

**Expected Value:**

* `integer`: Single identifier.  
* `integer[]`: Array of identifiers.

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

// Return only articles with with ID distinct than 11
$articles = ArticleSearch::instance(['filter.not_id' => 11]);

// Return only articles with with ID distinct than 11 or 12
$articles = ArticleSearch::instance(['filter.not_id' => [11, 12]]);
```

### filter.not_language <a id="filter.not_language"></a>

Return only articles assigned to a language with tag distinct that the specified ones.

**Expected Value:**

* `string`: Single language tag.  
* `string[]`: Array of language tags.

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

// Return only articles that are not in english
$articles = ArticleSearch::instance(['filter.not_language' => 'en-GB']);

// Return only articles that are not in english or spanish
$articles = ArticleSearch::instance(['filter.not_language' => ['en-GB', 'es-ES']]);
```

### filter.not_state <a id="filter.not_state"></a>

Return only articles with a state different than the specified.

**Expected Value:**

* `integer`: Single status identifier.  
* `integer[]`: Array of status identifiers.

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

// Return only articles that are not in published state
$articles = ArticleSearch::instance(['filter.not_state' => 1]);

// Return only articles that are not in unpublished state
$articles = ArticleSearch::instance(['filter.not_state' => 0]);
```

### filter.search <a id="filter.search"></a>

Return only articles with columns like specified value. 

It will search for the string in these columns:  

* `title`. Article title
* `alias`. Article alias

**Expected Value:**

* `string`: String to search.  

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

$articles = ArticleSearch::instance(['filter.search' => 'My title']);
$articles = ArticleSearch::instance(['filter.search' => 'my-alias']);
```

### filter.state <a id="filter.state"></a>

Return only articles in specified state.

**Expected Value:**

* `integer`: Single status identifier.  
* `integer[]`: Array of status identifiers.

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

// Return only articles that are published
$articles = ArticleSearch::instance(['filter.state' => 1]);

// Return only articles that are unpublished
$articles = ArticleSearch::instance(['filter.state' => 0]);
```

### filter.tag_id <a id="filter.tag_id"></a>

Return only articles with a one or more tags assigned.

**Expected Value:**

* `integer`: Single identifier.  
* `integer[]`: Array of identifiers.

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

// Return only articles with tag with ID 7 assigned
$articles = ArticleSearch::instance(['filter.tag_id' => 7]);

// Return only articles with tag with ID 7 or 33 assigned
$articles = ArticleSearch::instance(['filter.tag_id' => [7, 33]]);
```

## Search modifiers <a id="list"></a>

These modifiers do not filter data but tell the search how many items to return, which order to apply, etc. 

### list.direction <a id="list.direction"></a>

Tells the search if it has to order items ascendently(`ASC`) or descendently(`DESC`). 

**Expected Value:**

* `string`: `ASC` or `DESC`.  

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

// Return articles ordered by title in descending order
$articles = ArticleSearch::instance(['list.ordering' => 's.title', 'list.direction' => 'DESC']);
```

### list.limit <a id="list.limit"></a>

Tells the search how many items return. 

**Expected Value:**

* `integer`: Number of items.  

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

// Return only 5 articles
$articles = ArticleSearch::instance(['list.limit' => 5]);
```

### list.ordering <a id="list.ordering"></a>

Tells the search the column that will be used for ordering. 

**Expected Value:**

* `string`: Column.  

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

// Return articles ordered by title in descending order
$articles = ArticleSearch::instance(['list.ordering' => 'c.title', 'list.direction' => 'DESC']);
```

### list.start <a id="list.start"></a>

Tells the search the column that returns values from specific result. It's usally used for pagination: return 5 items from result 5

**Expected Value:**

* `integer`: First result returned.  

**Examples:**

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

// Return only results from 5 to 10
$articles = ArticleSearch::instance(['list.start' => 5, 'list.limit' => 5]);
```
