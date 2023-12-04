<?php
 ?><?php
include_once "functions.php";
if (!isset($_GET["serial"])) {
    exit("serial is not present");
}
onRfidSerialRead($_GET["serial"]);
