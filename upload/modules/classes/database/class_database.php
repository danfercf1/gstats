<?php

/*

	MansLibrary
    Copyright (C) 2009 Marc André 'Manhim' Audet

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/

class Database
{
	private $type;
	
	private $mysql_connect;
	private $mysql_select_db;
	
	private $mysqli;
	
	private $sqlite3;
	
	private $query_id;
	private $query_result = array();
	
	private $queries_num = 0;
	
	public $connection_error = false;
	private $cfg;
	
	function getCfg()
	{
		return $this->cfg;
	}

	function __construct($fType, $fArray, $cfg)
	{
		$this->type = $fType;
		$this->query_id = 0;
		$this->cfg = $cfg;
		switch ($fType)
		{
			case 'mysql':
				$this->mysql_connect = mysql_connect ($fArray['mysql_hostname'] . ':' . $fArray['mysql_port'], $fArray['mysql_username'], $fArray['mysql_password']);
				if ($this->mysql_connect)
				{
					$this->mysql_select_db = mysql_select_db ($fArray['mysql_database'], $this->mysql_connect);
					if(!$this->mysql_select_db)
					{
						$this->connection_error = true;
						return;
					}
				}
				else
				{
					$this->connection_error = true;
					return;
				}
				$this->connection_error = false;
				return;
			case 'mysqli':
				$this->mysqli = new mysqli($fArray['mysql_hostname'], $fArray['mysql_username'], $fArray['mysql_password'], $fArray['mysql_database'], $fArray['mysql_port']);
				if ($this->mysqli->connect_error OR mysqli_connect_error())
				{
					$this->connection_error = true;
					return;
				}
				if ($this->mysqli->error)
				{
					$this->connection_error = true;
					return;
				}
				$this->connection_error = false;
				return;
			case 'sqlite3':
				$this->sqlite3 = new PDO('sqlite:' . $fArray['sqlite3_filepath']);
				if (!$this->sqlite3)
				{
					$this->connection_error = true;
					return;
				}
				$this->connection_error = false;
				return;
			default: $this->connection_error = true; return;
		}
	}
	
	function get_queries_num()
	{
		return $this->queries_num;
	}
	
	function query($query)
	{
		$query_id = $this->query_id;
		$this->query_id++;
		$this->queries_num++;
		switch ($this->type)
		{
			case 'mysql': $this->query_result[$query_id] = mysql_query($query); if ($this->query_result[$query_id] == false) return false; else return $query_id;
			case 'mysqli': $this->query_result[$query_id] = $this->mysqli->query($query); if ($this->query_result[$query_id] == false) return false; else return $query_id;
			case 'sqlite3': $this->query_result[$query_id] = $this->sqlite3->query($query); if ($this->query_result[$query_id] == false) return false; else return $query_id;
		}
	}
	
	function fetch_array($query_id)
	{
		switch ($this->type)
		{
			case 'mysql': return mysql_fetch_array($this->query_result[$query_id], MYSQL_ASSOC);
			case 'mysqli': return $this->query_result[$query_id]->fetch_array(MYSQLI_ASSOC);
			case 'sqlite3': return $this->query_result[$query_id]->fetch(PDO::FETCH_ASSOC);
		}
	}
	
	function num_rows($query_id)
	{
		switch ($this->type)
		{
			case 'mysql': return mysql_num_rows($this->query_result[$query_id]);
			case 'mysqli': return $this->query_result[$query_id]->num_rows;
			case 'sqlite3': trigger_error('It\'s impossible to get the correct num_rows values for SQLite3 database, therefore it\'s completly disabled.', E_USER_ERROR); return false;
		}
	}
	
	function error()
	{
		switch ($this->type)
		{
			case 'mysql': return mysql_error($this->mysql_connect);
			case 'mysqli': return $this->mysqli->error;
			case 'sqlite3': $error = $this->sqlite3->errorInfo(); return $error[2];
		}
	}
	
	function real_escape_string($string)
	{
		switch ($this->type)
		{
			case 'mysql': return '\'' . mysql_real_escape_string($string) . '\'';
			case 'mysqli': return '\'' . $this->mysqli->real_escape_string($string) . '\'';
			case 'sqlite3': return $this->sqlite3->quote($string);
		}
	}
	
	function rreal_escape_string($string)
	{
		switch ($this->type)
		{
			case 'mysql': return mysql_real_escape_string($string);
			case 'mysqli': return $this->mysqli->real_escape_string($string);
			case 'sqlite3': trigger_error('Can\'t use rreal_espace_string with sqlite3 databases.', E_USER_ERROR); break;
		}
	}
	
	function close()
	{
		switch ($this->type)
		{
			case 'mysql': return;
			case 'mysqli': return;
			case 'sqlite3': $this->sqlite3 = null; unset($this->sqlite3); return;
		}
	}
	
}
	
?>