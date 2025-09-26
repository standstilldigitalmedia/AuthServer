<?php
class SSDMToken
{
    public static function generate_unique_id($strength, $length) 
    {
        if($length < 1)
        {
            return false;
        }

        $characters = '0123456789';
        switch($strength)
        {
            case 0:
                $characters = '123456789';
                break;
            case 1:
                $characters = '123456789ABCDEFGHIJKLMNPQRSTUVWXYZ';
                break;
            case 2:
                $characters = '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ';
                break;
            case 3:
                $characters = '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ$-_.+!*();?:@=&<>#%{}|\^~[]';
                break;
        }

        $characters_length = strlen($characters);
        $random_string = '';
        $bytes = random_bytes($length);
        for ($i = 0; $i < $length; $i++) 
        {
            $random_string .= $characters[ord($bytes[$i]) % $characters_length];
        }
        return $random_string;
    }

    private static function base64_url_encode($data)
    {
        $base64 = base64_encode($data);
        $base64_url = strtr($base64, '+/', '-_');
        return rtrim($base64_url, '=');
    }

    private static function base64_url_decode($data)
    {
        $base64 = strtr($data, '-_', '+/');
        $base64_padded = str_pad($base64, strlen($base64) % 4, '=', STR_PAD_RIGHT);
        return base64_decode($base64_padded);
    }

    public static function create_token($payload, $secret_key)
    {
        $header = SSDMToken::base64_url_encode(json_encode(['alg' => 'HS256', 'typ' => 'JWT']));
        $payload = SSDMToken::base64_url_encode(json_encode($payload));
        $signature = hash_hmac('sha256', $header . '.' . $payload, $secret_key, false);
        $signature = SSDMToken::base64_url_encode($signature);
        return $header . '.' . $payload . '.' . $signature;
    }

    public static function validate_token($token, $secret_key)
    {
        $explode = explode('.', $token);
        if(count($explode) < 3)
        {
            //error
        }
        $header = $explode[0];
        $payload = $explode[1];
        $signature = $explode[2];
        $signature = SSDMToken::base64_url_decode($signature);
        $expected_signature = hash_hmac('sha256', $header . '.' . $payload, $secret_key, false);
        return hash_equals($signature, $expected_signature);
    }
    
    public static function decode_header($token)
    {
        $explode = explode('.', $token);
        $header = $explode[0];
        $header = SSDMToken::base64_url_decode($header);
        return json_decode($header, false);
    }

    public static function decode_payload($token)
    {
        $explode = explode('.', $token);
        $payload = $explode[1];
        $payload = SSDMToken::base64_url_decode($payload);
        return json_decode($payload, false);
    }
}
?>