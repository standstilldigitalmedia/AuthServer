<?php
header("Content-Type: application/json");

require_once('SSDMRequestRegister.php');

$incoming_request = json_decode(file_get_contents('php://input'), false);
$request_register = new SSDMRequestRegister();
echo $request_register->process_request($incoming_request);
?>