<?
/**
  * @author jrobinson (robotically)
  * @date 2015/09/25
  * This file is only generated once
  * Put your class specific code in here
  */
class DB_Peak_Phone extends DBCore_Peak_Phone {

    public function validate () {

        $this->phone = preg_replace('/[^0-9]/', '', $this->phone);

        if ( strlen($this->phone) > 12 ) {
            $this->phone = substr($this->phone,0, 12);
        }
    }
}

?>