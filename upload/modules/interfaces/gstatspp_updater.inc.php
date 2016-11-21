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

interface iUpdater
{
	function __construct(Database &$dbh, Database &$dbs, $botid, $usebotid, $maxinsert, $maxselect, $use_set_time_limit, $dbs_botid);
	
	function update();
}

abstract class Updater
{
	protected $_dbh;
	protected $_dbs;
	protected $_botid;
	protected $_usebotid;
	protected $_maxinsert;
	protected $_maxselect;
	protected $_use_set_time_limit;
	protected $_dbs_botid;
	
	function __construct(Database &$dbh, Database &$dbs, $botid, $usebotid, $maxinsert, $maxselect, $use_set_time_limit, $dbs_botid)
	{
		$this->_dbh = &$dbh;
		$this->_dbs = &$dbs;
		$this->_botid = $botid;
		$this->_usebotid = $usebotid;
		$this->_maxinsert = $maxinsert;
		$this->_maxselect = $maxselect;
		$this->_use_set_time_limit = $use_set_time_limit;
		$this->_dbs_botid = $dbs_botid;
	}
}

?>