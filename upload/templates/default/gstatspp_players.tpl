{{include file='gstatspp_header.tpl'}}

<script type="text/javascript" language="javascript" src="medias/jquery-1.3.2.min.js"></script> 
<script type="text/javascript" language="javascript" src="medias/jquery.dataTables.min.js"></script> 
<script type="text/javascript" language="javascript" src="medias/wz_tooltip.js"></script>
<script type="text/javascript" charset="utf-8"> 
	$(document).ready(function() {
		$("#dataTable tbody, #dataTable2 tbody").click(function(event) {
			var hasTheClass;
			var target;
			
			if ($(event.target).context.toString() != '[object HTMLTableCellElement]')
				if ($(event.target).context.toString() == '[object HTMLImageElement]' || $(event.target).context.toString() == '[object HTMLElement]')
					target = $(event.target.parentNode.parentNode);
				else
					target = false;
			else
				target = $(event.target.parentNode);
				
			if (target != false)
			{
				if (target.hasClass('dataTable_selectedrow'))
					hasTheClass = true;
				else
					hasTheClass = false;
				
				{{if $cfg.show_normal_games}}$(oTable.fnSettings().aoData).each(function (){
					$(this.nTr).removeClass('dataTable_selectedrow');
				});{{/if}}
				
				{{if $cfg.show_dota_games}}$(oTable2.fnSettings().aoData).each(function (){
					$(this.nTr).removeClass('dataTable_selectedrow');
				});{{/if}}
				
				if (!hasTheClass)
					target.addClass('dataTable_selectedrow');
			}
		} );
	
		{{if $cfg.show_normal_games}}oTable = $('#dataTable').dataTable( {
				"bPaginate": true,
				"sPaginationType": "full_numbers",
				"bAutoWidth": false,
				"bLengthChange": false,
				"bInfo": false,
				"iDisplayLength": 10,
				"bProcessing": false,
				"sDom": 'rt<"dataTable_bottom"<"dataTable_search"f><"dataTable_paginate"p>>',
				"aoColumns": [ 
					{ "sClass": "dataTable_firstcolumn" },
					{ "bSortable": false, "sClass": "dataTable_column" },
					{ "sClass": "dataTable_column" },
					{ "sClass": "dataTable_column" },
					{ "sClass": "dataTable_column" },
					{ "bVisible": false },
					{ "bVisible": false },
					{ "bVisible": false }
				],
				"aaSorting": [[4,'asc']],
				"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
				
					var colour;
					switch (aData[5])
					{
						case "0": colour = 'FF0303'; break;
						case "1": colour = '0042FF'; break;
						case "2": colour = '1CB619'; break;
						case "3": colour = '540081'; break;
						case "4": colour = 'CCCC01'; break; //FFFF01
						case "5": colour = 'FE8A0E'; break;
						case "6": colour = '20C000'; break;
						case "7": colour = 'E55BB0'; break;
						case "8": colour = '959697'; break;
						case "9": colour = '7EBFF1'; break;
						case "10": colour = '106246'; break;
						case "11": colour = '4E2A04'; break;
					}
					
					var status;
					switch (aData[1])
					{
						case "0": status = 'status_admin.png'; break;
						case "1": status = 'status_vip.png'; break;
						case "2": status = 'status_active.png'; break;
						case "3": status = 'status_spoofer.png'; break;
						case "4": status = 'status_banned.png'; break;
					}
					
					var perc;
					var intdata = parseInt(aData[7]);
					if (intdata <= 60)
						perc = 'red; font-weight: bold;';
					else if (intdata <= 90)
						perc = 'orange; font-weight: bold;';
					else
						perc = 'green';
				
					$('td:eq(0)', nRow).html('<a href="?gstatspp;page=normalgameplayers&gameid=' + aData[6] + '" style="color: #' + colour + '">' + aData[0] + '</a>');
					
					$('td:eq(1)', nRow).html('<img src="medias/default/' + status + '" />');
					
					$('td:eq(3)', nRow).html('<span style="color: ' + perc + '">' + toTimeHours(aData[3]) + ' (' + aData[7] + '%)</span>');
					
					var d = new Date(aData[4] * 1000);
					$('td:eq(4)', nRow).html( padLeft(d.getFullYear(), 4, "0") + '/' + padLeft((d.getMonth() + 1), 2, "0") + '/' + padLeft(d.getDate(), 2, "0") 
						+ ' ' + padLeft(d.getHours(), 2, "0") + ':' + padLeft(d.getMinutes(), 2, "0") + ':' + padLeft(d.getSeconds(), 2, "0") );
					
					return nRow;
				},
				"oLanguage": {
					"oPaginate": {
						"sFirst": '{{$lang.General.First}}',
						"sPrevious": '{{$lang.General.Previous}}',
						"sNext": '{{$lang.General.Next}}',
						"sLast": '{{$lang.General.Last}}'
					},
					"sInfo": '{{$lang.General.Info}}',
					"sInfoFiltered": '{{$lang.General.InfoFiltered}}',
					"sInfoEmpty": '{{$lang.General.InfoEmpty}}',
					"sSearch": '{{$lang.General.Search}}',
					"sZeroRecords": '<div class="dataTable_norecords">{{$lang.General.NoMatchingRecordsFound}}</div>'
				}
		} );{{/if}}
		
		{{if $cfg.show_dota_games}}oTable2 = $('#dataTable2').dataTable( {
				"bPaginate": true,
				"sPaginationType": "full_numbers",
				"bAutoWidth": false,
				"bLengthChange": false,
				"bInfo": false,
				"iDisplayLength": 10,
				"bProcessing": false,
				"sDom": 'rt<"dataTable_bottom"<"dataTable_search"f><"dataTable_paginate"p>>',
				"aoColumns": [ 
					{ "sClass": "dataTable_firstcolumn" },
					{ "bSortable": false, "sClass": "dataTable_column" },
					{ "sClass": "dataTable_column" },
					{ "sClass": "dataTable_column" },
					{ "sClass": "dataTable_column" },
					{ "sClass": "dataTable_column" },
					{ "sClass": "dataTable_column" },
					{ "sClass": "dataTable_column" },
					{ "sClass": "dataTable_column" },
					{ "sClass": "dataTable_column" },
					{ "sClass": "dataTable_column" },
					{ "bVisible": false },
					{ "bVisible": false },
					{ "bVisible": false }
				],
				"aaSorting": [[8,'asc']],
				"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
				
					var colour;
					switch (aData[11])
					{
						case "0": colour = 'FF0303'; break;
						case "1": colour = '0042FF'; break;
						case "2": colour = '1CB619'; break;
						case "3": colour = '540081'; break;
						case "4": colour = 'CCCC01'; break; //FFFF01
						case "5": colour = 'FE8A0E'; break;
						case "6": colour = '20C000'; break;
						case "7": colour = 'E55BB0'; break;
						case "8": colour = '959697'; break;
						case "9": colour = '7EBFF1'; break;
						case "10": colour = '106246'; break;
						case "11": colour = '4E2A04'; break;
					}
					
					var status;
					switch (aData[1])
					{
						case "0": status = 'status_admin.png'; break;
						case "1": status = 'status_vip.png'; break;
						case "2": status = 'status_active.png'; break;
						case "3": status = 'status_spoofer.png'; break;
						case "4": status = 'status_banned.png'; break;
					}
					
					var perc;
					var intdata = parseInt(aData[13]);
					if (intdata <= 60)
						perc = 'red; font-weight: bold;';
					else if (intdata <= 90)
						perc = 'orange; font-weight: bold;';
					else
						perc = 'green';
				
					$('td:eq(0)', nRow).html('<a href="?gstatspp;page=dotagameplayers&gameid=' + aData[12] + '" style="color: #' + colour + '">' + aData[0] + '</a>');
					
					$('td:eq(1)', nRow).html('<img src="medias/default/' + status + '" />');
					
					$('td:eq(3)', nRow).html('<span style="color: ' + perc + '">' + toTimeHours(aData[3]) + ' (' + aData[13] + '%)</span>');
					
					if (aData[9] == 0)
						$('td:eq(9)', nRow).html('<span onmouseover="Tip(\'{{$lang.Players.UndLong}}\')" onmouseout="UnTip()">{{$lang.Players.Und}}</span>');
					else if ((aData[9] == 1 && aData[11] >= 1 && aData[11] <= 5) || (aData[9] == 2 && aData[11] >= 7 && aData[11] <= 11))
						$('td:eq(9)', nRow).html('<span style="color: green; font-weight: bold;">{{$lang.Players.Won}}</span>');
					else
						$('td:eq(9)', nRow).html('<span style="color: red; font-weight: bold;">{{$lang.Players.Lost}}</span>');
					
					var d = new Date(aData[10] * 1000);
					$('td:eq(10)', nRow).html( padLeft(d.getFullYear(), 4, "0") + '/' + padLeft((d.getMonth() + 1), 2, "0") + '/' + padLeft(d.getDate(), 2, "0") 
						+ ' ' + padLeft(d.getHours(), 2, "0") + ':' + padLeft(d.getMinutes(), 2, "0") + ':' + padLeft(d.getSeconds(), 2, "0") );
					
					return nRow;
				},
				"oLanguage": {
					"oPaginate": {
						"sFirst": '{{$lang.General.First}}',
						"sPrevious": '{{$lang.General.Previous}}',
						"sNext": '{{$lang.General.Next}}',
						"sLast": '{{$lang.General.Last}}'
					},
					"sInfo": '{{$lang.General.Info}}',
					"sInfoFiltered": '{{$lang.General.InfoFiltered}}',
					"sInfoEmpty": '{{$lang.General.InfoEmpty}}',
					"sSearch": '{{$lang.General.Search}}',
					"sZeroRecords": '<div class="dataTable_norecords">{{$lang.General.NoMatchingRecordsFound}}</div>'
				}
		} );{{/if}}
	} );
</script> 

<div class="fullcontent">
<br />
	<div style="margin: 0; padding: 10px; background: #f5f5f5; border-top: 1px #b6b6b6 solid; border-bottom: 1px #b6b6b6 solid;">
		<div style="margin: 0; padding-top: 9px; margin-bottom: 20px;">
			<center><h2 style="padding-bottom: 0; margin-bottom: 0;">{{$player.name}}</h2> @ {{$player.realm}}</center>
		</div>
		<div style="height: 28px; margin: 0; padding-top: 9px;">
			{{$s_lang.XHaveAnAverageLoadingTimeOfX}}
		</div>
		<div style="height: 28px; margin: 0; padding-top: 9px;">
			{{if $player.gout > 0 && $player.lout > 0}}
				{{$s_lang.HeGayedOutOfXGamesAndLeftATotalOfXGamesBeforeTheEnd}}
			{{elseif $player.gout == 0 && $player.lout > 0}}
				{{$s_lang.HeLeftXGamesBeforeTheEnd}}
			{{else}}
				{{$s_lang.HeNeverLeftBeforeTheEndOfAGame}}
			{{/if}}
		</div>
		<div style="height: 28px; margin: 0; padding-top: 9px;">
			{{$s_lang.HePlayedATotalTimeOfX}}
		</div>
	</div>
</div>

{{if $cfg.show_normal_games}}<br style="clear: both" />
<br />

<div class="fullcontent">
<p style="color: #000; font-size: 40px; font-weight: bold; font-family: Calibri; margin: 15px; margin-bottom: 10px; margin-left: 25px;">{{$lang.Players.NormalGames}}</p>
<table cellpadding="0" cellspacing="0" border="0" class="display2" id="dataTable" width="100%"> 
	<thead> 
		<tr> 
			<th width="30%" height="42">{{$lang.Players.GameName}}</th> 
			<th width="10%">{{$lang.Players.Status}}</th> 
			<th width="15%">{{$lang.Players.NumberOfPlayers}}</th> 
			<th width="20%">{{$lang.Players.LeftOP}}</th>
			<th width="25%">{{$lang.Players.EndDateTime}}</th> 
			<th></th>
			<th></th>
			<th></th>
		</tr> 
	</thead> 
	<tbody> 
{{foreach from=$pgames item=pgame}}
		<tr> 
			<td>{{$pgame.gamename}}</td>
			<td>{{$pgame.status}}</td>
			<td>{{$pgame.playersnum}}</td>
			<td>{{$pgame.left}}</td>
			<td>{{$pgame.datetime}}</td>
			<td>{{$pgame.colour}}</td>
			<td>{{$pgame.gameid}}</td>
			<td>{{$pgame.leftp}}</td>
		</tr> 
{{/foreach}}
	</tbody> 
</table> 
</div>{{/if}}

{{if $cfg.show_dota_games}}<br style="clear: both" />
<br />
<br />

<center><img src="medias/default/dota-logo.png" /></center>

<div class="fullcontent">
	<div style="margin: 0; padding: 10px; width: 1009px; background: #f5f5f5; border-top: 1px #b6b6b6 solid; border-bottom: 1px #b6b6b6 solid;">
		<div style="height: 22px; margin: 0; padding-top: 5px;">
			{{$s_lang.AverageKillsPerGamesX}}
		</div>
		<div style="height: 22px; margin: 0; padding-top: 5px;">
			{{$s_lang.AverageDeathsPerGamesX}}
		</div>
		<div style="height: 22px; margin: 0; padding-top: 5px;">
			{{$s_lang.AverageAssistsPerGamesX}}
		</div>
		<div style="height: 22px; margin: 0; padding-top: 5px;">
			{{$s_lang.GlobalKillsDeathsRatio}}
		</div>
		<div style="height: 22px; margin: 0; padding-top: 5px;">
			{{$s_lang.GamesWinLost}} {{$s_lang.GamesIgnored}}
		</div>
	</div>
</div>

<br style="clear: both" />
		
<div class="fullcontent">
<table cellpadding="0" cellspacing="0" border="0" class="display2" id="dataTable2" width="100%"> 
	<thead> 
		<tr> 
			<th width="27%" height="42">{{$lang.Players.GameName}}</th> 
			<th width="10%">{{$lang.Players.Status}}</th> 
			<th width="5%">{{$lang.Players.Match}}</th> 
			<th width="13%">{{$lang.Players.LeftOP}}</th>
			<th width="5%">{{$lang.Players.Hero}}</th>
			<th width="5%" onmouseover="Tip('{{$lang.Players.Kills}}')" onmouseout="UnTip()">{{$lang.Players.K}}</th>
			<th width="5%" onmouseover="Tip('{{$lang.Players.Deaths}}')" onmouseout="UnTip()">{{$lang.Players.D}}</th>
			<th width="5%" onmouseover="Tip('{{$lang.Players.KillsDeathsRatio}}')" onmouseout="UnTip()">{{$lang.Players.KD}}</th>
			<th width="5%" onmouseover="Tip('{{$lang.Players.Assists}}')" onmouseout="UnTip()">{{$lang.Players.A}}</th>
			<th width="5%">{{$lang.Players.Game}}</th>
			<th width="15%">{{$lang.Players.EndDateTime}}</th> 
			<th></th>
			<th></th>
			<th></th>
		</tr> 
	</thead> 
	<tbody> 
{{foreach from=$pdotas item=pdota}}
		<tr> 
			<td>{{$pdota.gamename}}</td>
			<td>{{$pdota.status}}</td>
			<td>{{$pdota.match}}</td>
			<td>{{$pdota.left}}</td>
			<td><img src="dota/heroes/{{$pdota.hero_image}}" onmouseover="Tip('{{if isset($pdota.items.1.image)}}<img src=\'dota/items/{{$pdota.items.1.image}}\' width=\'32\' height=\'32\' /> {{/if}}{{$pdota.items.1.name}}<br />{{if isset($pdota.items.2.image)}}<img src=\'dota/items/{{$pdota.items.2.image}}\' width=\'32\' height=\'32\' /> {{/if}}{{$pdota.items.2.name}}<br />{{if isset($pdota.items.3.image)}}<img src=\'dota/items/{{$pdota.items.3.image}}\' width=\'32\' height=\'32\' /> {{/if}}{{$pdota.items.3.name}}<br />{{if isset($pdota.items.4.image)}}<img src=\'dota/items/{{$pdota.items.4.image}}\' width=\'32\' height=\'32\' /> {{/if}}{{$pdota.items.4.name}}<br />{{if isset($pdota.items.5.image)}}<img src=\'dota/items/{{$pdota.items.5.image}}\' width=\'32\' height=\'32\' /> {{/if}}{{$pdota.items.5.name}}<br />{{if isset($pdota.items.6.image)}}<img src=\'dota/items/{{$pdota.items.6.image}}\' width=\'32\' height=\'32\' /> {{/if}}{{$pdota.items.6.name}}', TITLE, '{{$pdota.hero_name}}', CLICKCLOSE, true, CLOSEBTN, true, FOLLOWMOUSE, false, STICKY, true)" onmouseout="UnTip" /></td>
			<td>{{$pdota.kills}}</td>
			<td>{{$pdota.deaths}}</td>
			<td>{{$pdota.kd}}</td>
			<td>{{$pdota.assists}}</td>
			<td>{{$pdota.winner}}</td>
			<td>{{$pdota.datetime}}</td>
			<td>{{$pdota.colour}}</td>
			<td>{{$pdota.gameid}}</td>
			<td>{{$pdota.leftp}}</td>
		</tr> 
{{/foreach}}
	</tbody> 
</table> 
</div>{{/if}}

<div class="dataTable_row" style="display: none;"></div>
<div class="dataTable_selectedrow" style="display: none;"></div>

{{include file='gstatspp_footer.tpl'}}