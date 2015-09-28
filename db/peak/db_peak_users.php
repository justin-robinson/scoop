<?
/**
  * @author jrobinson (robotically)
  * @date 2015/09/25
  * This file is only generated once
  * Put your class specific code in here
  */
class DB_Peak_Users extends DBCore_Peak_Users {

    public function __construct ($dataArray = []) {
        parent::__construct($dataArray);


        // pull related addresses and phone numbers if we can
        if ( $this->has_id() ){
            $this->addresses = DB_Peak_Addresses::fetch_where("`userID` = " . Database_Connection::real_escape_string($this->userID));

            $this->phones = DB_Peak_Phone::fetch_where("`userID` = " . Database_Connection::real_escape_string($this->userID));
        }
    }

    public function save () {

        parent::save();

        foreach ( $this->addresses as $address ) {
            $address->save();
        }

        foreach ( $this->phones as $phone ) {
            $phone->save();
        }

    }

    public function validate () {

        // we only want to save the employeeID for employees and the company name for users
        $userTypeID = $this->userTypeID ?: 1;

        $userType = DB_Peak_UserTypes::fetch_by_id($userTypeID);

        switch ( $userType->userType ) {
            case 'employee' :
                $this->companyName = null;
                break;
            case 'customer' :
                $this->employeeID = null;
                if ( !preg_match('/[^0-9a-zA-Z]/', $this->employeeID) ) {
                    serverError("User's employeeID must be alphanumeric");
                }
                break;
            default:
                break;
        }

    }

    public function addAddress( DB_Peak_Addresses $address ) {

        $this->save();

        $address->userID = $this->userID;

        $address->save();
    }

    public function addPhone ( DB_Peak_Phone $phone ) {

        $this->save();

        $phone->userID = $this->userID;

        $phone->save();
    }

}

?>