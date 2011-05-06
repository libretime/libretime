function populateForm(entries){
    //$('#user_details').show();
        
    $('.errors').remove();
    $('.success').remove();
    
    $('#user_id').val(entries.id);
    $('#login').val(entries.login);
    $('#first_name').val(entries.first_name);
    $('#last_name').val(entries.last_name);
    $('#type').val(entries.type);
    $('#email').val(entries.email);
    $('#skype').val(entries.skype_contact);
    $('#jabber').val(entries.jabber_contact);
    
    if (entries.id.length != 0){
        $('#login').attr('readonly', 'readonly');
        $('#password').val("xxxxxx");
    } else {
        $('#login').removeAttr('readonly');
        $('#password').val("");
    }
}

function rowClickCallback(row_id){
      $.ajax({ url: '/User/get-user-data/id/'+ row_id +'/format/json', dataType:"json", success:function(data){
        populateForm(data.entries);
	  }});    
}

function removeUserCallback(row_id, nRow){
      $.ajax({ url: '/User/remove-user/id/'+ row_id +'/format/json', dataType:"text", success:function(data){
        var o = $('#users_datatable').dataTable().fnDeleteRow(nRow);
	  }});
}

function rowCallback( nRow, aData, iDisplayIndex ){
    $(nRow).click(function(){rowClickCallback(aData[0])});
    if( aData[5] != "self"){
    	$('td:eq(4)', nRow).append( '<span class="ui-icon ui-icon-closethick"></span>').children('span').click(function(e){e.stopPropagation(); removeUserCallback(aData[0], nRow)});
    }else{
    	$('td:eq(4)', nRow).empty().append( '<span class="ui-icon ui-icon-closethick"></span>').children('span').click(function(e){e.stopPropagation(); alert("Can't delete yourself!")});
    }

    if ( aData[4] == "A" )
    {
	    $('td:eq(3)', nRow).html( 'Admin' );
    } else if ( aData[4] == "H" )
    {
	    $('td:eq(3)', nRow).html( 'Host' );
    } else if ( aData[4] == "G" )
    {
	    $('td:eq(3)', nRow).html( 'Guest' );
    }
    
    return nRow;
}

$(document).ready(function() {
    $('#users_datatable').dataTable( {
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": "/User/get-user-data-table-info/format/json",
        "fnServerData": function ( sSource, aoData, fnCallback ) {
            $.ajax( {
                "dataType": 'json', 
                "type": "POST", 
                "url": sSource, 
                "data": aoData, 
                "success": fnCallback
            } );
        },
        "fnRowCallback": rowCallback,
        "aoColumns": [
            /* Id */         { "sName": "id", "bSearchable": false, "bVisible": false },
            /* user name */  { "sName": "login" },
            /* first name */ { "sName": "first_name" },
            /* last name */  { "sName": "last_name" },
            /* user type */  { "sName": "type", "bSearchable": false },
            /* del button */ { "sName": "null as delete", "bSearchable": false, "bSortable": false}
        ],
        "bJQueryUI": true,
        "bAutoWidth": false,
        "bLengthChange": false,
        "oLanguage": {
            "sSearch": ""
        }
    });
    
    //$('#user_details').hide();
    
    var newUser = {login:"", first_name:"", last_name:"", type:"G", id:""};
    
    $('#add_user_button').click(function(){populateForm(newUser)});
    
});
