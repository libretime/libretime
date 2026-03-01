/**
 * Created by asantoni on 11/09/15.
 */

$(document).ready(function () {
  var aoColumns = [
    /* Artwork */ {
      sTitle: $.i18n._("Artwork"),
      mDataProp: "artwork",
      bVisible: false,
      sClass: "library_artwork",
      sWidth: "150px",
    },
    /* Title */ {
      sTitle: $.i18n._("Title"),
      mDataProp: "track_title",
      sClass: "library_title",
      sWidth: "170px",
    },
    /* Creator */ {
      sTitle: $.i18n._("Creator"),
      mDataProp: "artist_name",
      sClass: "library_creator",
      sWidth: "160px",
    },
    /* Upload Time */ {
      sTitle: $.i18n._("Uploaded"),
      mDataProp: "utime",
      bVisible: false,
      sClass: "library_upload_time",
      sWidth: "155px",
    },
    /* Website */ {
      sTitle: $.i18n._("Website"),
      mDataProp: "info_url",
      bVisible: false,
      sClass: "library_url",
      sWidth: "150px",
    },
    /* Year */ {
      sTitle: $.i18n._("Year"),
      mDataProp: "year",
      bVisible: false,
      sClass: "library_year",
      sWidth: "60px",
    },
  ];
  var ajaxSourceURL = baseUrl + "rest/media";

  var myToolbarButtons = AIRTIME.widgets.Table.getStandardToolbarButtons();
  myToolbarButtons[
    AIRTIME.widgets.Table.TOOLBAR_BUTTON_ROLES.NEW
  ].eventHandlers.click = function (e) {
    alert("New!");
  };
  myToolbarButtons[
    AIRTIME.widgets.Table.TOOLBAR_BUTTON_ROLES.EDIT
  ].eventHandlers.click = function (e) {
    alert("Edit!");
  };
  myToolbarButtons[
    AIRTIME.widgets.Table.TOOLBAR_BUTTON_ROLES.DELETE
  ].eventHandlers.click = function (e) {
    alert("Delete!");
  };

  //Set up the div with id "example-table" as a datatable.
  var table = new AIRTIME.widgets.Table(
    $("#example-table"), //DOM node to create the table inside.
    true, //Enable item selection
    myToolbarButtons, //Toolbar buttons
    {
      //Datatables overrides.
      aoColumns: aoColumns,
      sAjaxSource: ajaxSourceURL,
    },
  );
});
