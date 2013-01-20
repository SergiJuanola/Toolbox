<?php

require_once 'class.builder.php';

class Orm extends Builder {
	const STATUS_ERROR= 0;
	const STATUS_OK= 1;
	public static $_lastId;
	public static $_lastError;
	public static $_lastQuery;

	public static $default = array('dbprefix'=>'');

	public static function build($config = array()) {
		$orm = new self($config);
		$orm->connect();
		return $orm;
	}

	public function connect()
	{
		$this->_connection = mysql_connect($this->host, $this->user, $this->pass);
		mysql_select_db($this->db);
	}

	public function from($tableName)
	{
		$this->_tableName = $this->dbprefix.$tableName;
		return $this;
	}

	public static function query($query, $assoc = true, $levels = 0)
	{
		$cursor = mysql_query($query);
		$items = array();
		while ($item = mysql_fetch_assoc($cursor)) {
			if($levels > 0)
			{
				foreach ($item as $key => $value) {
					if (strpos($key, "_id") !== FALSE && !empty($item[$key])) {
						$item[substr($key, 0,-3)] = Orm::table(substr($key, 0,-3))->selectById($value, $assoc, $levels-1); 
					}
				}
			}
			if($assoc)
				$items[] = $item;
			else
				$items[] = (object)$item;
		}
		Orm::$_lastQuery = $query;
		return $items;
	}

	public function orderBy($orderParams)
	{
		if(!empty($orderParams))
			$this->_orderBy = " ORDER BY ".$orderParams;
		return $this;
	}

	public function limit($limit)
	{
		if(!empty($limit))
			$this->_limit = " LIMIT ".$limit;
		return $this;
	}

	public function offset($offset)
	{
		if(!empty($offset))
			$this->_offset = " OFFSET ".$offset;
		return $this;
	}

	public function selectAll($where = NULL, $assoc = true, $levels = 0)
	{
		$query = "SELECT * FROM ".$this->_tableName;
		if(!empty($where))
			$query = $query." WHERE ".$where;
		$query .= $this->_orderBy.$this->_limit.$this->_offset;
		
		$cursor = mysql_query($query);
		$items = array();
		Orm::$_lastError = mysql_error();
		if(empty(Orm::$_lastError))
		{
			while ($item = mysql_fetch_assoc($cursor)) {
				if($levels > 0)
				{
					foreach ($item as $key => $value) {
						if (strpos($key, "_id") !== FALSE && !empty($item[$key])) {
							$item[substr($key, 0,-3)] = Orm::table(substr($key, 0,-3))->selectById($value, $assoc, $levels-1); 
						}
					}
				}
				if($assoc)
					$items[] = $item;
				else
					$items[] = (object)$item;
			}
		}
		Orm::$_lastQuery = $query;
		return $items;
	}

	public function select($where = NULL, $assoc = true, $levels = 0)
	{
		$query = "SELECT * FROM ".$this->_tableName;
		if(!empty($where))
			$query = $query." WHERE ".$where;
		$this->_limit == 1;
		$query .= $this->_orderBy.$this->_limit.$this->_offset;
		
		$cursor = mysql_query($query);
		$items = array();
		Orm::$_lastError = mysql_error();
		if(empty(Orm::$_lastError))
		{
			while ($item = mysql_fetch_assoc($cursor)) {
				if($levels > 0)
				{
					foreach ($item as $key => $value) {
						if (strpos($key, "_id") !== FALSE && !empty($item[$key])) {
							$item[substr($key, 0,-3)] = Orm::table(substr($key, 0,-3))->selectById($value, $assoc, $levels-1); 
						}
					}
				}
				if($assoc)
					$items[] = $item;
				else
					$items[] = (object)$item;
			}
		}
		Orm::$_lastQuery = $query;
		return !empty($items)? $items[0] : null;
	}

	public function selectById($id, $assoc = true, $levels = 0)
	{
		$query = "SELECT * FROM ".$this->_tableName." WHERE id=".$id." LIMIT 1";
		$item = mysql_query($query);
		$item = mysql_fetch_assoc($item);

		if($item === FALSE)
			return null;

		if($levels > 0)
		{
			foreach ($item as $key => $value) {
				if (strpos($key, "_id") !== FALSE && !empty($item[$key])) {
					$item[substr($key, 0,-3)] = Orm::table(substr($key, 0,-3))->selectById($value, $assoc, $levels-1); 
				}
			}
		}
		Orm::$_lastQuery = $query;
		if($assoc)
			return $item;
		else
			return (object)$item;
	}

	public function deleteById($id)
	{
		$query = "DELETE FROM ".$this->_tableName." WHERE id =".$id;
		$ok = mysql_query($query);
		Orm::$_lastId = NULL;
		Orm::$_lastError = mysql_error();
		Orm::$_lastQuery = $query;
		if($ok)
			return Orm::STATUS_OK;
		else
			return Orm::STATUS_ERROR;
	}

	public function deleteAll($where)
	{
		$query = "DELETE FROM ".$this->_tableName." WHERE ".$where;
		$ok = mysql_query($query);
		Orm::$_lastId = NULL;
		Orm::$_lastError = mysql_error();
		Orm::$_lastQuery = $query;
		if($ok)
			return Orm::STATUS_OK;
		else
			return Orm::STATUS_ERROR;
	}

	public function insert($params, $useId = FALSE)
	{
		$query = "INSERT INTO ".$this->_tableName." SET ";
		$parameters = array();
		foreach ($params as $key => $value) {
			if($key != 'id' || $useId)
			{
				if(!isset($value))
					$parameters[] = "$key = NULL";
				else
					$parameters[] = "`$key` = '".mysql_real_escape_string($value)."'";
			}

		}
		$query .= implode(', ', $parameters);
		$ok = (mysql_query($query))? Orm::STATUS_OK : Orm::STATUS_ERROR;
		Orm::$_lastId = mysql_insert_id();
		Orm::$_lastError = mysql_error();
		Orm::$_lastQuery = $query;
		return $ok;
	}

	public function update($params)
	{
		$query = "UPDATE ".$this->_tableName." SET ";
		$parameters = array();
		foreach ($params as $key => $value) {
			if($key != 'id')
			{
				if(!isset($value))
					$parameters[] = "$key = NULL";
				else
					$parameters[] = "`$key` = '".mysql_real_escape_string($value)."'";
			}

		}
		$query .= implode(', ', $parameters)." WHERE id=".$params['id'];
		$ok = (mysql_query($query))? Orm::STATUS_OK : Orm::STATUS_ERROR;
		Orm::$_lastId = $params['id'];
		Orm::$_lastError = mysql_error();
		Orm::$_lastQuery = $query;
		return $ok;
	}

	public function save($params)
	{
		if(!empty($params['id']))
			return $this->update($params);
		else
			return $this->insert($params);
	}

	public static function lastId()
	{
		return Orm::$_lastId;
	}

	public static function lastError()
	{
		return Orm::$_lastError;
	}

	public static function lastQuery()
	{
		return Orm::$_lastQuery;
	}
}