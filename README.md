#phpr
##multi site php 7 framework and orm

######Note: still a work in progress and only for php7. There is a php56 branch for php 5.6 but that isn't maintained

###Setup
```bash
git clone git@github.com:justin-robinson/phpr.git
cd phpr
# edit configs/db.php
./scripts/generate_db_model.php
# Classes will be generated in ../phpr-classes.
# Just include phpr/base.php in your php file and you are all set
```

* Built for speed and efficiency
* Mysqli based and caches prepared statements
* All classes are autoloaded based on the full namespace, so no messy autoload config files
* Only two classes per table, one for core functionality and another for you to add to
* Properly documented for all that modern ide helper magic
* One installation can manage and segregate multiple code bases and db connections
* All configs are in php ( configs/db.php is the only one you'll need to touch ) 
* DB file generation just works ( scripts/generate_db_model.php )
* You can override any class or config option on a global or per site basis

PS. do a `composer install` for some colorized output on db model generation

```php
<?php

// sets up autoloader and db connections
require_once 'phpr/base.php';

/**
 * returns Rows (collection) of Models
 * Rows implements Iterator, ArrayAccess, and JsonSerializable so you can treat it like an array
 */
$rows = \DB\YourSchema\YourTable::fetch_where('column = ?', ['value']);

/**
 * You can iterate over the rows
 */
foreach ( $rows as $row ) {
    
    // dynamic getters and setters for column values
    $row->column = 'new value';
    echo $row->column;
    
    // easy save ( this will actually do an update because we loaded this row from the database ) 
    $row->save();
}

/**
 * You can index into the rows
 */
$row = $rows[0];


/**
 * Make a new row
 */
$row = new \DB\Schema\Table();

// set values to string literals
$row->someDateColumn = new \phpr\Database\Literal('NOW()');

$row->save();


/**
 * Complex queries can be handled via a generic db model, since there
 * isn't a query builder ( yet? probably not )
 */
$sql=
    "SELECT
       foo.*,
       bar.baz
     FROM
        `schema`.`table` foo
        LEFT JOIN `schema2`.`users` bar
     WHERE
        foo.biz IS NOT NULL
     GROUP BY
        bar.baz
     LIMIT 500;"

\DB\Model\Generic::fetch($sql);

```