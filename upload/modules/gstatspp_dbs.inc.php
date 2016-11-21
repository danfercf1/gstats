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

	Module: gstatspp_dbs
	This module is the database driver for a GStats++ database

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

	if ($_cfg['dbs_mysql']['port'] == 0) $_cfg['dbs_mysql']['port'] == 3306; 

	if ($_cfg['dbs_type'] == 'mysql' && $_cfg['dbs_mysql']['mysqli']) $_cfg['dbs_type'] = 'mysqli';
	$fType = $_cfg['dbs_type'];
	
	switch ($_cfg['dbs_type'])
	{
		case 'mysql': case 'mysqli':
			$fArray['mysql_hostname'] = $_cfg['dbs_mysql']['host'];
			$fArray['mysql_port'] = $_cfg['dbs_mysql']['port'];
			$fArray['mysql_username'] = $_cfg['dbs_mysql']['username'];
			$fArray['mysql_password'] = $_cfg['dbs_mysql']['password'];
			$fArray['mysql_database'] = $_cfg['dbs_mysql']['database'];
			break;
		default: die('Wrong database type in configuration (DBS).'); break;
	}
	
	$dbs = require 'classes/gstatspp_database.inc.php';
	
	if ($dbs->connection_error)
		die ('Could not connect to the GStats++ database.');
		
	$out = $dbs;
}
else if ($status == 'CALLBACK')
{
	$in['gstatspp_dbs']->close();
}
	
?>