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

function getColorById($id)
{
	switch ($id)
	{
		case "0": return 'FF0303';
		case "1": return '0042FF';
		case "2": return '1CB619';
		case "3": return '540081';
		case "4": return 'CCCC01'; //FFFF01
		case "5": return 'FE8A0E';
		case "6": return '20C000';
		case "7": return 'E55BB0';
		case "8": return '959697';
		case "9": return '7EBFF1';
		case "10": return '106246';
		case "11": return '4E2A04';
	}
}

function sec2min($s){
	if (($s/60) < 10) 
		return sprintf("%sm%02ss","0".floor($s/60),ceil($s%60));
	else 
		return sprintf("%sm%02ss",floor($s/60),ceil($s%60));
}

class PageDotagameplayers extends Page implements iPage
{
	function run()
	{
		if (!isset($this->_args['gameid']) || !$this->_cfg['show_dota_games'])
			die();
			
		if (!$this->_tpl->is_cached('gstatspp_dotagameplayers.tpl', $this->_args['gameid']))
		{
			if (($query_game = $this->_dbs->query('
				SELECT *
				FROM `dbs_dotagames`
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
			$game_array['versus'] = $game['versus'];
			$game_array['winner'] = $game['dota_winner'];
			
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
				SELECT *
				FROM `dbs_dotagameplayers`
				WHERE `gameid` = ' . $this->_dbs->rreal_escape_string($this->_args['gameid']) . '
				')) === false)
				die ('There was an error while retreiving the game informations.<br />Error: ' . $this->_dbs->error());

			$gameplayers = array();
			require_once 'dota/dota-heroes.php';
			require_once 'dota/dota-items.php';
			while ($row = $this->_dbs->fetch_array($query_gameplayers))
			{
				$gp_array = array();
				
				$gp_array['player_id'] = $row['player_id'];
				$gp_array['name'] = $row['name'];
				$gp_array['team'] = $row['team'];
				$gp_array['loadingtime'] = $row['loadingtime'];
				$gp_array['spoofedrealm'] = $row['spoofedrealm'];
				if ($row['dota_newcolour'] != $row['colour'] && $row['dota_newcolour'] != 0) 
				{
					$gp_array['colour'] = $row['dota_newcolour'];
					$gp_array['a_colour'] = $row['colour'];
				}
				else 
				{
					$gp_array['colour'] = $row['colour'];
					$gp_array['a_colour'] = -1;
				}
				$gp_array['left'] = round(($row['left'] / $game['duration']) * 100);
			
				if (isset($dota['heroes']['' . strtolower($row['dota_hero']) . '']) && $dota['heroes']['' . strtolower($row['dota_hero']) . '']['is_duplicate'] == false)
				{
					$gp_array['hero_name'] = $dota['heroes']['' . strtolower($row['dota_hero']) . '']['name'];
					$gp_array['hero_image'] = $dota['heroes']['' . strtolower($row['dota_hero']) . '']['art'];
				}
				else if (isset($dota['heroes']['' . strtolower($row['dota_hero']) . '']))
				{
					$gp_array['hero_name'] = $dota['heroes']['' . $dota['heroes']['' . strtolower($row['dota_hero']) . '']['original_id'] . '']['name'];
					$gp_array['hero_image'] = $dota['heroes']['' . $dota['heroes']['' . strtolower($row['dota_hero']) . '']['original_id'] . '']['art'];
				}
				else
				{
					$gp_array['hero_name'] = $this->_lang['General']['Unknown']. ' '. $row['dota_hero'];
					$gp_array['hero_image'] = 'Empty_Hero.png';
				}
				
				for ($i = 1; $i <= 6; $i++)
				{
					$itemid = trim(strtolower($row['dota_item' . $i . '']));
					
					if (isset($row['dota_item' . $i . '']) && trim($itemid) != '' && $itemid != "\0\0\0\0" && isset($dota['items']['' . $itemid . '']))
					{
						$i_name = $dota['items']['' . $itemid . '']['name'];
						$i_image = $dota['items']['' . $itemid . '']['art'];
					}
					else if (!isset($row['dota_item' . $i . '']) && trim($itemid) != '' && $itemid != "\0\0\0\0")
					{
						$i_name = $this->_lang['General']['Unknown'] . ' ' . $row['dota_item' . $i . ''];
					}
					else
					{
						$i_name = $this->_lang['General']['Empty'];
					}
					
					$gp_array['items'][$i]['name'] = str_replace('\'', '\\\'', $i_name);
					if (isset($i_image) && $i_image !== null)
						$gp_array['items'][$i]['image'] = str_replace('\'', '\\\'', $i_image);
					else
						$gp_array['items'][$i]['image'] = 'Empty_Item.png';
					$i_name = null;
					$i_image = null;
				}
				
				$gp_array['kills'] = $row['dota_kills'];
				$gp_array['deaths'] = $row['dota_deaths'];
				if ($row['dota_deaths'] != 0) $gp_array['kd'] = round($row['dota_kills'] / $row['dota_deaths'], 2);
				else if ($row['dota_deaths'] == 0 && $row['dota_kills'] != 0) $gp_array['kd'] = '&#8734;';
				else $gp_array['kd'] = 0;
				$gp_array['assists'] = $row['dota_assists'];
				$gp_array['creepkills'] = $row['dota_creepkills'];
				$gp_array['neutralkills'] = $row['dota_neutralkills'];
				
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
			
			$s_highest_k_a = -1;
			$s_highest_k_ar = array();
			$s_lowest_d_a = -1;
			$s_lowest_d_ar = array();
			$s_highest_kd_a = -1;
			$s_highest_kd_ar = array();
			$s_highest_a_a = -1;
			$s_highest_a_ar = array();
			
			$sc_highest_k_a = -1;
			$sc_highest_k_ar = array();
			$sc_lowest_d_a = -1;
			$sc_lowest_d_ar = array();
			$sc_highest_kd_a = -1;
			$sc_highest_kd_ar = array();
			$sc_highest_a_a = -1;
			$sc_highest_a_ar = array();
			
			while ($player = current($gameplayers))
			{
				/* SENTINELS */
				if ($player['colour'] >= 1 && $player['colour'] <= 5)
				{
					/* kills */
					if ($player['kills'] > $s_highest_k_a)
					{
						$s_highest_k_a = $player['kills'];
						$s_highest_k_ar = array();
						$s_highest_k_ar[] = array('kills' => $player['kills'], 'name' => $player['name'], 'colour' => getColorById($player['colour']));
					}
					else if ($player['kills'] == $s_highest_k_a)
					{
						$s_highest_k_ar[] = array('kills' => $player['kills'], 'name' => $player['name'], 'colour' => getColorById($player['colour']));
					}
					
					/* deaths */
					if ($player['deaths'] < $s_lowest_d_a || $s_lowest_d_a == -1)
					{
						$s_lowest_d_a = $player['deaths'];
						$s_lowest_d_ar = array();
						$s_lowest_d_ar[] = array('deaths' => $player['deaths'], 'name' => $player['name'], 'colour' => getColorById($player['colour']));
					}
					else if ($player['deaths'] == $s_lowest_d_a)
					{
						$s_lowest_d_ar[] = array('deaths' => $player['deaths'], 'name' => $player['name'], 'colour' => getColorById($player['colour']));
					}
					
					/* kd */
					if ($player['deaths'] != 0 && $player['kills'] != 0)
					{
						if ($player['kills'] / $player['deaths'] > $s_highest_kd_a && $s_highest_kd_a != 'inf')
						{
							$s_highest_kd_a = $player['kills'] / $player['deaths'];
							$s_highest_kd_ar = array();
							$s_highest_kd_ar[] = array('kd' => round($player['kills'] / $player['deaths'], 2), 'name' => $player['name'], 'colour' => getColorById($player['colour']));
						}
						else if ($player['assists'] == $s_highest_a_a && $s_highest_kd_a != 'inf')
						{
							$s_highest_kd_ar[] = array('kd' => round($player['kills'] / $player['deaths'], 2), 'name' => $player['name'], 'colour' => getColorById($player['colour']));
						}
					}
					else if ($player['kills'] != 0)
					{
						$s_highest_kd_a = 'inf';
						$s_highest_kd_ar[] = array('kd' => 'inf', 'name' => $player['name'], 'colour' => getColorById($player['colour']));
					}
					else
					{
						$s_highest_kd_ar[] = array('kd' => 0, 'name' => $player['name'], 'colour' => getColorById($player['colour']));
					}
					
					/* assists */
					if ($player['assists'] > $s_highest_a_a)
					{
						$s_highest_a_a = $player['assists'];
						$s_highest_a_ar = array();
						$s_highest_a_ar[] = array('assists' => $player['assists'], 'name' => $player['name'], 'colour' => getColorById($player['colour']));
					}
					else if ($player['assists'] == $s_highest_a_a)
					{
						$s_highest_a_ar[] = array('assists' => $player['assists'], 'name' => $player['name'], 'colour' => getColorById($player['colour']));
					}
				}
				
				/* SCOURGES */
				if ($player['colour'] >= 7 && $player['colour'] <= 11)
				{
					/* kills */
					if ($player['kills'] > $sc_highest_k_a)
					{
						$sc_highest_k_a = $player['kills'];
						$sc_highest_k_ar = array();
						$sc_highest_k_ar[] = array('kills' => $player['kills'], 'name' => $player['name'], 'colour' => getColorById($player['colour']));
					}
					else if ($player['kills'] == $sc_highest_k_a)
					{
						$sc_highest_k_ar[] = array('kills' => $player['kills'], 'name' => $player['name'], 'colour' => getColorById($player['colour']));
					}
					
					/* deaths */
					if ($player['deaths'] < $sc_lowest_d_a || $sc_lowest_d_a == -1)
					{
						$sc_lowest_d_a = $player['deaths'];
						$sc_lowest_d_ar = array();
						$sc_lowest_d_ar[] = array('deaths' => $player['deaths'], 'name' => $player['name'], 'colour' => getColorById($player['colour']));
					}
					else if ($player['deaths'] == $sc_lowest_d_a)
					{
						$sc_lowest_d_ar[] = array('deaths' => $player['deaths'], 'name' => $player['name'], 'colour' => getColorById($player['colour']));
					}
					
					/* kd */
					if ($player['deaths'] != 0 && $player['kills'] != 0)
					{
						if ($player['kills'] / $player['deaths'] > $sc_highest_kd_a && $sc_highest_kd_a != 'inf')
						{
							$sc_highest_kd_a = $player['kills'] / $player['deaths'];
							$sc_highest_kd_ar = array();
							$sc_highest_kd_ar[] = array('kd' => round($player['kills'] / $player['deaths'], 2), 'name' => $player['name'], 'colour' => getColorById($player['colour']));
						}
						else if ($player['kills'] / $player['deaths'] == $sc_highest_kd_a && $sc_highest_kd_a != 'inf')
						{
							$sc_highest_kd_ar[] = array('kd' => round($player['kills'] / $player['deaths'], 2), 'name' => $player['name'], 'colour' => getColorById($player['colour']));
						}
					}
					else if ($player['kills'] != 0)
					{
						$sc_highest_kd_a = 'inf';
						$sc_highest_kd_ar[] = array('kd' => 'inf', 'name' => $player['name'], 'colour' => getColorById($player['colour']));
					}
					else
					{
						$sc_highest_kd_ar[] = array('kd' => 0, 'name' => $player['name'], 'colour' => getColorById($player['colour']));
					}
					
					/* assists */
					if ($player['assists'] > $sc_highest_a_a)
					{
						$sc_highest_a_a = $player['assists'];
						$sc_highest_a_ar = array();
						$sc_highest_a_ar[] = array('assists' => $player['assists'], 'name' => $player['name'], 'colour' => getColorById($player['colour']));
					}
					else if ($player['assists'] == $sc_highest_a_a)
					{
						$sc_highest_a_ar[] = array('assists' => $player['assists'], 'name' => $player['name'], 'colour' => getColorById($player['colour']));
					}
				}
				
				next($gameplayers);
			}
			reset($gameplayers);
			
			$sentinels['kills'] = $s_highest_k_ar;
			$sentinels['deaths'] = $s_lowest_d_ar;
			$sentinels['kd'] = $s_highest_kd_ar;
			$sentinels['assists'] = $s_highest_a_ar;
			
			$sentinels['kills_c'] = count($sentinels['kills']);
			$sentinels['deaths_c'] = count($sentinels['deaths']);
			$sentinels['kd_c'] = count($sentinels['kd']);
			$sentinels['assists_c'] = count($sentinels['assists']);
			
			$scourges['kills'] = $sc_highest_k_ar;
			$scourges['deaths'] = $sc_lowest_d_ar;
			$scourges['kd'] = $sc_highest_kd_ar;
			$scourges['assists'] = $sc_highest_a_ar;
			
			$scourges['kills_c'] = count($scourges['kills']);
			$scourges['deaths_c'] = count($scourges['deaths']);
			$scourges['kd_c'] = count($scourges['kd']);
			$scourges['assists_c'] = count($scourges['assists']);
			$time_zone = 4*3600;
			if (file_exists(realpath($this->_cfg['replays_folder']) . '/GHost++ ' . date('Y-m-d H-i', (int)$game['datetime']+$time_zone). ' ' . str_replace(array('\\', '/', ':', '*', '?', '<', '>', '|'), '_', $game['gamename']) . ' ' . '(' . sec2min($game['duration']) . ').w3g'))
			{
				$this->_tpl->assign('show_replays', 1);
				$this->_tpl->assign('replay_link', 'dlreplay.php?replay=' . rawurlencode('GHost++ ' . date('Y-m-d H-i', (int)$game['datetime']+$time_zone). ' ' . str_replace(array('\\', '/', ':', '*', '?', '<', '>', '|'), '_', $game['gamename']) . ' ' . '(' . sec2min($game['duration']) . ').w3g'));
			}
			
			$this->_tpl->assign('gameplayers', $gameplayers);
			$this->_tpl->assign('sentinels', $sentinels);
			$this->_tpl->assign('scourges', $scourges);
			
			$s_lang = array();
			$s_lang['TheMapWasX'] = str_replace('_MAPNAME_', '<b>' . $game_array['map_name'] . '</b>', $this->_lang['DOTAGamePlayers']['TheMapWasX']);
			$s_lang['TheGameEndedAtX'] = str_replace('_DATETIME_', '<b>' . date('Y-m-d H:i:s', $game_array['datetime']) . '</b>', $this->_lang['DOTAGamePlayers']['TheGameEndedAtX']);
			$s_lang['ThereWasXPlayersInThisGameForAXMatch'] = str_replace('_NUMPLAYERS_', '<b>' . $game_array['playersnum'] . '</b>', $this->_lang['DOTAGamePlayers']['ThereWasXPlayersInThisGameForAXMatch']);
			$s_lang['ThereWasXPlayersInThisGameForAXMatch'] = str_replace('_MATCH_', '<b>' . $game_array['versus'] . '</b>', $s_lang['ThereWasXPlayersInThisGameForAXMatch']);
			$s_lang['TheGameLastedX'] = str_replace('_DURATION_', '<b>' . secondsToHours($game_array['duration']) . '</b>', $this->_lang['DOTAGamePlayers']['TheGameLastedX']);

			$this->_tpl->assign('s_lang', $s_lang);
		}
		
		$this->_tpl->display('gstatspp_dotagameplayers.tpl', $this->_args['gameid']);
	}
}

?>