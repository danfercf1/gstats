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

if (IN_MANHIM_FRAMEWORK !== true)
	die();

/*

	Module: gstatspp
	This module shows the pages for GStats++

*/

if ($status == 'FETCHING_DEPENDENCIES')
{
	$flags = array();
	$flags['is_main_module'] = true;
	$flags['out_by_reference'] = false;
	
	$out = array();
	$out[] = 'gstatspp_smarty';
	$out[] = 'gstatspp_languageloader';
	$out[] = 'gstatspp_updater';
	$out[] = 'gstatspp_dbs';
	$out[] = 'gstatspp_configloader';
}
else if ($status == 'RUNNING')
{
	$_tpl = &$in['gstatspp_smarty'];
	$_dbs = &$in['gstatspp_dbs'];
	$_cfg = $in['gstatspp_configloader'];
	$_lang = $in['gstatspp_languageloader'];
	
	$valid_pages = array();
	$valid_pages[] = 'index';
	$valid_pages[] = 'admins';
	$valid_pages[] = 'bans';
	$valid_pages[] = 'normalgames';
	$valid_pages[] = 'normalgameplayers';
	$valid_pages[] = 'players';
	$valid_pages[] = 'playerslist';
	$valid_pages[] = 'dotagames';
	$valid_pages[] = 'dotagameplayers';
	
	if ($args['page'] == '' || !isset($args['page']))
		$page = 'index';
	else
		$page = $args['page'];
	
	if (!in_array($page, $valid_pages))
		$page = 'index';
		
	require_once 'interfaces/gstatspp.inc.php';
	
	switch ($page)
	{
		case 'index': 
			require_once 'interfaces/gstatspp/index.inc.php';
			$title = $_lang['Title']['Index'];
			$_page = new PageIndex($_tpl, $_dbs, $args, $_cfg, $_lang);
			break;
		case 'admins':
			require_once 'interfaces/gstatspp/admins.inc.php';
			$title = $_lang['Title']['Admins'];
			$_page = new PageAdmins($_tpl, $_dbs, $args, $_cfg, $_lang);
			break;
		case 'bans':
			require_once 'interfaces/gstatspp/bans.inc.php';
			$title = $_lang['Title']['Bans'];
			$_page = new PageBans($_tpl, $_dbs, $args, $_cfg, $_lang);
			break;
		case 'normalgames':
			require_once 'interfaces/gstatspp/normalgames.inc.php';
			$title = $_lang['Title']['NormalGames'];
			$_page = new PageNormalgames($_tpl, $_dbs, $args, $_cfg, $_lang);
			break;
		case 'normalgameplayers':
			require_once 'interfaces/gstatspp/normalgameplayers.inc.php';
			$title = $_lang['Title']['NormalGamePlayers'];
			$_page = new PageNormalgameplayers($_tpl, $_dbs, $args, $_cfg, $_lang);
			break;
		case 'dotagames':
			require_once 'interfaces/gstatspp/dotagames.inc.php';
			$title = $_lang['Title']['DOTAGames'];
			$_page = new PageDotagames($_tpl, $_dbs, $args, $_cfg, $_lang);
			break;
		case 'dotagameplayers':
			require_once 'interfaces/gstatspp/dotagameplayers.inc.php';
			$title = $_lang['Title']['DOTAGamePlayers'];
			$_page = new PageDotagameplayers($_tpl, $_dbs, $args, $_cfg, $_lang);
			break;
		case 'players':
			require_once 'interfaces/gstatspp/players.inc.php';
			$title = $_lang['Title']['Players'];
			$_page = new PagePlayers($_tpl, $_dbs, $args, $_cfg, $_lang);
			break;
		case 'playerslist':
			require_once 'interfaces/gstatspp/playerslist.inc.php';
			$title = $_lang['Title']['PlayersList'];
			$_page = new PagePlayerslist($_tpl, $_dbs, $args, $_cfg, $_lang);
			break;
	}
	
	$_tpl->assign('lang', $_lang);
	$_tpl->assign('page', $page);
	$_tpl->assign('cfg', $_cfg);
	$_tpl->assign('title', 'GStats++ : ' . $title);
	
	$_page->run();
	
}
else if ($status == 'CALLBACK')
{

}
	
?>