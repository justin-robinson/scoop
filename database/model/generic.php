<?

namespace Database\Model;

class Generic {

    protected $loadedFromDB = false;
    protected $orignalDBValues;
    protected $DBColumnsArray = [];

    private static $sqlHistoryArray = [];

    public function __construct($dataArray = []) {

        // by default all values are null
        $this->orignalDBValues = array_fill_keys(
            array_keys($this->DBColumnsArray),
            null);

        $this->populate($dataArray);
    }

    // run a raw sql query
    public static function query ($sql) {

        // log the query
        self::$sqlHistoryArray[] = $sql;

        // start sql transaction
        \Database\Connection::begin_transaction();

        // run the query
        $result = \Database\Connection::query($sql);

        // check for errors
        if ( ! $result ) {
            \Database\Connection::rollback();
            trigger_error('MySQL Error Number ( ' . \Database\Connection::errno() . ' )' . \Database\Connection::error() );
            var_dump($sql);
        }

        // commit this transaction
        \Database\Connection::commit();

        // was this a select?
        $hasRows = is_object($result) && is_a($result, 'mysqli_result');

        // format the data if it was a select
        if ( $hasRows ) {

            // create a container for the rows
            $rows = new \Database\Rows();

            // put all rows in the container
            while ( $row = $result->fetch_assoc() ) {

                $dbObject = new static($row);

                // make that this came from the DB
                $dbObject->loaded_from_database();

                $rows->addRow($dbObject);

            }


            // give the container back
            return $rows;
        } else {

            // return raw result if no data was given back to us
            return $result;
        }

    }

    // generate a new instance of this class from an associative array
    public function populate ( $dataArray ) {

        $dataArray = (array)$dataArray;

        foreach ( $dataArray as $colName => $colValue ) {
            $this->$colName = $colValue;
        }
    }

    public function to_stdclass(array $columnsToInclude = []) {

        if ( empty($columnsToInclude) ) {
            $columnsToInclude = $this->get_column_names();
        }

        $stdClass = new \StdClass();

        foreach ($columnsToInclude as $columnName ) {
            $stdClass->$columnName = $this->$columnName;
        }

        return $stdClass;
    }

    public function get_column_names () {
        return array_keys($this->DBColumnsArray);
    }

    public static function get_sql_history() {
        return self::$sqlHistoryArray;
    }

    public function loaded_from_database () {

        $this->loadedFromDB = true;

        foreach ( $this->DBColumnsArray as $columnName => $properties ) {
            $this->orignalDBValues[$columnName] = $this->$columnName;
        }

    }

    public static function strip_comments($sql) {
        // TODO implement strip comments function
    }
}


?>