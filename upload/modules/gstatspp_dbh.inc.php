<?php

/*

	GStats++: GHost++ Web-Based Statistics
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

if (IN_MANHIM_FRAMEWORK !== true)
	die();

/*

	Module: gstatspp_dbh
	This module is the database driver for a GHost++ database

*/

if ($status == 'FETCHING_DEPENDENCIES')
{
	$flags = array();
	$flags['is_main_module'] = false;
	$flags['out_by_reference'] = true;
	
	$out = array();
	$out[] = 'gstatspp_configloader';
}
else if ($status == 'RUNNING')
{
	$_cfg = $in['gstatspp_configloader'];
	
	$fArray = array();
	$dbh = array();
	
	$i = 0;
	foreach ($_cfg['ghostdbs'] as $c_cfg)
	{
		if ($c_cfg['use'] === true)
		{
			// Default MySQL port
			if ($c_cfg['dbh_mysql']['port'] == 0) $c_cfg['dbh_mysql']['port'] == 3306; 

			// MySQL and MySQLi
			if ($c_cfg['dbh_type'] == 'mysql' && $c_cfg['dbh_mysql']['mysqli']) $c_cfg['dbh_type'] = 'mysqli';
			$fType = $c_cfg['dbh_type'];
			
			$fCfg = $c_cfg;
			
			switch ($c_cfg['dbh_type'])
			{
				case 'mysql': case 'mysqli':
					$fArray['mysql_hostname'] = $c_cfg['dbh_mysql']['host'];
					$fArray['mysql_port'] = $c_cfg['dbh_mysql']['port'];
					$fArray['mysql_username'] = $c_cfg['dbh_mysql']['username'];
					$fArray['mysql_password'] = $c_cfg['dbh_mysql']['password'];
					$fArray['mysql_database'] = $c_cfg['dbh_mysql']['database'];
					break;
				case 'sqlite3':
					$fArray['sqlite3_filepath'] = $c_cfg['dbh_sqlite3']['filepath'];
					break;
				default: die('Wrong database type in configuration (DBH). (' . $c_cfg['alias'] . ')'); break;
			}
			
			$dbh[$i] = require 'classes/gstatspp_database.inc.php';
			
			if ($dbh[$i]->connection_error)
				die ('Could not connect to the GHost++ database. (' . $c_cfg['alias'] . ')');
				
			$i++;
		}
	}
		
	$out = $dbh;
}
else if ($status == 'CALLBACK')
{
	foreach($in['gstatspp_dbh'] as $c)
		$c->close();
}
	
?>