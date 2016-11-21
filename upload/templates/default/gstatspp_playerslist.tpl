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
		});
	
		oTable = $('#dataTable').dataTable( {
				"bPaginate": true,
				"sPaginationType": "full_numbers",
				"bAutoWidth": false,
				"bLengthChange": false,
				"iDisplayLength": 10,
				"bProcessing": false,
				"bServerSide": true,
				"sAjaxSource": "ajaxcalls.php?call=playerslist",
				"sDom": 'rt<"dataTable_bottom"<"dataTable_search"f><"dataTable_paginate"p>><"dataTable_info"i>',
				"aoColumns": [ 
					{ "sClass": "dataTable_firstcolumn" },
					{ "bSearchable": false, "bSortable": false, "sClass": "dataTable_column" },
					{ "sClass": "dataTable_column" },
					{ "bSearchable": false, "sClass": "dataTable_column" },
					{ "bSearchable": false, "bVisible": false}
				],
				"aaSorting": [[3,'desc']],
				"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
				
					$('td:eq(0)', nRow).html("<a href=\"?gstatspp;page=players&playerid=" + aData[4] + "\">" + aData[0] + "</a>");
					
					var status;
					switch (aData[1])
					{
						case "0": status = 'status_admin.png'; break;
						case "1": status = 'status_vip.png'; break;
						case "2": status = 'status_active.png'; break;
						case "3": status = 'status_spoofer.png'; break;
						case "4": status = 'status_banned.png'; break;
					}
					
					$('td:eq(1)', nRow).html('<img src="medias/default/' + status + '" />');
					
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

<div class="fullcontent">
<table cellpadding="0" cellspacing="0" border="0" class="display" id="dataTable" width="100%"> 
	<thead> 
		<tr class="dataTable_fullhead"> 
			<th width="30%" height="42">{{$lang.PlayersList.PlayerName}}</th> 
			<th width="10%">{{$lang.PlayersList.Status}}</th> 
			<th width="30%">{{$lang.PlayersList.Realm}}</th> 
			<th width="30%">{{$lang.PlayersList.NumberOfGamesPlayed}}</th> 
			<th></th>
		</tr> 
	</thead> 
	<tbody> 
		<tr> 
			<td colspan="5" id="dataTable_empty" class="dataTable_empty"><img src="medias/default/ajax-loader.gif" /></td> 
		</tr> 
	</tbody> 
</table> 
</div>

<div class="dataTable_row" style="display: none;"></div>
<div class="dataTable_selectedrow" style="display: none;"></div>

{{include file='gstatspp_footer.tpl'}}