{{include file='gstatspp_header.tpl'}}

<script type="text/javascript" language="javascript" src="medias/jquery-1.3.2.min.js"></script> 
<script type="text/javascript" language="javascript" src="medias/jquery.dataTables.min.js"></script> 
<script type="text/javascript" charset="utf-8"> 
	$(document).ready(function() {
		$("#dataTable tbody").click(function(event) {
			var hasTheClass;
			var target;
			
			if ($(event.target).context.toString() == '[object HTMLImageElement]')
				target = $(event.target.parentNode.parentNode);
			else
				target = $(event.target.parentNode);
			
			if (target.hasClass('dataTable_selectedrow'))
				hasTheClass = true;
			else
				hasTheClass = false;
			
			$(oTable.fnSettings().aoData).each(function (){
				$(this.nTr).removeClass('dataTable_selectedrow');
			});
			
			if (!hasTheClass)
				target.addClass('dataTable_selectedrow');
		});
	
		oTable = $('#dataTable').dataTable( {
				"bPaginate": true,
				"sPaginationType": "full_numbers",
				"bAutoWidth": false,
				"bLengthChange": false,
				"iDisplayLength": 10,
				"bProcessing": false,
				"bServerSide": true,
				"sAjaxSource": "ajaxcalls.php?call=bans",
				"sDom": 'rt<"dataTable_bottom"<"dataTable_search"f><"dataTable_paginate"p>><"dataTable_info"i>',
				"aoColumns": [ 
					{ "sClass": "dataTable_firstcolumn" },
					{ "bSearchable": false, "bSortable": false, "sClass": "dataTable_column" },
					{ "sClass": "dataTable_column" },
					{ "sClass": "dataTable_column" },
					{ "sClass": "dataTable_column" }
				],
				"aaSorting": [[0,'asc']],
				"fnRowCallback": function( nRow, aData, iDisplayIndex ) {
					if (aData[1] == "4")
						$('td:eq(1)', nRow).html( '<img src="medias/default/status_banned.png" class="status_image" />' );
						
					var d = new Date(aData[3] * 1000);
					$('td:eq(3)', nRow).html( d.getFullYear() + '/' + (d.getMonth() + 1) + '/' + d.getDate() );
					
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
			<th width="20%">{{$lang.Bans.Name}}</th> 
			<th width="10%">{{$lang.Bans.Status}}</th>
			<th width="20%">{{$lang.Bans.Realm}}</th> 
			<th width="15%">{{$lang.Bans.Date}}</th> 
			<th width="35%">{{$lang.Bans.Reason}}</th> 
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