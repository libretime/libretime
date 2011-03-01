//used by jjmenu
function getId() { 
	var tr_id =  $(this.triggerElement).attr("id");
	tr_id = tr_id.split("_");

	return tr_id[1];
}

function getType() { 
	var tr_id =  $(this.triggerElement).attr("id");
	tr_id = tr_id.split("_");

	return tr_id[0];
}
//end functions used by jjmenu

function deleteItem(type, id) {
	var tr_id, tr, dt;

	tr_id = type+"_"+id;
	tr = $("#"+tr_id);

	dt = $("#library_display").dataTable();
	dt.fnDeleteRow( tr );
}

//callbacks called by jjmenu
function deleteAudioClip(json) {
	if(json.message) {  
		alert(json.message);	
		return;
	}

	deleteItem("au", json.id);
}

function deletePlaylist(json) {
	if(json.message) {  
		alert(json.message);	
		return;
	}

	deleteItem("pl", json.id);
}
//end callbacks called by jjmenu

function addLibraryItemEvents() {

	$('#library_display tr[id ^= "au"]')
		.draggable({ 
			helper: 'clone' 
		});

	$('#library_display tbody tr')
		.jjmenu("click", 
			[{get:"/Library/context-menu/format/json/id/#id#/type/#type#"}],  
			{id: getId, type: getType}, 
			{xposition: "mouse", yposition: "mouse"});

}

function dtRowCallback( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
	var id, type, once;

    type = aData[6].substring(0,2);
    id = aData[0];

    if(type == "au") {
        $('td:eq(5)', nRow).html( '<img src="css/images/icon_audioclip.png">' );
    }
    else if(type == "pl") {
        $('td:eq(5)', nRow).html( '<img src="css/images/icon_playlist.png">' );
    }

	$(nRow).attr("id", type+'_'+id);

    $('td:eq(5) img', nRow).qtip({

        content: {
            url: '/Library/get-file-meta-data',
            type: 'post',
            data: ({format: "html", id : id, type: type}),
            title: {
               text: aData[1] + ' MetaData',
               button: 'Close' // Show a close link in the title
            }
         },
        
         position: {
        
            adjust: {
               screen: true // Keep the tooltip on-screen at all times
            }
         },
        
         style: {
            border: {
               width: 0,
               radius: 4
            },
            name: 'dark', // Use the default light style
            width: 570 // Set the tooltip width
         }
    });

	return nRow;
}

function dtDrawCallback() {
	addLibraryItemEvents();
}

$(document).ready(function() {

	$('.tabs').tabs();

	$('#library_display').dataTable( {
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "/Library/contents/format/json",
		"fnServerData": function ( sSource, aoData, fnCallback ) {
			$.ajax( {
				"dataType": 'json', 
				"type": "POST", 
				"url": sSource, 
				"data": aoData, 
				"success": fnCallback
			} );
		},
		"fnRowCallback": dtRowCallback,
		"fnDrawCallback": dtDrawCallback,
		"aoColumns": [ 
			/* Id */		{ "sName": "id", "bSearchable": false, "bVisible": false },
			/* Title */		{ "sName": "track_title" },
			/* Creator */	{ "sName": "artist_name" },
			/* Album */		{ "sName": "album_title" },
			/* Track */		{ "sName": "track_number" },
			/* Length */	{ "sName": "length" },
			/* Type */		{ "sName": "ftype", "bSearchable": false }
		],
		"aaSorting": [[2,'asc']],
		"sPaginationType": "full_numbers",
		"bJQueryUI": true,
		"bAutoWidth": false,
        "oLanguage": {
            "sSearch": ""
        }
	});
});
