var dTable;
var checkedCount = 0;
var checkedPLCount = 0;

//used by jjmenu
function getId() {
	var tr_id =  $(this.triggerElement).parent().attr("id");
	tr_id = tr_id.split("_");

	return tr_id[1];
}

function getType() {
	var tr_id =  $(this.triggerElement).parent().attr("id");
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

        if (json.ids != undefined) {
            for (var i = json.ids.length - 1; i >= 0; i--) {
                deleteItem("au", json.ids[i]);
            }
        } else if (json.id != undefined) {
            deleteItem("au", json.id);
        }
	location.reload(true);
} 

function confirmDeleteGroup() {
    if(confirm('Are you sure you want to delete the selected items?')){
        groupDelete();
    }
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
	window.open(link);
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
//end callbacks called by jjmenu

function addLibraryItemEvents() {

	$('#library_display tr[id ^= "au"]')
		.draggable({
			helper: 'clone',
			cursor: 'pointer'
		});

	$('#library_display tbody tr td').not('[class=library_checkbox]')
		.jjmenu("click",
			[{get:"/Library/context-menu/format/json/id/#id#/type/#type#"}],
			{id: getId, type: getType},
			{xposition: "mouse", yposition: "mouse"});

}

function dtRowCallback( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
	var id, type, once;

    type = aData["ftype"].substring(0,2);
    id = aData["id"];
    
    if(type == "au") {
        $('td.library_type', nRow).html( '<img src="css/images/icon_audioclip.png">' );
    } else if(type == "pl") {
        $('td.library_type', nRow).html( '<img src="css/images/icon_playlist.png">' );
    }

    $(nRow).attr("id", type+'_'+id);

    // insert id on lenth field
    $('td.library_length', nRow).attr("id", "length");

    return nRow;
}

function dtDrawCallback() {
    addLibraryItemEvents();
    addMetadataQtip();
    saveNumEntriesSetting();
    setupGroupActions();
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
    return parseInt(data.libraryInit.numEntries);
}

function groupAdd() {
    if (checkedPLCount > 0) {
        alert("Can't add playlist to another playlist");
        return;
    }
    disableGroupBtn('library_group_add');
    
    var ids = new Array();
    var addGroupUrl = '/Playlist/add-group';
    var newSPLUrl = '/Playlist/new/format/json';
    var dirty = true;
    $('#library_display tbody tr').each(function() {
        var idSplit = $(this).attr('id').split("_");
        var id = idSplit.pop();
        var type = idSplit.pop();
        if (dirty && $(this).find(":checkbox").attr("checked")) {
            if (type == "au") {
                ids.push(id);
            } else if (type == "pl") {
                alert("Can't add playlist to another playlist");
                dirty = false;
            }
        }
    });
    
    if (dirty && ids.length > 0) {
        stopAudioPreview();
        
        if ($('#spl_sortable').length == 0) {
            $.post(newSPLUrl, function(json) {
                openDiffSPL(json);
		redrawDataTablePage();
                
                $.post(addGroupUrl, {format: "json", ids: ids}, setSPLContent);
            });
        } else {
            $.post(addGroupUrl, {format: "json", ids: ids}, setSPLContent);
        }
    }
}

function groupDelete() {
    disableGroupBtn('library_group_delete');
    
    var auIds = new Array();
    var plIds = new Array();
    var auUrl = '/Library/delete-group';
    var plUrl = '/Playlist/delete-group';
    var dirty = true;
    $('#library_display tbody tr').each(function() {
        var idSplit = $(this).attr('id').split("_");
        var id = idSplit.pop();
        var type = idSplit.pop();
        if (dirty && $(this).find(":checkbox").attr("checked")) {
            if (type == "au") {
                auIds.push(id);
            } else if (type == "pl") {
                plIds.push(id);
            }
        }
    });
    
    if (dirty && (auIds.length > 0 || plIds.length > 0)) {
        stopAudioPreview();
        
        if (auIds.length > 0) {
            $.post(auUrl, {format: "json", ids: auIds}, deleteAudioClip);
        }
        if (plIds.length > 0) {
            $.post(plUrl, {format: "json", ids: plIds}, deletePlaylist);
        }
    }
}

function toggleAll() {
    var checked = $(this).attr("checked");
    $('#library_display tr').each(function() {
        var idSplit = $(this).attr('id').split("_");
        var type = idSplit[0];
        $(this).find(":checkbox").attr("checked", checked);
        if (checked) {
            if (type == "pl") {
                checkedPLCount++;
            }
            $(this).addClass('selected');
        } else {
            $(this).removeClass('selected');
        }
    });
    
    if (checked) {
        checkedCount = $('#library_display tbody tr').size();
        enableGroupBtn('library_group_add', groupAdd);
        enableGroupBtn('library_group_delete', confirmDeleteGroup);
    } else {
        checkedCount = 0;
        checkedPLCount = 0;
        disableGroupBtn('library_group_add');
        disableGroupBtn('library_group_delete');
    }
}

function enableGroupBtn(btnId, func) {
    btnId = '#' + btnId;
    if ($(btnId).hasClass('ui-state-disabled')) {
        $(btnId).removeClass('ui-state-disabled');
        $(btnId).unbind("click").click(func);
    }
}

function disableGroupBtn(btnId) {
    btnId = '#' + btnId;
    if (!$(btnId).hasClass('ui-state-disabled')) {
        $(btnId).addClass('ui-state-disabled');
        $(btnId).unbind("click");
    }
}

function checkBoxChanged() {
    var cbAll = $('#library_display thead').find(":checkbox");
    var cbAllChecked = cbAll.attr("checked");
    var checked = $(this).attr("checked");
    var size = $('#library_display tbody tr').size();
    var idSplit = $(this).parent().parent().attr('id').split("_");
    var type = idSplit[0];
    if (checked) {
       if (checkedCount < size) {
           checkedCount++;
       }
       if (type == "pl" && checkedPLCount < size) {
           checkedPLCount++;
       }
       enableGroupBtn('library_group_add', groupAdd);
       enableGroupBtn('library_group_delete', confirmDeleteGroup);
       $(this).parent().parent().addClass('selected');
    } else {
        if (checkedCount > 0) {
            checkedCount--;
        }
        if (type == "pl" && checkedPLCount > 0) {
           checkedPLCount--;
        }
        if (checkedCount == 0) {
            disableGroupBtn('library_group_add');
            disableGroupBtn('library_group_delete');
        }
        $(this).parent().parent().removeClass('selected');
    }
    
    if (cbAllChecked && checkedCount < size) {
        cbAll.attr("checked", false);
    } else if (!cbAllChecked && checkedCount == size) {
        cbAll.attr("checked", true);
    }
}

function setupGroupActions() {
    checkedCount = 0;
    checkedPLCount = 0;
    $('#library_display tr:nth-child(1)').find(":checkbox").attr("checked", false);
    $('#library_display thead').find(":checkbox").unbind('change').change(toggleAll);
    $('#library_display tbody tr').each(function() {
        $(this).find(":checkbox").unbind('change').change(checkBoxChanged);
    });
    
    disableGroupBtn('library_group_add');
    disableGroupBtn('library_group_delete');
}

function fnShowHide(iCol) {
	/* Get the DataTables object again - this is not a recreation, just a get of the object */
	var oTable = dTable;
	
	var bVis = oTable.fnSettings().aoColumns[iCol].bVisible;
	oTable.fnSetColumnVis( iCol, bVis ? false : true );
}

function createDataTable(data) {
    dTable = $('#library_display').dataTable( {
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "/Library/contents/format/json",
		"fnServerData": function ( sSource, aoData, testCallback ) {
			$.ajax( {
				"dataType": 'json',
				"type": "POST",
				"url": sSource,
				"data": aoData,
				"success": testCallback
			} );
		},
		"fnRowCallback": dtRowCallback,
		"fnDrawCallback": dtDrawCallback,
		"aoColumns": [
                    /* Checkbox */      {"sTitle": "<input type='checkbox' name='cb_all'>", "bSortable": false, "bSearchable": false, "mDataProp": "checkbox", "sWidth": "25px", "sClass": "library_checkbox"},
                    /* Id */            {"sName": "id", "bSearchable": false, "bVisible": false, "mDataProp": "id", "sClass": "library_id"},
                    /* Title */         {"sTitle": "Title", "sName": "track_title", "mDataProp": "track_title", "sClass": "library_title"},
                    /* Creator */       {"sTitle": "Creator", "sName": "artist_name", "mDataProp": "artist_name", "sClass": "library_creator"},
                    /* Album */         {"sTitle": "Album", "sName": "album_title", "mDataProp": "album_title", "sClass": "library_album"},
                    /* Genre */         {"sTitle": "Genre", "sName": "genre", "mDataProp": "genre", "sWidth": "10%", "sClass": "library_genre"},
                    /* Year */          {"sTitle": "Year", "sName": "year", "mDataProp": "year", "sWidth": "8%", "sClass": "library_year"},
                    /* Length */        {"sTitle": "Length", "sName": "length", "mDataProp": "length", "sWidth": "10%", "sClass": "library_length"},
                    /* Type */          {"sTitle": "Type", "sName": "ftype", "bSearchable": false, "mDataProp": "ftype", "sWidth": "9%", "sClass": "library_type"},
                    /* Upload Time */   {"sTitle": "Upload Time", "sName": "utime", "mDataProp": "utime", "sClass": "library_upload_time"},
                    /* Last Modified */ {"sTitle": "Last Modified", "sName": "mtime", "bVisible": false, "mDataProp": "mtime", "sClass": "library_modified_time"},
                ],
		"aaSorting": [[2,'asc']],
		"sPaginationType": "full_numbers",
		"bJQueryUI": true,
		"bAutoWidth": false,
                "oLanguage": {
                    "sSearch": ""
                },
                "iDisplayLength": getNumEntriesPreference(data),
                "bStateSave": true,
                // R = ColReorder, C = ColVis, see datatables doc for others
                "sDom": 'Rlfr<"H"C<"library_toolbar">>t<"F"ip>',
                "oColVis": {
                    "buttonText": "Show/Hide Columns",
                    "sAlign": "right",
                    "aiExclude": [0, 1, 2],
                    "sSize": "css",
                    "bShowAll": true
		},
                "oColReorder": {
                    "aiOrder": [ 0, 2, 3, 4, 5, 6, 7, 8, 9, 10 ] /* code this */,
                    "iFixedColumns": 3
		}
    });
    dTable.fnSetFilteringDelay(350);
    
    $("div.library_toolbar").html('<span class="fg-button ui-button ui-state-default" id="library_order_reset">Reset Order</span>' + 
        '<span class="fg-button ui-button ui-state-default ui-state-disabled" id="library_group_delete">Delete</span>' + 
        '<span class="fg-button ui-button ui-state-default ui-state-disabled" id="library_group_add">Add</span>');
    
    $('#library_order_reset').click(function() {
        ColReorder.fnReset( dTable );
        return false;
    });
}

$(document).ready(function() {
    $('.tabs').tabs();
    
    $.ajax({url: "/Api/library-init/format/json", dataType:"json", success:createDataTable, 
        error:function(jqXHR, textStatus, errorThrown){}});
    
    checkImportStatus();
    setInterval( "checkImportStatus()", 5000 );
    setInterval( "checkSCUploadStatus()", 5000 );
    
    addQtipToSCIcons();
});
