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

function openFileOnSoundCloud(link){
	window.open(link)
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

	return nRow;
}

function dtDrawCallback() {
	addLibraryItemEvents();
	addMetadataQtip();
}

function addProgressIcon(id) {
    if($("#au_"+id).find("td:eq(0)").find("span").length > 0){
        $("#au_"+id).find("td:eq(0)").find("span").removeClass();
        $("span[id="+id+"]").addClass("small-icon progress");
    }else{
        $("#au_"+id).find("td:eq(0)").append('<span id="'+id+'" class="small-icon progress"></span>')
    }
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

function addMetadataQtip(){
    var tableRow = $('#library_display tbody tr');
    tableRow.each(function(){
        var title = $(this).find('td:eq(0)').html()
        var info = $(this).attr("id")
        info = info.split("_");
        var id = info[1];
        var type = info[0];
        $(this).qtip({
            content: {
                text: "Loading...",
                title: {
                    text: title
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
                target: 'event',
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
            show: 'mousedown',
            events: {
               show: function(event, api) {
                  // Only show the tooltip if it was a right-click
                  if(event.originalEvent.button !== 2) {
                     event.preventDefault();
                  }
               }
            }
        })
    })
    
    tableRow.bind('contextmenu', function(e){
        return false;
    })
}

/**
 * Use user preference for number of entries to show;
 * defaults to 10 if preference was never set
 */
function getNumEntriesPreference(data) {
	var numEntries = data.libraryInit.numEntries;
    if(numEntries == '') {
    	numEntries = '10';
    }
    return parseInt(numEntries);
}

function createDataTable(data) {
	var dTable = $('#library_display').dataTable( {
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
        },
        "iDisplayLength": getNumEntriesPreference(data),
        "bStateSave": true
	});
	dTable.fnSetFilteringDelay(350);
    
    // Updates pref db when user changes the # of entries to show
    $('select[name=library_display_length]').change(function() {
		var url = '/Library/set-num-entries/format/json';
		$.post(url, {numEntries: $(this).val()}, 
				function(json){
					if(json.error) {
						alert(json.error);
					}
		});
	});
}

$(document).ready(function() {
	$('.tabs').tabs();
	
	$.ajax({ url: "/Api/library-init/format/json", dataType:"json", success:createDataTable
        , error:function(jqXHR, textStatus, errorThrown){}});
	
	checkImportStatus()
	setInterval( "checkImportStatus()", 5000 );
	setInterval( "checkSCUploadStatus()", 5000 );
	
	addQtipToSCIcons()
});
