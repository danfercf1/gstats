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

class PagePlayers extends Page implements iPage
{
	function run()
	{
		if (!isset($this->_args['playerid']) || (!$this->_cfg['show_normal_games'] && !$this->_cfg['show_dota_games']))
			die();
			
		if (!$this->_tpl->is_cached('gstatspp_players.tpl', $this->_args['playerid']))
		{		
			if (($query_player = $this->_dbs->query('
				SELECT `id`, `name`, `realm`
				FROM `dbs_players`
				WHERE `id` = ' . $this->_dbs->real_escape_string($this->_args['playerid']) . '
				LIMIT 1
				')) === false)
				die ('There was an error while retreiving the player informations.<br />Error: ' . $this->_dbs->error());
				
			if ($this->_dbs->num_rows($query_player) == 0)
				die();
				
			$player = $this->_dbs->fetch_array($query_player);
				
			if ($this->_cfg['show_normal_games'])
			{
				if (($query_pgames = $this->_dbs->query('
					SELECT `dbs_normalgames`.`id` as g_id, `dbs_normalgames`.`datetime` as g_datetime, `dbs_normalgames`.`gamename` as g_gamename, `dbs_normalgames`.`duration` as g_duration, `dbs_normalgames`.`playersnum` as g_playersnum,
					`dbs_normalgameplayers`.`player_id` as p_player_id, `dbs_normalgameplayers`.`gameid` as p_gameid, `dbs_normalgameplayers`.`reserved` as p_reserved, `dbs_normalgameplayers`.`loadingtime` as p_loadingtime, `dbs_normalgameplayers`.`left` as p_left, `dbs_normalgameplayers`.`colour` as p_colour, `dbs_normalgameplayers`.`spoofedrealm` as p_spoofedrealm
					FROM `dbs_normalgames`
					LEFT JOIN `dbs_normalgameplayers`
					ON `dbs_normalgames`.`id` = `dbs_normalgameplayers`.`gameid`
					WHERE `dbs_normalgameplayers`.`player_id` = ' . $player['id']
				)) === false)
					die ('There was an error while retreiving the player\'s games informations.<br />Error: ' . $this->_dbs->error());
					
				$avg_ldt = 0;
				$avg_ldtn = 0;
				
				$avg_lp = 0;
				$avg_lpn = 0;
				
				$gout = 0;
				$lout = 0;
				
				$tptime = 0;
				
				$pgames = array();
				while ($row = $this->_dbs->fetch_array($query_pgames))
				{
					$p_array = array();
					
					$p_array['gamename'] = $row['g_gamename'];
					$p_array['playersnum'] = $row['g_playersnum'];
					$p_array['left'] = $row['p_left'];
					if ($row['g_duration'] != 0)
						$p_array['leftp'] = round(($row['p_left'] / $row['g_duration']) * 100);
					else
						$p_array['leftp'] = 100;
					$p_array['datetime'] = $row['g_datetime'];
					$p_array['colour'] = $row['p_colour'];
					$p_array['gameid'] = $row['g_id'];
					
					if ($p_array['leftp'] <= 90) $lout++;
					if ($p_array['leftp'] <= 60) $gout++;
					
					if ($row['p_reserved'] == 1) $p_array['status'] = 1;
					else $p_array['status'] = 2;
					
					$tptime += $row['p_left'];
					
					$avg_ldt += $row['p_loadingtime'];
					$avg_ldtn++;
					
					$avg_lp += $p_array['leftp'];
					$avg_lpn++;
					
					$pgames[] = $p_array;
				}
			}
			
			if ($this->_cfg['show_dota_games'])
			{
			
				if (($query_pdotas = $this->_dbs->query('
					SELECT `dbs_dotagames`.`id` as g_id, `dbs_dotagames`.`datetime` as g_datetime, `dbs_dotagames`.`gamename` as g_gamename, `dbs_dotagames`.`duration` as g_duration, `dbs_dotagames`.`playersnum` as g_playersnum,
					`dbs_dotagameplayers`.`player_id` as p_player_id, `dbs_dotagameplayers`.`gameid` as p_gameid, `dbs_dotagameplayers`.`reserved` as p_reserved, `dbs_dotagameplayers`.`loadingtime` as p_loadingtime, `dbs_dotagameplayers`.`left` as p_left, `dbs_dotagameplayers`.`colour` as p_colour, `dbs_dotagameplayers`.`spoofedrealm` as p_spoofedrealm,
					`dbs_dotagameplayers`.`dota_kills` as dota_kills, `dbs_dotagameplayers`.`dota_deaths` as dota_deaths, `dbs_dotagameplayers`.`dota_assists` as dota_assists, `dbs_dotagameplayers`.`dota_creepkills` as dota_creepkills, `dbs_dotagameplayers`.`dota_neutralkills` as dota_neutralkills, `dbs_dotagames`.`versus` as versus, `dbs_dotagames`.`dota_winner` as dota_winner, `dbs_dotagameplayers`.`dota_newcolour` as dota_newcolour,
					`dbs_dotagameplayers`.`dota_hero` as dota_hero, `dbs_dotagameplayers`.`dota_item1` as dota_item1, `dbs_dotagameplayers`.`dota_item2` as dota_item2, `dbs_dotagameplayers`.`dota_item3` as dota_item3, `dbs_dotagameplayers`.`dota_item4` as dota_item4, `dbs_dotagameplayers`.`dota_item5` as dota_item5, `dbs_dotagameplayers`.`dota_item6` as dota_item6
					FROM `dbs_dotagames`
					LEFT JOIN `dbs_dotagameplayers`
					ON `dbs_dotagames`.`id` = `dbs_dotagameplayers`.`gameid`
					WHERE `dbs_dotagameplayers`.`player_id` = ' . $player['id']
				)) === false)
					die ('There was an error while retreiving the player\'s games informations.<br />Error: ' . $this->_dbs->error());
					
				$d_avg_k = 0;
				$d_avg_k_n = 0;
				$d_avg_d = 0;
				$d_avg_d_n = 0;
				$d_avg_a = 0;
				$d_avg_a_n = 0;
				
				$d_won = 0;
				$d_lost = 0;
				$d_und = 0;
				$d_u = 0;
				
				require_once 'dota/dota-heroes.php';
				require_once 'dota/dota-items.php';
				$pdotas = array();
				while ($row = $this->_dbs->fetch_array($query_pdotas))
				{
					$p_array = array();
					
					$p_array['match'] = $row['versus'];
					$p_array['kills'] = $row['dota_kills'];
					$p_array['deaths'] = $row['dota_deaths'];
					$p_array['assists'] = $row['dota_assists'];
					$p_array['creepkills'] = $row['dota_creepkills'];
					$p_array['neutralkills'] = $row['dota_neutralkills'];
					$p_array['winner'] = $row['dota_winner'];
					
					if ($row['dota_deaths'] != 0) $p_array['kd'] = round($row['dota_kills'] / $row['dota_deaths'], 2);
					else if ($row['dota_deaths'] == 0 && $row['dota_kills'] != 0) $p_array['kd'] = '&#8734;';
					else $p_array['kd'] = 0;
					
					if (isset($dota['heroes']['' . strtolower($row['dota_hero']) . '']) && $dota['heroes']['' . strtolower($row['dota_hero']) . '']['is_duplicate'] == false)
					{
						$p_array['hero_name'] = $dota['heroes']['' . strtolower($row['dota_hero']) . '']['name'];
						$p_array['hero_image'] = trim($dota['heroes']['' . strtolower($row['dota_hero']) . '']['art']);
					}
					else if (isset($dota['heroes']['' . strtolower($row['dota_hero']) . '']))
					{
						$p_array['hero_name'] = $dota['heroes']['' . $dota['heroes']['' . strtolower($row['dota_hero']) . '']['original_id'] . '']['name'];
						$p_array['hero_image'] = trim($dota['heroes']['' . $dota['heroes']['' . strtolower($row['dota_hero']) . '']['original_id'] . '']['art']);
					}
					else
					{
						$p_array['hero_name'] = 'Unknown '. $row['dota_hero'];
						$p_array['hero_image'] = 'Empty_Hero.png';
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
							$i_name = 'Unknown ' . $row['dota_item' . $i . ''];
						}
						else
						{
							$i_name = 'Empty';
						}
						
						$p_array['items'][$i]['name'] = str_replace('\'', '\\\'', $i_name);
						if (isset($i_image) && $i_image !== null)
							$p_array['items'][$i]['image'] = trim(str_replace('\'', '\\\'', $i_image));
						else
							$p_array['items'][$i]['image'] = 'Empty_Item.png';
						$i_name = null;
						$i_image = null;
					}
					
					$p_array['gamename'] = $row['g_gamename'];
					$p_array['playersnum'] = $row['g_playersnum'];
					$p_array['left'] = $row['p_left'];
					if ($row['g_duration'] != 0)
						$p_array['leftp'] = round(($row['p_left'] / $row['g_duration']) * 100);
					else
						$p_array['leftp'] = 100;
					$p_array['datetime'] = $row['g_datetime'];
					if ($row['dota_newcolour'] != 0)
						$p_array['colour'] = $row['dota_newcolour'];
					else
						$p_array['colour'] = $row['p_colour'];
					$p_array['gameid'] = $row['g_id'];
					
					if ($p_array['leftp'] <= 90) $lout++;
					if ($p_array['leftp'] <= 60) $gout++;
					
					if ($row['p_reserved'] == 1) $p_array['status'] = 1;
					else $p_array['status'] = 2;
					
					$tptime += $row['p_left'];
					
					$avg_ldt += $row['p_loadingtime'];
					$avg_ldtn++;
					
					$avg_lp += $p_array['leftp'];
					$avg_lpn++;
					
					$d_avg_k += $row['dota_kills'];
					$d_avg_d += $row['dota_deaths'];
					$d_avg_a += $row['dota_assists'];
					$d_avg_k_n++;
					$d_avg_d_n++;
					$d_avg_a_n++;
					
					if ($p_array['leftp'] > 90)
					{
						if ($row['dota_winner'] != 0)
						{
							if (($row['dota_winner'] == 1 && $row['dota_newcolour'] >= 1 && $row['dota_newcolour'] <= 5) || ($row['dota_winner'] == 2 && $row['dota_newcolour'] >= 7 && $row['dota_newcolour'] <= 11))
								$d_won++;
							else
								$d_lost++;
						}
						else
							$d_und++;
					}
					else
						$d_u++;
					
					$pdotas[] = $p_array;
				}
				
				if ($d_avg_d != 0) $kd = round($d_avg_k / $d_avg_d, 2);
				else if ($d_avg_d == 0 && $d_avg_k != 0) $kd = '&#8734;';
				else $kd = 0;
					
				if ($d_avg_k_n != 0)
					$d_avg_k = round($d_avg_k / $d_avg_k_n, 2);
					
				if ($d_avg_d_n != 0)
					$d_avg_d = round($d_avg_d / $d_avg_d_n, 2);
					
				if ($d_avg_a_n != 0)
					$d_avg_a = round($d_avg_a / $d_avg_a_n, 2);
					
				$player['dota_avgkills'] = $d_avg_k;
				$player['dota_avgdeaths'] = $d_avg_d;
				$player['dota_avgassists'] = $d_avg_a;
				$player['dota_kd'] = $kd;
				$player['dota_won'] = $d_won;
				$player['dota_lost'] = $d_lost;
				$player['dota_und'] = $d_und;
				$player['dota_u'] = $d_u;
			}
			
			if ($avg_ldtn != 0)
				$avg_ldt = round($avg_ldt / $avg_ldtn);
				
			if ($avg_lpn != 0)
				$avg_lp = round($avg_lp / $avg_lpn);

			$player['gout'] = $gout;
			$player['lout'] = $lout;
			$player['avg_ldt'] = $avg_ldt;
			$player['avg_lp'] = $avg_lp;
			$player['tptime'] = $tptime;
			
			$this->_tpl->assign('player', $player);
			if ($this->_cfg['show_normal_games']) $this->_tpl->assign('pgames', $pgames);
			if ($this->_cfg['show_dota_games']) $this->_tpl->assign('pdotas', $pdotas);
			
			$s_lang = array();
			$s_lang['XHaveAnAverageLoadingTimeOfX'] = str_replace('_PLAYERNAME_', '<b>' . $player['name'] . '</b>', $this->_lang['Players']['XHaveAnAverageLoadingTimeOfX']);
			$s_lang['XHaveAnAverageLoadingTimeOfX'] = str_replace('_LOADINGTIME_', '<b>' . round($avg_ldt / 1000, 2) . ' Sec</b>', $s_lang['XHaveAnAverageLoadingTimeOfX']);
			$s_lang['HeGayedOutOfXGamesAndLeftATotalOfXGamesBeforeTheEnd'] = str_replace('_GOUT_', '<b>' . $gout . '</b>', $this->_lang['Players']['HeGayedOutOfXGamesAndLeftATotalOfXGamesBeforeTheEnd']);
			$s_lang['HeGayedOutOfXGamesAndLeftATotalOfXGamesBeforeTheEnd'] = str_replace('_LOUT_', '<b>' . $lout . '</b>', $s_lang['HeGayedOutOfXGamesAndLeftATotalOfXGamesBeforeTheEnd']);
			$s_lang['HeLeftXGamesBeforeTheEnd'] = str_replace('_LOUT_', '<b>' . $lout . '</b>', $this->_lang['Players']['HeLeftXGamesBeforeTheEnd']);
			$s_lang['HeNeverLeftBeforeTheEndOfAGame'] = $this->_lang['Players']['HeNeverLeftBeforeTheEndOfAGame'];
			$s_lang['HePlayedATotalTimeOfX'] = str_replace('_TPTIME_', '<b>' . secondsToHours($tptime) . '</b>', $this->_lang['Players']['HePlayedATotalTimeOfX']);
			$s_lang['AverageKillsPerGamesX'] = str_replace('_STAT_', '<b>' . $d_avg_k . '</b>', $this->_lang['Players']['AverageKillsPerGamesX']);
			$s_lang['AverageDeathsPerGamesX'] = str_replace('_STAT_', '<b>' . $d_avg_d . '</b>', $this->_lang['Players']['AverageDeathsPerGamesX']);
			$s_lang['AverageAssistsPerGamesX'] = str_replace('_STAT_', '<b>' . $d_avg_a . '</b>', $this->_lang['Players']['AverageAssistsPerGamesX']);
			$s_lang['GlobalKillsDeathsRatio'] = str_replace('_STAT_', '<b>' . $kd . '</b>', $this->_lang['Players']['GlobalKillsDeathsRatio']);

			$s_lang['GamesWinLost'] = str_replace('_WON_', '<b>' . $d_won . '</b>', $this->_lang['Players']['GamesWinLost']);
			$s_lang['GamesWinLost'] = str_replace('_WON-S_', ($d_won > 1 ? 's' : ''), $s_lang['GamesWinLost']);
			$s_lang['GamesWinLost'] = str_replace('_LOST_', '<b>' . $d_lost . '</b>', $s_lang['GamesWinLost']);
			$s_lang['GamesWinLost'] = str_replace('_LOST-S_', ($d_lost > 1 ? 's' : ''), $s_lang['GamesWinLost']);
			$s_lang['GamesWinLost'] = str_replace('_UND_', '<b>' . $d_und . '</b>', $s_lang['GamesWinLost']);
			$s_lang['GamesWinLost'] = str_replace('_UND-S_', ($d_und > 1 ? 's' : ''), $s_lang['GamesWinLost']);
			
			if ($d_u > 0) 
			{
				$s_lang['GamesIgnored'] = str_replace('_IGNORED_', '<b>' . $d_u . '</b>', $this->_lang['Players']['GamesIgnored']);
				$s_lang['GamesIgnored'] = str_replace('_IGNORED-S_', ($d_u > 1 ? 's' : ''), $s_lang['GamesIgnored']);
				$s_lang['GamesIgnored'] = str_replace('_WORD_', ($d_u > 1 ? $this->_lang['Players']['WordPlural'] : $this->_lang['Players']['WordSingular']), $s_lang['GamesIgnored']);
			}
			else $s_lang['GamesIgnored'] = $this->_lang['Players']['NoGamesWereIgnoredForLeavingPrematurely'];

			$this->_tpl->assign('s_lang', $s_lang);
		}
		
		$this->_tpl->display('gstatspp_players.tpl', $this->_args['playerid']);
	}
}

?>