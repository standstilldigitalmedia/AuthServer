<?php
class SSDMResponseBase
{
    public $success = false;
    public $message = '';
    public $token = "";

    public function set_message($message)
    {
        if($this->message == '')
        {
            $this->message = $message;
        }
    }

    public function create_expire_token($time_to_add, $secret_key)
    {
        $payload = new stdClass();
        $payload->expires = time() + $time_to_add;
        $this->token = SSDMToken::create_token($payload, $secret_key);
    }

    public function serialize_response()
    {
        return json_encode($this);
    }
}
?>