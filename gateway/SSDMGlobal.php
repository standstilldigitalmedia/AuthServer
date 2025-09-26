<?php
session_start();

const GATEWAY_ACTIVATE_URL = "http://localhost/gateway/activate.php";
const AUTH_REGISTER_URL = "http://localhost/authentication/register.php";
const MAX_NAME_LENGTH = 30;
const MAX_EMAIL_LENGTH = 255;
const MIN_NAME_LENGTH = 6;
const MIN_PASSWORD_LENGTH = 8;
const MAX_CLIENT_ID_LENGTH = 255;

const TICKET_LENGTH = 16;

const SECOND = 1;
const MINUTE = 60 * SECOND;
const HOUR = 60 * MINUTE;
const DAY = 24 * HOUR;

const ACTIVATE_EXPIRATION_TIME = MINUTE * 10;
const REMEMBER_ME_EXPIRATION_TIME = DAY * 31;
const LOGIN_EXPIRATION_TIME = MINUTE;

const REGISTER_REQUEST = 1;
const LOGIN_REQUEST = 3;
const REMEMBER_ME_REQUEST = 4;

?>