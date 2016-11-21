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

class UpdaterBans extends Updater implements iUpdater
{
	function update()
	{
		if ($this->_use_set_time_limit == true)
		{
			set_time_limit(0);
		}
		
		if (!$this->_dbs->query('
			DELETE FROM `dbs_lastupdates`
			WHERE `name` = \'bans\''))
			die ('There was an error in the update sequence (UPDATE_ERRORID=bans-01) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
		
		if (!$this->_dbs->query('
			INSERT INTO `dbs_lastupdates`
			SET
				`id` = null,
				`name` = \'bans\',
				`time` = ' . time() . '
			'))
			die ('There was an error in the update sequence (UPDATE_ERRORID=bans-02) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
			
		if (($query_dbh_bans = $this->_dbh->query('
			SELECT 
				`id`, 
				' . ($this->_usebotid ? '`botid`,' : '') . '
				`server`, 
				`name`, 
				`date`, 
				`gamename`, 
				`admin`, 
				`reason`
			FROM `bans`
			' . ($this->_usebotid ? 'WHERE `botid` = ' . $this->_botid : ''))) === false)
			die ('There was an error in the update sequence (UPDATE_ERRORID=bans-03) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbh->error());
			
		/*if (!$this->_dbs->query('
			TRUNCATE TABLE `dbs_bans`'))
			die ('There was an error in the update sequence (UPDATE_ERRORID=bans-04) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());*/
		
		$j = 0;
		$query_add = '';
		$last_row = false;
		while ($last_row == false)
		{
			if (($row = $this->_dbh->fetch_array($query_dbh_bans)) != false)
			{
				$j++;
				
				$date_e = explode(' ', $row['date']);
				$date = $date_e[0];
				
				$pattern = '/([0-9]{4})-([0-9]{2})-([0-9]{2})/';
				preg_match($pattern, $date, $matches);
						
				$new_date = mktime(0, 0, 0, $matches[2], $matches[3], $matches[1]);
				
				$query_add .= '(';
				
				$query_add .= 'null, ';
				$query_add .= $row['id'] . ', ';
				$query_add .= ($this->_usebotid ? $row['botid'] : 0) . ', ';
				$query_add .= $this->_dbs->real_escape_string($this->_dbs_botid) . ', ';
				$query_add .= $this->_dbs->real_escape_string($row['name']) . ', ';
				$query_add .= $this->_dbs->real_escape_string($row['server']) . ', ';
				$query_add .= '' . $new_date . ', ';
				$query_add .= $this->_dbs->real_escape_string($row['gamename']) . ', ';
				$query_add .= $this->_dbs->real_escape_string($row['admin']) . ', ';
				$query_add .= $this->_dbs->real_escape_string($row['reason']);
				
				$query_add .= ')';
				$query_add .= (($j % $this->_maxinsert) == 0 ? ';' : ',') . "\n";	
				
			}
			else
				$last_row = true;
			
			if ((($j % $this->_maxinsert) == 0 || $last_row == true) && $j > 0)
			{
				if ($last_row == true)
				{
					$query_add_split = str_split(trim($query_add), strlen($query_add) - 2);
					$query_add = $query_add_split[0];
					$query_add .= ';';
				}
				
				if (!$this->_dbs->query('
					INSERT INTO `dbs_bans` (
						`id`, 
						`ghostid`, 
						`ghostbotid`, 
						`botid`, 
						`name`,
						`server`,
						`date`,
						`gamename`,
						`admin`,
						`reason`)
					VALUES
					' . $query_add))
					die ('There was an error in the update sequence (UPDATE_ERRORID=bans-05) (' . $this->_dbs_botid . ')<br />Error: ' . $this->_dbs->error());
					
				$query_add = '';
			}
		}
		
		return true;
	}
}

?>