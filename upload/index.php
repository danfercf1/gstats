<?php
date_default_timezone_set('America/La_Paz');
/*

	Manhim's Framework
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

/*****************************************************
***  Configuration part
*****************************************************/

$cfg['allowed_main_modules'] = array();
$cfg['allowed_main_modules'][] = 'gstatspp';
$cfg['output_buffering'] = true;
$cfg['gz_output'] = true;
$cfg['mb_output'] = true;
$cfg['default_module'] = 'gstatspp';
$cfg['default_args'] = array();
$cfg['on_error_module'] = 'gstatspp';


/*****************************************************
***  Edit the rest at your own risks
*****************************************************/

error_reporting(E_ALL & ~E_NOTICE);

$message = 'Wrong configuration in Manhim\'s Framework.';
$pattern = '/([a-zA-Z0-9_-]+)(.*)/';

if (!is_array($cfg['allowed_main_modules'])) exit ($message);
if (!is_array($cfg['default_args'])) exit ($message);
if (!is_bool($cfg['output_buffering'])) exit ($message);
if (!is_bool($cfg['gz_output'])) exit ($message);
if (!is_bool($cfg['mb_output'])) exit ($message);
preg_match($pattern, $cfg['default_module'], $matches); if (!isset($matches[2])) exit ($message);
preg_match($pattern, $cfg['on_error_module'], $matches); if (!isset($matches[2])) exit ($message);
define ('IN_MANHIM_FRAMEWORK', true);


/*****************************************************
***  Use GZ encoding callback using the output buffer
*****************************************************/

// steve at mrclay dot org
function isBuggyIe() {
    $ua = $_SERVER['HTTP_USER_AGENT'];

    if (0 !== strpos($ua, 'Mozilla/4.0 (compatible; MSIE ')
        || false !== strpos($ua, 'Opera')) {
        return false;
    }

    $version = (float)substr($ua, 30); 
    return (
        $version < 6
        || ($version == 6  && false === strpos($ua, 'SV1'))
    );
}

if ($cfg['output_buffering'] === true)
{
	if (!isBuggyIe() || $cfg['gz_output'] === true) ob_start('ob_gzhandler');
	else ob_start();

	if ($cfg['mb_output'] === true)
	{
		mb_http_output("UTF-8");
		ob_start("mb_output_handler");
	}
	else ob_start();
}


/*****************************************************
***  8-Bit Unicode Transformation Format
*****************************************************/

header('Content-type: text/html; charset=utf-8');


/*****************************************************
***  Verify and parse the query string
*****************************************************/

$pattern = '/([a-zA-Z0-9_-]+)(.*)/';
preg_match($pattern, $_SERVER['QUERY_STRING'], $matches);

if (isset($matches[2]))
{
	$pattern = '/([a-zA-Z0-9_-]+)=([a-zA-Z0-9_-]+)/';
	preg_match_all($pattern, $matches[2], $matches2);
}

if (isset($matches[1]))
{
	$module = $matches[1];
	if (isset($matches[2]) && isset($matches2[0]) && count($matches2[0]) > 0)
	{
		$args = array();
		for ($i=0; $i<count($matches2[0]); $i++)
		{
			$args['' . $matches2[1][$i] . ''] = $matches2[2][$i];
		}
	}
	else
		$args = null;
}
else
{
	$module = $cfg['default_module'];
	$args = $cfg['default_args'];
}




/*****************************************************
***  function safe_require
*****************************************************/

function safe_require($file, $status, $args, $cfg, &$flags, $in = null)
{
	require $file;
	
	if (isset($out) && $out !== null)
		return $out;
	else
		return null;
}


/*****************************************************
***  Load the first module and get dependencies
*****************************************************/

if (!in_array($module, $cfg['allowed_main_modules'], true)) 
	$module = $cfg['on_error_module'];

$status = 'FETCHING_DEPENDENCIES';
$dependencies = safe_require ('modules/' . $module . '.inc.php', $status, $args, $cfg, $flags);

if ($flags['is_main_module'] !== true)
	die('The target module is not a main module, therefore Manhim\'s framework can\t open it.');
else
	$modules_flags['' . $module . ''] = $flags;


/*****************************************************
***  Load the dependencies and their dependencies
*****************************************************/

$status = 'FETCHING_DEPENDENCIES';

if (!is_array($dependencies) || $dependencies === null)
	$finished = true;
else
	$finished = false;

$i = 0;
while (!$finished)
{
	$out = safe_require ('modules/' . $dependencies[$i] . '.inc.php', $status, $args, $cfg, $flags);
	$modules_flags['' . $dependencies[$i] . ''] = $flags;
	
	if (is_array($out) && $out !== null)
	{
		for ($j=0; $j<count($out); $j++)
		{
			$dependencies[] = $out[$j];
		}
	}
	
	$i++;
	
	if (count($dependencies) == $i)
		$finished = true;
}
unset($out);


/*****************************************************
***  Run the modules
*****************************************************/

$status = 'RUNNING';

$modules = array();

// Sort the modules inverted from the dependencies list
for ($i=count($dependencies) - 1; $i>=0; $i--)
{
	if (!in_array($dependencies[$i], $modules))
		$modules[] = $dependencies[$i];
}

// Add the main module to the list
$modules[] = $module;

// Run the modules
$in_original = array();

for ($i=0; $i<count($modules); $i++)
{
	$in = null;
	$in = array();
	
	$keys = array_keys($in_original);
	for ($j=0; $j<count($keys); $j++)
	{
		if ($modules_flags['' . $keys[$j] . '']['out_by_reference'] === true)
			$in['' . $keys[$j] . ''] = &$in_original['' . $keys[$j] . ''];
		else if ($modules_flags['' . $keys[$j] . '']['out_by_reference'] === false && is_object($in_original['' . $keys[$j] . '']))
			$in['' . $keys[$j] . ''] = clone $in_original['' . $keys[$j] . ''];
		else if ($modules_flags['' . $keys[$j] . '']['out_by_reference'] === false)
			$in['' . $keys[$j] . ''] = $in_original['' . $keys[$j] . ''];
		else
			die('Wrong out_by_reference value in the module ' . $modules[$i]);
	}
	
	$in_original['' . $modules[$i] . ''] = safe_require ('modules/' . $modules[$i] . '.inc.php', $status, $args, $cfg, $flags, $in);
}


/*****************************************************
***  Run the modules' callbacks
*****************************************************/

$status = 'CALLBACK';

// Run the modules' callbacks (Inverted from the modules list)
for ($i=count($modules) - 1; $i>=0; $i--)
{
	$in = null;
	$in = array();
	
	$keys = array_keys($in_original);
	for ($j=0; $j<count($keys); $j++)
	{
		if ($modules_flags['' . $keys[$j] . '']['out_by_reference'] === true)
			$in['' . $keys[$j] . ''] = &$in_original['' . $keys[$j] . ''];
		else if ($modules_flags['' . $keys[$j] . '']['out_by_reference'] === false && is_object($in_original['' . $keys[$j] . '']))
			$in['' . $keys[$j] . ''] = clone $in_original['' . $keys[$j] . ''];
		else if ($modules_flags['' . $keys[$j] . '']['out_by_reference'] === false)
			$in['' . $keys[$j] . ''] = $in_original['' . $keys[$j] . ''];
		else
			die('Wrong out_by_reference value in the module ' . $modules[$i]);
	}
	
	safe_require ('modules/' . $modules[$i] . '.inc.php', $status, $args, $cfg, $flags, $in);
}

?>