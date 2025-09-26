<?php
header("Content-Type: application/json");

require_once("SSDMRequestRegister.php");

$request_register = new SSDMRequestRegister();
if(isset($_POST['token']))
{
    
    echo $request_register->process_request($_POST['token']);
}
else
{
    echo $request_register->invalid_request_response();
}
?>