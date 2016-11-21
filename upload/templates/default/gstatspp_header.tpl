<?xml version="1.1" encoding="UTF-8"?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" 
	"http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

<head>

	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

	<title>{{$title}}</title>
	
	<script type="text/javascript" language="javascript" src="medias/utils.js"></script> 
	
	<style type="text/css">
	<!--
		@import url('medias/default/style.css');
	-->
	</style>
	
	<map name="footer">
		<area href="http://code.google.com/p/gstatspp" alt="GStats Google Code Site" title="GStats++ Google Code Site"
			shape="rect" coords="6,6,107,61" />
		<!-- <area href="http://www.engel-studios.com/" alt="Engel Studios" title="Engel Studios"
			shape="rect" coords="845,6,1033,61" /> -->
	</map>
	
</head>

<body>

<div class="container">

	<div class="header">
	
		<div class="header_top">
			<img src="medias/default/header.png" />
		</div>
		
		<div class="header_menu">
			<div class="header_menu_container">
		
				<table class="header_menu" cellspacing="0" cellpadding="0">
					<tr>
					
						<td class="header_menufirst{{if $page == 'index'}}_selected{{/if}}" width="{{if $cfg.show_normal_games && $cfg.show_dota_games}}17{{elseif ($cfg.show_normal_games && !$cfg.show_dota_games) || ($cfg.show_dota_games && !$cfg.show_normal_games)}}20{{else}}34{{/if}}%"><a href="?gstatspp;page=index"><div></div>{{$lang.Menu.Index}}</a></td>
						<td class="header_menu{{if $page == 'admins'}}_selected{{/if}}" width="{{if $cfg.show_normal_games && $cfg.show_dota_games}}16{{elseif ($cfg.show_normal_games && !$cfg.show_dota_games) || ($cfg.show_dota_games && !$cfg.show_normal_games)}}20{{else}}33{{/if}}%"><a href="?gstatspp;page=admins"><div></div>{{$lang.Menu.Admins}}</a></td>
						<td class="header_menu{{if $page == 'bans'}}_selected{{/if}}" width="{{if $cfg.show_normal_games && $cfg.show_dota_games}}16{{elseif ($cfg.show_normal_games && !$cfg.show_dota_games) || ($cfg.show_dota_games && !$cfg.show_normal_games)}}20{{else}}33{{/if}}%"><a href="?gstatspp;page=bans"><div></div>{{$lang.Menu.Bans}}</a></td>
						{{if $cfg.show_normal_games}}<td class="header_menu{{if $page == 'normalgames' || $page == 'normalgameplayers'}}_selected{{/if}}" width="{{if $cfg.show_dota_games}}16{{else}}20{{/if}}%"><a href="?gstatspp;page=normalgames"><div></div>{{$lang.Menu.NormalGames}}</a></td>{{/if}}
						{{if $cfg.show_dota_games}}<td class="header_menu{{if $page == 'dotagames' || $page == 'dotagameplayers'}}_selected{{/if}}" width="{{if $cfg.show_normal_games}}16{{else}}20{{/if}}%"><a href="?gstatspp;page=dotagames"><div></div>{{$lang.Menu.DOTAGames}}</a></td>{{/if}}
						{{if $cfg.show_normal_games || $cfg.show_dota_games}}<td class="header_menu{{if $page == 'playerslist' || $page == 'players'}}last_selected{{/if}}" width="{{if $cfg.show_normal_games && $cfg.show_dota_games}}16{{else}}20{{/if}}%"><a href="?gstatspp;page=playerslist"><div></div>{{$lang.Menu.PlayersList}}</a></td>{{/if}}
					</tr>
				</table>
				
			</div>
		</div>
	
	</div>
	
	<div class="base" style="width: 1039px;">
	<div class="base2">
	<div class="base3">