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

class UpdaterNormalgameplayers extends Updater implements iUpdater
{
	function update()
	{
		if ($this->_use_set_time_limit == true)
		{
			set_time_limit(0);
		}
		
		if (!$this->_dbs->query('
			DELETE FROM `dbs_lastupdates`
			WHERE `name` = \'normalgameplayers\''))
			die ('There was an error in the update sequence (UPDATE_ERRORID=normalgameplayers-01) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
		
		if (!$this->_dbs->query('
			INSERT INTO `dbs_lastupdates`
			SET
				`id` = null,
				`name` = \'normalgameplayers\',
				`time` = ' . time() . '
			'))
			die ('There was an error in the update sequence (UPDATE_ERRORID=normalgameplayers-02) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
		
		if (($query_dbh_playersmaxid = $this->_dbh->query('
			SELECT 
				MAX(`id`) as maxid
			FROM `gameplayers`
			' . ($this->_usebotid ? 'WHERE `botid` = ' . $this->_botid : ''))) === false)
			die ('There was an error in the update sequence (UPDATE_ERRORID=normalgameplayers-03) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbh->error());
			
		$row = $this->_dbh->fetch_array($query_dbh_playersmaxid);
		$dbh_playersmaxid = $row['maxid'];
		
		if (($query_dbs_playersmaxid = $this->_dbs->query('
			SELECT 
				`id`,
				`botid`,
				`name`,
				`entry`
			FROM `dbs_lastentries`
			WHERE `botid` = ' . $this->_dbs->real_escape_string($this->_dbs_botid) . ' AND name = \'normalgameplayers_playersmaxid\'')) === false)
			die ('There was an error in the update sequence (UPDATE_ERRORID=normalgameplayers-04) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
			
		$retained_row = null; 
		while ($row = $this->_dbs->fetch_array($query_dbs_playersmaxid))
			if ($retained_row === null || $retained_row['entry'] > $row['entry'])
				$retained_row = $row;
				
		if ($retained_row !== null)
			$minimalplayersid = $retained_row['entry'];
		else
			$minimalplayersid = 0;
			
		if ($dbh_playersmaxid > $minimalplayersid)
		{
		
			if (($query_dbh_dotaplayers = $this->_dbh->query('
				SELECT 
					`id`, 
					' . ($this->_usebotid ? '`botid`,' : '') . '
					`gameid`
				FROM `dotagames`
				' . ($this->_usebotid ? 'WHERE `botid` = ' . $this->_botid : ''))) === false)
				die ('There was an error in the update sequence (UPDATE_ERRORID=normalgameplayers-05) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbh->error());
				
			$dotagames = array(); 
			while ($row = $this->_dbh->fetch_array($query_dbh_dotaplayers))
				$dotagames[] = $row['gameid'];
				
			if (($query_dbs_p_players = $this->_dbs->query('
				SELECT
					`id`,
					`name`,
					`realm`
				FROM `dbs_players`
				')) === false)
				die ('There was an error in the update sequence (UPDATE_ERRORID=normalgameplayers-06) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
			
			$p_players_num = 0;
			$p_players = array(); 
			while ($row = $this->_dbs->fetch_array($query_dbs_p_players))
				$p_players[$p_players_num++] = $row;

			if (($query_dbh_players = $this->_dbh->query('
				SELECT 
					`id`, 
					' . ($this->_usebotid ? '`botid`,' : '') . '
					`gameid`,
					`name`,
					`spoofed`,
					`reserved`,
					`loadingtime`,
					`left`,
					`leftreason`,
					`team`,
					`colour`,
					`spoofedrealm`
				FROM `gameplayers`
				WHERE `id` >= ' . $minimalplayersid . '' . ($this->_usebotid ? ' AND `botid` = ' . $this->_botid : '') . '
				LIMIT 0, ' . $this->_maxselect)) === false) // AND `spoofed` = 1
				die ('There was an error in the update sequence (UPDATE_ERRORID=normalgameplayers-07) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbh->error());
			
			$j = 0;
			$p = 0;
			$query_add = '';
			$back_query_rows = array();
			$p_query_add = '';
			$s_p_players = array();
			$last_row = false;
			while ($last_row == false)
			{
				if (($row = $this->_dbh->fetch_array($query_dbh_players)) != false)
				{
					$last_id = $row['id'];
					if (!in_array($row['gameid'], $dotagames))
					{
						$player_id = -1;
						
						if ($row['spoofedrealm'] != '' && $row['spoofed'] != 0)
						{
							$p_in_db = false;

							$finished = false;
							$s_p = false;
							while ($p_player = current($p_players))
							{
								if ($p_player['name'] == $row['name'] && $p_player['realm'] == $row['spoofedrealm'])
								{
									$key = key($p_players);
									$finished = true;
									break;
								}
								next($p_players);
							}
							reset($p_players);
							
							if ($finished == false)
							{
								while ($p_player = current($s_p_players))
								{
									if ($p_player['name'] == $row['name'] && $p_player['realm'] == $row['spoofedrealm'])
									{
										$key = key($s_p_players);
										$s_p = true;
										$finished = true;
										break;
									}
									next($s_p_players);
								}
								reset($s_p_players);
							}

							if ($finished == false)
							{
								$p++;
								
								if (($query_dbs_findgameid = $this->_dbs->query('
									SELECT
										`id`
									FROM `dbs_normalgames`
									WHERE `botid` = ' . $this->_dbs->real_escape_string($this->_dbs_botid) . ' && `ghostid` = ' . $row['gameid'] . '
									LIMIT 1
									')) === false)
									die ('There was an error in the update sequence (UPDATE_ERRORID=normalgameplayers-s01) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
									
								$f_gameid = $this->_dbs->fetch_array($query_dbs_findgameid);
								$gameid = $f_gameid['id'];
								
								$new_array = array();
								$new_array['id'] = -1;
								$new_array['name'] = $row['name'];
								$new_array['realm'] = $row['spoofedrealm'];
								$new_array['botid'] = $row['botid'];
								
								$s_p_players[$p_players_num++] = $new_array;

								unset($new_array);
								
								$p_last_was_last = true;
							}
							else
							{
								if ($s_p == false)
								{
									if ($p_players[$key]['id'] == -1)
									{
										if (($query_dbs_p_s_players = $this->_dbs->query('
											SELECT
												`id`
											FROM `dbs_players`
											WHERE `name` = ' . $this->_dbs->real_escape_string($row['name']) . ' && `realm` = ' . $this->_dbs->real_escape_string($row['spoofedrealm']) . '
											LIMIT 1
											')) === false)
											die ('There was an error in the update sequence (UPDATE_ERRORID=normalgameplayers-08) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
										
										$p_s_player = $this->_dbs->fetch_array($query_dbs_p_s_players);
										
										$p_players[$key]['id'] = $p_s_player['id'];
									}
										
									$player_id = $p_players[$key]['id'];
								}
								else
								{
									if (($query_dbs_findgameid2 = $this->_dbs->query('
										SELECT
											`id`
										FROM `dbs_normalgames`
										WHERE `botid` = ' . $this->_dbs->real_escape_string($this->_dbs_botid) . ' && `ghostid` = ' . $row['gameid'] . '
										LIMIT 1
										')) === false)
										die ('There was an error in the update sequence (UPDATE_ERRORID=normalgameplayers-s02) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
										
									$f_gameid = $this->_dbs->fetch_array($query_dbs_findgameid2);
									$gameid = $f_gameid['id'];

									$player_id = $s_p_players[$key]['id'];
								}
							}
						}
						
						if ($player_id != -1)
						{
							$j++;
							$ad = false;
							
							if (($query_ngid = $this->_dbs->query('
								SELECT `id`, `ghostid`
								FROM `dbs_normalgames`
								WHERE `botid` = ' . $this->_dbs->real_escape_string($this->_dbs_botid) . ' AND `ghostid` = ' . $row['gameid'] . '' . ($this->_usebotid ? ' AND `ghostbotid` = ' . $row['botid'] : '') . '
								LIMIT 1
								')) === false)
								die ('There was an error in the update sequence (UPDATE_ERRORID=normalgameplayers-10) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
								
							$row_ngid = $this->_dbs->fetch_array($query_ngid);
							$new_gameid = $row_ngid['id'];
							
							if (!isset($new_gameid) || $new_gameid == '' || $new_gameid == 0)
								$new_gameid = 0;
							
							$query_add .= '(';
							
							$query_add .= 'null, ';
							$query_add .= $row['id'] . ', ';
							$query_add .= ($this->_usebotid ? $row['botid'] : 0) . ', ';
							$query_add .= $this->_dbs->real_escape_string($this->_dbs_botid) . ', ';
							$query_add .= $player_id . ', ';
							$query_add .= $new_gameid . ', ';
							$query_add .= $this->_dbs->real_escape_string($row['name']) . ', ';
							$query_add .= $row['spoofed'] . ', ';
							$query_add .= $row['reserved'] . ', ';
							$query_add .= $row['loadingtime'] . ', ';
							$query_add .= $row['left'] . ', ';
							$query_add .= $this->_dbs->real_escape_string($row['leftreason']) . ', ';
							$query_add .= $row['team'] . ', ';
							$query_add .= $row['colour'] . ', ';
							$query_add .= $this->_dbs->real_escape_string($row['spoofedrealm']);
							
							$query_add .= ')';
							$query_add .= (($j % $this->_maxinsert) == 0 ? ';' : ',') . "\n";
						}
						else
						{
							$back_query_rows[] = $row;
						}
					}
				}
				else
					$last_row = true;
				
				if ((($j % $this->_maxinsert) == 0 || $last_row == true) && $j > 0 && $ad == false)
				{
					$ad = true;
					
					if ($last_row == true)
					{
						$query_add_split = str_split(trim($query_add), strlen($query_add) - 2);
						$query_add = $query_add_split[0];
						$query_add .= ';';
					}

					if (!$this->_dbs->query('
						INSERT INTO `dbs_normalgameplayers` (
							`id`, 
							`ghostid`, 
							`ghostbotid`, 
							`botid`, 
							`player_id`,
							`gameid`,
							`name`, 
							`spoofed`,
							`reserved`,
							`loadingtime`,
							`left`,
							`leftreason`,
							`team`,
							`colour`,
							`spoofedrealm`)
						VALUES
						' . $query_add))
						die ('There was an error in the update sequence (UPDATE_ERRORID=normalgameplayers-11) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
						
					$query_add = '';
				}
				
				if ((($p % $this->_maxinsert) == 0 && $p != 0 || $last_row == true) && $p > 0 && count($s_p_players) > 0)
				{
					while ($s_p_player = current($s_p_players))
					{
						$p_query_add .= '(';
						
						$p_query_add .= 'null, ';
						$p_query_add .= $this->_dbs->real_escape_string($s_p_player['name']) . ', ';
						$p_query_add .= $this->_dbs->real_escape_string($s_p_player['realm']);
						
						$p_query_add .= '),' . "\n";
						
						unset ($s_p_players[key($s_p_players)]['botid']);
						
						next($s_p_players);
					}
					reset($s_p_players);

					$p_query_add_split = str_split(trim($p_query_add), strlen($p_query_add) - 2);
					$p_query_add = $p_query_add_split[0];
					$p_query_add .= ';';
				
					if (!$this->_dbs->query('
						INSERT INTO `dbs_players` (
							`id`,  
							`name`, 
							`realm`)
						VALUES
						' . $p_query_add))
						die ('There was an error in the update sequence (UPDATE_ERRORID=normalgameplayers-12) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
					
					$p_players = array_merge($p_players, $s_p_players);
					$s_p_players = array();
					
					$p_query_add = '';
				}
			}
			
			$i = 0;
			$j = 0;
			$query_add = '';
			$count = count($back_query_rows);
			$o = count($back_query_rows);
			while ($count > 0)
			{
				$j++;
				$count--;
				
				$row = $back_query_rows[$i];
				
				if ($row['spoofedrealm'] != '' && $row['spoofed'] == 1)
				{
					if (($query_dbs_p_s_player = $this->_dbs->query('
						SELECT
							`id`,
							`name`,
							`realm`
						FROM `dbs_players`
						WHERE `name` = ' . $this->_dbs->real_escape_string($row['name']) . ' && `realm` = ' . $this->_dbs->real_escape_string($row['spoofedrealm']) . '
						LIMIT 1
						')) === false)
						die ('There was an error in the update sequence (UPDATE_ERRORID=normalgameplayers-13) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
						
					$new_row = $this->_dbs->fetch_array($query_dbs_p_s_player);

					$player_id = $new_row['id'];
				}
				else
				{
					$player_id = '\'-1\'';
				}
				
				if (($query_ngid = $this->_dbs->query('
					SELECT `id`, `ghostid`
					FROM `dbs_normalgames`
					WHERE `botid` = ' . $this->_dbs->real_escape_string($this->_dbs_botid) . ' AND `ghostid` = ' . $row['gameid'] . '' . ($this->_usebotid ? ' AND `ghostbotid` = ' . $row['botid'] : '') . '
					LIMIT 1
					')) === false)
					die ('There was an error in the update sequence (UPDATE_ERRORID=normalgameplayers-14) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
					
				$row_ngid = $this->_dbs->fetch_array($query_ngid);
				$new_gameid = $row_ngid['id'];
				
				if (!isset($new_gameid) || $new_gameid == '' || $new_gameid == 0)
					$new_gameid = 0;
						
				$query_add .= '(';
				
				$query_add .= 'null, ';
				$query_add .= $row['id'] . ', ';
				$query_add .= ($this->_usebotid ? $row['botid'] : 0) . ', ';
				$query_add .= $this->_dbs->real_escape_string($this->_dbs_botid) . ', ';
				$query_add .= $player_id . ', ';
				$query_add .= $new_gameid . ', ';
				$query_add .= $this->_dbs->real_escape_string($row['name']) . ', ';
				$query_add .= $row['spoofed'] . ', ';
				$query_add .= $row['reserved'] . ', ';
				$query_add .= $row['loadingtime'] . ', ';
				$query_add .= $row['left'] . ', ';
				$query_add .= $this->_dbs->real_escape_string($row['leftreason']) . ', ';
				$query_add .= $row['team'] . ', ';
				$query_add .= $row['colour'] . ', ';
				$query_add .= $this->_dbs->real_escape_string($row['spoofedrealm']);
				
				$query_add .= ')';
				$query_add .= ((($j % $this->_maxinsert) == 0 || $count == 0) ? ';' : ',') . "\n";
				
				if ((($j % $this->_maxinsert) == 0 || $count == 0) && $j > 0)
				{
					if (!$this->_dbs->query('
						INSERT INTO `dbs_normalgameplayers` (
							`id`, 
							`ghostid`, 
							`ghostbotid`, 
							`botid`, 
							`player_id`,
							`gameid`,
							`name`, 
							`spoofed`,
							`reserved`,
							`loadingtime`,
							`left`,
							`leftreason`,
							`team`,
							`colour`,
							`spoofedrealm`)
						VALUES
						' . $query_add))
						die ('There was an error in the update sequence (UPDATE_ERRORID=normalgameplayers-15) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());

					$query_add = '';
				}
				
				$i++;
			}
			
			if (!$this->_dbs->query('
				DELETE FROM `dbs_lastentries`
				WHERE `botid` = ' . $this->_dbs->real_escape_string($this->_dbs_botid) . ' AND `name` = \'normalgameplayers_playersmaxid\''))
				die ('There was an error in the update sequence (UPDATE_ERRORID=normalgameplayers-16) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
			
			if (!$this->_dbs->query('
				INSERT INTO `dbs_lastentries` (
					`id`,  
					`botid`, 
					`name`,
					`entry`)
				VALUES
				(null, ' . $this->_dbs->real_escape_string($this->_dbs_botid) . ', \'normalgameplayers_playersmaxid\', ' . ($last_id + 1) . ');'))
				die ('There was an error in the update sequence (UPDATE_ERRORID=normalgameplayers-17) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
		}
	}
}

?>