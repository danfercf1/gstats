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

function secondsToHours($sec)
{
    $hms = "";
    $hours = intval(intval($sec) / 3600); 
    $hms .= str_pad($hours, 2, "0", STR_PAD_LEFT). ':';
    $minutes = intval(($sec / 60) % 60); 
    $hms .= str_pad($minutes, 2, "0", STR_PAD_LEFT). ':';
    $seconds = intval($sec % 60); 
    $hms .= str_pad($seconds, 2, "0", STR_PAD_LEFT);

    return $hms;
}

class PageNormalgameplayers extends Page implements iPage
{
	function run()
	{
		if (!isset($this->_args['gameid']) || !$this->_cfg['show_normal_games'])
			die();
			
		if (!$this->_tpl->is_cached('gstatspp_normalgameplayers.tpl', $this->_args['gameid']))
		{
			if (($query_game = $this->_dbs->query('
				SELECT `id`, `map`, `datetime`, `gamename`, `duration`, `playersnum`
				FROM `dbs_normalgames`
				WHERE `id` = ' . $this->_dbs->real_escape_string($this->_args['gameid']) . '
				LIMIT 1
				')) === false)
				die ('There was an error while retreiving the game informations.<br />Error: ' . $this->_dbs->error());
				
			if ($this->_dbs->num_rows($query_game) == 0)
				die();
				
			$game = $this->_dbs->fetch_array($query_game);
			
			$ex = explode('\\', $game['map']);
			
			if (count($ex) <= 1)
				$ex = explode('/', $game['map']);
				
			$map_full = $ex[(count($ex) - 1)];
			
			$ex = explode('.', $map_full);
			
			$map_ext = $ex[(count($ex) - 1)];
			
			$map_name = '';
			for ($i = 0; $i < count($ex) - 1; $i++)
			{
				if ($i != 0)
					$map_name .= '.';
				
				$map_name .= $ex[$i];
			}
			
			if (file_exists('medias/maps/' . $map_name . '.png'))
				$map_image = 'png';
			else
				if (file_exists('medias/maps/' . $map_name . '.PNG'))
					$map_image = 'PNG';
				else
					if (file_exists('medias/maps/' . $map_name . '.jpg'))
						$map_image = 'jpg';
					else
						if (file_exists('medias/maps/' . $map_name . '.JPG'))
							$map_image = 'JPG';
						else
							$map_image = 'unknown';
							
			$game_array = array();
			
			$game_array['gamename'] = $game['gamename'];
			$game_array['datetime'] = $game['datetime'];
			$game_array['playersnum'] = $game['playersnum'];
			$game_array['duration'] = $game['duration'];
			
			$game_array['map_name'] = $map_name;
			
			if ($map_image == 'unknown')
				$game_array['map_image'] = 'medias/map_unknown.png';
			else
				$game_array['map_image'] = 'medias/maps/' . $map_name . '.' . $map_image;
			
			$this->_tpl->assign('game', $game_array);
			
			if (($query_bans = $this->_dbs->query('
				SELECT `id`, `name`, `server`
				FROM `dbs_bans`
				')) === false)
				die ('There was an error while retreiving the game informations.<br />Error: ' . $this->_dbs->error());
				
			$bans = array();
			while ($row = $this->_dbs->fetch_array($query_bans))
				$bans[] = $row;
				
			if (($query_admins = $this->_dbs->query('
				SELECT `id`, `name`, `server`
				FROM `dbs_admins`
				')) === false)
				die ('There was an error while retreiving the game informations.<br />Error: ' . $this->_dbs->error());
			
			$admins = array();
			while ($row = $this->_dbs->fetch_array($query_admins))
				$admins[] = $row;
			
			if (($query_gameplayers = $this->_dbs->query('
				SELECT `id`, `player_id`, `gameid`, `name`, `spoofed`, `reserved`, `loadingtime`, `left`, `colour`, `spoofedrealm`
				FROM `dbs_normalgameplayers`
				WHERE `gameid` = ' . $this->_dbs->rreal_escape_string($this->_args['gameid']) . '
				')) === false)
				die ('There was an error while retreiving the game informations.<br />Error: ' . $this->_dbs->error());

			$gameplayers = array();
			while ($row = $this->_dbs->fetch_array($query_gameplayers))
			{
				$gp_array = array();
				
				$gp_array['player_id'] = $row['player_id'];
				$gp_array['name'] = $row['name'];
				$gp_array['loadingtime'] = $row['loadingtime'];
				$gp_array['spoofedrealm'] = $row['spoofedrealm'];
				$gp_array['colour'] = $row['colour'];
				$gp_array['left'] = round(($row['left'] / $game['duration']) * 100);
				
				$finished = false;
				while ($ban = current($bans))
				{
					if ($ban['name'] == $row['name'] && $ban['server'] == $row['spoofedrealm'])
					{
						$finished = true;
						break;
					}
					next($bans);
				}
				reset($bans);
				
				if ($finished == false)
				{
					if ($row['spoofed'] != 0 && $row['spoofedrealm'] != '')
					{
						$gp_array['status'] = 2;
						
						if ($row['reserved'] == 1)
							$gp_array['status'] = 1;
							
						$finished = false;
						while ($admin = current($admins))
						{
							if ($admin['name'] == $row['name'] && $admin['server'] == $row['spoofedrealm'])
							{
								$finished = true;
								break;
							}
							next($admins);
						}
						reset($admins);
						
						if ($finished == true)
							$gp_array['status'] = 0;
					}
					else
						$gp_array['status'] = 3;
				}
				else
					$gp_array['status'] = 4;

				$gameplayers[] = $gp_array;
			}
			
			$this->_tpl->assign('gameplayers', $gameplayers);
			
			$s_lang = array();
			$s_lang['TheMapWasX'] = str_replace('_MAPNAME_', '<b>' . $game_array['map_name'] . '</b>', $this->_lang['NormalGamePlayers']['TheMapWasX']);
			$s_lang['TheGameEndedAtX'] = str_replace('_DATETIME_', '<b>' . date('Y-m-d H:i:s', $game_array['datetime']) . '</b>', $this->_lang['NormalGamePlayers']['TheGameEndedAtX']);
			$s_lang['ThereWasXPlayersInThisGame'] = str_replace('_NUMPLAYERS_', '<b>' . $game_array['playersnum'] . '</b>', $this->_lang['NormalGamePlayers']['ThereWasXPlayersInThisGame']);
			$s_lang['TheGameLastedX'] = str_replace('_DURATION_', '<b>' . secondsToHours($game_array['duration']) . '</b>', $this->_lang['NormalGamePlayers']['TheGameLastedX']);

			$this->_tpl->assign('s_lang', $s_lang);
		}
		
		$this->_tpl->display('gstatspp_normalgameplayers.tpl', $this->_args['gameid']);
	}
}

?>