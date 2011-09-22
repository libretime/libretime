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

function deleteAudioClip(json) {
	if(json.message) {
		alert(json.message);
		return;
	}

	deleteItem("au", json.id);
	location.reload(true);
}

//callbacks called by jjmenu
function confirmDeleteAudioClip(params){
    if(confirm('The file will be deleted from disk, are you sure you want to delete?')){
        var url = '/Library/delete' + params;
        $.ajax({
          url: url,
          success: deleteAudioClip
        });
    }
}

//callbacks called by jjmenu
function confirmDeletePlaylist(params){
    if(confirm('Are you sure you want to delete?')){
        var url = '/Playlist/delete' + params;
        $.ajax({
          url: url,
          success: deletePlaylist
        });
    }
}

function checkImportStatus(){
    $.getJSON('/Preference/is-import-in-progress', function(data){
        var div = $('#import_status');
        if(data == true){
            div.css('visibility', 'visible');
        }else{
            div.css('visibility', 'hidden');
        }
    })
}

function deletePlaylist(json) {
	if(json.message) {
		alert(json.message);
		return;
	}

	deleteItem("pl", json.id);
	window.location.reload();
}
//end callbacks called by jjmenu

function addLibraryItemEvents() {

	$('#library_display tr[id ^= "au"]')
		.draggable({
			helper: 'clone',
			cursor: 'pointer'
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

	// insert id on lenth field
	$('td:eq(4)', nRow).attr("id", "length");
    
	$('td:gt(0)', nRow).qtip({
            content: {
	            text: "Loading...",
                title: {
                    text: aData[1] + " MetaData"
                },
                ajax: {
                    url: "/Library/get-file-meta-data",
                    type: "post",
                    data: ({format: "html", id : id, type: type}),
                    success: function(data, status){
                        this.set('content.text', data)
                    }
                }
            },
            position: {
                adjust: {
                    resize: true,
                    method: "flip flip"
                },
                at: "right center",
                my: "left top",
                viewport: $(window)
            },
            style: {
                width: 570,
                classes: "ui-tooltip-dark"
            },
            show: {
                delay: 700
            }
        }
    );

	return nRow;
}

function dtDrawCallback() {
	addLibraryItemEvents();
}

function addProgressIcon(id) {
    $("#au_"+id).find("td:eq(0)").append('<span id="'+id+'" class="small-icon progress"></span>')
    $("span[id="+id+"]").addClass("progress");
}

function checkSCUploadStatus(){
    var url = '/Library/get-upload-to-soundcloud-status/format/json';
    $("span[class*=progress]").each(function(){
        var id = $(this).attr("id");
        $.post(url, {format: "json", id: id, type:"file"}, function(json){
            if(json.sc_id > 0){
                $("span[id="+id+"]").removeClass("progress").addClass("soundcloud");
            }else if(json.sc_id == "-3"){
                $("span[id="+id+"]").removeClass("progress").addClass("sc-error");
            }
        });
    })
}

function addQtipToSCIcons(){
    $(".progress, .soundcloud, .sc-error").live('mouseover', function(){
        var id = $(this).attr("id");
        if($(this).hasClass("progress")){
            $(this).qtip({
                content: {
                    text: "Uploading in progress..."
                },
                position:{
                    adjust: {
                    resize: true,
                    method: "flip flip"
                    },
                    at: "right center",
                    my: "left top",
                    viewport: $(window)
                },
                show: {
                    ready: true // Needed to make it show on first mouseover event
                }
            })
        }else if($(this).hasClass("soundcloud")){
            $(this).qtip({
                content: {
                    text: "Retreiving data from the server...",
                    ajax: {
                        url: "/Library/get-upload-to-soundcloud-status",
                        type: "post",
                        data: ({format: "json", id : id, type: "file"}),
                        success: function(json, status){
                            this.set('content.text', "The soundcloud id for this file is: "+json.sc_id)
                        }
                    }
                },
                position:{
                    adjust: {
                    resize: true,
                    method: "flip flip"
                    },
                    at: "right center",
                    my: "left top",
                    viewport: $(window)
                },
                show: {
                    ready: true // Needed to make it show on first mouseover event
                }
            })
        }else if($(this).hasClass("sc-error")){
            $(this).qtip({
                content: {
                    text: "Retreiving data from the server...",
                    ajax: {
                        url: "/Library/get-upload-to-soundcloud-status",
                        type: "post",
                        data: ({format: "json", id : id, type: "file"}),
                        success: function(json, status){
                            this.set('content.text', "There was error while uploading to soundcloud.<br>"+"Error code: "+json.error_code+
                                    "<br>"+"Error msg: "+json.error_msg+"<br>")
                        }
                    }
                },
                position:{
                    adjust: {
                    resize: true,
                    method: "flip flip"
                    },
                    at: "right center",
                    my: "left top",
                    viewport: $(window)
                },
                show: {
                    ready: true // Needed to make it show on first mouseover event
                }
            })
        }
    });
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
	}).fnSetFilteringDelay(350);
	
	checkImportStatus()
	setInterval( "checkImportStatus()", 5000 );
	setInterval( "checkSCUploadStatus()", 5000 );
	
	addQtipToSCIcons()
});
