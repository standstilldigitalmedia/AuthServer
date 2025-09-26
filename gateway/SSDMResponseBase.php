<?php
class SSDMResponseBase
{
    public $success = false;
    public $message = '';

    public function set_message($message)
    {
        if($this->message == '')
        {
            $this->message = $message;
        }
    }

    public function serialize_response()
    {
        return json_encode($this);
    }
}
?>