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

class UpdaterNormalgames extends Updater implements iUpdater
{
	function update()
	{
		if ($this->_use_set_time_limit == true)
		{
			set_time_limit(0);
		}
		
		$this->_maxselect = $this->_maxselect * 2;
		
		if (!$this->_dbs->query('
			DELETE FROM `dbs_lastupdates`
			WHERE `name` = \'normalgames\''))
			die ('There was an error in the update sequence (UPDATE_ERRORID=normalgames-01) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
		
		if (!$this->_dbs->query('
			INSERT INTO `dbs_lastupdates`
			SET
				`id` = null,
				`name` = \'normalgames\',
				`time` = ' . time() . '
			'))
			die ('There was an error in the update sequence (UPDATE_ERRORID=normalgames-02) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
		
		if (($query_dbh_gamesmaxid = $this->_dbh->query('
			SELECT 
				MAX(`id`) as maxid
			FROM `games`
			' . ($this->_usebotid ? 'WHERE `botid` = ' . $this->_botid : ''))) === false)
			die ('There was an error in the update sequence (UPDATE_ERRORID=normalgames-03) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbh->error());
			
		$row = $this->_dbh->fetch_array($query_dbh_gamesmaxid);
		$dbh_gamesmaxid = $row['maxid'];
		
		if (($query_dbs_gamesmaxid = $this->_dbs->query('
			SELECT 
				`id`,
				`botid`,
				`name`,
				`entry`
			FROM `dbs_lastentries`
			WHERE `botid` = ' . $this->_dbs->real_escape_string($this->_dbs_botid) . ' AND name = \'normalgames_gamesmaxid\'')) === false)
			die ('There was an error in the update sequence (UPDATE_ERRORID=normalgames-04) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
			
		$retained_row = null;
		while ($row = $this->_dbs->fetch_array($query_dbs_gamesmaxid))
			if ($retained_row === null || $retained_row['entry'] > $row['entry'])
				$retained_row = $row;
				
		if ($retained_row !== null)
			$minimalgamesid = $retained_row['entry'];
		else
			$minimalgamesid = 0;
			
		if ($dbh_gamesmaxid > $minimalgamesid)
		{
		
			if (($query_dbh_dotagames = $this->_dbh->query('
				SELECT 
					`id`, 
					' . ($this->_usebotid ? '`botid`,' : '') . '
					`gameid`
				FROM `dotagames`
				WHERE `gameid` >= ' . $minimalgamesid . '' . ($this->_usebotid ? ' AND `botid` = ' . $this->_botid : ''))) === false)
				die ('There was an error in the update sequence (UPDATE_ERRORID=normalgames-05) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbh->error());
				
			$dotagames = array();
			while ($row = $this->_dbh->fetch_array($query_dbh_dotagames))
				$dotagames[] = $row['gameid'];
				
			if (($query_dbh_gamesplayersnum = $this->_dbh->query('
				SELECT 
					id, 
					' . ($this->_usebotid ? '`botid`,' : '') . '
					gameid, 
					COUNT(id) as playersnum
				FROM `gameplayers` 
				WHERE `gameid` >= ' . $minimalgamesid . '' . ($this->_usebotid ? ' AND `botid` = ' . $this->_botid : '') . '
				GROUP BY `gameid`')) === false)
				die ('There was an error in the update sequence (UPDATE_ERRORID=normalgames-06) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbh->error());
				
			$playersnum = array();
			while ($row = $this->_dbh->fetch_array($query_dbh_gamesplayersnum))
				if (!in_array($row['gameid'], $dotagames) && $row['gameid'] != '')
					$playersnum[$row['gameid']] = $row['playersnum'];
			
			if (($query_dbh_games = $this->_dbh->query('
				SELECT 
					`id`, 
					' . ($this->_usebotid ? '`botid`,' : '') . '
					`map`, 
					`datetime`,
					`gamename`
				FROM `games`
				WHERE `id` >= ' . $minimalgamesid . '' . ($this->_usebotid ? ' AND `botid` = ' . $this->_botid : '') . '
				LIMIT 0, ' . $this->_maxselect)) === false)
				die ('There was an error in the update sequence (UPDATE_ERRORID=normalgames-07) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbh->error());
			
			$j = 0;
			$query_add = '';
			$last_row = false;
			while ($last_row == false)
			{
				if (($row = $this->_dbh->fetch_array($query_dbh_games)) != false)
				{
					$last_id = $row['id'];
					if (!in_array($row['id'], $dotagames))
					{
						$j++;
						$ad = false;
						
						$pattern = '/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/';
						preg_match($pattern, $row['datetime'], $matches);
						
						$new_datetime = mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
						
						if (($query_dbh_durationcalc = $this->_dbh->query('
							SELECT MAX(`left`) as duration
							FROM `gameplayers`
							WHERE `gameid` = ' . $row['id'] . '' . ($this->_usebotid ? ' AND `botid` = ' . $this->_botid : '') . '
							')) === false)
							die ('There was an error in the update sequence (UPDATE_ERRORID=normalgames-08) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbh->error());
							
						$durationcalc = $this->_dbh->fetch_array($query_dbh_durationcalc);
						$duration = $durationcalc['duration'];
						
						$query_add .= '(';
						
						$query_add .= 'null, ';
						$query_add .= $row['id'] . ', ';
						$query_add .= ($this->_usebotid ? $row['botid'] : 0) . ', ';
						$query_add .= $this->_dbs->real_escape_string($this->_dbs_botid) . ', ';
						$query_add .= $this->_dbs->real_escape_string($row['map']) . ', ';
						$query_add .= $this->_dbs->real_escape_string($new_datetime) . ', ';
						$query_add .= $this->_dbs->real_escape_string($row['gamename']) . ', ';
						$query_add .= $duration . ', ';
						$query_add .= $playersnum[$row['id']]; 
						
						$query_add .= ')';
						$query_add .= (($j % $this->_maxinsert) == 0 ? ';' : ',') . "\n";
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
						INSERT INTO `dbs_normalgames` (
							`id`, 
							`ghostid`, 
							`ghostbotid`, 
							`botid`, 
							`map`, 
							`datetime`,
							`gamename`,
							`duration`,
							`playersnum`)
						VALUES
						' . $query_add))
						die ('There was an error in the update sequence (UPDATE_ERRORID=normalgames-09) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());

					$query_add = '';
				}
			}
			
			if (!$this->_dbs->query('
				DELETE FROM `dbs_lastentries`
				WHERE `botid` = ' . $this->_dbs->real_escape_string($this->_dbs_botid) . ' AND `name` = \'normalgames_gamesmaxid\''))
				die ('There was an error in the update sequence (UPDATE_ERRORID=normalgames-10) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
			
			if (!$this->_dbs->query('
				INSERT INTO `dbs_lastentries` (
					`id`,  
					`botid`, 
					`name`,
					`entry`)
				VALUES
				(null, ' . $this->_dbs->real_escape_string($this->_dbs_botid) . ', \'normalgames_gamesmaxid\', ' . ($last_id + 1) . ');'))
				die ('There was an error in the update sequence (UPDATE_ERRORID=normalgames-11) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
				
		}
	}
}

?>