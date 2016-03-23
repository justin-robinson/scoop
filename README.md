#Scoop
##Multi site php 7 framework and orm for mysql

###### Note: still a work in progress and only for php7. There is a php56 branch for php 5.6 but that isn't maintained

###Features
* Built for speed and efficiency
* Mysqli based and caches prepared statements
* All classes are autoloaded based on the full namespace, so no messy autoload config files
* Only two classes per table, one for core functionality and another for you to add to
* Properly documented for all that modern ide helper magic
* One installation can manage and segregate multiple code bases and db connections
* All configs are in php ( configs/db.php is the only one you'll need to touch ) 
* DB file generation just works ( bin/scoop --action generate_db_model )
* You can override any class or config option on a global or per site basis


###Example
```php
<?php

// sets up autoloader and db connections
require_once 'scoop/bootstrap.php';

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
$row->someDateColumn = new \Scoop\Database\Literal('NOW()');

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

###Setup

####Composer
```json
{
  "require": {
    "justin-robinson/scoop": "*",
  }
}
```

```shell
composer install
# edit ./vendor/justin-robinson/scoop/configs/db.php
./vendor/bin/scoop --action generate_db_models
# Classes will be generated in ../scoop-classes
```

```php
<?php

require_once 'vendor/autoload.php';
```

#####Customize your setup by creating a ./vendor/justin-robinson/scoop/configs/custom.php
```php
<?php

// all options can be found in ./vendor/justin-robinson/scoop/configs/framework.php

return [
    'sites_folder' => '/var/www/sites',
    'timezone' => 'America/Chicago' // http://php.net/manual/en/timezones.php
];
```

####Git
```shell
git clone git@github.com:justin-robinson/scoop.git
cd scoop
composer install
# edit .configs/db.php
./bin/scoop --action generate_db_models
# Classes will be generated in ../scoop-classes
```

```php
<?php

require_once 'scoop/bootstrap.php';
```

#####Customize your setup by creating a ./scoop/configs/custom.php
```php
<?php

// all options can be found in ./scoop/configs/framework.php

return [
    'sites_folder' => '/var/www/sites',
    'timezone' => 'America/Chicago' // http://php.net/manual/en/timezones.php
];
```



###Generate DB Models
`./bin/scoop --action generate_db_model`

####Args
| Arg | Description |
| --- | --- |
| `--site=example.com` | stores configs in example.com classpath |
| `--schema=schemaName` | only generate for this schema |
| `--table=tableName` | only generate for this table ( only valid when --schema is specified ) |


###Setting up a site
1. In configs/framework.php, `'sites_folder'` is set to `'../'` by default.  You can change this to a path relative to the Scoop installation or hardcode an absolute path
2. run `./bin/scoop --action generate_site_folders --site=yoursite.com`

####(Optional) Generate site isolated db models
`./bin/scoop --action generate_db_model --site=example.com`


######Note: $_SERVER\['site_name'\] needs to be set to the name of your site.  This needs to correlate to name of the folder the site's configs live in


