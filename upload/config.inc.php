<?php

/*

	This is the configuration file for GStats++
	Please read the comments before editing the values.
	
	GStats++ configuration file for version '1.1 Alpha'

*/

$_cfg = array(

	/*****************************************************
	***  GHost++ database configuration
	*****************************************************/
	/* This is your GHost++ database configuration      */
	/* Please read the README file for more informations*/

	// This is your GHost++ database type
	// It can be one of the following:
	//   - mysql
	//   - sqlite3
	
	'ghostdbs' => array(
	
		array(
	
			'alias' => 'Main',					// THE ALIAS NEEDS TO BE UNIQUE
			'use' => true,						// Set to true to use this database
			'dbh_type' => 'mysql',			// The database type to use
			
			/*** mysql ***/
			'dbh_mysql' => array(
			
				'mysqli' => true,  				// Set to true to use MySQLi if your web server supports it.
				'host' => 'localhost', 			// Your GHost++ MySQL database hostname or IP
				'port' => 3306, 					// Your GHost++ MySQL database port (Put 0 for default)
				'username' => 'root',			// Your GHost++ MySQL database username
				'password' => 'mysql',				// Your GHost++ MySQL database password
				'database' => 'ghost',			// Your GHost++ MySQL database name
				'botid' => 1,					// The BotID to use
				'select_limit' => 2000,			// The limit of selected rows for the updater
				
			),
			
			/*** sqlite3 ***/
			/*'dbh_sqlite3' => array(
			
				'filepath' => './ghost.dbs',	// The path to your sqlite3 database
				'select_limit' => 2000,			// The limit of selected rows for the updater
			
			),*/
		
		),
		
		array(
		
			'alias' => 'Secondary',
			'use' => false,
			'dbh_type' => 'sqlite3',

			'dbh_mysql' => array(
			
				'mysqli' => true,
				'host' => 'localhost',
				'port' => 3306,
				'username' => 'root',
				'password' => 'mysql',
				'database' => 'ghost',
				'botid' => 1,
				'select_limit' => 2000,
				
			),

			/*'dbh_sqlite3' => array(
			
				'filepath' => './ghost2.dbs',
				'select_limit' => 2000,
			
			),*/
		
		),
	
	),
	
	/*****************************************************
	***  GStats++ database configuration
	*****************************************************/
	/* This is your GStats++ database configuration      */
	/* Please read the README file for more informations*/

	// This is your GStats++ database type
	// It can be one of the following:
	//   - mysql
	'dbs_type' => 'mysql',
	
	/*** mysql ***/
	'dbs_mysql' => array(
	
		'mysqli' => true,  				// Set to true to use MySQLi if your web server supports it.
		'host' => 'localhost', 			// Your GHost++ MySQL database hostname or IP
		'port' => 3306, 					// Your GHost++ MySQL database port (Put 0 for default)
		'username' => 'root',			// Your GHost++ MySQL database username
		'password' => 'mysql',				// Your GHost++ MySQL database password
		'database' => 'gstatspp',		// Your GHost++ MySQL database name
		'max_inserts' => 500,			// The maximum number of rows to insert in the database in a single query
		'optimize_tables' => true,		// Optimize the tables after updates
	
	),
	
	/*****************************************************
	***  GStats++ general configuration
	*****************************************************/
	'language' => 'english',			// The language file to include
	'template' => 'default',			// The templates folder to use
	'use_set_time_limit' => true,		// Allow or disallow the use of the set_time_limit() function it's recommended to leave it as true on servers that supports it
	'use_replays' => true,				// Show or hide replays
	'replays_folder' => 'replays/',		// Full or relative path to your replays
	'show_normal_games' => true,		// Set to true to show the normal games
	'show_dota_games' => true,			// Set to true to shoe the DOTA games
	
	/*****************************************************
	***  GStats++ cache configuration
	*****************************************************/
	'cache' => array(
	
		'use_cache' => true,			// Set to false to disable the cache
		'cache_lifetime' => 0,			// The lifetime of the cache files in seconds
	
	),
	
	/*****************************************************
	***  GStats++ updater module configuration
	*****************************************************/
	'updater' => array(
	
		'updaterate' => 0,				// At what rate should the database be updated (minimaly)
		'updaterate_stats' => 30,		// At what rate should the global statistics be updated (A value greater then db_updaterate and divisible by db_updaterate is suggested)
	
	),
);

?>