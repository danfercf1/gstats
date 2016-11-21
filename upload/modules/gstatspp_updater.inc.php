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

if (IN_MANHIM_FRAMEWORK != true)
	die();

/*

	Module: gstatspp_updater
	This module update the database for GStats++.

*/

if ($status == 'FETCHING_DEPENDENCIES')
{
	$flags = array();
	$flags['is_main_module'] = false;
	$flags['out_by_reference'] = false;
	
	$out = array();
	$out[] = 'gstatspp_configloader';
	$out[] = 'gstatspp_dbh';
	$out[] = 'gstatspp_dbs';
}
else if ($status == 'RUNNING')
{
	$_cfg = $in['gstatspp_configloader'];
	$_dbh = &$in['gstatspp_dbh'];
	$_dbs = &$in['gstatspp_dbs'];
	
	$max_inserts = $_cfg['dbs_mysql']['max_inserts'];
	
	$valid_names = array();
	
	$valid_names[] = 'admins';
	$valid_names[] = 'bans';
	$valid_names[] = 'normalgames';
	$valid_names[] = 'normalgameplayers';
	$valid_names[] = 'dotagames';
	$valid_names[] = 'dotagameplayers';
	
	/*
	$valid_names[] = 'downloads';
	$valid_names[] = 'stats';*/
	
	if (($lastupdates_query = $_dbs->query('
		SELECT `id`, `name`, `time` 
		FROM `dbs_lastupdates`')) === false)
		die ('There was an error in the update sequence (UPDATE_ERRORID=0-01)<br />Error: ' . $_dbs->error());
	
	$update_flags = array();
	$update_saves = array();
	while ($row = $_dbs->fetch_array($lastupdates_query))
	{
		if (!in_array($row['name'], $update_saves))
			$update_saves[] = $row['name'];
			
		if (($row['time'] >= (time() - ($_cfg['updater']['updaterate'] * 60))) === false && in_array($row['name'], $valid_names)&& !in_array($row['name'], $update_flags))
			$update_flags[] = $row['name'];
		else if (!in_array($row['name'], $valid_names))
			if (!$_dbs->query('
				DELETE FROM `dbs_lastupdates`
				WHERE `name` = ' . $_dbs->real_escape_string($row['name']) . ''))
				die ('There was an error in the update sequence (UPDATE_ERRORID=0-02)<br />Error: ' . $_dbs->error());
	}
	
	require_once 'interfaces/gstatspp_updater.inc.php';
	
	for ($i=0; $i<count($valid_names); $i++)
	{
		if ((in_array($valid_names[$i], $update_flags) || !in_array($valid_names[$i], $update_saves)))
		{
			if ($valid_names[$i] == 'admins')
				if (!$_dbs->query('
					TRUNCATE TABLE `dbs_admins`'))
					die ('There was an error in the update sequence (UPDATE_ERRORID=0-03) (Admins)<br />Error: ' . $_dbs->error());
					
			if ($valid_names[$i] == 'bans')
				if (!$_dbs->query('
					TRUNCATE TABLE `dbs_bans`'))
					die ('There was an error in the update sequence (UPDATE_ERRORID=0-03) (Bans)<br />Error: ' . $_dbs->error());
		}
	}
	
	$optimize_table = array();

	for ($i=0; $i<count($valid_names); $i++)
	{
		if ((in_array($valid_names[$i], $update_flags) || !in_array($valid_names[$i], $update_saves)))
		{
			$keys = array_keys($_dbh);
			foreach ($keys as $key)
			{
				$a_dbh = $_dbh[$key];
				$a_cfg = $a_dbh->getCfg();
				if ($a_cfg['use'] === true)
				{
					$usebotid = $a_cfg['dbh_type'] == 'sqlite3' ? false : true;
					$botid = $a_cfg['dbh_type'] != 'sqlite3' ? $a_cfg['dbh_mysql']['botid'] : 0;
					$select_limit = $a_cfg['dbh_type'] != 'sqlite3' ? $a_cfg['dbh_mysql']['select_limit'] : $a_cfg['dbh_sqlite3']['select_limit'];
					$use_set_time_limit = $a_cfg['use_set_time_limit'];
					$dbs_botid = $a_cfg['alias'];
	
					switch ($valid_names[$i])
					{
						case 'admins': 
							require_once 'interfaces/gstatspp_updater/admins.inc.php';
							$_updater = new UpdaterAdmins($a_dbh, $_dbs, $botid, $usebotid, $max_inserts, $select_limit, $use_set_time_limit, $dbs_botid);
							if (!in_array('dbs_admins', $optimize_table)) $optimize_table[] = 'dbs_admins';
							break;
						case 'bans': 
							require_once 'interfaces/gstatspp_updater/bans.inc.php';
							$_updater = new UpdaterBans($a_dbh, $_dbs, $botid, $usebotid, $max_inserts, $select_limit, $use_set_time_limit, $dbs_botid);
							if (!in_array('dbs_bans', $optimize_table)) $optimize_table[] = 'dbs_bans';
							break;
						case 'normalgames': 
							require_once 'interfaces/gstatspp_updater/normalgames.inc.php';
							$_updater = new UpdaterNormalgames($a_dbh, $_dbs, $botid, $usebotid, $max_inserts, $select_limit, $use_set_time_limit, $dbs_botid);
							if (!in_array('dbs_normalgames', $optimize_table)) $optimize_table[] = 'dbs_normalgames';
							break;
						case 'normalgameplayers': 
							require_once 'interfaces/gstatspp_updater/normalgameplayers.inc.php';
							$_updater = new UpdaterNormalgameplayers($a_dbh, $_dbs, $botid, $usebotid, $max_inserts, $select_limit, $use_set_time_limit, $dbs_botid);
							if (!in_array('dbs_normalgameplayers', $optimize_table)) $optimize_table[] = 'dbs_normalgameplayers';
							if (!in_array('dbs_players', $optimize_table)) $optimise_table[] = 'dbs_players';
							break;
						case 'dotagames':
							require_once 'interfaces/gstatspp_updater/dotagames.inc.php';
							$_updater = new UpdaterDotagames($a_dbh, $_dbs, $botid, $usebotid, $max_inserts, $select_limit, $use_set_time_limit, $dbs_botid);
							if (!in_array('dbs_dotagames', $optimize_table)) $optimize_table[] = 'dbs_dotagames';
							break;
						case 'dotagameplayers':
							require_once 'interfaces/gstatspp_updater/dotagameplayers.inc.php';
							$_updater = new UpdaterDotagameplayers($a_dbh, $_dbs, $botid, $usebotid, $max_inserts, $select_limit, $use_set_time_limit, $dbs_botid);
							if (!in_array('dbs_dotagameplayers', $optimize_table)) $optimize_table[] = 'dbs_dotagameplayers';
							if (!in_array('dbs_players', $optimize_table)) $optimize_table[] = 'dbs_players';
							break;
					}
					
					$_updater->update(); 
					$_updater = null; unset ($_updater);
					
					if ($a_cfg['dbs_mysql']['optimize_tables'] === true && $optimize_table != null) 
						foreach($optimize_table as $o_table)
						{
							$_dbs->query('OPTIMIZE TABLE `' . $o_table . '`');
						}

					$optimize_table = array();
				}
			}
		}
	}
	
	$out = null;
}
else if ($status == 'CALLBACK')
{

}

?>