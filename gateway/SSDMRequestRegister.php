<?php
require_once('SSDMGlobal.php');
require_once('SSDMRequestBase.php');
require_once('SSDMResponseBase.php');

class SSDMRequestRegister extends SSDMRequestBase
{
    public $user_name = '';
    public $display_name = '';
    public $email = '';
    public $password = '';

    function send_activation_email($request_register, $ticket)
    {
        $headers = 'From: ' . getenv('ADMIN_EMAIL') . '\r\n' .
        'Reply-To: ' . getenv('REPLY_TO_EMAIL') . '\r\n' .
        'X-Mailer: PHP/' . phpversion();

        $subject = 'Activate your Standstill Digital Media account';

        $message = 'Thanks for creating an account with us. Once activated, you can use this account to log into a variety of games on the Standstill Digital Media platform. \r\n \r\n' .
        'Account Details:\r\n' .
        'User Name: ' . $request_register->user_name . '\r\n' .
        'Display Name: ' . $request_register->display_name . 'r\n \r\n' .
        'Activation Link: \r\n' .
        GATEWAY_ACTIVATE_URL . '?code=' . $ticket;

        if(!mail($request_register->email, $subject, $message, $headers)) 
        {
            return false;
        } 
        return true;
    }

    public function set_user_name($user_name)
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

    public function set_display_name($display_name)
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

    public function set_email($email)
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

    public function set_password($password)
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

    public function set_register_request($incoming_request)
    {
        $this->set_user_name($incoming_request->user_name);
        $this->set_display_name($incoming_request->display_name);
        $this->set_email($incoming_request->email);
        $this->set_password($incoming_request->password);
    }

    public function validate_register_request($incoming_request)
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

    public function process_request($incoming_request)
    {
        $this->deserialize_request($incoming_request);
        $response_register = new SSDMResponseBase();
        if($this->success)
        {
            $response = $this->send_curl_request(AUTH_REGISTER_URL);
            if(!$response)
            {
                $response_register->set_message("Communication error. Please try again.");
                return json_encode($response_register);
            }
            if(!property_exists($response, 'success'))
            {
                $response_register->set_message("Invalid response from server. Please try again.");
                return json_encode($response_register);
            } 
            if(!$response->success)
            {
                $response_register->set_message($response->message);
                return json_encode($response_register);
            }
            if(!$this->send_activation_email($this, $response->message))
            {
                $response_register->set_message('Unable to send activation email');
                return json_encode($response_register);
            }
            $response_register->success = true;
            $response_register->set_message("tis true");
        }
        else
        {
            $response_register->set_message("no suiccess");
            $response_register->set_message($this->message);
        }
        $response_register->set_message("end");
        return json_encode($response_register);
    }
}
?>