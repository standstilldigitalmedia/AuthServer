<?php
require_once('/home/www-data/authentication/SSDMGlobal.php');
require_once('/home/www-data/authentication/SSDMDatabase.php');

$player_auth_sql = 'CREATE TABLE player_auth (
    player_id VARCHAR(16) PRIMARY KEY,
    user_name VARCHAR(30) NOT NULL,
    display_name VARCHAR(30) NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    account_created DATETIME DEFAULT CURRENT_TIMESTAMP,
    last_login DATETIME DEFAULT CURRENT_TIMESTAMP,
    auth TINYINT UNSIGNED DEFAULT 0
)';

$sessions_sql = 'CREATE TABLE sessions (
    id int UNSIGNED AUTO_INCREMENT PRIMARY KEY, 
    player_id VARCHAR(16),
    session_ticket VARCHAR(16),
    expiration_date int UNSIGNED     
)';

$mysqli = SSDMDatabase::connect();
if(!$mysqli)
{
    return false;
}

$stmt = $mysqli->prepare($player_auth_sql);
if(!$stmt)
{
    $mysqli->close();
    SSDMDatabase::writeDbError('Prepare failed. ' . $sql);
    return false;
}

if(!$stmt->execute())
{
    $mysqli->close();
    SSDMDatabase::writeDbError('Execute failed. ' . $sql);
    return false;
}


$stmt = $mysqli->prepare($sessions_sql);
if(!$stmt)
{
    $mysqli->close();
    SSDMDatabase::writeDbError('Prepare failed. ' . $sql);
    return false;
}

if(!$stmt->execute())
{
    $mysqli->close();
    SSDMDatabase::writeDbError('Execute failed. ' . $sql);
    return false;
}

?>