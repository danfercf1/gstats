<?php
include 'config.inc.php';

$filename = '/' . trim(basename(rawurldecode($_GET['replay'])));

if (file_exists(realpath($_cfg['replays_folder']) . $filename))
{
	if(ini_get('zlib.output_compression')) 
		ini_set('zlib.output_compression', 'Off'); 
	header('Content-Type: application/octet-stream');
	header("Pragma: public"); 
	header("Expires: 0"); 
	header("Cache-Control: must-revalidate, post-check=0, pre-check=0"); 
	header("Cache-Control: private",false); 
	header("Content-Disposition: attachment; filename=\"".basename($filename)."\";"); 
	header("Content-Transfer-Encoding: binary"); 
	header("Content-Length: ".@filesize(realpath($_cfg['replays_folder']) . $filename)); 
	set_time_limit(0); 
	@readfile(realpath($_cfg['replays_folder']) . $filename) or die("File not found."); 
}
else
{
	die ('File not found.');
}
?>