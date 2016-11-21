{{include file='gstatspp_header.tpl'}}

<script type="text/javascript" language="javascript" src="medias/jquery-1.3.2.min.js"></script> 
<script type="text/javascript" language="javascript" src="medias/jquery.dataTables.min.js"></script> 
<script type="text/javascript" charset="utf-8"> 
	$(document).ready(function() {
		$("#dataTable tbody").click(function(event) {
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
					{ "sClass": "dataTable_column" },
					{ "bVisible": false },
					{ "bVisible": false }
				],
				"aaSorting": [[5,'asc']],
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
					var intdata = parseInt(aData[4]);
					if (intdata <= 60)
						perc = 'red; font-weight: bold;';
					else if (intdata <= 90)
						perc = 'orange; font-weight: bold;';
					else
						perc = 'green';
						
					if (aData[6] != -1 && aData[6] != 0)
						$('td:eq(0)', nRow).html('<a href="?gstatspp;page=players&playerid=' + aData[6] + '" style="color: #' + colour + '">' + aData[0] + '</a>');
					else
						$('td:eq(0)', nRow).html('<span style="color: #' + colour + '">' + aData[0] + '</span>');
					
					$('td:eq(1)', nRow).html('<img src="medias/default/' + status + '" />');
					
					$('td:eq(3)', nRow).html(((Math.round((aData[3] / 1000) * 100)) / 100) + " Sec");
					
					$('td:eq(4)', nRow).html('<span style="color: ' + perc + '">' + aData[4] + '</span>');
					
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
			{{$s_lang.ThereWasXPlayersInThisGame}}
		</div>
		<div style="height: 28px; margin: 0; padding-top: 9px;">
			{{$s_lang.TheGameLastedX}}
		</div>
	</div>
</div>

<br style="clear: both" />
<br />

<div class="fullcontent">
<table cellpadding="0" cellspacing="0" border="0" class="display2" id="dataTable" width="100%"> 
	<thead> 
		<tr> 
			<th width="30%" height="42">{{$lang.NormalGamePlayers.PlayerName}}</th> 
			<th width="10%">{{$lang.NormalGamePlayers.Status}}</th> 
			<th width="30%">{{$lang.NormalGamePlayers.Realm}}</th> 
			<th width="15%">{{$lang.NormalGamePlayers.LoadingTime}}</th>
			<th width="15%">{{$lang.NormalGamePlayers.LeftP}}</th> 
			<th></th>
			<th></th>
		</tr> 
	</thead> 
	<tbody> 
{{foreach from=$gameplayers item=gameplayer}}
		<tr> 
			<td>{{$gameplayer.name}}</td>
			<td>{{$gameplayer.status}}</td>
			<td>{{$gameplayer.spoofedrealm}}</td>
			<td>{{$gameplayer.loadingtime}}</td>
			<td>{{$gameplayer.left}}</td>
			<td>{{$gameplayer.colour}}</td>
			<td>{{$gameplayer.player_id}}</td>
		</tr> 
{{/foreach}}
	</tbody> 
</table> 
</div>

<div class="dataTable_row" style="display: none;"></div>
<div class="dataTable_selectedrow" style="display: none;"></div>

{{include file='gstatspp_footer.tpl'}}