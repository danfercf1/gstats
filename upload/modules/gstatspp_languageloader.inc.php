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

	Module: gstatspp_languageloader
	This module load the language file for GStats++

*/

if ($status == 'FETCHING_DEPENDENCIES')
{
	$flags = array();
	$flags['is_main_module'] = false;
	$flags['out_by_reference'] = false;
	
	$out = array();
	$out[] = 'gstatspp_configloader';
}
else if ($status == 'RUNNING')
{
	$_cfg = $in['gstatspp_configloader'];
	require 'languages/gstatspp_' . $_cfg['language'] . '.inc.php';
	$out = $_lang;
}
else if ($status == 'CALLBACK')
{

}

?>