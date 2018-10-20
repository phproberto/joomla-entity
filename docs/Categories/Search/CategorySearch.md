# Category searcher 

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
\JLoader::import('phproberto_entity.library');

use Phproberto\Joomla\Entity\Categories\Search\CategorySearch;

// Start searching!
$searcher = new CategorySearch(['list.limit' => 5]);
$categories = $searcher->search();

// You can do it in one line!
$categories = CategorySearch::instance(['list.limit' => 5])->search();
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
