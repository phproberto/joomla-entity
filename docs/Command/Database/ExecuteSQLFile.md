# ExecuteSQLFile command

`Phproberto\Joomla\Entity\Command\Database\ExecuteSQLFile`

> Command to easily execute an SQL file.

## Usage <a id="usage"></a> 

```php
use Phproberto\Joomla\Entity\Command\Database\ExecuteSQLFile;

// Standard usage
try 
{
	$command = new ExecuteSQLFile(__DIR__ '/sql/my_sql_file.sql');
	$command->execute();
} 
catch (\Exception $e) 
{
	// Error happened
	echo $e->getMessage();
}

// Fast usage
ExecuteSQLFile::instance([__DIR__ '/sql/my_sql_file.sql'])->execute();

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
	$command = new ExecuteSQLFile(__DIR__ '/sql/my_sql_file.sql', ['db' => $db]);
	$command->execute();
} 
catch (\Exception $e) 
{
	// Error happened
	echo $e->getMessage();
}
```
