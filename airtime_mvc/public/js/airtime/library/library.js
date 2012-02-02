function addToolBarButtonsLibrary(aButtons) {
	var i,
		length = aButtons.length,
		libToolBar,
		html,
		buttonClass = '',
		DEFAULT_CLASS = 'ui-button ui-state-default',
		DISABLED_CLASS = 'ui-state-disabled';
	
	libToolBar = $(".library_toolbar");
	
	for ( i=0; i < length; i+=1 ) {
		buttonClass = '';
		
		//add disabled class if not enabled.
		if (aButtons[i][2] === false) {
			buttonClass+=DISABLED_CLASS;
		}
		
		html = '<div id="'+aButtons[i][1]+'" class="ColVis TableTools"><button class="'+DEFAULT_CLASS+' '+buttonClass+'"><span>'+aButtons[i][0]+'</span></button></div>';	
		libToolBar.append(html);
		libToolBar.find("#"+aButtons[i][1]).click(aButtons[i][3]);
	}
}

function enableGroupBtn(btnId, func) {
    btnId = '#' + btnId;
    if ($(btnId).hasClass('ui-state-disabled')) {
        $(btnId).removeClass('ui-state-disabled');
    }
}

function disableGroupBtn(btnId) {
    btnId = '#' + btnId;
    if (!$(btnId).hasClass('ui-state-disabled')) {
        $(btnId).addClass('ui-state-disabled');
    }
}

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

    if (json.ids != undefined) {
        for (var i = json.ids.length - 1; i >= 0; i--) {
            deleteItem("au", json.ids[i]);
        }
    } 
    else if (json.id != undefined) {
        deleteItem("au", json.id);
    }
    location.reload(true);
} 

function confirmDeleteGroup() {
    if(confirm('Are you sure you want to delete the selected items?')){
        groupDelete();
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
    });
}

function deletePlaylist(json) {
	if(json.message) {
            alert(json.message);
            return;
	}
        
        if (json.ids != undefined) {
            for (var i = json.ids.length - 1; i >= 0; i--) {
                deleteItem("pl", json.ids[i]);
            }
        } else if (json.id != undefined) {
            deleteItem("pl", json.id);
        }
	window.location.reload();
}

function addProgressIcon(id) {
    if($("#au_"+id).find("td.library_title").find("span").length > 0){
        $("#au_"+id).find("td.library_title").find("span").removeClass();
        $("span[id="+id+"]").addClass("small-icon progress");
    }else{
        $("#au_"+id).find("td.library_title").append('<span id="'+id+'" class="small-icon progress"></span>');
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
    });
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
            });
        }else if($(this).hasClass("soundcloud")){
            $(this).qtip({
                content: {
                    text: "Retreiving data from the server...",
                    ajax: {
                        url: "/Library/get-upload-to-soundcloud-status",
                        type: "post",
                        data: ({format: "json", id : id, type: "file"}),
                        success: function(json, status){
                            this.set('content.text', "The soundcloud id for this file is: "+json.sc_id);
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
            });
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
                                    "<br>"+"Error msg: "+json.error_msg+"<br>");
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
            });
        }
    });
}

function addMetadataQtip(){
    var tableRow = $('#library_display tbody tr');
    tableRow.each(function(){
        var title = $(this).find('td.library_title').html();
        var info = $(this).attr("id");
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
                        this.set('content.text', data);
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
        });
    });
    
    tableRow.bind('contextmenu', function(e){
        return false;
    });
}

/**
 * Updates pref db when user changes the # of entries to show
 */
function saveNumEntriesSetting() {
    $('select[name=library_display_length]').change(function() {
        var url = '/Library/set-num-entries/format/json';
        $.post(url, {numEntries: $(this).val()});
    });
}

/**
 * Use user preference for number of entries to show
 */
function getNumEntriesPreference(data) {
    return parseInt(data.libraryInit.numEntries, 10);
}

function groupAdd() {
   
}

function groupDelete() {
   
}

function createDataTable(data) {
	var oTable;
	
    oTable = $('#library_display').dataTable( {
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "/Library/contents",
		"fnServerData": function ( sSource, aoData, testCallback ) {
    		aoData.push( { name: "format", value: "json"} );
			$.ajax( {
				"dataType": 'json',
				"type": "GET",
				"url": sSource,
				"data": aoData,
				"success": testCallback
			} );
		},
		"fnRowCallback": fnLibraryTableRowCallback,
		"fnDrawCallback": fnLibraryTableDrawCallback,
		"fnHeaderCallback": function(nHead) {
			$(nHead).find("input[type=checkbox]").attr("checked", false);
		},
		
		"aoColumns": [
                /* Checkbox */      {"sTitle": "<input type='checkbox' name='pl_cb_all'>", "bSortable": false, "bSearchable": false, "mDataProp": "checkbox", "sWidth": "25px", "sClass": "library_checkbox"},
                /* Type */          {"sName": "ftype", "bSearchable": false, "mDataProp": "image", "sWidth": "25px", "sClass": "library_type"},
                /* Title */         {"sTitle": "Title", "sName": "track_title", "mDataProp": "track_title", "sClass": "library_title"},
                /* Creator */       {"sTitle": "Creator", "sName": "artist_name", "mDataProp": "artist_name", "sClass": "library_creator"},
                /* Album */         {"sTitle": "Album", "sName": "album_title", "mDataProp": "album_title", "sClass": "library_album"},
                /* Genre */         {"sTitle": "Genre", "sName": "genre", "mDataProp": "genre", "sClass": "library_genre"},
                /* Year */          {"sTitle": "Year", "sName": "year", "mDataProp": "year", "sClass": "library_year"},
                /* Length */        {"sTitle": "Length", "sName": "length", "mDataProp": "length", "sClass": "library_length"},
                /* Upload Time */   {"sTitle": "Upload Time", "sName": "utime", "mDataProp": "utime", "sClass": "library_upload_time"},
                /* Last Modified */ {"sTitle": "Last Modified", "sName": "mtime", "bVisible": false, "mDataProp": "mtime", "sClass": "library_modified_time"}
            ],
		"aaSorting": [[2,'asc']],
		"sPaginationType": "full_numbers",
		"bJQueryUI": true,
		"bAutoWidth": false,
        "oLanguage": {
            "sSearch": ""
        },
        "iDisplayLength": getNumEntriesPreference(data),

        // R = ColReorder, C = ColVis, T = TableTools
        "sDom": 'Rlfr<"H"T<"library_toolbar"C>>t<"F"ip>',
        
        "oTableTools": {
        	"sRowSelect": "multi",
			"aButtons": [],
			"fnRowSelected": function ( node ) {
                    
                //seems to happen if everything is selected
                if ( node === null) {
                	oTable.find("input[type=checkbox]").attr("checked", true);
                }
                else {
                	$(node).find("input[type=checkbox]").attr("checked", true);
                }
            },
            "fnRowDeselected": function ( node ) {
             
              //seems to happen if everything is deselected
                if ( node === null) {
                	oTable.find("input[type=checkbox]").attr("checked", false);
                }
                else {
                	$(node).find("input[type=checkbox]").attr("checked", false);
                }
            }
		},
		
        "oColVis": {
            "buttonText": "Show/Hide Columns",
            "sAlign": "right",
            "aiExclude": [0, 1],
            "sSize": "css",
            "bShowAll": true
		},
		
		"oColReorder": {
			"iFixedColumns": 2
		}
		
    });
    oTable.fnSetFilteringDelay(350);
    
    setupLibraryToolbar(oTable);
    
    $('#library_order_reset').click(function() {
        ColReorder.fnReset( oTable );
        return false;
    });
    
    $('[name="pl_cb_all"]').click(function(){
    	var oTT = TableTools.fnGetInstance('library_display');
    	
    	if ($(this).is(":checked")) {
    		oTT.fnSelectAll();
    	}
    	else {
    		oTT.fnSelectNone();
    	}       
    });
}

$(document).ready(function() {
    $('.tabs').tabs();
    
    $.ajax({url: "/Api/library-init/format/json", dataType:"json", success:createDataTable, 
        error:function(jqXHR, textStatus, errorThrown){}});
    
    checkImportStatus();
    //setInterval( "checkImportStatus()", 5000 );
    //setInterval( "checkSCUploadStatus()", 5000 );
    
    addQtipToSCIcons();
});
