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

error_reporting(~E_ALL);

require_once 'config.inc.php';

if ($_cfg['dbs_mysql']['port'] == 0) $_cfg['dbs_mysql']['port'] == 3306; 

if ($_cfg['dbs_type'] == 'mysql' && $_cfg['dbs_mysql']['mysqli']) $_cfg['dbs_type'] = 'mysqli';
$fType = $_cfg['dbs_type'];
$fCfg = array();

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

$_dbs = require_once 'modules/classes/gstatspp_database.inc.php';

if ($_dbs->connection_error)
	die ('Could not connect to the GStats++ database.');
	
interface iAjaxcalls
{
	function purify($input);
	function __construct(Database &$dbs, $get, $search, $cfg);
	
	function run();
}

abstract class Ajaxcalls
{
	protected $_dbs;
	protected $_get;
	protected $_search;
	protected $_cfg;
	
	protected $_sLimit;
	protected $_sOrder;
	protected $_sWhere;
	
	function purify($input)
	{
		$pattern = array('/;/', '/\'/', '/"/', '/\\{/', '/\\|/', '/\\}/', '/\\[/', '/\\\\/', '/\\]/', '/\\(/', '/\\)/', '/,/', '/\\//');
		$replace = array('&#59;', '&#39;', '&quot;', '&#123;', '&#124;', '&#125;', '&#91;', '&#92;', '&#93;', '&#40;', '&#41;', '&#44;', '&#47;');
		return preg_replace($pattern, $replace, $input);
	}
	
	function __construct(Database &$dbs, $get, $search, $cfg)
	{
		$this->_dbs = &$dbs;
		$this->_get = $get;
		$this->_search = $search;
		$this->_cfg = $cfg;
		
		$this->_sLimit = '';
		if (isset($this->_get['iDisplayStart']) && isset($this->_get['iDisplayLength']))
			$this->_sLimit = 'LIMIT ' . $this->_dbs->rreal_escape_string($this->_get['iDisplayStart']) . ', ' . $this->_dbs->rreal_escape_string($this->_get['iDisplayLength']);
			
		$this->_sOrder = '';
		if (isset($this->_get['iSortCol_0']))
		{
			$this->_sOrder = 'ORDER BY  ';
			
			for ($i=0; $i < $this->_dbs->rreal_escape_string($this->_get['iSortingCols']); $i++ )
			{
				$this->_sOrder .= $this->_search[$this->_dbs->rreal_escape_string($this->_get['iSortCol_' . $i])] . ' ' . $this->_dbs->rreal_escape_string($this->_get['iSortDir_' . $i]) . ', ';
			}
			
			$this->_sOrder = substr_replace( $this->_sOrder, '', -2 );
		}
		
		$this->_sWhere = '';
		if (isset($this->_get['sSearch']) && $this->_dbs->rreal_escape_string($this->_get['sSearch']) != '' && count($this->_search) > 0)
		{
			$keys = array_keys($this->_search);
			
			for ($i=0; $i < count($this->_search); $i++)
			{
				if ($i == 0 && ($this->_get['call'] != 'normalplayerslist' || $keys[$i] != 3))
					$this->_sWhere = 'WHERE ' . $this->_search[$keys[$i]] . ' LIKE \'%' . $this->_dbs->rreal_escape_string($this->_get['sSearch']) . '%\'';
				else if ($this->_get['call'] != 'normalplayerslist' || $keys[$i] != 3)
					$this->_sWhere .= ' OR ' . $this->_search[$keys[$i]] . ' LIKE \'%' . $this->_dbs->rreal_escape_string($this->_get['sSearch']) . '%\'';
			}
		}
	}
}

$valid_names = array();
$valid_names[] = 'admins';
$valid_names[] = 'bans';
$valid_names[] = 'normalgames';
$valid_names[] = 'dotagames';
$valid_names[] = 'playerslist';

if (!in_array($_GET['call'], $valid_names))
	die ('Invalid call');
	
$search = array();
	
switch ($_GET['call'])
{
	case 'admins': 
		require_once 'ajaxcalls/admins.inc.php';
		$search[0] = 'name';
		$search[2] = 'server';
		$_ajaxcalls = new AjaxcallsAdmins($_dbs, $_GET, $search, $_cfg);
		break;
	case 'bans': 
		require_once 'ajaxcalls/bans.inc.php';
		$search[0] = 'name';
		$search[2] = 'server';
		$search[3] = 'date';
		$search[4] = 'reason';
		$_ajaxcalls = new AjaxcallsBans($_dbs, $_GET, $search, $_cfg);
		break;
	case 'normalgames':
		require_once 'ajaxcalls/normalgames.inc.php';
		$search[0] = 'gamename';
		$search[1] = 'playersnum';
		$search[2] = 'duration';
		$search[3] = 'datetime';
		$_ajaxcalls = new AjaxcallsNormalgames($_dbs, $_GET, $search, $_cfg);
		break;
	case 'dotagames':
		require_once 'ajaxcalls/dotagames.inc.php';
		$search[0] = 'gamename';
		$search[1] = 'playersnum';
		$search[2] = 'versus';
		$search[3] = 'duration';
		$search[4] = 'datetime';
		$_ajaxcalls = new AjaxcallsDotagames($_dbs, $_GET, $search, $_cfg);
		break;
	case 'playerslist':
		require_once 'ajaxcalls/playerslist.inc.php';
		$search[0] = '`dbs_players`.`name`';
		$search[2] = '`dbs_players`.`realm`';
		$search[3] = 'gp_count';
		$_ajaxcalls = new AjaxcallsPlayerslist($_dbs, $_GET, $search, $_cfg);
		break;
}

$_ajaxcalls->run();

?>