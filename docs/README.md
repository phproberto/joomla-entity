# Funny and reliable API for !Joomla  

The goal of this library is to make things easier for Joomla! developers using a semantical language. Because Developer Experience (DX) in open source matters as much as User Experience (UX) in our websites. 

Being fun is not incompatible with reliability. That's why this library is also fully covered by tests.

##  Cool things you can do with joomla-entity:

### Create an article

```php
use Phproberto\Joomla\Entity\Content\Article;

// Create a com_content article in 1 line!
$article = Article::create(['title' => 'My article', 'catid' => 12]);
```

### Fast search for articles

```php
use Phproberto\Joomla\Entity\Content\Search\ArticleSearch;

$articles = ArticleSearch::instance(
    [
        // Only published articles
        'filter.published' => 1, 
        // Only articles accessible by active user
        'filter.active_user_access' => true,
        // Only return 5 articles
        'list.limit' => 5,
        // Return latest created articles first
        'list.ordering' => 'a.created',
        'list.direction' => 'DESC'
    ]
)->search();
```

### Retrieve article stuff

```php
use Phproberto\Joomla\Entity\Content\Article;

$article = Article::find(12);

// Article link + title
echo '<a href="' . $article->link() . '">' . $article->get('title') . '</a>';

// Article author
if ($article->hasAuthor())
{
    echo $article->author()->get('name');
}

// Article fields
foreach ($article->fields() as $field)
{
    echo 'Article `' . $article->get('title') . '` has field `' . $field->get('name') . '`';
}

// Or directly a field value
echo $article->fieldByName('manufacturer')->value();

// Article category
echo 'Article `' . $article->get('title') . '` is in category `' . $article->category()->get('title') . '`';
```
