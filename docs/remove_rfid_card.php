<?php
?><?php
if (!isset($_GET["rfid_serial"])) {
    exit("rfid_serial is not present");
}

include_once "functions.php";
removeRfidFromEmployee($_GET["rfid_serial"]);
