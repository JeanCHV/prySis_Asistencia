<?php
?><?php
if (!isset($_GET["employee_id"])) {
    exit("employee_id is not present");
}
include_once "functions.php";
$employee = getEmployeeRfidById($_GET["employee_id"]);
$serial = "";
if ($employee) {
    $serial = $employee->rfid_serial;
}
echo json_encode($serial);
