# EmptyTable command

`Phproberto\Joomla\Entity\Command\Database\EmptyTable`

> Command to easily empty a database table.

## Usage <a id="usage"></a> 

```php
use Phproberto\Joomla\Entity\Command\Database\EmptyTable;

// Standard usage
try 
{
	$command = new EmptyTable('#__my_db_table');
	$command->execute();
} 
catch (\Exception $e) 
{
	// Error happened
	echo $e->getMessage();
}

// Fast usage
EmptyTable::instance(['#__my_db_table'])->execute();

// Use a database different than Joomla DB
$db = \JDatabaseDriver::getInstance(
	[
		'driver'   => 'mysqli',
		'host'     => 'localhost',
		'database' => 'my_database',
		'user'     => 'db_user',
		'password' => 'db_password',
		'prefix'   => 'jos_'
	]
);

try 
{
	$command = new EmptyTable('#__my_db_table', ['db' => $db]);
	$command->execute();
} 
catch (\Exception $e) 
{
	// Error happened
	echo $e->getMessage();
}
```
