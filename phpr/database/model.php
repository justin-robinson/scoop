<?php

namespace phpr\Database;

/**
 * Class Model
 * @package phpr\Database
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
     * @param int $limit
     * @param int $offset
     * @return Rows
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
     * @return Model
     */
    public static function fetch_one () {

        return self::fetch ( 1 )->current ();
    }

    /**
     * @param $where
     * @param int $limit
     * @param int $offset
     * @param array $queryParams
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
     * @param $where string
     * @param array $queryParams
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
     * @param $ID
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

            // column names to insert
            $names = array_keys ( $columns );

            // build sql statement
            // todo replace sql safe string function with mysqli binding setup
            $sql =
                "INSERT INTO
              " . static::get_sql_table_name () . "
              ( " . self::array_to_sql_safe_string ( $names, '`' ) . ")
              VALUES
              ( " . self::array_to_sql_safe_string ( $columns ) . ")";

            // execute sql statement
            $result = self::query ( $sql );

            // log change on success
            if ( $result ) {

                // get auto incremented id if one was generated
                if ( $ID = Connection::get_insert_id () ) {

                    $IDColumn = static::AUTO_INCREMENT_COLUMN;

                    $this->$IDColumn = $ID;

                }

                $this->loaded_from_database ();
            }
        }

    }

    /**
     * Update a model loaded from the db
     */
    protected function update () {

        // what column values did we change?
        $dirtyColumns = $this->get_dirty_columns ();

        // did we change any?
        if ( count ( $dirtyColumns ) > 0 ) {

            $sqlColumnChanges = [ ];
            foreach ( $dirtyColumns as $columnName => $value ) {
                $sqlColumnChanges[] = r3a ( $columnName, '`' ) . '=' . print_sql ( $value );
            }

            $primaryKeyWhere = [ ];
            foreach ( static::PRIMARY_KEYS as $columnName ) {
                $primaryKeyWhere[] = r3a ( $columnName, '`' ) . '=' . print_sql ( $this->$columnName );
            }

            $sql =
                "UPDATE
                " . $this->get_sql_table_name () . "
               SET
               " . implode ( ',', $sqlColumnChanges ) . "
               WHERE
               " . implode ( ' AND ', $primaryKeyWhere );

            $result = self::query ( $sql );

            if ( $result ) {
                $this->loaded_from_database ();
            }

        }

    }

    /**
     * get column names of the model
     * @return array
     */
    public function get_db_values_array () {

        return $this->dBValuesArray;
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
     * @return string
     */
    public static function get_sql_table_name () {

        return "`" . static::SCHEMA . "`.`" . static::TABLE . "`";
    }

    /**
     * @param $array
     * @param string $quoteChar
     * @return string
     */
    public static function array_to_sql_safe_string ( &$array, $quoteChar = "'" ) {

        r3a_array ( $array, $quoteChar );

        return implode ( ',', $array );

    }

    /**
     * @return bool
     */
    public function has_id () {

        $IDColumn = static::AUTO_INCREMENT_COLUMN;

        return is_numeric ( $this->$IDColumn );
    }

    /**
     * @param $key string
     * @param $value
     */
    public function set_literal ( $key, $value ) {

        $this->$key = new Literal( $value );
    }

    /**
     * Mark this object as loaded from the database
     */
    public function loaded_from_database () {

        $this->loadedFromDb = true;

        $this->orignalDbValuesArray = $this->dBValuesArray;

    }
}