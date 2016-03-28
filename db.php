<!-- Utility PHP -->
<!-- Based off of https://netbeans.org/kb/docs/php/wish-list-lesson2.html -->


<?php
class RideshareDB extends mysqli
{

// single instance of self shared among all instances
    private static $instance = null;
    // db connection config vars
    private $user = "DataBasicTeam";
    private $pass = "CPSC304!";
    private $dbName = "DataBasic";
    private $dbHost = "databasic.cvhyllwoxxb3.us-west-1.rds.amazonaws.com";


    //This method must be static, and must return an instance of the object if the object
    //does not already exist.
    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    // The clone and wakeup methods prevents external instantiation of copies of the Singleton class,
    // thus eliminating the possibility of duplicate objects.
    public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

    public function __wakeup()
    {
        trigger_error('Deserializing is not allowed.', E_USER_ERROR);
    }

    // private constructor
    public function __construct()
    {
        parent::__construct($this->dbHost, $this->user, $this->pass, $this->dbName);
        if (mysqli_connect_error()) {
            exit('Connect Error (' . mysqli_connect_errno() . ') '
                . mysqli_connect_error());
        }
        parent::set_charset('utf-8');
    }

    public function verify_user_credentials($name, $password)
    {
        $name = $this->real_escape_string($name);
        $password = $this->real_escape_string($password);
        $result = $this->query("SELECT 1 FROM Driver WHERE name = '"
            . $name . "' AND password = '" . $password . "'");
        return $result->data_seek(0);
    }

    public function insert_car($licenseNum, $type, $color)
    {
        $licenseNum = $this->real_escape_string($licenseNum);
        $type = $this->real_escape_string($type);
        $color = $this->real_escape_string($color);

        $this->query("INSERT INTO Car (licenseNum, type, color)" .
            " VALUES ('" . $licenseNum . "', '" . $type. "', '" .$color."')");
    }

    public function create_driver($name, $email, $phoneNum, $password, $licenseNum) {
        $name = $this->real_escape_string($name);
        $email = $this->real_escape_string($email);
        $phoneNum = $this->real_escape_string($phoneNum);
        $licenseNum = $this->real_escape_string($licenseNum);
        $password = $this->real_escape_string($password);

        $this->query("INSERT INTO Driver (name, email, phoneNum, password, licenseNum)" .
        "VALUES ('" . $name . "',
         '" . $email . "',
         '" . $phoneNum . "',
         '" . $licenseNum . "',
         '" . $password . "'
         )");
    }

    function format_date_for_sql($date){
        if ($date == "")
            return null;
        else {
            $dateParts = date_parse($date);
            return $dateParts["year"]*10000 + $dateParts["month"]*100 + $dateParts["day"];
        }

    }


    public function create_rideshare($RID, $DID, $destination, $price, $address, $postalCode, $province,
                                     $city, $rdate, $rtime, $Ctime, $CDate,$seats,$seatsLeft){
        //initialize variables
        $RID = $this->real_escape_string($RID);
        $DID = $this->real_escape_string($DID);
        $destination = $this->real_escape_string($destination);
        $price = $this->real_escape_string($price);
        $address = $this->real_escape_string($address);
        $postalCode = $this->real_escape_string($postalCode);
        $province = $this->real_escape_string($province);
        $city = $this->real_escape_string($city);
        $rdate = $this->real_escape_string($rdate);
        $rtime = $this->real_escape_string($rtime);
        $Ctime = $this->real_escape_string($Ctime);
        $CDate = $this->real_escape_string($CDate);
        $seats = $this->real_escape_string($seats);
        $seatsLeft = $this->real_escape_string($seatsLeft);

        $this->query("INSERT INTO RideShare (RID,DID, destination, price, address, postalCode, province, city, rdate, rtime, Ctime, CDate, seats, seatsLeft)" .
            " VALUES (" . $RID . ", " . $DID . ",
             " . $destination . ", " . $price . ",
             " . $address . "," . $postalCode .",
             " . $province . ", " . $city . ",
             " . $this->format_date_for_sql($rdate) . "," . $rtime .",
             " . $Ctime . ", " . $this->format_date_for_sql($CDate) . ",
             " . $seats . ", " . $seatsLeft .")");}

    public function get_available_rideshares(){
        return $this->query("SELECT rdate, name, destination, price, seats, seatsLeft
                  FROM RideShare R, Driver D
                  WHERE R.DID = D.DID AND seatsLeft > 0 /*AND R.rdate >= cast(getdate() as date)*/ /*AND r.rtime >= cast(gettime() as time)*/");
    }

}
    ?>