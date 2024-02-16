function populateForm(entries) {
  $(".errors").remove();
  $(".success").remove();

  $("#tracktype_id").val(entries.id);
  $("#code").val(entries.code);
  $("#type_name").val(entries.type_name);
  $("#description").val(entries.description);

  if (entries.visibility) {
    var visibility_value = 1;
  } else {
    var visibility_value = 0;
  }
  $("#visibility").val(visibility_value);

  $("#analyze_cue_points").prop("checked", entries.analyze_cue_points);
}

function rowClickCallback(row_id) {
  $.ajax({
    url: baseUrl + "Tracktype/get-tracktype-data/id/" + row_id + "/format/json",
    dataType: "json",
    success: function (data) {
      populateForm(data.entries);
      $("#tracktype_details").css("visibility", "visible");
    },
  });
}

function removeTracktypeCallback(row_id, nRow) {
  if (confirm($.i18n._("Are you sure you want to delete this tracktype?"))) {
    $.ajax({
      url: baseUrl + "Tracktype/remove-tracktype/id/" + row_id + "/format/json",
      dataType: "text",
      success: function (data) {
        var o = $("#tracktypes_datatable").dataTable().fnDeleteRow(nRow);
      },
    });
  }
}

function rowCallback(nRow, aData, iDisplayIndex) {
  $(nRow).click(function () {
    rowClickCallback(aData["id"]);
  });
  if (aData["delete"] != "self") {
    $("td:eq(4)", nRow)
      .append('<span class="ui-icon ui-icon-closethick"></span>')
      .children("span")
      .click(function (e) {
        e.stopPropagation();
        removeTracktypeCallback(aData["id"], nRow);
      });
  } else {
    $("td:eq(4)", nRow)
      .empty()
      .append('<span class="ui-icon ui-icon-closethick"></span>')
      .children("span")
      .click(function (e) {
        e.stopPropagation();
        alert("Can't delete yourself!");
      });
  }

  if (aData["visibility"] == "1") {
    $("td:eq(3)", nRow).html($.i18n._("Enabled"));
  } else {
    $("td:eq(3)", nRow).html($.i18n._("Disabled"));
  }

  return nRow;
}

function populateTracktypeTable() {
  var dt = $("#tracktypes_datatable");
  dt.dataTable({
    bProcessing: true,
    bServerSide: true,
    sAjaxSource:
      baseUrl + "Tracktype/get-tracktype-data-table-info/format/json",
    fnServerData: function (sSource, aoData, fnCallback) {
      $.ajax({
        dataType: "json",
        type: "POST",
        url: sSource,
        data: aoData,
        success: fnCallback,
      });
    },
    fnRowCallback: rowCallback,
    aoColumns: [
      /* Id */ {
        sName: "id",
        bSearchable: false,
        bVisible: false,
        mDataProp: "id",
      },
      /* code */ { sName: "code", mDataProp: "code" },
      /* type_name */ { sName: "type_name", mDataProp: "type_name" },
      /* description */ { sName: "description", mDataProp: "description" },
      /* visibility */ {
        sName: "visibility",
        bSearchable: false,
        mDataProp: "visibility",
      },
      /* del button */ {
        sName: "null as delete",
        bSearchable: false,
        bSortable: false,
        mDataProp: "delete",
      },
    ],
    bJQueryUI: true,
    bAutoWidth: false,
    bLengthChange: false,
    oLanguage: getDatatablesStrings({
      sEmptyTable: $.i18n._("No track types were found."),
      sEmptyTable: $.i18n._("No track types found"),
      sZeroRecords: $.i18n._("No matching track types found"),
      sInfo: $.i18n._("Showing _START_ to _END_ of _TOTAL_ track types"),
      sInfoEmpty: $.i18n._("Showing 0 to 0 of 0 track types"),
      sInfoFiltered: $.i18n._("(filtered from _MAX_ total track types)"),
    }),
    sDom: '<"H"lf<"dt-process-rel"r>><"#tracktype_list_inner_wrapper"t><"F"ip>',
  });
}

function sizeFormElements() {
  $("dt[id$='label']").addClass("tracktype-form-label");
  $("dd[id$='element']").addClass("tracktype-form-element");
}

function initTracktypeData() {
  var visibility = $("#visibility");

  var table = $("#tracktypes_datable"); //.DataTable();
  $(".datatable tbody").on("click", "tr", function () {
    $(this).parent().find("tr.selected").removeClass("selected");
    $(this).addClass("selected");
  });

  $("#button").click(function () {
    table.row(".selected").remove().draw(false);
  });

  var newTracktype = {
    code: "",
    type_name: "",
    description: "",
    visibility: "1",
    id: "",
    analyze_cue_points: true,
  };

  $("#add_tracktype_button").live("click", function () {
    populateForm(newTracktype);
    $("#tracktype_details").css("visibility", "visible");
  });
}

$(document).ready(function () {
  populateTracktypeTable();
  initTracktypeData();

  $("#save_tracktype").live("click", function () {
    var data = $("#tracktype_form").serialize();
    var url = baseUrl + "Tracktype/add-tracktype";

    $.post(url, { format: "json", data: data }, function (json) {
      if (json.valid === "true") {
        $("#content").empty().append(json.html);
        populateTracktypeTable();
        init(); // Reinitialize
      } else {
        //if form is invalid we only need to redraw the form
        $("#tracktype_form")
          .empty()
          .append($(json.html).find("#tracktype_form").children());
      }
      setTimeout(removeSuccessMsg, 5000);
      sizeFormElements();
    });
  });

  sizeFormElements();
});
