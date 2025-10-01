<?php
require_once('SSDMGlobal.php');
require_once('SSDMToken.php');

class SSDMRequestBase
{
    public $secret_key = "berryjuice";
    public $success = false;
    public $message = '';
    public $client_id = '';
    

    public function set_message($message)
    {
        if($this->message == '')
        {
            $this->message = $message;
        }
    }

    public function set_client_id($client_id)
    {
        if(empty($client_id))
        {
            $this->set_message('Client ID required');
            return;
        }
        
        if(strlen($client_id) > MAX_CLIENT_ID_LENGTH)
        {
            $this->set_message('Client ID too long');
            return;
        }
        $this->client_id = $client_id;
    }

    protected function set_incoming_request($incoming_request)
    {
        $this->set_client_id($incoming_request->client_id);
    }

    protected function validate_incoming_request($incoming_request)
    {
        if(!isset($incoming_request))
        {
            $this->set_message('No request');
            return false;
        }
        
        if(!property_exists($incoming_request, 'client_id'))
        {
            $this->set_message('No client id set');
            return false;
        }
        return true;
    }

    public function send_curl_request($url, $token)
    {
        $data['token'] = $token;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_POST, true); // Specify POST method
        curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data)); // Attach POST data
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        if (curl_errno($curl)) 
        {
            $this->set_message(curl_error($curl));
            curl_close($curl);
            return false;
        }
        else
        {
            curl_close($curl);
        } 
        
        file_put_contents(getenv('DB_LOG_FILE'), 'now response ' . $response, FILE_APPEND);

        if(!SSDMToken::validate_token($response, $this->secret_key))
        {
            $this->set_message("Invalid token.");
            return false;
        }
        file_put_contents(getenv('DB_LOG_FILE'), 'returning', FILE_APPEND);
        return SSDMToken::decode_payload($response);
    }

    public function serialize_request()
    {
        return json_encode($this);
    }

    public function deserialize_request($incoming_request)
    {
        if($this->validate_incoming_request($incoming_request))
        {
            $this->set_incoming_request($incoming_request);
            if(empty($this->message))
            {
                $this->success = true;
            }
            else
            {
                $this->success = false;
            }
        }
    }
}
?>