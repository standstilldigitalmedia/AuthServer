<?php
require_once('SSDMGlobal.php');
require_once('SSDMRequestBase.php');
require_once('SSDMResponseBase.php');

class SSDMRequestActivate extends SSDMRequestBase
{
    public $token = '';
    public $code = '';

    public function set_token($token)
    {
        if(empty($token))
        {
            $this->set_message('Invalid request');
            return;
        }
        $this->token = $token;
    }

    public function set_code($code)
    {
        if(empty($code))
        {
            $this->set_message('Invalid request');
            return;
        }
        $this->code = $code;
    }

    public function set_activate_request($incoming_request)
    {
        $this->set_incoming_request($incoming_request);
        $this->set_token($incoming_request->token);
        $this->set_code($incoming_request->code);
    }

    public function validate_activate_request($incoming_request)
    {
        if(!$this->validate_incoming_request($incoming_request))
        {
            return false;
        }
        if(!property_exists($incoming_request, 'token'))
        {
            $this->set_message('Invalid request.');
            return false;
        }
        if(!property_exists($incoming_request, 'code'))
        {
            $this->set_message('Invalid request.');
            return false;
        }
        return true;
    }

    public function deserialize_request($incoming_request)
    {
        if($this->validate_activate_request($incoming_request))
        {
            $this->set_activate_request($incoming_request);
        }
        parent::deserialize_request($incoming_request);
    }

    public function process_request($incoming_request)
    {
        $this->deserialize_request($incoming_request);
        $response_activate = new SSDMResponseBase();
        if($this->success)
        {
            $token = SSDMToken::create_token($this, $this->secret_key);
            $response = $this->send_curl_request(AUTH_ACTIVATE_URL, $token);
            if(!$response)
            {
                $response_activate->set_message("Communication error. Please try again.");
                return json_encode($response_activate);
            }
            if(!property_exists($response, 'success'))
            {
                $response_activate->set_message("Invalid response from server. Please try again.");
                return json_encode($response_activate);
            } 
            if(!$response->success)
            {
                $response_activate->set_message($response->message);
                return json_encode($response_activate);
            }
            $response_activate->success = true;
            $response_activate->create_expire_token(10 * MINUTE, $this->secret_key);
        }
        else
        {
            $response_activate->set_message($this->message);
        }
        return json_encode($response_activate);
    }
}
?>