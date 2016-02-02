#!/usr/bin/env php
<?php

/**
 * Genereate phpr db models
 *
 * args
 *  --site=example.com
 *      stores configs in example.com classpath
 *  --schema=schemaName
 *      only generate for this schema
 *  --table=tableName
 *      only generate for this table ( only valid when --schema is specified )
 */

$args = require_once dirname ( __FILE__ ) . '/_script_core.php';

// global or site specific class path?
if ( array_key_exists ( 'site', $args ) ) {
    $outPath = phpr\Config::get_site_class_path_by_name ( $args['site'] );
} else {
    $outPath = phpr\Config::get_shared_class_path ();
}

$where = [
    "table_schema NOT IN (
               'information_schema',
               'mysql',
               'performance_schema')"
];
$queryParams = [ ];
// process table and schema longopts
if ( isset( $args['schema'] ) ) {
    $where[] = 'table_schema = ?';
    $queryParams[] = $args['schema'];

    // table is only recognized when a schema a specified
    if ( isset( $args['table'] ) ) {
        $where[] = 'table_name = ?';
        $queryParams[] = $args['table'];
    }
}

$where = implode ( ' AND ', $where );

/*
 * Generates db models from all user created schemas
 */

$getAllSchemas = "
           SELECT
             *
           FROM
             INFORMATION_SCHEMA.COLUMNS
           WHERE
             {$where}
           ORDER BY
             table_schema,
             table_name,
             ordinal_position";

$rows = phpr\Database\Model\Generic::query ( $getAllSchemas, $queryParams );
$schema = null;
$table = null;

if ( class_exists ( '\Colors\Color' ) ) {
    $c = new \Colors\Color();
}

/**
 * @var  $row \phpr\Database\Model
 */
// each row is a new column in a specific table
foreach ( $rows as $index => $row ) {

    $isNewSchema = $schema !== $row->TABLE_SCHEMA;
    $isNewTable = $table !== $row->TABLE_NAME;

    // new schema?
    if ( $isNewSchema ) {
        $schema = $row->TABLE_SCHEMA;
        $safeSchema = classSafeName ( $schema );
    }

    // new table?
    if ( $isNewTable ) {
        $table = $row->TABLE_NAME;
        $safeTable = classSafeName ( $table );
    }

    // does this row belong to a different table the last one?
    if ( $isNewSchema || $isNewTable ) {

        echo 'processing ';

        if ( isset( $c ) ) {
            echo $c( "`{$schema}`" )->black->highlight ( 'cyan' );
            echo '.';
            echo $c( "`{$table}`" )->white->bold->highlight ( 'green' );
        } else {
            echo "`{$schema}`.`{$table}";
        }

        echo PHP_EOL;

        // save file if we have one
        if ( isset( $coreGenerator ) ) {

            save ();
        }

        // create class name
        $namespace = 'DB\\' . $safeSchema;
        $name = $safeTable;
        $coreNamespace = 'DBCore\\' . $safeSchema;
        $coreName = $safeTable;

        // create file path
        $filepath = strtolower ( $outPath . '/db/' . $safeSchema . '/' . $name . '.php' );
        $coreFilepath = strtolower ( $outPath . '/dbcore/' . $safeSchema . '/' . $coreName . '.php' );

        $dbClass = new phpr\ClassGen\ClassGenClass( $name );
        $dbClass->set_extends ( '\\' . $coreNamespace . '\\' . $coreName )
                ->set_namespace ( $namespace );
        $generator = new phpr\ClassGen\ClassGenGenerator( $dbClass, $filepath );

        $dbCoreClass = new phpr\ClassGen\ClassGenClass( $coreName );
        $dbCoreClass->set_extends ( 'Model' )
                    ->set_namespace ( $coreNamespace )
                    ->append_use ( 'phpr\Database\Model' );
        $coreGenerator = new phpr\ClassGen\ClassGenGenerator( $dbCoreClass, $coreFilepath );

        // add table and schema name
        $schemaProperty = new phpr\ClassGen\ClassGenProperty( 'schema', $row->TABLE_SCHEMA );
        $tableProperty = new phpr\ClassGen\ClassGenProperty( 'table', $row->TABLE_NAME );

        $schemaProperty->set_const ();
        $tableProperty->set_const ();

        $coreGenerator->addProperty ( $schemaProperty );
        $coreGenerator->addProperty ( $tableProperty );

        // reset these
        $autoIncrementColumn = '';
        $primaryKeys = [ ];
        $nonNullColumns = [ ];
        $dBColumnsArray = [ ];

    }

    // add all columns to helper array
    $dBColumnsArray[$row->COLUMN_NAME] = [ ];

    // add special properties
    if ( $row->COLUMN_KEY === 'PRI' ) {
        $primaryKeys[] = $row->COLUMN_NAME;
        $dBColumnsArray[$row->COLUMN_NAME][] = phpr\Database\Model::PROP_PRIMARY_KEY;
    }

    if ( $row->IS_NULLABLE === 'NO' ) {
        $nonNullColumns[] = $row->COLUMN_NAME;
    }

    // parse extra column properties into array
    $extras = explode ( ',', $row->EXTRA );
    if ( in_array ( 'auto_increment', $extras ) ) {
        $autoIncrementColumn = $row->COLUMN_NAME;
        $dBColumnsArray[$row->COLUMN_NAME][] = phpr\Database\Model::PROP_AUTO_INCREMENT;
    }

    if ( $rows->isLastRow () ) {

        save ();

    }

}

function classSafeName ( $name ) {

    // strip out anything that isn't a letter
    $name = preg_split ( '/[^a-zA-Z]/', $name, -1, PREG_SPLIT_NO_EMPTY );

    // capitalize first letter in each word
    $name = array_map (
        function ( $word ) {

            return ucwords ( $word );
        }, $name );

    // glue all words back together
    $name = implode ( '', $name );

    return $name;

}

function save () {

    global $generator, $coreGenerator, $autoIncrementColumn, $primaryKeys, $nonNullColumns, $dBColumnsArray;

    // add comments to the class
    $date = date ( 'Y/m/d' );
    $generator->class->phpDoc =
        "/**
 * Class {$generator->class->name}
 * @package {$generator->class->namespace}
 * @author jrobinson (robotically)
 * @date {$date}
 * @inheritdoc
 * This file is only generated once
 * Put your class specific code in here
 */";

    $phpDoc =
        "/**
 * Class {$coreGenerator->class->name}
 * @package {$coreGenerator->class->namespace}
 * @author jrobinson (robotically)
 * @date {$date}
";

    // add magic properties to core class
    foreach ( $dBColumnsArray as $columnName => $properties ) {
        $phpDoc .= " * @property mixed \${$columnName}
";
    }

    $phpDoc .=
        " * AUTO-GENERATED FILE
 * DO NOT EDIT THIS FILE BECAUSE IT WILL BE LOST
 * Put your code in {$generator->class->name}
 */";

    $coreGenerator->class->phpDoc = $phpDoc;

    // add the primary keys and autoincrement columns
    $AIProperty = new phpr\ClassGen\ClassGenProperty( 'autoIncrementColumn', $autoIncrementColumn );
    $AIProperty->set_const ();

    $primaryKeys = new phpr\ClassGen\ClassGenProperty( 'primaryKeys', $primaryKeys );
    $primaryKeys->set_const ();

    $nonNullColumns = new phpr\ClassGen\ClassGenProperty( 'nonNullColumns', $nonNullColumns );
    $nonNullColumns->set_const ();

    $columnProperties = new phpr\ClassGen\ClassGenProperty( 'dBColumnPropertiesArray', $dBColumnsArray );
    $columnProperties->isStatic = true;

    $columnDefaultValues = new phpr\ClassGen\ClassGenProperty( 'dBColumnDefaultValuesArray', array_fill_keys ( array_keys ( $dBColumnsArray ), null ) );
    $columnDefaultValues->isStatic = true;

    $coreGenerator->addProperty ( $AIProperty );
    $coreGenerator->addProperty ( $primaryKeys );
    $coreGenerator->addProperty ( $nonNullColumns );
    $coreGenerator->addProperty ( $columnProperties );
    $coreGenerator->addProperty ( $columnDefaultValues );

    // save file if we have one
    if ( isset( $coreGenerator ) ) {
        $coreGenerator->save ();
    }

    if ( !file_exists ( $generator->filepath ) ) {
        $generator->save ();
    }

}
