# DeleteFolderRecursively command

`Phproberto\Joomla\Entity\Command\Database\DeleteFolderRecursively`

> Command to easily delete a folder.

## Usage <a id="usage"></a> 

```php
use Phproberto\Joomla\Entity\Command\FileSystem\DeleteFolderRecursively;

// Standard usage
try 
{
	$command = new DeleteFolderRecursively(__DIR__ . '/my_folder');
	$command->execute();	
} 
catch (\Exception $e) 
{
	// Error happened
	echo $e->getMessage();
}

// Fast usage
DeleteFolderRecursively::instance([__DIR__ . '/my_folder'])->execute();
```