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

if (IN_MANHIM_FRAMEWORK != true)
	die();

/*

	Module: gstatspp_smarty
	This module load the smarty class and configure it for GStats++

*/

if ($status == 'FETCHING_DEPENDENCIES')
{
	$flags = array();
	$flags['is_main_module'] = false;
	$flags['out_by_reference'] = true;
	
	$out = array();
	$out[] = 'gstatspp_configloader';
}
else if ($status == 'RUNNING')
{
	$_cfg = $in['gstatspp_configloader'];
	
	$smarty = require 'classes/gstatspp_smarty.inc.php';
	
	$smarty->template_dir = './templates/' . $_cfg['template'];
	$smarty->compile_dir = './internal/templates_c';
	$smarty->config_dir = './internal/configs';
	$smarty->cache_dir = './internal/cache';
	$smarty->left_delimiter = '{{';
	$smarty->right_delimiter = '}}';
	
	if ($_cfg['cache']['use_cache'] == true)
	{
		$smarty->caching = 2;
		$smarty->cache_lifetime = $_cfg['cache']['cache_lifetime'];
	}
	else
	{
		$smarty->caching = 0;
	}
	
	$out = $smarty;
}
else if ($status == 'CALLBACK')
{

}
	
?>