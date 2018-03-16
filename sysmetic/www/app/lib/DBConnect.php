<?php
class DBConnect
{
	private $host;
	private $user;
	private $pass;
	private $dbname;

	public $conn;

	function __construct($host, $user, $pass, $dbname)
	{
		$this->host = $host;
		$this->user = $user;
		$this->pass = $pass;
		$this->dbname = $dbname;
	}

	function connect()
	{
		$this->conn = new mysqli($this->host, $this->user, $this->pass, $this->dbname);

		/* check connection */
		if ($this->conn->connect_errno) {
			// printf("Connect failed: %s\n", mysqli_connect_error());
			// printResultCode('4', $lang['ERROR_SERVER'], MAINTAINANCE_MESSAGE);
			header("HTTP/1.0 500 Internal Server Error");
			if(isset($_SERVER['HTTP_ACCEPT']) && $_SERVER['HTTP_ACCEPT'] == 'application/json'){
			}
			exit();
			// throw new Exception("Cannot connect to database.");
		}

		/* change character set to utf8 */
		if (!$this->conn->set_charset("utf8")) {
			// printf("Error loading character set utf8: %s\n", $mysqli->error);
			header("HTTP/1.0 500 Internal Server Error");
			exit();
			// throw new Exception("Cannot connect to database.");
		} else {
			// printf("Current character set: %s\n", $mysqli->character_set_name());
		}

		// mysql timezone을 php와 동기화 php에서의 timezone이 먼저 세팅되어야함
		$now = new DateTime();
		$mins = $now->getOffset() / 60;
		$sgn = ($mins < 0 ? -1 : 1);
		$mins = abs($mins);
		$hrs = floor($mins / 60);
		$mins -= $hrs * 60;
		$offset = sprintf('%+d:%02d', $hrs*$sgn, $mins);
		$this->conn->query("SET time_zone='$offset';");

		return $this->conn;
	}

	function select($table, $rows = '*', $where = array(), $order = array(), $start = 0, $limit = 0)
	{
		$q = 'SELECT '.$rows.' FROM '.$table;

		if(is_array($where) && $where != '' && !empty($where)){
			$q .= ' WHERE ';

			$parts = array();
			foreach($where as $k => $v){
				if(is_string($v)){
					$parts[] = $k.' = \''.$this->conn->real_escape_string($v).'\'';
				}else{
					$parts[] = $k.' = '.$this->conn->real_escape_string($v);
				}
			}

			$q .= implode(' AND ', $parts);
		}

		if(is_array($order) && $order != '' && !empty($order)){
			$q .= ' ORDER BY ';

			$parts = array();
			foreach($order as $k =>$v){
				$parts[] = $k.' '.$v;
			}

			$q .= implode(' , ', $parts);
		}

		if($limit){
			$q .= ' LIMIT '.$start.', '.$limit;
		}

		$rows = array();
		$result = $this->conn->query($q);
		if (!$result) {
			echo $q;
			echo '<br />';
		}
		while($row = $result->fetch_assoc()){
			$rows[] = $row;
		}

		return $rows;
	}

	function selectOne($table, $rows = '*', $where = array(), $order = array(), $start = 0, $limit = 0)
	{
		$row = $this->select($table, $rows, $where, $order, 0, 1);

		if(isset($row[0]) && !empty($row[0])) return $row[0];
		else return null;
	}

	function selectCount($table, $where = array())
	{
		$q = 'SELECT COUNT(*) FROM '.$table;

		if(is_array($where) && $where != '' && !empty($where)){
			$q .= ' WHERE ';

			$parts = array();
			foreach($where as $k => $v){
				if(is_string($v)){
					$parts[] = $k.' = \''.$this->conn->real_escape_string($v).'\'';
				}else{
					$parts[] = $k.' = '.$this->conn->real_escape_string($v);
				}
			}

			$q .= implode(' AND ', $parts);
		}

		$count = 0;

		$result = $this->conn->query($q);
		while($row = $result->fetch_array()){
			$count = $row[0];
			break;
		}

		return $count;
	}

	function getRow($sql) {
		$rows = array();
		$result = $this->conn->query($sql);
		$row = $result->fetch_assoc();
		return $row;
	}

	function getAll($sql) {
		$rows = array();
		$result = $this->conn->query($sql);
		while($row = $result->fetch_assoc()){
			$rows[] = $row;
		}
		return $rows;
	}

	function executesp($storedprocedure, $vars = array())
	{
		$keys = array();
		$values = array();

		foreach($vars as $k => $v){
			$keys[] = $k;
			if(is_string($v)){
				$values[] = '\''.$this->conn->real_escape_string($v).'\'';
			}else{
				$values[] = $this->conn->real_escape_string($v);
			}
		}

		$q = 'CALL '.$storedprocedure.'('.implode(',', $values).')';

		$this->conn->query($q);

	}


	function insert($table, $vars = array())
	{
		$keys = array();
		$values = array();

		$aSet = array();

		foreach($vars as $k => $v){

			$aSet[] = sprintf(" `%s` = %s ", $k, (is_string($v) || !isset($v)) ? '\''.$this->conn->real_escape_string($v).'\'' : $this->conn->real_escape_string($v));

			$keys[] = $k;
			if(is_string($v) || !isset($v)){
				$values[] = '\''.$this->conn->real_escape_string($v).'\'';
			}else{
				$values[] = $this->conn->real_escape_string($v);
			}
		}

		$q = sprintf( "INSERT INTO %s SET %s ", $table, join(',', $aSet));
			//- $q = 'INSERT INTO '.$table.'('.implode(',', $keys).') VALUES ('.implode(',', $values).')';

		$this->conn->query($q);

		return $this->conn->insert_id;
	}

	function update($table, $vars = array(), $where = array())
	{
		$q = 'UPDATE '.$table.' SET ';

		$parts = array();
		foreach($vars as $k => $v){
			if(is_string($v) || !isset($v)){
				$parts[] = $k.' = \''.$this->conn->real_escape_string($v).'\'';
			}else{
				$parts[] = $k.' = '.$this->conn->real_escape_string($v);
			}
		}

		$q .= implode(',', $parts);

		if(is_array($where) && $where != '' && !empty($where)){
			$q .= ' WHERE ';

			$parts = array();
			foreach($where as $k => $v){
				if(is_string($v)){
					$parts[] = $k.' = \''.$this->conn->real_escape_string($v).'\'';
				}else{
					$parts[] = $k.' = '.$this->conn->real_escape_string($v);
				}
			}

			$q .= implode(' AND ', $parts);
		}

		return $this->conn->query($q);
	}

	function delete($table, $where = array())
	{
		$q = 'DELETE FROM '.$table;

		if(is_array($where) && $where != '' && !empty($where)){
			$q .= ' WHERE ';

			$parts = array();
			foreach($where as $k => $v){
				if(is_string($v)){
					$parts[] = $k.' = \''.$this->conn->real_escape_string($v).'\'';
				}else{
					$parts[] = $k.' = '.$this->conn->real_escape_string($v);
				}
			}

			$q .= implode(' AND ', $parts);
		}

		return $this->conn->query($q);
	}

}
