{{include file='gstatspp_header.tpl'}}

<script type="text/javascript" language="javascript" src="medias/jquery-1.3.2.min.js"></script> 
<script type="text/javascript" language="javascript" src="medias/jquery.dataTables.min.js"></script> 
<script type="text/javascript" language="javascript" src="medias/wz_tooltip.js"></script>
<script type="text/javascript" charset="utf-8"> 
	$(document).ready(function() {
		$('#showhide').fadeOut();
		$('#preshowhide').fadeOut();
		
		var show = 'hide';
		$('#showhidelink').click(function() {
			if (show == 'hide')
			{
				$('#preshowhide').fadeIn(1000, function() {
					setTimeout(function() {
						$('#preshowhide').fadeOut(1000);
						setTimeout(function() {
							$('#showhide').fadeIn(2500);
						}, 1000);
					}, 2500);
				} );
				show = 'show';
			}
			else
			{
				$('#showhide').fadeOut('fast');
				show = 'hide';
			}
			return false;
		} );
	
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
				
				$(oTable.fnSettings().aoData).each(function (){
					$(this.nTr).removeClass('dataTable_selectedrow');
				});
				
				$(oTable2.fnSettings().aoData).each(function (){
					$(this.nTr).removeClass('dataTable_selectedrow');
				});
				
				if (!hasTheClass)
					target.addClass('dataTable_selectedrow');
			}
		} );

		oTable = $('#dataTable').dataTable( {
				"bPaginate": false,
				"bAutoWidth": false,
				"bLengthChange": false,
				"bInfo": false,
				"iDisplayLength": 12,
				"bProcessing": false,
				"sDom": 'rt',
				"aoColumns": [ 
					{ "sClass": "dataTable_firstcolumn" },
					{ "bSortable": false, "sClass": "dataTable_column" },
					{ "sClass": "dataTable_column" },
					{ "sClass": "dataTable_column" },
					{ "bSearchable": false, "sClass": "dataTable_column" },
					{ "bSearchable": false, "sClass": "dataTable_column" },
					{ "bSearchable": false, "sClass": "dataTable_column" },
					{ "bSearchable": false, "sClass": "dataTable_column" },
					{ "bSearchable": false, "sClass": "dataTable_column" },
					{ "bSearchable": false, "sClass": "dataTable_column" },
					{ "bSearchable": false, "sClass": "dataTable_column" },
					{ "bVisible": false },
					{ "bVisible": false },
					{ "bVisible": false }
				],
				"aaSorting": [[11,'asc']],
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
					
					
					if (aData[13] != -1)
					{
						var ncolour;
						switch (aData[13])
						{
							case "0": ncolour = 'FF0303'; break;
							case "1": ncolour = '0042FF'; break;
							case "2": ncolour = '1CB619'; break;
							case "3": ncolour = '540081'; break;
							case "4": ncolour = 'CCCC01'; break; //FFFF01
							case "5": ncolour = 'FE8A0E'; break;
							case "6": ncolour = '20C000'; break;
							case "7": ncolour = 'E55BB0'; break;
							case "8": ncolour = '959697'; break;
							case "9": ncolour = '7EBFF1'; break;
							case "10": ncolour = '106246'; break;
							case "11": ncolour = '4E2A04'; break;
						}
						
						var ncolour_n;
						switch (aData[13])
						{
							case "0": ncolour_n = '{{$lang.General.Red}}'; break;
							case "1": ncolour_n = '{{$lang.General.Blue}}'; break;
							case "2": ncolour_n = '{{$lang.General.Teal}}'; break;
							case "3": ncolour_n = '{{$lang.General.Purple}}'; break;
							case "4": ncolour_n = '{{$lang.General.Yellow}}'; break;
							case "5": ncolour_n = '{{$lang.General.Orange}}'; break;
							case "6": ncolour_n = '{{$lang.General.Green}}'; break;
							case "7": ncolour_n = '{{$lang.General.Pink}}'; break;
							case "8": ncolour_n = '{{$lang.General.Gray}}'; break;
							case "9": ncolour_n = '{{$lang.General.L-Blue}}'; break;
							case "10": ncolour_n = '{{$lang.General.D-Green}}'; break;
							case "11": ncolour_n = '{{$lang.General.Brown}}'; break;
						}
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
					var intdata = parseInt(aData[3]);
					if (intdata <= 60)
						perc = 'red; font-weight: bold;';
					else if (intdata <= 90)
						perc = 'orange; font-weight: bold;';
					else
						perc = 'green';
						
					if (aData[12] != -1 && aData[12] != 0 && aData[13] == -1)
						$('td:eq(0)', nRow).html('<a href="?gstatspp;page=players&playerid=' + aData[12] + '" style="color: #' + colour + '">' + aData[0] + '</a>');
					else if (aData[12] != -1 && aData[12] != 0 && aData[13] != -1)
						$('td:eq(0)', nRow).html('<a href="?gstatspp;page=players&playerid=' + aData[12] + '" style="color: #' + colour + '">' + aData[0] + '</a> {{$lang.General.Was}} <span style="color: #' + ncolour + '">' + ncolour_n + '</span>');
					else if ((aData[12] == -1 || aData[12] == 0) && aData[13] == -1)
						$('td:eq(0)', nRow).html('<span style="color: #' + colour + '">' + aData[0] + '</span>');
					else if ((aData[12] == -1 || aData[12] == 0) && aData[13] != -1)
						$('td:eq(0)', nRow).html('<span style="color: #' + colour + '">' + aData[0] + '</span> {{$lang.General.Was}} <span style="color: #' + ncolour + '">' + ncolour_n + '</span>');
					
					$('td:eq(1)', nRow).html('<img src="medias/default/' + status + '" />');
					
					$('td:eq(3)', nRow).html('<span style="color: ' + perc + '">' + aData[3] + '</span>');
					
					return nRow;
				},
				"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
					var iTotalKills = 0;
					for ( var i=0 ; i<aaData.length ; i++ )
						iTotalKills += aaData[i][5]*1;
						
					var iTotalDeaths = 0;
					for ( var i=0 ; i<aaData.length ; i++ )
						iTotalDeaths += aaData[i][6]*1;
						
					var iTotalAssists = 0;
					for ( var i=0 ; i<aaData.length ; i++ )
						iTotalAssists += aaData[i][8]*1;
						
					var iTotalCK = 0;
					for ( var i=0 ; i<aaData.length ; i++ )
						iTotalCK += aaData[i][9]*1;
						
					var iTotalNK = 0;
					for ( var i=0 ; i<aaData.length ; i++ )
						iTotalNK += aaData[i][10]*1;
						
					var iTotalKD = 0;
					if (iTotalDeaths > 0)
						iTotalKD = Math.round((iTotalKills/iTotalDeaths)*100)/100;
					else
						iTotalKD = 'inf';
					
					var nCells = nRow.getElementsByTagName('th');
					
					nCells[5].innerHTML = parseInt(iTotalKills);
					nCells[6].innerHTML = parseInt(iTotalDeaths);
					if (iTotalKD != 'inf')
						nCells[7].innerHTML = iTotalKD;
					else
						nCells[7].innerHTML = '&#8734;';
					nCells[8].innerHTML = parseInt(iTotalAssists);
					nCells[9].innerHTML = parseInt(iTotalCK);
					nCells[10].innerHTML = parseInt(iTotalNK);
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
		} );
		
		oTable2 = $('#dataTable2').dataTable( {
				"bPaginate": false,
				"bAutoWidth": false,
				"bLengthChange": false,
				"bInfo": false,
				"iDisplayLength": 12,
				"bProcessing": false,
				"sDom": 'rt',
				"aoColumns": [ 
					{ "sClass": "dataTable_firstcolumn" },
					{ "bSortable": false, "sClass": "dataTable_column" },
					{ "sClass": "dataTable_column" },
					{ "sClass": "dataTable_column" },
					{ "bSearchable": false, "sClass": "dataTable_column" },
					{ "bSearchable": false, "sClass": "dataTable_column" },
					{ "bSearchable": false, "sClass": "dataTable_column" },
					{ "bSearchable": false, "sClass": "dataTable_column" },
					{ "bSearchable": false, "sClass": "dataTable_column" },
					{ "bSearchable": false, "sClass": "dataTable_column" },
					{ "bSearchable": false, "sClass": "dataTable_column" },
					{ "bVisible": false },
					{ "bVisible": false },
					{ "bVisible": false }
				],
				"aaSorting": [[11,'asc']],
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
					
					
					if (aData[13] != -1)
					{
						var ncolour;
						switch (aData[13])
						{
							case "0": ncolour = 'FF0303'; break;
							case "1": ncolour = '0042FF'; break;
							case "2": ncolour = '1CB619'; break;
							case "3": ncolour = '540081'; break;
							case "4": ncolour = 'CCCC01'; break; //FFFF01
							case "5": ncolour = 'FE8A0E'; break;
							case "6": ncolour = '20C000'; break;
							case "7": ncolour = 'E55BB0'; break;
							case "8": ncolour = '959697'; break;
							case "9": ncolour = '7EBFF1'; break;
							case "10": ncolour = '106246'; break;
							case "11": ncolour = '4E2A04'; break;
						}
						
						var ncolour_n;
						switch (aData[13])
						{
							case "0": ncolour_n = '{{$lang.General.Red}}'; break;
							case "1": ncolour_n = '{{$lang.General.Blue}}'; break;
							case "2": ncolour_n = '{{$lang.General.Teal}}'; break;
							case "3": ncolour_n = '{{$lang.General.Purple}}'; break;
							case "4": ncolour_n = '{{$lang.General.Yellow}}'; break;
							case "5": ncolour_n = '{{$lang.General.Orange}}'; break;
							case "6": ncolour_n = '{{$lang.General.Green}}'; break;
							case "7": ncolour_n = '{{$lang.General.Pink}}'; break;
							case "8": ncolour_n = '{{$lang.General.Gray}}'; break;
							case "9": ncolour_n = '{{$lang.General.L-Blue}}'; break;
							case "10": ncolour_n = '{{$lang.General.D-Green}}'; break;
							case "11": ncolour_n = '{{$lang.General.Brown}}'; break;
						}
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
					var intdata = parseInt(aData[3]);
					if (intdata <= 60)
						perc = 'red; font-weight: bold;';
					else if (intdata <= 90)
						perc = 'orange; font-weight: bold;';
					else
						perc = 'green';
						
					if (aData[12] != -1 && aData[12] != 0 && aData[13] == -1)
						$('td:eq(0)', nRow).html('<a href="?gstatspp;page=players&playerid=' + aData[12] + '" style="color: #' + colour + '">' + aData[0] + '</a>');
					else if (aData[12] != -1 && aData[12] != 0 && aData[13] != -1)
						$('td:eq(0)', nRow).html('<a href="?gstatspp;page=players&playerid=' + aData[12] + '" style="color: #' + colour + '">' + aData[0] + '</a> {{$lang.General.Was}} <span style="color: #' + ncolour + '">' + ncolour_n + '</span>');
					else if ((aData[12] == -1 || aData[12] == 0) && aData[13] == -1)
						$('td:eq(0)', nRow).html('<span style="color: #' + colour + '">' + aData[0] + '</span>');
					else if ((aData[12] == -1 || aData[12] == 0) && aData[13] != -1)
						$('td:eq(0)', nRow).html('<span style="color: #' + colour + '">' + aData[0] + '</span> {{$lang.General.Was}} <span style="color: #' + ncolour + '">' + ncolour_n + '</span>');
					
					$('td:eq(1)', nRow).html('<img src="medias/default/' + status + '" />');
					
					$('td:eq(3)', nRow).html('<span style="color: ' + perc + '">' + aData[3] + '</span>');
					
					return nRow;
				},
				"fnFooterCallback": function ( nRow, aaData, iStart, iEnd, aiDisplay ) {
					var iTotalKills = 0;
					for ( var i=0 ; i<aaData.length ; i++ )
						iTotalKills += aaData[i][5]*1;
						
					var iTotalDeaths = 0;
					for ( var i=0 ; i<aaData.length ; i++ )
						iTotalDeaths += aaData[i][6]*1;
						
					var iTotalAssists = 0;
					for ( var i=0 ; i<aaData.length ; i++ )
						iTotalAssists += aaData[i][8]*1;
						
					var iTotalCK = 0;
					for ( var i=0 ; i<aaData.length ; i++ )
						iTotalCK += aaData[i][9]*1;
						
					var iTotalNK = 0;
					for ( var i=0 ; i<aaData.length ; i++ )
						iTotalNK += aaData[i][10]*1;
						
					var iTotalKD = 0;
					if (iTotalDeaths > 0)
						iTotalKD = Math.round((iTotalKills/iTotalDeaths)*100)/100;
					else
						iTotalKD = 'inf';
					
					var nCells = nRow.getElementsByTagName('th');
					
					nCells[5].innerHTML = parseInt(iTotalKills);
					nCells[6].innerHTML = parseInt(iTotalDeaths);
					if (iTotalKD != 'inf')
						nCells[7].innerHTML = iTotalKD;
					else
						nCells[7].innerHTML = '&#8734;';
					nCells[8].innerHTML = parseInt(iTotalAssists);
					nCells[9].innerHTML = parseInt(iTotalCK);
					nCells[10].innerHTML = parseInt(iTotalNK);
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
		} );
	} );
</script> 

<div class="content">
	<h1>{{$game.gamename}}</h1>
</div>

<div class="fullcontent">
	<div style="float: left; margin: 0; padding: 0; width: 170px; height: 170px;">
		<img src="{{$game.map_image}}" />
	</div>
	<div style="float: left; margin: 0; padding: 10px; width: 839px; height: 148px; background: #f5f5f5; border-top: 1px #b6b6b6 solid; border-bottom: 1px #b6b6b6 solid;">
		<div style="height: 28px; margin: 0; padding-top: 9px;">
			{{$s_lang.TheMapWasX}}
		</div>
		<div style="height: 28px; margin: 0; padding-top: 9px;">
			{{$s_lang.TheGameEndedAtX}}
		</div>
		<div style="height: 28px; margin: 0; padding-top: 9px;">
			{{$s_lang.ThereWasXPlayersInThisGameForAXMatch}}
		</div>
		<div style="height: 28px; margin: 0; padding-top: 9px;">
			{{$s_lang.TheGameLastedX}}
		</div>
	</div>
</div>

<br style="clear: both" />
<br />

<div class="fullcontent">
	<p style="color: #FF0303; font-size: 40px; font-weight: bold; font-family: Calibri; margin: 15px; margin-bottom: 10px; margin-left: 25px;">{{$lang.DOTA.Sentinels}}</p>

	<div style="margin: 0; padding: 10px; width: 1009px; background: #f5f5f5; border-top: 1px #b6b6b6 solid; border-bottom: 1px #b6b6b6 solid;">
		<div style="height: 22px; margin: 0; padding-top: 5px;">
			{{foreach from=$sentinels.kills item=sent_kills key=sent_key}}<span style="color: #{{$sent_kills.colour}}"><b>{{$sent_kills.name}}</b></span>{{if $sent_key < $sentinels.kills_c - 2}}{{$lang.DOTAGamePlayers.Commas}}{{elseif $sent_key == $sentinels.kills_c - 2}} {{$lang.DOTAGamePlayers.And}}{{/if}} {{/foreach}} {{$lang.DOTAGamePlayers._DidTheMostAmmountOfKillsWith}} {{$sentinels.kills.0.kills}} {{$lang.DOTAGamePlayers.DidTheMostAmmountOfKillsWith_}}{{if $sentinels.kills_c > 1}} {{$lang.DOTAGamePlayers.Each}}{{/if}}{{$lang.DOTAGamePlayers.End}}
		</div>
		<div style="height: 22px; margin: 0; padding-top: 5px;">
			{{foreach from=$sentinels.deaths item=sent_deaths key=sent_key}}<span style="color: #{{$sent_deaths.colour}}"><b>{{$sent_deaths.name}}</b></span>{{if $sent_key < $sentinels.deaths_c - 2}}{{$lang.DOTAGamePlayers.Commas}}{{elseif $sent_key == $sentinels.deaths_c - 2}} {{$lang.DOTAGamePlayers.And}}{{/if}} {{/foreach}} {{$lang.DOTAGamePlayers._DiedTheLeastWith}} {{$sentinels.deaths.0.deaths}} {{$lang.DOTAGamePlayers.DiedTheLeastWith_}}{{if $sentinels.deaths_c > 1}} {{$lang.DOTAGamePlayers.Each}}{{/if}}{{$lang.DOTAGamePlayers.End}}
		</div>
		<div style="height: 22px; margin: 0; padding-top: 5px;">
			{{foreach from=$sentinels.kd item=sent_kd key=sent_key}}<span style="color: #{{$sent_kd.colour}}"><b>{{$sent_kd.name}}</b></span>{{if $sent_key < $sentinels.kd_c - 2}}{{$lang.DOTAGamePlayers.Commas}}{{elseif $sent_key == $sentinels.kd_c - 2}} {{$lang.DOTAGamePlayers.And}}{{/if}} {{/foreach}} {{$lang.DOTAGamePlayers._HaveTheBestKillsDeathsRatioWithAKFOf}} {{if $sentinels.kd.0.kd != 'inf'}}{{$sentinels.kd.0.kd}}{{else}}&#8734;{{/if}} {{$lang.DOTAGamePlayers.HaveTheBestKillsDeathsRatioWithAKFOf_}}{{$lang.DOTAGamePlayers.End}}
		</div>
		<div style="height: 22px; margin: 0; padding-top: 5px;">
			{{foreach from=$sentinels.assists item=sent_assists key=sent_key}}<span style="color: #{{$sent_assists.colour}}"><b>{{$sent_assists.name}}</b></span>{{if $sent_key < $sentinels.assists_c - 2}}{{$lang.DOTAGamePlayers.Commas}}{{elseif $sent_key == $sentinels.assists_c - 2}} {{$lang.DOTAGamePlayers.And}}{{/if}} {{/foreach}} {{$lang.DOTAGamePlayers._AssistsDid}} {{$sentinels.assists.0.assists}} {{$lang.DOTAGamePlayers.AssistsDid_}}{{if $sentinels.assists_c > 1}} {{$lang.DOTAGamePlayers.Each}}{{/if}}{{$lang.DOTAGamePlayers.End}}
		</div>
	</div>
</div>

<br style="clear: both" />

<div class="fullcontent">
<table cellpadding="0" cellspacing="0" border="0" class="display2" id="dataTable" width="100%"> 
	<thead> 
		<tr> 
			<th width="25%" height="42">{{$lang.DOTAGamePlayers.PlayerName}}</th> 
			<th width="10%">{{$lang.DOTAGamePlayers.Status}}</th> 
			<th width="25%">{{$lang.DOTAGamePlayers.Realm}}</th> 
			<th width="5%">{{$lang.DOTAGamePlayers.LeftP}}</th> 
			<th width="5%">{{$lang.DOTAGamePlayers.Hero}}</th>
			<th width="5%" onmouseover="Tip('{{$lang.DOTAGamePlayers.Kills}}')" onmouseout="UnTip()">{{$lang.DOTAGamePlayers.K}}</th>
			<th width="5%" onmouseover="Tip('{{$lang.DOTAGamePlayers.Deaths}}')" onmouseout="UnTip()">{{$lang.DOTAGamePlayers.D}}</th>
			<th width="5%" onmouseover="Tip('{{$lang.DOTAGamePlayers.KillsDeathsRatio}}')" onmouseout="UnTip()">{{$lang.DOTAGamePlayers.KD}}</th>
			<th width="5%" onmouseover="Tip('{{$lang.DOTAGamePlayers.Assists}}')" onmouseout="UnTip()">{{$lang.DOTAGamePlayers.A}}</th>
			<th width="5%" onmouseover="Tip('{{$lang.DOTAGamePlayers.CreepKills}}')" onmouseout="UnTip()">{{$lang.DOTAGamePlayers.CK}}</th>
			<th width="5%" onmouseover="Tip('{{$lang.DOTAGamePlayers.NeutralKills}}')" onmouseout="UnTip()">{{$lang.DOTAGamePlayers.NK}}</th>
			<th></th>
			<th></th>
			<th></th>
		</tr> 
	</thead> 
	<tbody> 
{{foreach from=$gameplayers item=gameplayer}}
{{if $gameplayer.colour >= 1 AND $gameplayer.colour <= 5}}
		<tr> 
			<td>{{$gameplayer.name}}</td>
			<td>{{$gameplayer.status}}</td>
			<td>{{$gameplayer.spoofedrealm}}</td>
			<td>{{$gameplayer.left}}</td>
			<td><img src="dota/heroes/{{$gameplayer.hero_image}}" onmouseover="Tip('{{if isset($gameplayer.items.1.image)}}<img src=\'dota/items/{{$gameplayer.items.1.image}}\' width=\'32\' height=\'32\' /> {{/if}}{{$gameplayer.items.1.name}}<br />{{if isset($gameplayer.items.2.image)}}<img src=\'dota/items/{{$gameplayer.items.2.image}}\' width=\'32\' height=\'32\' /> {{/if}}{{$gameplayer.items.2.name}}<br />{{if isset($gameplayer.items.3.image)}}<img src=\'dota/items/{{$gameplayer.items.3.image}}\' width=\'32\' height=\'32\' /> {{/if}}{{$gameplayer.items.3.name}}<br />{{if isset($gameplayer.items.4.image)}}<img src=\'dota/items/{{$gameplayer.items.4.image}}\' width=\'32\' height=\'32\' /> {{/if}}{{$gameplayer.items.4.name}}<br />{{if isset($gameplayer.items.5.image)}}<img src=\'dota/items/{{$gameplayer.items.5.image}}\' width=\'32\' height=\'32\' /> {{/if}}{{$gameplayer.items.5.name}}<br />{{if isset($gameplayer.items.6.image)}}<img src=\'dota/items/{{$gameplayer.items.6.image}}\' width=\'32\' height=\'32\' /> {{/if}}{{$gameplayer.items.6.name}}', TITLE, '{{$gameplayer.hero_name}}', CLICKCLOSE, true, CLOSEBTN, true, FOLLOWMOUSE, false, STICKY, true)" onmouseout="UnTip" /></td>
			<td>{{$gameplayer.kills}}</td>
			<td>{{$gameplayer.deaths}}</td>
			<td>{{$gameplayer.kd}}</td>
			<td>{{$gameplayer.assists}}</td>
			<td>{{$gameplayer.creepkills}}</td>
			<td>{{$gameplayer.neutralkills}}</td>
			<td>{{$gameplayer.colour}}</td>
			<td>{{$gameplayer.player_id}}</td>
			<td>{{$gameplayer.a_colour}}</td>
		</tr> 
{{/if}}
{{/foreach}}
	</tbody> 
	<tfoot> 
		<tr>
			<th height="42"></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th> 
		</tr> 
	</tfoot> 
</table> 
</div>

<br style="clear: both" />
<br />

<div class="fullcontent">
	<p style="color: #20C000; font-size: 40px; font-weight: bold; font-family: Calibri; margin: 15px; margin-bottom: 10px; margin-left: 25px;">{{$lang.DOTA.Scourges}}</p>

	<div style="margin: 0; padding: 10px; width: 1009px; background: #f5f5f5; border-top: 1px #b6b6b6 solid; border-bottom: 1px #b6b6b6 solid;">
		<div style="height: 22px; margin: 0; padding-top: 5px;">
			{{foreach from=$scourges.kills item=sent_kills key=sent_key}}<span style="color: #{{$sent_kills.colour}}"><b>{{$sent_kills.name}}</b></span>{{if $sent_key < $scourges.kills_c - 2}}{{$lang.DOTAGamePlayers.Commas}}{{elseif $sent_key == $scourges.kills_c - 2}} {{$lang.DOTAGamePlayers.And}}{{/if}} {{/foreach}} {{$lang.DOTAGamePlayers._DidTheMostAmmountOfKillsWith}} {{$scourges.kills.0.kills}} {{$lang.DOTAGamePlayers.DidTheMostAmmountOfKillsWith_}}{{if $scourges.kills_c > 1}} {{$lang.DOTAGamePlayers.Each}}{{/if}}{{$lang.DOTAGamePlayers.End}}
		</div>
		<div style="height: 22px; margin: 0; padding-top: 5px;">
			{{foreach from=$scourges.deaths item=sent_deaths key=sent_key}}<span style="color: #{{$sent_deaths.colour}}"><b>{{$sent_deaths.name}}</b></span>{{if $sent_key < $scourges.deaths_c - 2}}{{$lang.DOTAGamePlayers.Commas}}{{elseif $sent_key == $scourges.deaths_c - 2}} {{$lang.DOTAGamePlayers.And}}{{/if}} {{/foreach}} {{$lang.DOTAGamePlayers._DiedTheLeastWith}} {{$scourges.deaths.0.deaths}} {{$lang.DOTAGamePlayers.DiedTheLeastWith_}}{{if $scourges.deaths_c > 1}} {{$lang.DOTAGamePlayers.Each}}{{/if}}{{$lang.DOTAGamePlayers.End}}
		</div>
		<div style="height: 22px; margin: 0; padding-top: 5px;">
			{{foreach from=$scourges.kd item=sent_kd key=sent_key}}<span style="color: #{{$sent_kd.colour}}"><b>{{$sent_kd.name}}</b></span>{{if $sent_key < $scourges.kd_c - 2}}{{$lang.DOTAGamePlayers.Commas}}{{elseif $sent_key == $scourges.kd_c - 2}} {{$lang.DOTAGamePlayers.And}}{{/if}} {{/foreach}} {{$lang.DOTAGamePlayers._HaveTheBestKillsDeathsRatioWithAKFOf}} {{if $scourges.kd.0.kd != 'inf'}}{{$scourges.kd.0.kd}}{{else}}&#8734;{{/if}} {{$lang.DOTAGamePlayers.HaveTheBestKillsDeathsRatioWithAKFOf_}}{{$lang.DOTAGamePlayers.End}}
		</div>
		<div style="height: 22px; margin: 0; padding-top: 5px;">
			{{foreach from=$scourges.assists item=sent_assists key=sent_key}}<span style="color: #{{$sent_assists.colour}}"><b>{{$sent_assists.name}}</b></span>{{if $sent_key < $scourges.assists_c - 2}}{{$lang.DOTAGamePlayers.Commas}}{{elseif $sent_key == $scourges.assists_c - 2}} {{$lang.DOTAGamePlayers.And}}{{/if}} {{/foreach}} {{$lang.DOTAGamePlayers._AssistsDid}} {{$scourges.assists.0.assists}} {{$lang.DOTAGamePlayers.AssistsDid_}}{{if $scourges.assists_c > 1}} {{$lang.DOTAGamePlayers.Each}}{{/if}}{{$lang.DOTAGamePlayers.End}}
		</div>
	</div>
</div>

<br style="clear: both" />

<div class="fullcontent">
<table cellpadding="0" cellspacing="0" border="0" class="display2" id="dataTable2" width="100%"> 
	<thead> 
		<tr> 
			<th width="25%" height="42">{{$lang.DOTAGamePlayers.PlayerName}}</th> 
			<th width="10%">{{$lang.DOTAGamePlayers.Status}}</th> 
			<th width="25%">{{$lang.DOTAGamePlayers.Realm}}</th> 
			<th width="5%">{{$lang.DOTAGamePlayers.LeftP}}</th> 
			<th width="5%">{{$lang.DOTAGamePlayers.Hero}}</th>
			<th width="5%" onmouseover="Tip('{{$lang.DOTAGamePlayers.Kills}}')" onmouseout="UnTip()">{{$lang.DOTAGamePlayers.K}}</th>
			<th width="5%" onmouseover="Tip('{{$lang.DOTAGamePlayers.Deaths}}')" onmouseout="UnTip()">{{$lang.DOTAGamePlayers.D}}</th>
			<th width="5%" onmouseover="Tip('{{$lang.DOTAGamePlayers.KillsDeathsRatio}}')" onmouseout="UnTip()">{{$lang.DOTAGamePlayers.KD}}</th>
			<th width="5%" onmouseover="Tip('{{$lang.DOTAGamePlayers.Assists}}')" onmouseout="UnTip()">{{$lang.DOTAGamePlayers.A}}</th>
			<th width="5%" onmouseover="Tip('{{$lang.DOTAGamePlayers.CreepKills}}')" onmouseout="UnTip()">{{$lang.DOTAGamePlayers.CK}}</th>
			<th width="5%" onmouseover="Tip('{{$lang.DOTAGamePlayers.NeutralKills}}')" onmouseout="UnTip()">{{$lang.DOTAGamePlayers.NK}}</th>
			<th></th>
			<th></th>
			<th></th>
		</tr> 
	</thead> 
	<tbody> 
{{foreach from=$gameplayers item=gameplayer}}
{{if $gameplayer.colour >= 7 AND $gameplayer.colour <= 11}}
		<tr> 
			<td>{{$gameplayer.name}}</td>
			<td>{{$gameplayer.status}}</td>
			<td>{{$gameplayer.spoofedrealm}}</td>
			<td>{{$gameplayer.left}}</td>
			<td><img src="dota/heroes/{{$gameplayer.hero_image}}" onmouseover="Tip('{{if isset($gameplayer.items.1.image)}}<img src=\'dota/items/{{$gameplayer.items.1.image}}\' width=\'32\' height=\'32\' /> {{/if}}{{$gameplayer.items.1.name}}<br />{{if isset($gameplayer.items.2.image)}}<img src=\'dota/items/{{$gameplayer.items.2.image}}\' width=\'32\' height=\'32\' /> {{/if}}{{$gameplayer.items.2.name}}<br />{{if isset($gameplayer.items.3.image)}}<img src=\'dota/items/{{$gameplayer.items.3.image}}\' width=\'32\' height=\'32\' /> {{/if}}{{$gameplayer.items.3.name}}<br />{{if isset($gameplayer.items.4.image)}}<img src=\'dota/items/{{$gameplayer.items.4.image}}\' width=\'32\' height=\'32\' /> {{/if}}{{$gameplayer.items.4.name}}<br />{{if isset($gameplayer.items.5.image)}}<img src=\'dota/items/{{$gameplayer.items.5.image}}\' width=\'32\' height=\'32\' /> {{/if}}{{$gameplayer.items.5.name}}<br />{{if isset($gameplayer.items.6.image)}}<img src=\'dota/items/{{$gameplayer.items.6.image}}\' width=\'32\' height=\'32\' /> {{/if}}{{$gameplayer.items.6.name}}', TITLE, '{{$gameplayer.hero_name}}', CLICKCLOSE, true, CLOSEBTN, true, FOLLOWMOUSE, false, STICKY, true)" onmouseout="UnTip" /></td>
			<td>{{$gameplayer.kills}}</td>
			<td>{{$gameplayer.deaths}}</td>
			<td>{{$gameplayer.kd}}</td>
			<td>{{$gameplayer.assists}}</td>
			<td>{{$gameplayer.creepkills}}</td>
			<td>{{$gameplayer.neutralkills}}</td>
			<td>{{$gameplayer.colour}}</td>
			<td>{{$gameplayer.player_id}}</td>
			<td>{{$gameplayer.a_colour}}</td>
		</tr> 
{{/if}}
{{/foreach}}
	</tbody> 
	<tfoot> 
		<tr>
			<th height="42"></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th>
			<th></th> 
		</tr> 
	</tfoot> 
</table> 
</div>

<br style="clear: both" />
<br />

<div class="fullcontent">
	<div style="margin: 0; padding: 10px; width: 1009px; background: #f5f5f5; border-top: 1px #b6b6b6 solid; border-bottom: 1px #b6b6b6 solid; text-align: center;">
		{{if $show_replays == 1}}<a style="color: darkblue; text-decoration: none;" href="{{$replay_link}}">Download the replay</a><br />(To see the replay you need to copy the map to w3 folder \Maps\DotA v6.83d.w3x)<br />{{/if}}
		<a style="color: darkblue; text-decoration: none;" id="showhidelink" href="#">Show/Hide the result</a><br />
		<div id="preshowhide">
			<span style="color: #000; font-size: 32px; font-weight: bold; font-family: Calibri;">And the winner is...</span>
		</div>
		<div id="showhide">
		{{if $game.winner == 0}} <h3>Winner haven't been determined.</h3>
		{{else}}
			{{if $game.winner == 1}}<span style="color: #FF0303; font-size: 40px; font-weight: bold; font-family: Calibri;">The Sentinels have won the match!</span>
			{{elseif $game.winner == 2}}<span style="color: #20C000; font-size: 40px; font-weight: bold; font-family: Calibri;">The Scourges have won the match!</span>
			{{/if}}
		{{/if}}
		</div>
	</div>
</div>

<div class="dataTable_row" style="display: none;"></div>
<div class="dataTable_selectedrow" style="display: none;"></div>

{{include file='gstatspp_footer.tpl'}}