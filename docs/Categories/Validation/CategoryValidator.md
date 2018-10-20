# Category validator 

`Phproberto\Joomla\Entity\Categories\Validation\CategoryValidator`

> This validator that ensures that categories cannot be saved without expected data. It's automatically executed each time you try to save a category entity but you can also execute it manually.

## Index <a id="index"></a>

* [Usage](#usage)
* [Rules](#rules)
    * [access (optional)](#access)
    * [extension](#extension)
    * [level (optional)](#level)
    * [parent_id (optional)](#parent_id)
    * [title](#title)

## Usage <a id="usage"></a>

```php
use Phproberto\Joomla\Entity\Categories\Category;
use Phproberto\Joomla\Entity\Categories\Validation\CategoryValidator;
use Phproberto\Joomla\Entity\Validation\Exception\ValidationException;

$category = Category::fromData(['title' => 'Category title']);
$validator = new CategoryValidator($category);

try
{
    $validator->validate();
}
catch (ValidationException $e)
{
    echo 'Category cannot be saved: ' . $e->getMessage();
}

// Category already executes validation on save. This would be equivalent to previous code:
$category = Category::fromData(['title' => 'Category title']);

try 
{
    $category->save();
} 
catch (SaveException $e) 
{
    echo 'Error saving category: ' . $e->getMessage();
}

```

## Rules <a id="rules"></a>

This validator assigns the following rules:   

### access (optional) <a id="access"></a>

The value of the `access` column:  

* It's optional
* If specified it must be null or a positive integer. 

### extension <a id="extension"></a>

The value of the `extension` column:  

* It's required
* It cannot be empty.

### level (optional) <a id="level"></a>

The value of the `level` column:  

* It's optional
* If specified it must be null or a positive integer. 


### parent_id (optional) <a id="parent_id"></a>

The value of the `parent_id` column:  

* It's optional
* If specified it must be null or a positive integer. 


### title <a id="title"></a>

The value of the `title` column:  

* It's required
* It cannot be empty.


