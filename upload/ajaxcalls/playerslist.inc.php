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

class AjaxcallsPlayerslist extends Ajaxcalls implements iAjaxcalls
{
	function run()
	{
		$query_build = 'SELECT `dbs_players`.`id` AS p_id,  `dbs_players`.`name` AS p_name,  `dbs_players`.`realm` AS p_realm, (';
		
		if ($this->_cfg['show_normal_games'])
		{
			$query_build .= '
				SELECT COUNT( `dbs_normalgameplayers`.`player_id` ) 
				FROM `dbs_normalgameplayers` 
				WHERE `dbs_normalgameplayers`.`player_id` = p_id';
		}
		
		if ($this->_cfg['show_normal_games'] && $this->_cfg['show_dota_games'])
		{
			$query_build .= '
				) + ( ';
		}
		
		if ($this->_cfg['show_dota_games'])
		{
			$query_build .= '
				SELECT COUNT( `dbs_dotagameplayers`.`player_id` ) 
				FROM `dbs_dotagameplayers` 
				WHERE `dbs_dotagameplayers`.`player_id` = p_id ';
		}
		
		$query_build .= '
			) AS gp_count
			FROM `dbs_players`';
			
		if (($rResult = $this->_dbs->query('
			' . $query_build . '
			' . $this->_sWhere . '
			' . $this->_sOrder . '
			' . $this->_sLimit)) === false)
			die ('Query error: ' . $this->_dbs->error());
			
		if (($rResultTotal = $this->_dbs->query('
			SELECT id
			FROM `dbs_players`')) === false)
			die ('Query error: ' . $this->_dbs->error());
			
		$iTotal = $this->_dbs->num_rows($rResultTotal);
		
		if ($this->_sWhere != '')
		{
			if (($rResultFilterTotal = $this->_dbs->query('
				' . $query_build . '
				' . $this->_sWhere)) === false)
				die ('Query error: ' . $this->_dbs->error());
			
			$iFilteredTotal = $this->_dbs->num_rows($rResultFilterTotal);
		}
		else
		{
			$iFilteredTotal = $iTotal;
		}
		
		$out = '{';
		$out .= '"iTotalRecords": ' . $iTotal . ', ';
		$out .= '"iTotalDisplayRecords": ' . $iFilteredTotal . ', ';
		$out .= '"aaData": [ ';
		
		if (($query_bans = $this->_dbs->query('
			SELECT `id`, `name`, `server`
			FROM `dbs_bans`
			WHERE `botid` = 0
			')) === false)
			die ('There was an error while retreiving the game informations.<br />Error: ' . $this->_dbs->error());
			
		$bans = array();
		while ($row = $this->_dbs->fetch_array($query_bans))
			$bans[] = $row;
			
		if (($query_admins = $this->_dbs->query('
			SELECT `id`, `name`, `server`
			FROM `dbs_admins`
			WHERE `botid` = 0
			')) === false)
			die ('There was an error while retreiving the game informations.<br />Error: ' . $this->_dbs->error());
		
		$admins = array();
		while ($row = $this->_dbs->fetch_array($query_admins))
			$admins[] = $row;
		
		while ($row = $this->_dbs->fetch_array($rResult))
		{
			$finished = false;
			while ($ban = current($bans))
			{
				if ($ban['name'] == $row['p_name'] && $ban['server'] == $row['p_realm'])
				{
					$finished = true;
					break;
				}
				next($bans);
			}
			reset($bans);
			
			if ($finished == false)
			{
				$status = 2;
					
				$finished = false;
				while ($admin = current($admins))
				{
					if ($admin['name'] == $row['p_name'] && $admin['server'] == $row['p_realm'])
					{
						$finished = true;
						break;
					}
					next($admins);
				}
				reset($admins);
				
				if ($finished == true)
					$status = 0;
			}
			else
				$status = 4;
		
			$out .= '[';
			$out .= '\'' . $this->purify($row['p_name']) . '\',';
			$out .= '\'' . $status . '\',';
			$out .= '\'' . $row['p_realm'] . '\',';
			$out .= '\'' . $row['gp_count'] . '\',';
			$out .= '\'' . $row['p_id'] . '\'';
			$out .= '],';
		}
		
		echo substr_replace($out, '] }', -1);
	}
}

?>