# Simple PHP File Management System
This is a simple file management system built with PHP, MySQL and mostly AJAX with the following features:
- User authentication
- Only the owner can read or write the files of his
- File Upload, Rename, Download, Delete
- Directory creation for organizaing files

------------
### Dependency and Libraries
The system requires PHP 5.3 (or above) and MySQL 5.5.3 (or above). The system also requires OpenSSL extension enabled for PHP

This system uses a number of open source projects to work properly:
* [jQuery] - Powerful javascript libary
* [Popper] - A plugin for jQuery
* [Bootstrap] - A wonderful CSS styling library powered by Twitter
* [Font Awesome] - Great Free Icons
* [DropZone] - A library for creating drag and drop upload division

------------
### Installation
Firstly, import the table structure which you can find in "*./SQL*" folder into the database with a name of your choice.

Secondly, edit the database connection parameters which is stored in "*./PHPServices/handleDatabase.php*. Replace *SERVER*, *USERNAME*, *PASSWORD*, *DBNAME* with the connection info of your database.
```sh
    $DB_SERVER = 'SERVER';
    $DB_USERNAME= 'USERNAME';
    $DB_PASSWORD = 'PASSWORD';
    $DB_DATABASE = 'DBNAME';
    $DB_CONNECTION = new PDO("mysql:host=$DB_SERVER;dbname=$DB_DATABASE;charset=utf8mb4", $DB_USERNAME, $DB_PASSWORD);
    $DB_CONNECTION->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    return $DB_CONNECTION;
```

Thirdly, you are now good to go!

Forth, to create a user for login, please use *userCreation.php*.

------------
### Todos
 - Move-To function
 - Extension checking
 - Header checking for all images
 
**ALL RIGHTS RESERVED**
