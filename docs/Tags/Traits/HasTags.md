# HasTags trait

> Trait for entities that have associated tags.

## Index  

* [Requirements](#requirements)
* [Usage](#usage)
* [Methods](#methods)

## Requirements <a id="requirements"></a>

Using this trait requires that your entity implements the loadTags() method like:

```php
	/**
	 * Load associated tags from DB.
	 *
	 * @return  Collection
	 */
	protected function loadTags()
	{
		if (!$this->hasId())
		{
			return new Collection;
		}

		$tagHelper = new \JHelperTags;

		$tags = array_map(
			function($tag)
			{
				return Tag::instance($tag->id)->bind($tag);
			},
			$tagHelper->getItemTags('com_content.article', $this->id()) ?: array()
		);

		return new Collection($tags);
	}
```