<?php

namespace phpr\Database;

abstract class Model extends Model\Generic {

    const SCHEMA = null;
    const TABLE = null;
    const AUTO_INCREMENT_COLUMN = null;
    const PRIMARY_KEYS = null;

    const AUTO_INCREMENT = 0;
    const PRIMARY_KEY = 1;
    const UNIQUE = 2;
    const NOT_NULL = 3;
    const NULL = 4;


    // fetch rows from models assigned table
    public static function fetch( $limit = 1000 ) {

        // build sql
        $sql =
            "SELECT
                *
            FROM
              " . static::get_sql_table_name() . "
            LIMIT {$limit}";

        // run sql
        return self::query($sql);

    }

    public static function fetch_one () {
        return self::fetch(1)->current();
    }

    public static function fetch_where ($where, $limit = 1000) {

        $sql = "
            SELECT
                *
            FROM
              " . static::get_sql_table_name() . "
            WHERE " . $where . "
            LIMIT {$limit}";

        // run sql
        return self::query($sql);

    }

    public static function fetch_one_where($where) {
        $rows = static::fetch_where($where, 1);

        if ( $rows->numRows === 1 ) {
            return $rows[0];
        } else {
            return false;
        }
    }

    public static function fetch_by_id($ID) {

        $row = false;

        if ( !empty(static::AUTO_INCREMENT_COLUMN) ) {
            $where = '`' . static::AUTO_INCREMENT_COLUMN . '` = ' . r3a($ID);

            $row = static::fetch_one_where($where);
        }

        return $row;
    }

    // save this instance to the database
    public function save () {

        if (method_exists($this, 'validate') ) {
            $this->validate();
        }

        if ( property_exists($this, 'dateTimeUpdated') ) {
            $this->set_literal('dateTimeUpdated', 'NOW()');
        }

        if ( $this->loadedFromDB ) {
            $this->update();
        } else {

        if ( property_exists($this, 'dateTimeAdded') ) {
            $this->set_literal('dateTimeAdded', 'NOW()');
        }
            $columns = $this->get_columns();

            // column names to insert
            $names = array_keys($columns);

            // build sql statement
            $sql =
                "INSERT INTO
              " . static::get_sql_table_name() . "
              ( " . self::array_to_sql_safe_string($names, '`'). ")
              VALUES
              ( " . self::array_to_sql_safe_string($columns) . ")";

            // execute sql statement
            $result = self::query($sql);


            // log change on success
            if ( $result ) {

                // get auto incremented id if one was generated
                if ( $ID = Connection::insert_id() ) {

                    $IDColumn = static::AUTO_INCREMENT_COLUMN;

                    $this->$IDColumn = $ID;

                }

                $this->log_history();
                $this->loaded_from_database();
            }
        }


    }

    protected function update () {

        // what column values did we change?
        $dirtyColumns = $this->get_dirty_columns();

        // did we change any?
        if ( count($dirtyColumns) > 0 ) {

            $sqlColumnChanges = [];
            foreach ( $dirtyColumns as $columnName => $value ) {
                $sqlColumnChanges[] = r3a($columnName, '`') . '=' . print_sql($value);
            }

            $primaryKeyWhere = [];
            foreach ( static::PRIMARY_KEYS as $columnName ) {
                $primaryKeyWhere[] = r3a($columnName, '`') . '=' . print_sql($this->$columnName);
            }

            $sql =
                "UPDATE
                " . $this->get_sql_table_name() . "
               SET
               " . implode(',', $sqlColumnChanges) . "
               WHERE
               " . implode(' AND ', $primaryKeyWhere);

            $result = self::query($sql);

            if ( $result ) {
                $this->log_history();
                $this->loaded_from_database();
            }

        }

    }

    // get column names of the model
    public function get_columns () {

        // associate array of column names and values
        $columns = [];

        // build column values array
        foreach ( $this->DBColumnsArray as $columnName => $properties ) {

            // don't insert on auto increment columns
            if ( $columnName !== static::AUTO_INCREMENT_COLUMN ) {

                $columns[$columnName] = $this->$columnName;

            }
        }

        return $columns;
    }

    public function get_dirty_columns() {
        $dirtyColumns = array_diff_assoc( $this->get_columns(), $this->orignalDBValues);

        // we don't care about timestamps
        unset($dirtyColumns['dateTimeAdded']);
        unset($dirtyColumns['dateTimeUpdated']);

        return $dirtyColumns;
    }

    public static function get_sql_table_name () {
        return "`" . static::SCHEMA . "`.`" . static::TABLE . "`";
    }

    public static function array_to_sql_safe_string ( &$array, $quoteChar = "'" ) {

        r3a_array($array, $quoteChar);

        return implode(',', $array);

    }

    public function has_id () {

        $IDColumn = static::AUTO_INCREMENT_COLUMN;

        return is_numeric($this->$IDColumn);
    }

    public function set_literal($key, $value) {
        $this->$key = new Literal($value);
    }

    public function loaded_from_database () {

        $this->loadedFromDB = true;

        foreach ( $this->DBColumnsArray as $columnName => $properties ) {
            $this->orignalDBValues[$columnName] = $this->$columnName;
        }

    }

    public function log_history() {

        // log only changes to certain tables
        $userIDColumnExists = array_key_exists('userID', $this->DBColumnsArray);
        $notHistoryTable = static::TABLE !== 'history';

        if ( $userIDColumnExists && $notHistoryTable ) {

            // get the columns that have changed
            $dirtyColumns = $this->get_dirty_columns();

            // insert each change into the history table
            foreach ( $dirtyColumns as $dirtyColumnName => $newValue ) {

                $data = [
                    'userID' => $this->userID,
                    'columnName' => $dirtyColumnName,
                    'old' => $this->orignalDBValues[$dirtyColumnName],
                    'new' => $newValue
                ];

                $history = new DB_Peak_History($data);
                $history->save();


            }

        }
    }
}