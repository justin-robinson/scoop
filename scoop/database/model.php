<?php

namespace Scoop\Database;

/**
 * Class Model
 * @package Scoop\Database
 */
abstract class Model extends Model\Generic {

    /**
     * db schema
     */
    const SCHEMA = null;

    /**
     * db table
     */
    const TABLE = null;

    /**
     * Name of auto increment column
     */
    const AUTO_INCREMENT_COLUMN = null;

    /**
     * array of primary keys
     */
    const PRIMARY_KEYS = null;

    /**
     * array of non null columns
     */
    const NON_NULL_COLUMNS = null;

    const PROP_AUTO_INCREMENT = 0;

    const PROP_PRIMARY_KEY = 1;

    const PROP_UNIQUE = 2;

    const PROP_NOT_NULL = 3;

    const PROP_NULL = 4;

    /**
     * @var array
     */
    public static $dBColumnPropertiesArray;

    /**
     * @var array
     */
    public static $dBColumnDefaultValuesArray;

    /**
     * @return bool|int|Model|Rows
     */
    public static function fetch_one () {

        $one = self::fetch ( 1 );

        return $one ? $one->current () : $one;

    }

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return bool|int|Rows
     */
    public static function fetch ( $limit = 1000, $offset = 0 ) {

        // build sql
        $sql =
            "SELECT
                *
            FROM
              " . static::get_sql_table_name () . "
            LIMIT ?,?";

        // run sql
        return self::query ( $sql, [ $offset, $limit ] );

    }

    /**
     * @return string
     */
    public static function get_sql_table_name () {

        return "`" . static::SCHEMA . "`.`" . static::TABLE . "`";
    }

    /**
     * @param $ID
     *
     * @return bool|Model
     */
    public static function fetch_by_id ( $ID ) {

        $row = false;

        if ( !empty( static::AUTO_INCREMENT_COLUMN ) ) {
            $where = '`' . static::AUTO_INCREMENT_COLUMN . '` = ?';

            $row = static::fetch_one_where ( $where, [ $ID ] );
        }

        return $row;
    }

    /**
     * @param       $where string
     * @param array $queryParams
     *
     * @return Model|bool
     */
    public static function fetch_one_where ( $where, array $queryParams = [ ] ) {

        $rows = static::fetch_where ( $where, $queryParams, 1 );

        if ( !empty( $rows ) ) {
            return $rows[0];
        } else {
            return false;
        }
    }

    /**
     * @param       $where
     * @param int   $limit
     * @param int   $offset
     * @param array $queryParams
     *
     * @return Rows
     */
    public static function fetch_where ( $where, array $queryParams = [ ], $limit = 1000, $offset = 0 ) {

        $sql = "
            SELECT
                *
            FROM
              " . static::get_sql_table_name () . "
            WHERE " . $where . "
            LIMIT ?,?";

        $queryParams[] = $offset;
        $queryParams[] = $limit;

        // run sql
        return self::query ( $sql, $queryParams );

    }

    /**
     * Assigns db column values to the dbValuesArray and all other values directly to
     * the object
     *
     * @param $name
     * @param $value
     */
    public function __set ( $name, $value ) {

        if ( array_key_exists ( $name, static::$dBColumnPropertiesArray ) ) {
            $this->dBValuesArray[$name] = $value;
        } else {
            $this->{$name} = $value;
        }
    }

    /**
     * Save this instance to the database
     */
    public function save () {

        if ( method_exists ( $this, 'validate' ) ) {
            $this->validate ();
        }

        if ( property_exists ( $this, 'dateTimeUpdated' ) ) {
            $this->set_literal ( 'dateTimeUpdated', 'NOW()' );
        }

        if ( $this->loadedFromDb ) {
            $this->update ();
        } else {

            if ( property_exists ( $this, 'dateTimeAdded' ) ) {
                $this->set_literal ( 'dateTimeAdded', 'NOW()' );
            }

            $columns = $this->get_db_values_array ();

            // remove columns marked by the db to be NON NULL but we have them locally as null
            foreach ( static::NON_NULL_COLUMNS as $columnName ) {
                if ( array_key_exists ( $columnName, $columns ) && is_null ( $columns[$columnName] ) ) {
                    unset( $columns[$columnName] );
                }
            }

            // build names of columns we are saving and add query params
            $columnNames = '';
            $values = '';
            $queryParams = [ ];
            foreach ( $columns as $columnName => $value ) {

                // add column name
                $columnNames .= "`{$columnName}`,";

                // value placeholder
                $values .= '?,';

                // value param
                $queryParams[] = $value;
            }
            // remove last comma
            $columnNames = rtrim ( $columnNames, ',' );
            $values = rtrim ( $values, ',' );

            // build sql statement
            $sql =
                "INSERT INTO
              " . static::get_sql_table_name () . "
              ({$columnNames})
              VALUES
              ({$values})";

            // execute sql statement
            $result = self::query ( $sql, $queryParams );

            // log change on success
            if ( $result ) {

                // get auto incremented id if one was generated
                if ( $ID = Connection::get_insert_id () ) {

                    $IDColumn = static::AUTO_INCREMENT_COLUMN;

                    $this->{$IDColumn} = $ID;

                }

                $this->loaded_from_database ();
            }
        }

    }

    /**
     * @param $key string
     * @param $value
     */
    public function set_literal ( $key, $value ) {

        $this->{$key} = new Literal( $value );
    }

    /**
     * Update a model loaded from the db
     */
    protected function update () {

        // what column values did we change?
        $dirtyColumns = $this->get_dirty_columns ();

        // did we change any?
        if ( !empty ( $dirtyColumns ) > 0 ) {

            $queryParams = [ ];

            // build the values we are updating
            $updatedValues = '';
            foreach ( $dirtyColumns as $columnName => $value ) {
                $updatedValues .= "`{$columnName}` = ?,";
                $queryParams[] = $value;
            }
            $updatedValues = rtrim ( $updatedValues, ',' );

            // try to identify by primary key or original db values
            $rowIdentifiers = empty( static::PRIMARY_KEYS )
                ? array_keys ( $this->orignalDbValuesArray )
                : static::PRIMARY_KEYS;

            // identify the row we are updating
            $where = '';
            foreach ( $rowIdentifiers as $columnName ) {
                $where .= "`{$columnName}` = ?,";
                $queryParams[] = $this->orignalDbValuesArray[$columnName];
            }
            $where = rtrim ( $where, ',' );

            $sql =
                "UPDATE
                " . $this->get_sql_table_name () . "
               SET
                {$updatedValues}
               WHERE
                {$where}";

            $result = self::query ( $sql, $queryParams );

            if ( $result ) {
                $this->loaded_from_database ();
            }

        }

    }

    /**
     * Get columns that have changed since we loaded from the db
     * @return array
     */
    public function get_dirty_columns () {

        $dbValuesArray = $this->get_db_values_array ();

        // auto increment columns should never be marked as dirty
        unset( $dbValuesArray[static::AUTO_INCREMENT_COLUMN] );

        if ( $this->loadedFromDb ) {
            $dirtyColumns = array_diff_assoc ( $dbValuesArray, $this->orignalDbValuesArray );

            // we don't care about timestamps
            if ( isset( $dirtyColumns['dateTimeAdded'] ) ) {
                unset( $dirtyColumns['dateTimeAdded'] );
            }
            if ( isset( $dirtyColumns['dateTimeUpdated'] ) ) {
                unset( $dirtyColumns['dateTimeUpdated'] );
            }
        } else {
            $dirtyColumns = $dbValuesArray;
        }

        return $dirtyColumns;
    }

    /**
     * get column names of the model
     * @return array
     */
    public function get_db_values_array () {

        return $this->dBValuesArray;
    }

    /**
     * @return array
     */
    public function get_column_names () {

        return array_keys ( static::$dBColumnPropertiesArray );
    }

    /**
     * @return bool
     */
    public function has_id () {

        $IDColumn = static::AUTO_INCREMENT_COLUMN;

        return is_numeric ( $this->{$IDColumn} );
    }

    /**
     * @param array $dataArray
     */
    public function populate ( array $dataArray ) {

        $this->dBValuesArray = $this->dBValuesArray = array_merge(
            $this->dBValuesArray,
            array_intersect_key($dataArray, $this->dBValuesArray));
    }
}
