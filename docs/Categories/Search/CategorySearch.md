# Category search class 

`Phproberto\Joomla\Entity\Categories\Search\CategorySearch`

> Allows to easily search categories.

## Index <a id="index"></a>

* [Usage](#usage)
* [Filters](#filters)
    * [filter.access](#filter.access)
    * [filter.active_language](#filter.active_language)
    * [filter.active_user_access](#filter.active_user_access)
    * [filter.ancestor_id](#filter.ancestor_id)
    * [filter.descendant_id](#filter.descendant_id)
    * [filter.extension](#filter.extension)
    * [filter.id](#filter.id)
    * [filter.language](#filter.language)
    * [filter.level](#filter.level)
    * [filter.not_id](#filter.not_id)
    * [filter.parent_id](#filter.parent_id)
    * [filter.published](#filter.published)
    * [filter.search](#filter.search)
* [Search modifiers](#list)
    * [list.direction](#list.direction)
    * [list.limit](#list.limit)
    * [list.ordering](#list.ordering)
    * [list.start](#list.start)

## Usage <a id="usage"></a>

To start using the category searcher you have to load the `phproberto_library` and add the use statement like:

```php
use Phproberto\Joomla\Entity\Categories\Search\CategorySearch;

// Start searching!
$searcher = new CategorySearch(['filter.language' => 'es-ES', 'list.limit' => 5]);
$categories = $searcher->search();

// You can do it in one line!
$categories = CategorySearch::instance(['filter.language' => 'es-ES', 'list.limit' => 5])->search();
```

## Filters <a id="filters"></a>

Filters are ways to fine tune your search to retrieve exactly the data that you want. 

### filter.access <a id="filter.access"></a>

Allows to search categories only available for specific view levels. 

**Expected Value:**

* `integer`: Single View level identifier.  
* `integer[]`: Array of view levels identifiers.

**Examples:**

```php
use Phproberto\Joomla\Entity\Categories\Search\CategorySearch;

// Search only visible categories for Public view level (id: 1)
$categories = CategorySearch::instance(['filter.access' => 1]);

// Search only visible categories for Public or Registered view levels
$categories = CategorySearch::instance(['filter.access' => [1, 2]]);
``` 

### filter.active_language <a id="filter.active_language"></a>

When this filter is enabled only categories of the active language will be returned. 

**Expected Value:**

* `boolean`: True to to enable it. 

**Examples:**

```php
use Phproberto\Joomla\Entity\Categories\Search\CategorySearch;

$categories = CategorySearch::instance(['filter.active_language' => true]);
```

### filter.active_user_access <a id="filter.active_user_access"></a>

When this filter is enabled only categories that have a view level viewable by the active language will be returned. You will usually use this in all your frontend stuff.

**Expected Value:**

* `boolean`: True to to enable it. 

**Examples:**

```php
use Phproberto\Joomla\Entity\Categories\Search\CategorySearch;

$categories = CategorySearch::instance(['filter.active_user_access' => true]);
```

### filter.ancestor_id <a id="filter.ancestor_id"></a>

Return only categories with one or more specific ancestors.

**Expected Value:**

* `integer`: Single ancestor identifier.  
* `integer[]`: Array of ancestor identifiers.

**Examples:**

```php
use Phproberto\Joomla\Entity\Categories\Search\CategorySearch;

// Return only categories descendant of category with ID 1
$categories = CategorySearch::instance(['filter.ancestor_id' => 1]);

// Return only categories descendant of category with ID 1 or category with ID 2
$categories = CategorySearch::instance(['filter.ancestor_id' => [1, 2]]);
```

### filter.descendant_id <a id="filter.descendant_id"></a>

Return only categories with one or more specific descendants.

**Expected Value:**

* `integer`: Single descendant identifier.  
* `integer[]`: Array of descendant identifiers.

**Examples:**

```php
use Phproberto\Joomla\Entity\Categories\Search\CategorySearch;

// Return only ancestors of category with ID 11
$categories = CategorySearch::instance(['filter.descendant_id' => 11]);

// Return only ancestors of category with ID 11 or category with ID 12
$categories = CategorySearch::instance(['filter.descendant_id' => [11, 12]]);
```

### filter.extension <a id="filter.extension"></a>

Return only categories from one or more specified extensions.

**Expected Value:**

* `string`: Single extension.  
* `string[]`: Array of extensions.

**Examples:**

```php
use Phproberto\Joomla\Entity\Categories\Search\CategorySearch;

// Return only categories from com_content
$categories = CategorySearch::instance(['filter.extension' => 'com_content']);

// Return only categories from com_banners or com_content
$categories = CategorySearch::instance(['filter.extension' => ['com_banners', 'com_content']]);
```

### filter.id <a id="filter.id"></a>

Return only categories with one or more specific ids.

**Expected Value:**

* `integer`: Single identifier.  
* `integer[]`: Array of identifiers.

**Examples:**

```php
use Phproberto\Joomla\Entity\Categories\Search\CategorySearch;

// Return only category with ID 11
$categories = CategorySearch::instance(['filter.id' => 11]);

// Return only categories with ID 11 or 12
$categories = CategorySearch::instance(['filter.id' => [11, 12]]);
```

### filter.language <a id="filter.language"></a>

Return only categories from one or more specified languages.

**Expected Value:**

* `string`: Single language tag.  
* `string[]`: Array of language tags.

**Examples:**

```php
use Phproberto\Joomla\Entity\Categories\Search\CategorySearch;

// Return only categories in english
$categories = CategorySearch::instance(['filter.language' => 'en-GB']);

// Return only categories in english or spanish
$categories = CategorySearch::instance(['filter.language' => ['en-GB', 'es-ES']]);
```

### filter.level <a id="filter.level"></a>

Return only categories with one or more specific levels.

**Expected Value:**

* `integer`: Single level.  
* `integer[]`: Array of levels.

**Examples:**

```php
use Phproberto\Joomla\Entity\Categories\Search\CategorySearch;

// Return only category in level 1
$categories = CategorySearch::instance(['filter.level' => 1]);

// Return only category in level 1 or 2
$categories = CategorySearch::instance(['filter.level' => [1, 2]]);
```

### filter.not_id <a id="filter.not_id"></a>

Return only categories with ids not in the specified list.

**Expected Value:**

* `integer`: Single identifier.  
* `integer[]`: Array of identifiers.

**Examples:**

```php
use Phproberto\Joomla\Entity\Categories\Search\CategorySearch;

// Return only categories with ID distinct than 11
$categories = CategorySearch::instance(['filter.not_id' => 11]);

// Return only categories with ID distinct than 11 and distinct than 12
$categories = CategorySearch::instance(['filter.not_id' => [11, 12]]);
```

### filter.parent_id <a id="filter.parent_id"></a>

Return only categories with from one or more parents.

**Expected Value:**

* `integer`: Single parent category identifier.  
* `integer[]`: Array of parent category identifiers.

**Examples:**

```php
use Phproberto\Joomla\Entity\Categories\Search\CategorySearch;

// Return only categories that are direct children of category 11
$categories = CategorySearch::instance(['filter.parent_id' => 11]);

// Return only categories that are direct children of category 11 or 12
$categories = CategorySearch::instance(['filter.parent_id' => [11, 12]]);
```

### filter.published <a id="filter.published"></a>

Return only categories in the specifiec status.

**Expected Value:**

* `integer`: Single status identifier.  
* `integer[]`: Array of status identifiers.

**Examples:**

```php
use Phproberto\Joomla\Entity\Categories\Search\CategorySearch;

// Return only categories that are published
$categories = CategorySearch::instance(['filter.published' => 1]);

// Return only categories that are in status published or unpublished
$categories = CategorySearch::instance(['filter.published' => [0, 1]]);
```

### filter.search <a id="filter.search"></a>

Return only categories with columns like specified value. 

It will search for the string in these columns:  

* `title`. Category title
* `alias`. Category alias
* `path`. Full category path
* `extension`. Category extension

**Expected Value:**

* `string`: String to search.  

**Examples:**

```php
use Phproberto\Joomla\Entity\Categories\Search\CategorySearch;

$categories = CategorySearch::instance(['filter.search' => 'My title']);
$categories = CategorySearch::instance(['filter.search' => 'com_content']);
$categories = CategorySearch::instance(['filter.search' => 'my-alias']);
$categories = CategorySearch::instance(['filter.search' => 'parent-alias/my-alias']);
```

## Search modifiers <a id="list"></a>

These modifiers do not filter data but tell the search how many items to return, which order to apply, etc. 

### list.direction <a id="list.direction"></a>

Tells the search if it has to order items ascendently(`ASC`) or descendently(`DESC`). 

**Expected Value:**

* `string`: `ASC` or `DESC`.  

**Examples:**

```php
use Phproberto\Joomla\Entity\Categories\Search\CategorySearch;

// Return categories ordered by title in descending order
$categories = CategorySearch::instance(['list.ordering' => 'c.title', 'list.direction' => 'DESC']);
```

### list.limit <a id="list.limit"></a>

Tells the search how many items return. 

**Expected Value:**

* `integer`: Number of items.  

**Examples:**

```php
use Phproberto\Joomla\Entity\Categories\Search\CategorySearch;

// Return only 5 categories
$categories = CategorySearch::instance(['list.limit' => 5]);
```

### list.ordering <a id="list.ordering"></a>

Tells the search the column that will be used for ordering. 

**Expected Value:**

* `string`: Column.  

**Examples:**

```php
use Phproberto\Joomla\Entity\Categories\Search\CategorySearch;

// Return categories ordered by title in descending order
$categories = CategorySearch::instance(['list.ordering' => 'c.title', 'list.direction' => 'DESC']);
```

### list.start <a id="list.start"></a>

Tells the search the column that returns values from specific result. It's usally used for pagination: return 5 items from result 5

**Expected Value:**

* `integer`: First result returned.  

**Examples:**

```php
use Phproberto\Joomla\Entity\Categories\Search\CategorySearch;

// Return only results from 5 to 10
$categories = CategorySearch::instance(['list.start' => 5, 'list.limit' => 5]);
```
