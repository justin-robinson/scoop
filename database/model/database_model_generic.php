<?

class Database_Model_Generic {

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

        // run the query
        $result = Database_Connection::query($sql);

        // check for errors
        if ( ! $result ) {
            trigger_error('MySQL Error Number ( ' . Database_Connection::errno() . ' )' . Database_Connection::error() );
            var_dump($sql);
        }

        // was this a select?
        $hasRows = is_object($result) && is_a($result, 'mysqli_result');

        // format the data if it was a select
        if ( $hasRows ) {

            // create a container for the rows
            $rows = new Database_Rows();

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

    public static function get_sql_history() {
        return self::$sqlHistoryArray;
    }

    public function loaded_from_database () {

        $this->loadedFromDB = true;

        foreach ( $this->DBColumnsArray as $columnName => $properties ) {
            $this->orignalDBValues[$columnName] = $this->$columnName;
        }

    }
}


?>