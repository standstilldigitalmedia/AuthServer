<?php
require_once('SSDMGlobal.php');
require_once('SSDMRequestBase.php');
require_once('SSDMResponseBase.php');
require_once('SSDMToken.php');
require_once('SSDMDatabase.php');

class SSDMRequestRegister extends SSDMRequestBase
{
    public $secret_key = "berryjuice";
    public $user_name = '';
    public $display_name = '';
    public $email = '';
    public $password = '';
    public $player_id = '';

    private function set_user_name($user_name)
    {
        if(empty($user_name))
        {
            $this->set_message('User name required');
            return;
        }
        
        if(strlen($user_name) > MAX_NAME_LENGTH)
        {
            $this->set_message('Username is too long');
            return;
        }
        
        if(strlen($user_name) < MIN_NAME_LENGTH)
        {
            $this->set_message('Username is too short');
            return;
        }
        $this->user_name = $user_name;
    }

    private function set_display_name($display_name)
    {
        if(empty($display_name))
        {
            $this->set_message('Display name required');
            return;
        }
        
        if(strlen($display_name) > MAX_NAME_LENGTH)
        {
            $this->set_message('Display name is too long');
            return;
        }
        
        if(strlen($display_name) < MIN_NAME_LENGTH)
        {
            $this->set_message('Display name is too short');
            return;
        }
        $this->display_name = $display_name;
    }

    private function set_email($email)
    {
        
        if(empty($email))
        {
            $this->set_message('Email address required');
            return;
        }
        
        if(strlen($email) > MAX_EMAIL_LENGTH)
        {
            $this->set_message('Email is too long');
            return;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) 
        {
            $this->set_message('Email is invalid.');
            return;
        }
        $this->email = $email;
    }        

    private function set_password($password)
    {
        if(empty($password))
        {
            $this->set_message('Password required');
            return false;
        }
        
        if(strlen($password) < MIN_PASSWORD_LENGTH)
        {
            $this->set_message('Password is too short');
            return false;
        }
        $this->password = $password;
    }

    private function set_register_request($incoming_request)
    {
        $this->set_user_name($incoming_request->user_name);
        $this->set_display_name($incoming_request->display_name);
        $this->set_email($incoming_request->email);
        $this->set_password($incoming_request->password);
    }

    private function validate_register_request($incoming_request)
    {
        if(!property_exists($incoming_request, 'user_name'))
        {
            $this->set_message('No username');
            return false;
        }
        if(!property_exists($incoming_request, 'display_name'))
        {
            $this->set_message('No display name');
            return false;
        }
        if(!property_exists($incoming_request, 'email'))
        {
            $this->set_message('No email');
            return false;
        }
        if(!property_exists($incoming_request, 'password'))
        {
            $this->set_message('No password');
            return false;
        }
        return true;
    }

    public function deserialize_request($incoming_request)
    {
        if($this->validate_register_request($incoming_request))
        {
            $this->set_register_request($incoming_request);
        }
        parent::deserialize_request($incoming_request);
    }

    public function process_request($incoming_token)
    {
        $response_register = new SSDMResponseBase();
        if(SSDMToken::validate_token($incoming_token, $this->secret_key))
        {
            $this->deserialize_request(SSDMToken::decode_payload($incoming_token));
            if(!empty($request_register->message))
            {
                $response_register->set_message($this->message);
                return SSDMToken::create_token($response_register, $this->secret_key);
            }
            if(SSDMDatabase::new_player_exists($this))
            {
                $response_register->message = "Record already exists";
                return SSDMToken::create_token($response_register, $this->secret_key);
            }
            $player_id = SSDMDatabase::add_new_player_to_database($this);
            if(!$player_id)
            {
                $response_register->message = "Database error. Please try again";
                return SSDMToken::create_token($response_register, $this->secret_key);
            }
            $this->player_id = $player_id;
            $session_ticket = SSDMDatabase::add_activate_session_to_database($this);
            if(!$session_ticket)
            {
                $response_register->message = "Database error. Please try again";
                return SSDMToken::create_token($response_register, $this->secret_key);
            }
            $response_register->success = true;
            $response_register->message = $session_ticket;
        }
        return SSDMToken::create_token($response_register, $this->secret_key);
    }
}
?>