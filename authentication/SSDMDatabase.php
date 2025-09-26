<?php
class SSDMDatabase
{
	public static function connect()
	{
		$mysqli = new mysqli(getenv('DB_SERVER'), getenv('DB_USERNAME'), getenv('DB_PASSWORD'), getenv('DB_DATABASE'));
		if($mysqli->connect_errno != 0)
		{
			SSDMDatabase::writeDbError($mysqli->connect_error);
			return false;
		}
		return $mysqli;
	}

	public static function writeDbError($error)
	{
		$error_date = date('Y-m-d H:i:s');
		$message = $error_date . ' : ' . $error . PHP_EOL;
		file_put_contents(getenv('DB_LOG_FILE'), $message, FILE_APPEND);
	}

	public static function dbQuery($sql, $arg_string, $arg_array, $query_type)
	{
		$mysqli = SSDMDatabase::connect();
		if(!$mysqli)
		{
			return false;
		}
		$stmt = $mysqli->prepare($sql);
		if(!$stmt)
		{
			$mysqli->close();
			SSDMDatabase::writeDbError('Prepare failed. ' . $sql);
			return false;
		}
		
		if(!$stmt->bind_param($arg_string, ...$arg_array))
		{
			$mysqli->close();
			SSDMDatabase::writeDbError('Bind Param failed. ' . $sql);
			return false;
		}
		if(!$stmt->execute())
		{
			$mysqli->close();
			SSDMDatabase::writeDbError('Execute failed. ' . $sql);
			return false;
		}
		$data = null;
		switch($query_type)
		{
			case QueryType::Insert:
				$data['id'] = 1;
				break;
			case QueryType::Select:
				$result = $stmt->get_result();
				if($result)
				{
					$data = mysqli_fetch_assoc($result);
				}
				break;
			case QueryType::Delete:
				$data['delete'] = 1;
				break;
			case QueryType::Update:
				$affected_rows = $stmt->affected_rows;
				if($affected_rows > 0)
				{
					$data['update'] = 1;
				}
				break;
		}
		$mysqli->close();
		return $data;
	}

	public static function player_id_exists($player_id)
	{
		$sql = 'SELECT auth FROM player_auth WHERE player_id=?';
		$arg_array = [$player_id];
		$data = SSDMDatabase::dbQuery($sql, 's', $arg_array, QueryType::Select);
		if($data)
		{
			return true;
		}
		return false;
	}

	public static function new_player_exists($request_register)
	{
		$sql = 'SELECT player_id FROM player_auth WHERE user_name=? OR display_name=? OR email=?';
		$data = SSDMDatabase::dbQuery($sql, 'sss', [$request_register->user_name, $request_register->display_name, $request_register->email], QueryType::Select);
		if($data)
		{
			SSDMDatabase::writeDbError('returning true');
			return true;
		}
		SSDMDatabase::writeDbError('returning false');
		return false;
	}

	public static function add_new_player_to_database($request_register)
	{
		$player_id = SSDMToken::generate_unique_id(2, TICKET_LENGTH);
		while(SSDMDatabase::player_id_exists($player_id))
		{
			$player_id = SSDMToken::generate_unique_id(2, TICKET_LENGTH);
		}
		$password = password_hash($request_register->password, PASSWORD_DEFAULT);
		$current_date_time = new DateTime();
		$current_date_time->setTimezone(new DateTimeZone('EST'));
		$sql = 'INSERT INTO player_auth (player_id, user_name, display_name, email, password, account_created, last_login) VALUES(?,?,?,?,?,?,?)';
		$arg_array = [$player_id, $request_register->user_name, $request_register->display_name, $request_register->email, $password, $current_date_time->format('Y-m-d H:i:s'), $current_date_time->format('Y-m-d H:i:s')];
		$data = SSDMDatabase::dbQuery($sql, 'sssssss', $arg_array, QueryType::Insert);
		if(!$data)
		{
			SSDMDatabase::writeDbError('Unable to add new player to database');
			return false;
		}
		return $player_id;
	}

	public static function add_activate_session_to_database($request_register)
	{
		SSDMDatabase::writeDbError('here client id = ' . $request_register->client_id);
		$session_ticket = SSDMToken::generate_unique_id(1, 8);
		$expiration_time = time() + ACTIVATE_EXPIRATION_TIME;
		$sql = 'INSERT INTO sessions (client_id, session_ticket, expiration_date, player_id) VALUES (?,?,?,?)';
		$arg_array = [$request_register->client_id, $session_ticket, $expiration_time, $request_register->player_id];
		if(!SSDMDatabase::dbQuery($sql, 'ssss', $arg_array, QueryType::Insert))
		{
			SSDMDatabase::writeDbError('Unable to add session to database');
			return false;
		}
		return $session_ticket;
	}

	/*private function get_player()
	{
		$sql = 'SELECT password, player_id, auth, display_name FROM player_auth WHERE user_name=?';
		$data = SSDMDatabase::db_query($sql, 's', [$this->user_name], QueryType::Select);
		if(!$data)
		{
			$this->error = 'Invalid username and password combination.';
			return false;
		}
		return $data;
	}*/

	/*private function set_last_login($playerId)
	{
		$sql = 'UPDATE player_auth SET last_login=? WHERE player_id=?';
		$current_date_time = new DateTime();
		$current_date_time->setTimezone(new DateTimeZone('EST'));
		$data = SSDMDatabase::db_query($sql, 'si', [$current_date_time->format('Y-m-d H:i:s'), (int)$playerId], QueryType::Update);
		if(!$data)
		{
			SSDMDatabase::write_db_error('Unable to set last login');
			return false;
		}
		return $data;
	}

	protected function activate_account_in_database($playerId)
	{
		$sql = 'UPDATE player_auth SET auth=1 WHERE player_id=?';
		if(!SSDMDatabase::db_query($sql, 'i', [$playerId], QueryType::Update))
		{
			SSDMDatabase::write_db_error('Unable to update player auth in activate_account_in_database');
			return false;
		}
		return true;
	}*/


	/*protected function session_exists()
	{
		$sql = 'SELECT session_type FROM sessions WHERE client_id=? AND player_id=?';
		$data = SSDMDatabase::db_query($sql, 'ss', [$this->client_id, $this->player_id], QueryType::Select);
		if($data)
		{
			return true;
		}
		return false;
	}*/

	/*private function setToExpireNow($clientId)
	{
		$sql = 'UPDATE sessions SET expiration_date=? WHERE client_id=?';
		$data = SSDMDatabase::db_query($sql, 'is', [time(), $clientId], QueryType::Update);
		if(!$data)
		{
			SSDMDatabase::write_db_error('Unable to set expire now.');
			return false;
		}
		return true;
	}*/


	/*private function load_session_from_database()
	{
		$sql = 'SELECT session_type, expiration_date, session_ticket FROM sessions WHERE session_ticket=? AND client_id=? AND player_id=? LIMIT 1';
		$data = SSDMDatabase::db_query($sql, 'sii', [$this->session_ticket, $this->client_id, $this->player_id], QueryType::Select);
		if(!$data)
		{
			SSDMDatabase::write_db_error('Unable to load session from database');
			$this->error = 'Database Error';
			return false;
		}
		$this->set_session_type($data['session_type']);
		$this->set_session_ticket($data['session_ticket']);
		$this->expiration_date = DateTime::createFromFormat('Y-m-d H:i:s', $data['expiration_date']);
		return true;
	}*/

	/*private function delete_all_sessions_from_database($playerId, $clientId)
	{
		$sql = 'DELETE FROM sessions WHERE player_id=? AND client_id=?';
		$data = SSDMDatabase::db_query($sql, 'ii', [$playerId, $clientId], QueryType::Delete);
		if(!$data)
		{
			SSDMDatabase::write_db_error('Unable to delete all sessions from database.');
			return false;
		}
		return true;
	}*/
}
?>