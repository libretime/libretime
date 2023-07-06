$(document).ready(function () {
  setSmartBlockEvents();
});

function setSmartBlockEvents() {
  var activeTab = $(".active-tab"),
    form = activeTab.find(".smart-block-form");

  /********** ADD CRITERIA ROW **********/
  form.find("#criteria_add").live("click", function () {
    var div = $('dd[id="sp_criteria-element"]').children("div:visible:last");

    if (div.length == 0) {
      div = $('dd[id="sp_criteria-element"]').children("div:first");
      div.children().removeAttr("disabled");
      div.show();

      appendAddButton();
      appendModAddButton();
      removeButtonCheck();
      disableAndHideDateTimeDropdown(newRowVal);
    } else {
      div.find(".db-logic-label").text("and").css("display", "table");
      div.removeClass("search-row-or").addClass("search-row-and");

      div = div.next().show();

      div.children().removeAttr("disabled");
      div.find(".modifier_add_link").show();

      div = div.next();
      if (div.length === 0) {
        $(this).hide();
      }

      appendAddButton();
      appendModAddButton();
      removeButtonCheck();
      // disableAndHideDateTimeDropdown(newRowVal);
      groupCriteriaRows();
    }
  });

  /********** ADD MODIFIER ROW **********/
  form.find('a[id^="modifier_add"]').live("click", function () {
    var criteria_value = $(this)
      .siblings('select[name^="sp_criteria_field"]')
      .val();

    //make new modifier row
    var newRow = $(this).parent().clone(),
      newRowCrit = newRow.find('select[name^="sp_criteria_field"]'),
      newRowMod = newRow.find('select[name^="sp_criteria_modifier"]'),
      newRowVal = newRow.find('input[name^="sp_criteria_value"]'),
      newRowExtra = newRow.find('input[name^="sp_criteria_extra"]'),
      newRowRemove = newRow.find('a[id^="criteria_remove"]');

    //remove error msg
    if (newRow.children().hasClass("errors sp-errors")) {
      newRow.find('span[class="errors sp-errors"]').remove();
    }

    //hide the critieria field select box
    newRowCrit.addClass("sp-invisible");

    //keep criteria value the same
    newRowCrit.val(criteria_value);

    //reset all other values
    newRowMod.val("0");
    newRowVal.val("");
    newRowExtra.val("");
    disableAndHideExtraField(newRowVal);
    disableAndHideDateTimeDropdown(newRowVal);
    disableAndHideExtraDateTimeDropdown(newRowVal);
    sizeTextBoxes(newRowVal, "sp_extra_input_text", "sp_input_text");

    //remove the 'criteria add' button from new modifier row
    newRow.find("#criteria_add").remove();

    $(this).parent().after(newRow);

    // remove extra spacing from previous row
    newRow.prev().removeClass("search-row-and").addClass("search-row-or");

    reindexElements();
    appendAddButton();
    appendModAddButton();
    removeButtonCheck();
    groupCriteriaRows();
  });

  /********** REMOVE ROW **********/
  form.find('a[id^="criteria_remove"]').live("click", function () {
    var curr = $(this).parent();
    var curr_pos = curr.index();
    var list = curr.parent();
    var list_length = list.find("div:visible").length;
    var count = list_length - curr_pos;
    var next = curr.next();
    var item_to_hide;
    var prev;
    var index;
    //remove error message from current row, if any
    var error_element = curr.find('span[class="errors sp-errors"]');
    if (error_element.is(":visible")) {
      error_element.remove();
    }

    /* In the case that there is only one element we need to remove the
     * date_select drop down.
     */

    if (count == 0) {
      disableAndHideDateTimeDropdown(curr.find(":first-child"), index);
      disableAndHideExtraDateTimeDropdown(curr.find(":first-child"), index);
      disableAndHideExtraField(curr.find(":first-child"), index);
    }

    /* assign next row to current row for all rows below and including
     * the row getting removed
     */

    for (var i = 0; i < count; i++) {
      index = getRowIndex(curr);

      var criteria = next.find('[name^="sp_criteria_field"]').val();
      curr.find('[name^="sp_criteria_field"]').val(criteria);

      var modifier = next.find('[name^="sp_criteria_modifier"]').val();
      populateModifierSelect(curr.find('[name^="sp_criteria_field"]'), false);
      curr.find('[name^="sp_criteria_modifier"]').val(modifier);

      var criteria_value = next.find('[name^="sp_criteria_value"]').val();
      curr.find('[name^="sp_criteria_value"]').val(criteria_value);

      /* if current and next row have the extra criteria value
       * (for 'is in the range' modifier), then assign the next
       * extra value to current and remove that element from
       * next row
       */
      if (
        curr.find('[name^="sp_criteria_extra"]').attr("disabled") !=
          "disabled" &&
        next.find("#extra_criteria").is(":visible")
      ) {
        var criteria_extra = next.find('[name^="sp_criteria_extra"]').val();
        curr.find('[name^="sp_criteria_extra"]').val(criteria_extra);
        disableAndHideExtraField(next.find(":first-child"), getRowIndex(next));

        /* if only the current row has the extra criteria value,
         * then just remove the current row's extra criteria element
         */
      } else if (
        curr.find('[name^="sp_criteria_extra"]').attr("disabled") !=
          "disabled" &&
        next.find("#extra_criteria").not(":visible")
      ) {
        disableAndHideExtraField(curr.find(":first-child"), index);

        /* if only the next row has the extra criteria value,
         * then add the extra criteria element to current row
         * and assign next row's value to it
         */
      } else if (next.find("#extra_criteria").is(":visible")) {
        criteria_extra = next.find('[name^="sp_criteria_extra"]').val();
        enableAndShowExtraField(curr.find(":first-child"), index);
        curr.find('[name^="sp_criteria_extra"]').val(criteria_extra);
      }

      /* if current and next row have the date_time_select_criteria visible
       * then show the current and it from the next row
       */
      if (
        curr.find('[name^="sp_criteria_datetime_select"]').attr("disabled") !=
          "disabled" &&
        next.find("#datetime_select").is(":visible")
      ) {
        var criteria_datetime = next
          .find('[name^="sp_criteria_datetime_select"]')
          .val();
        curr
          .find('[name^="sp_criteria_datetime_select"]')
          .val(criteria_datetime);
        disableAndHideDateTimeDropdown(
          next.find("first-child"),
          getRowIndex(next),
        );
        /* if only the current row has the extra criteria value,
         * then just remove the current row's extra criteria element
         */
      } else if (
        curr.find('[name^="sp_criteria_datetime_select"]').attr("disabled") !=
          "disabled" &&
        next.find("#datetime_select").not(":visible")
      ) {
        disableAndHideDateTimeDropdown(curr.find(":first-child"), index);
        /* if only the next row has date_time_select then just enable it on the current row
         */
      } else if (next.find("#datetime_select").is(":visible")) {
        criteria_datetime = next
          .find('[name^="sp_criteria_datetime_select"]')
          .val();
        enableAndShowDateTimeDropdown(curr.find(":first-child"), index);
        curr
          .find('[name^="sp_criteria_datetime_select"]')
          .val(criteria_datetime);
      }

      /* if current and next row have the extra_date_time_select_criteria visible
       * then show the current and it from the next row
       */
      if (
        curr
          .find('[name^="sp_criteria_extra_datetime_select"]')
          .attr("disabled") != "disabled" &&
        next.find("#extra_datetime_select").is(":visible")
      ) {
        var extra_criteria_datetime = next
          .find('[name^="sp_criteria_extra_datetime_select"]')
          .val();
        curr
          .find('[name^="sp_criteria_extra_datetime_select"]')
          .val(extra_criteria_datetime);
        disableAndHideExtraDateTimeDropdown(
          next.find("first-child"),
          getRowIndex(next),
        );
        /* if only the current row has the extra criteria value,
         * then just remove the current row's extra criteria element
         */
      } else if (
        curr
          .find('[name^="sp_criteria_extra_datetime_select"]')
          .attr("disabled") != "disabled" &&
        next.find("#extra_datetime_select").not(":visible")
      ) {
        disableAndHideExtraDateTimeDropdown(curr.find(":first-child"), index);
        /* if only the next row has date_time_select then just enable it on the current row
         */
      } else if (next.find("#datetime_select").is(":visible")) {
        criteria_datetime = next
          .find('[name^="sp_criteria_extra_datetime_select"]')
          .val();
        enableAndShowExtraDateTimeDropdown(curr.find(":first-child"), index);
        curr
          .find('[name^="sp_criteria_extra_datetime_select"]')
          .val(criteria_datetime);
      }

      /* determine if current row is a modifier row
       * if it is, make the criteria select invisible
       */
      prev = curr.prev();
      if (
        curr.find('[name^="sp_criteria_field"]').val() ==
        prev.find('[name^="sp_criteria_field"]').val()
      ) {
        if (
          !curr
            .find('select[name^="sp_criteria_field"]')
            .hasClass("sp-invisible")
        ) {
          curr
            .find('select[name^="sp_criteria_field"]')
            .addClass("sp-invisible");
        }
      } else {
        if (
          curr
            .find('select[name^="sp_criteria_field"]')
            .hasClass("sp-invisible")
        ) {
          curr
            .find('select[name^="sp_criteria_field"]')
            .removeClass("sp-invisible");
        }
      }

      curr = next;
      next = curr.next();
    }

    /* Disable the last visible row since it holds the values the user removed
     * Reset the values to empty and resize the criteria value textbox
     * in case the row had the extra criteria textbox
     */
    item_to_hide = list.find("div:visible:last");
    item_to_hide.children().attr("disabled", "disabled");
    item_to_hide
      .find('[name^="sp_criteria_datetime_select"]')
      .attr("disabled", "disabled");
    item_to_hide
      .find('[name^="sp_criteria_extra"]')
      .attr("disabled", "disabled");
    item_to_hide
      .find('[name^="sp_criteria_extra_datetime_select"]')
      .attr("disabled", "disabled");
    if (
      item_to_hide
        .find('select[name^="sp_criteria_field"]')
        .hasClass("sp-invisible")
    ) {
      item_to_hide
        .find('select[name^="sp_criteria_field"]')
        .removeClass("sp-invisible");
    }
    item_to_hide
      .find('[name^="sp_criteria_field"]')
      .val(0)
      .end()
      .find('[name^="sp_criteria_modifier"]')
      .val(0)
      .end()
      .find('[name^="sp_criteria_datetime_select"]')
      .end()
      .find('[name^="sp_criteria_value"]')
      .val("")
      .end()
      .find('[name^="sp_criteria_extra"]')
      .val("")
      .find('[name^="sp_criteria_extra_datetime_select"]')
      .end();

    sizeTextBoxes(
      item_to_hide.find('[name^="sp_criteria_value"]'),
      "sp_extra_input_text",
      "sp_input_text",
    );
    item_to_hide.hide();

    list.next().show();

    //check if last row is a modifier row
    var last_row = list.find("div:visible:last");
    if (
      last_row.find('[name^="sp_criteria_field"]').val() ==
      last_row.prev().find('[name^="sp_criteria_field"]').val()
    ) {
      if (
        !last_row
          .find('select[name^="sp_criteria_field"]')
          .hasClass("sp-invisible")
      ) {
        last_row
          .find('select[name^="sp_criteria_field"]')
          .addClass("sp-invisible");
      }
    }

    // always put '+' button on the last enabled row
    appendAddButton();

    reindexElements();

    // always put '+' button on the last modifier row
    appendModAddButton();

    // remove the 'x' button if only one row is enabled
    removeButtonCheck();

    groupCriteriaRows();
  });

  /********** SAVE ACTION **********/
  // moved to spl.js

  /********** GENERATE ACTION **********/
  activeTab.find('button[id="generate_button"]').live("click", function () {
    buttonClickAction("generate", "playlist/smart-block-generate");
  });

  /********** SHUFFLE ACTION **********/
  activeTab.find('button[id="shuffle_button"]').live("click", function () {
    buttonClickAction("shuffle", "playlist/smart-block-shuffle");
  });

  /********** CHANGE PLAYLIST TYPE **********/
  form.find('dd[id="sp_type-element"]').live("change", function () {
    //buttonClickAction('generate', 'playlist/empty-content');
    $(".active-tab").find('button[id="save_button"]').click();
    setupUI();
    AIRTIME.library.checkAddButton();
  });

  /********** LIMIT CHANGE *************/
  form.find('select[id="sp_limit_options"]').live("change", function () {
    var limVal = form.find('input[id="sp_limit_value"]');
    if ($(this).val() === "remaining") {
      disableAndHideLimitValue();
    } else {
      enableAndShowLimitValue();
    }
  });

  /********** CRITERIA CHANGE **********/
  form
    .find(
      'select[id^="sp_criteria"]:not([id^="sp_criteria_modifier"]):not([id^="sp_criteria_datetime"]):not([id^="sp_criteria_extra_datetime"]):not([id^="sp_criteria_value"])',
    )
    .live("change", function () {
      var index = getRowIndex($(this).parent());
      //need to change the criteria value for any modifier rows
      var critVal = $(this).val();
      var divs = $(this).parent().nextAll(":visible");
      $.each(divs, function (i, div) {
        var critSelect = $(div).children('select[id^="sp_criteria_field"]');
        if (critSelect.hasClass("sp-invisible")) {
          critSelect.val(critVal);
          /* If the select box is visible we know the modifier rows
           * have ended
           */
        } else {
          return false;
        }
      });

      // disable extra field and hide the span
      disableAndHideExtraField($(this), index);
      disableAndHideDateTimeDropdown($(this), index);
      disableAndHideExtraDateTimeDropdown($(this), index);

      if (
        $("#sp_criteria_field_" + index + " option:selected").val() ===
        "track_type_id"
      ) {
        populateTracktypeSelect(this, false);
      } else {
        disableAndHideTracktypeDropdown($(this), index);
        populateModifierSelect(this, true);
      }
    });

  /********** MODIFIER CHANGE **********/
  form.find('select[id^="sp_criteria_modifier"]').live("change", function () {
    var criteria_value = $(this).next(),
      index_num = getRowIndex($(this).parent());

    if ($(this).val().match("before|after")) {
      enableAndShowDateTimeDropdown(criteria_value, index_num);
      console.log($(this).val());
    } else {
      disableAndHideDateTimeDropdown(criteria_value, index_num);
      disableAndHideExtraDateTimeDropdown(criteria_value, index_num);
    }

    if ($(this).val().match("is in the range")) {
      enableAndShowExtraField(criteria_value, index_num);
    } else {
      disableAndHideExtraField(criteria_value, index_num);
    }
    if ($(this).val().match("between")) {
      enableAndShowExtraField(criteria_value, index_num);
      enableAndShowDateTimeDropdown(criteria_value, index_num);
      enableAndShowExtraDateTimeDropdown(criteria_value, index_num);
    } else {
      disableAndHideExtraDateTimeDropdown(criteria_value, index_num);
    }

    var get_crit_field = $(this).siblings(":first-child");
    var crit_field = get_crit_field[0]["id"];
    if ($("#" + crit_field + " option:selected").val() === "track_type_id") {
      if ($(this).val() == "is" || $(this).val() == "is not") {
        enableAndShowTracktypeDropdown(criteria_value, index_num);
      } else {
        disableAndHideTracktypeDropdown(criteria_value, index_num);
      }
    }
  });

  setupUI();
  appendAddButton();
  appendModAddButton();
  removeButtonCheck();
}

function getRowIndex(ele) {
  var id = ele.find('[name^="sp_criteria_field"]').attr("id"),
    delimiter = "_",
    start = 3,
    tokens = id.split(delimiter).slice(start),
    index = tokens.join(delimiter);

  return index;
}

/* This function appends a '+' button for the last
 * modifier row of each criteria.
 * If there are no modifier rows, the '+' button
 * remains at the criteria row
 */
function appendModAddButton() {
  var divs = $(".active-tab .smart-block-form")
    .find('div select[name^="sp_criteria_modifier"]')
    .parent(":visible");
  $.each(divs, function (i, div) {
    if (i > 0) {
      /* If the criteria field is hidden we know it is a modifier row
       * and can hide the previous row's modifier add button
       */
      if (
        $(div)
          .find('select[name^="sp_criteria_field"]')
          .hasClass("sp-invisible")
      ) {
        $(div).prev().find('a[id^="modifier_add"]').addClass("sp-invisible");
      } else {
        $(div).prev().find('a[id^="modifier_add"]').removeClass("sp-invisible");
      }
    }

    //always add modifier add button to the last row
    if (i + 1 == divs.length) {
      $(div).find('a[id^="modifier_add"]').removeClass("sp-invisible");
    }
  });
}

/* This function re-indexes all the form elements.
 * We need to do this everytime a row gets deleted
 */
function reindexElements() {
  var divs = $(".active-tab .smart-block-form")
      .find('div select[name^="sp_criteria_field"]')
      .parent(),
    index = 0,
    modIndex = 0;
  /* Hide all logic labels
   * We will re-add them as each row gets indexed
   */
  $(".db-logic-label").text("").hide();

  $.each(divs, function (i, div) {
    if (i > 0 && index < 26) {
      /* If the current row's criteria field is hidden we know it is
       * a modifier row
       */
      if (
        $(div)
          .find('select[name^="sp_criteria_field"]')
          .hasClass("sp-invisible")
      ) {
        if ($(div).is(":visible")) {
          $(div).prev().find(".db-logic-label").text($.i18n._("or")).show();
        }
        modIndex++;
      } else {
        if ($(div).is(":visible")) {
          $(div).prev().find(".db-logic-label").text($.i18n._("and")).show();
        }
        index++;
        modIndex = 0;
      }

      $(div)
        .find('select[name^="sp_criteria_field"]')
        .attr("name", "sp_criteria_field_" + index + "_" + modIndex);
      $(div)
        .find('select[name^="sp_criteria_field"]')
        .attr("id", "sp_criteria_field_" + index + "_" + modIndex);
      $(div)
        .find('select[name^="sp_criteria_modifier"]')
        .attr("name", "sp_criteria_modifier_" + index + "_" + modIndex);
      $(div)
        .find('select[name^="sp_criteria_modifier"]')
        .attr("id", "sp_criteria_modifier_" + index + "_" + modIndex);
      $(div)
        .find('input[name^="sp_criteria_value"]')
        .attr("name", "sp_criteria_value_" + index + "_" + modIndex);
      $(div)
        .find('input[name^="sp_criteria_value"]')
        .attr("id", "sp_criteria_value_" + index + "_" + modIndex);
      $(div)
        .find('select[name^="sp_criteria_value"]')
        .attr("name", "sp_criteria_value_" + index + "_" + modIndex);
      $(div)
        .find('select[name^="sp_criteria_value"]')
        .attr("id", "sp_criteria_value_" + index + "_" + modIndex);
      $(div)
        .find('input[name^="sp_criteria_extra"]')
        .attr("name", "sp_criteria_extra_" + index + "_" + modIndex);
      $(div)
        .find('input[name^="sp_criteria_extra"]')
        .attr("id", "sp_criteria_extra_" + index + "_" + modIndex);
      $(div)
        .find('a[name^="modifier_add"]')
        .attr("id", "modifier_add_" + index);
      $(div)
        .find('a[id^="criteria_remove"]')
        .attr("id", "criteria_remove_" + index + "_" + modIndex);
    } else if (i > 0) {
      $(div).remove();
    }
  });
}

function buttonClickAction(clickType, url) {
  var data = $(".active-tab .smart-block-form").serializeArray(),
    obj_id = $(".active-tab .obj_id").val();

  enableLoadingIcon();
  $.post(
    url,
    {
      format: "json",
      data: data,
      obj_id: obj_id,
      obj_type: "block",
      modified: AIRTIME.playlist.getModified(),
    },
    function (data) {
      callback(data, clickType);
      disableLoadingIcon();
    },
  );
}

function setupUI() {
  var activeTab = $(".active-tab"),
    playlist_type = activeTab.find("input:radio[name=sp_type]:checked").val();

  /* Activate or Deactivate shuffle button
   * It is only active if playlist is not empty
   */
  var sortable = activeTab.find(".spl_sortable"),
    plContents = sortable.children(),
    shuffleButton = activeTab.find('button[name="shuffle_button"]'),
    generateButton = activeTab.find('button[name="generate_button"]'),
    fadesButton = activeTab.find("#spl_crossfade, #pl-bl-clear-content");
  if (activeTab.find("#sp_limit_options").val() == "remaining") {
    disableAndHideLimitValue();
  }

  if (!plContents.hasClass("spl_empty")) {
    if (shuffleButton.hasClass("ui-state-disabled")) {
      shuffleButton.removeClass("ui-state-disabled");
      shuffleButton.removeAttr("disabled");
    }
  } else if (!shuffleButton.hasClass("ui-state-disabled")) {
    shuffleButton.addClass("ui-state-disabled");
    shuffleButton.attr("disabled", "disabled");
  }

  if (activeTab.find(".obj_type").val() == "block") {
    if (playlist_type == "1") {
      shuffleButton.removeAttr("disabled");
      generateButton.removeAttr("disabled");
      generateButton.html($.i18n._("Generate"));
      fadesButton.removeAttr("disabled");
      //sortable.children().show();
    } else {
      shuffleButton.attr("disabled", "disabled");
      generateButton.html($.i18n._("Preview"));
      fadesButton.attr("disabled", "disabled");
      //sortable.children().hide();
    }
  }

  $(".playlist_type_help_icon").qtip({
    content: {
      text:
        $.i18n._(
          "A static smart block will save the criteria and generate the block content immediately. This allows you to edit and view it in the Library before adding it to a show.",
        ) +
        "<br /><br />" +
        $.i18n._(
          "A dynamic smart block will only save the criteria. The block content will get generated upon adding it to a show. You will not be able to view and edit the content in the Library.",
        ),
    },
    hide: {
      delay: 500,
      fixed: true,
    },
    style: {
      border: {
        width: 0,
        radius: 4,
      },
      classes: "ui-tooltip-dark ui-tooltip-rounded",
    },
    position: {
      my: "left bottom",
      at: "right center",
    },
  });

  $(".repeat_tracks_help_icon").qtip({
    content: {
      text: sprintf(
        $.i18n._(
          "The desired block length will not be reached if %s cannot find enough unique tracks to match your criteria. Enable this option if you wish to allow tracks to be added multiple times to the smart block.",
        ),
        PRODUCT_NAME,
      ),
    },
    hide: {
      delay: 500,
      fixed: true,
    },
    style: {
      border: {
        width: 0,
        radius: 4,
      },
      classes: "ui-tooltip-dark ui-tooltip-rounded",
    },
    position: {
      my: "left bottom",
      at: "right center",
    },
  });

  $(".overflow_tracks_help_icon").qtip({
    content: {
      text: sprintf(
        $.i18n._(
          "<p>If this option is unchecked, the smartblock will schedule as many tracks as can be played out <strong>in their entirety</strong> within the specified duration. This will usually result in audio playback that is slightly less than the specified duration.</p><p>If this option is checked, the smartblock will also schedule one final track which will overflow the specified duration. This final track may be cut off mid-way if the show into which the smartblock is added finishes.</p>",
        ),
        PRODUCT_NAME,
      ),
    },
    hide: {
      delay: 500,
      fixed: true,
    },
    style: {
      border: {
        width: 0,
        radius: 4,
      },
      classes: "ui-tooltip-dark ui-tooltip-rounded",
    },
    position: {
      my: "left bottom",
      at: "right center",
    },
  });

  activeTab
    .find(".collapsible-header")
    .off("click")
    .on("click", function () {
      $(this).toggleClass("visible");
      $(".smart-block-advanced").toggle();
    });
}

function enableAndShowTracktypeDropdown(valEle, index) {
  console.log("tracktype show");
  $("#sp_criteria_value_" + index).replaceWith(
    '<select name="sp_criteria_value_' +
      index +
      '" id="sp_criteria_value_' +
      index +
      '" class="input_select sp_input_select"></select>',
  );
  $.each(stringTracktypeOptions, function (key, value) {
    $("#sp_criteria_value_" + index).append(
      $("<option></option>").attr("value", key).text(value),
    );
  });
}

function disableAndHideTracktypeDropdown(valEle, index) {
  console.log("tracktype hide");
  $("#sp_criteria_value_" + index).replaceWith(
    '<input type="text" name="sp_criteria_value_' +
      index +
      '" id="sp_criteria_value_' +
      index +
      '" value="" class="input_text sp_input_text">',
  );
}

/* Utilizing jQuery this function finds the #datetime_select element on the given row
 * and shows the criteria drop-down
 */
function enableAndShowDateTimeDropdown(valEle, index) {
  console.log("datetime show");
  var spanDatetime = valEle.nextAll("#datetime_select");
  spanDatetime
    .children("#sp_criteria_datetime_select_" + index)
    .removeAttr("disabled");
  spanDatetime
    .children("#sp_criteria_extra_datetime_select_" + index)
    .removeAttr("disabled");
  spanDatetime.show();

  //make value input smaller since we have extra element now
  var criteria_val = $("#sp_criteria_value_" + index);
  sizeTextBoxes(criteria_val, "sp_input_text", "sp_extra_input_text");
}

/* Utilizing jQuery this function finds the #datetime_select element on the given row
 * and hides the datetime criteria drop-down
 */

function disableAndHideDateTimeDropdown(valEle, index) {
  console.log("datetime hide");
  var spanDatetime = valEle.nextAll("#datetime_select");
  spanDatetime
    .children("#sp_criteria_datetime_select_" + index)
    .val("")
    .attr("disabled", "disabled");
  spanDatetime.hide();

  //make value input larger since we don't have extra field now
  var criteria_value = $("#sp_criteria_value_" + index);
  sizeTextBoxes(criteria_value, "sp_extra_input_text", "sp_input_text");
}

/* Utilizing jQuery this function finds the #datetime_select element on the given row
 * and shows the criteria drop-down
 */
function enableAndShowExtraDateTimeDropdown(valEle, index) {
  console.log("datetime show");
  var spanDatetime = valEle.nextAll("#extra_datetime_select");
  spanDatetime
    .children("#sp_criteria_extra_datetime_select_" + index)
    .removeAttr("disabled");
  spanDatetime.show();

  //make value input smaller since we have extra element now
  var criteria_val = $("#sp_criteria_value_" + index);
  sizeTextBoxes(criteria_val, "sp_input_text", "sp_extra_input_text");
}
/* Utilizing jQuery this function finds the #datetime_select element on the given row
 * and hides the datetime criteria drop-down
 */

function disableAndHideExtraDateTimeDropdown(valEle, index) {
  console.log("datetime hide");
  var spanDatetime = valEle.nextAll("#extra_datetime_select");
  spanDatetime
    .children("#sp_criteria_extra_datetime_select_" + index)
    .val("")
    .attr("disabled", "disabled");
  spanDatetime.hide();

  //make value input larger since we don't have extra field now
  var criteria_value = $("#sp_criteria_value_" + index);
  sizeTextBoxes(criteria_value, "sp_extra_input_text", "sp_input_text");
}

function enableAndShowExtraField(valEle, index) {
  var spanExtra = valEle.nextAll("#extra_criteria");
  console.log("shown");
  spanExtra.children("#sp_criteria_extra_" + index).removeAttr("disabled");
  spanExtra.show();

  //make value input smaller since we have extra element now
  var criteria_val = $("#sp_criteria_value_" + index);
  sizeTextBoxes(criteria_val, "sp_input_text", "sp_extra_input_text");
}

function disableAndHideExtraField(valEle, index) {
  var spanExtra = valEle.nextAll("#extra_criteria");
  spanExtra
    .children("#sp_criteria_extra_" + index)
    .val("")
    .attr("disabled", "disabled");
  spanExtra.hide();
  console.log("hidden");

  //make value input larger since we don't have extra field now
  var criteria_value = $("#sp_criteria_value_" + index);
  sizeTextBoxes(criteria_value, "sp_extra_input_text", "sp_input_text");
}
function disableAndHideLimitValue() {
  console.log("we hide it");
  $("#sp_limit_value").hide();
}
function enableAndShowLimitValue() {
  console.log("we show it");
  $("#sp_limit_value").show();
}

function sizeTextBoxes(ele, classToRemove, classToAdd) {
  if (ele.hasClass(classToRemove)) {
    ele.removeClass(classToRemove).addClass(classToAdd);
  }
}

function populateModifierSelect(e, popAllMods) {
  var criteria_type = getCriteriaOptionType(e),
    index = getRowIndex($(e).parent()),
    divs;

  if (popAllMods) {
    index = index.substring(0, 1);
  }
  divs = $(e)
    .parents()
    .find('select[id^="sp_criteria_modifier_' + index + '"]');

  $.each(divs, function (i, div) {
    $(div).children().remove();

    if (criteria_type == "s") {
      $.each(stringCriteriaOptions, function (key, value) {
        $(div).append($("<option></option>").attr("value", key).text(value));
      });
    } else if (criteria_type == "d") {
      $.each(dateTimeCriteriaOptions, function (key, value) {
        $(div).append($("<option></option>").attr("value", key).text(value));
      });
    } else if (criteria_type == "tt") {
      $.each(stringIsNotOptions, function (key, value) {
        $(div).append($("<option></option>").attr("value", key).text(value));
      });
    } else {
      $.each(numericCriteriaOptions, function (key, value) {
        $(div).append($("<option></option>").attr("value", key).text(value));
      });
    }
  });
}

function populateTracktypeSelect(e, popAllMods) {
  var criteria_type = getTracktype(e),
    index = getRowIndex($(e).parent()),
    divs;

  if (popAllMods) {
    index = index.substring(0, 1);
  }
  divs = $(e)
    .parents()
    .find('select[id^="sp_criteria_modifier_' + index + '"]');
  $.each(divs, function (i, div) {
    $(div).children().remove();
    $.each(stringIsNotOptions, function (key, value) {
      $(div).append($("<option></option>").attr("value", key).text(value));
    });
  });
}

function getCriteriaOptionType(e) {
  var criteria = $(e).val();
  return criteriaTypes[criteria];
}

function getTracktype(e) {
  var type = $(e).val();
  return stringTracktypeOptions[type];
}

function callback(json, type) {
  var dt = $('table[id="library_display"]').dataTable(),
    form = $(".active-tab .smart-block-form");

  if (json.modified !== undefined) {
    AIRTIME.playlist.setModified(json.modified);
  }

  if (type == "shuffle" || type == "generate") {
    if (json.error !== undefined) {
      alert(json.error);
    }
    if (json.result == "0") {
      if (type == "shuffle") {
        form.find(".success").text($.i18n._("Smart block shuffled"));
      } else if (type == "generate") {
        form
          .find(".success")
          .text($.i18n._("Smart block generated and criteria saved"));
        //redraw library table so the length gets updated
        dt.fnStandingRedraw();
      }

      AIRTIME.playlist.playlistResponse(json);

      form.find(".success").show();
    }
    removeButtonCheck();

    form.find(".smart-block-form").removeClass("closed");
  } else {
    if (json.result == "0") {
      $(".active-tab #sp-success-saved")
        .text($.i18n._("Smart block saved"))
        .show();

      AIRTIME.playlist.playlistResponse(json);

      //redraw library table so the length gets updated
      dt.fnStandingRedraw();
    } else {
      AIRTIME.playlist.playlistResponse(json);
      removeButtonCheck();
    }
    form.find(".smart-block-form").removeClass("closed");
  }
  setTimeout(removeSuccessMsg, 5000);
}

function appendAddButton() {
  /*
    var add_button = "<a class='btn btn-small' id='criteria_add'>" +
                     "<i class='icon-white icon-plus'></i>Add Criteria</a>";
    var rows = $('.active-tab .smart-block-form'),
        enabled = rows.find('select[name^="sp_criteria_field"]:enabled');

    rows.find('#criteria_add').remove();

    if (enabled.length > 1) {
        rows.find('select[name^="sp_criteria_field"]:enabled:last')
            .siblings('a[id^="criteria_remove"]')
            .after(add_button);
    } else {
        enabled.siblings('span[id="extra_criteria"]')
               .after(add_button);
    }
    */
}

function removeButtonCheck() {
  /*
    var rows = $('.active-tab dd[id="sp_criteria-element"]').children('div'),
        enabled = rows.find('select[name^="sp_criteria_field"]:enabled'),
        rmv_button = enabled.siblings('a[id^="criteria_remove"]');
    if (enabled.length == 1) {
        rmv_button.attr('disabled', 'disabled');
        rmv_button.hide();
    } else {
        rmv_button.removeAttr('disabled');
        rmv_button.show();
    }*/
}

function enableLoadingIcon() {
  // Disable the default overlay style
  $.blockUI.defaults.overlayCSS = {};
  $(".side_playlist.active-tab").block({
    //message: $.i18n._("Processing..."),
    message: $.i18n._(""),
    theme: true,
    allowBodyStretch: true,
    applyPlatformOpacityRules: false,
  });
}

function disableLoadingIcon() {
  $(".side_playlist.active-tab").unblock();
}

function groupCriteriaRows() {
  // check whether rows should be "grouped" and shown with an "or" "logic label", or separated by an "and" "logic label"
  var visibleRows = $("#sp_criteria-element > div:visible"),
    prevRowGroup = "0";

  visibleRows.each(function (index) {
    if (index > 0) {
      var fieldId = $(this).find('select[id^="sp_criteria_field"]').attr("id");
      var currRowGroup = fieldId[fieldId.length - 3];
      if (currRowGroup === prevRowGroup) {
        $(this).prev().addClass("search-row-or").removeClass("search-row-and");
      } else {
        $(this).prev().addClass("search-row-and").removeClass("search-row-or");
      }
      prevRowGroup = currRowGroup;
    }
  });

  // ensure spacing below last visible row
  $("#sp_criteria-element > div:visible:last")
    .addClass("search-row-and")
    .removeClass("search-row-or");
}

// We need to know if the criteria value will be a string
// or numeric value in order to populate the modifier
// select list
var criteriaTypes = {
  0: "",
  album_title: "s",
  bit_rate: "n",
  bpm: "n",
  composer: "s",
  conductor: "s",
  copyright: "s",
  cuein: "n",
  cueout: "n",
  description: "s",
  artist_name: "s",
  encoded_by: "s",
  utime: "d",
  mtime: "d",
  lptime: "d",
  genre: "s",
  isrc_number: "s",
  label: "s",
  language: "s",
  length: "n",
  mime: "s",
  mood: "s",
  owner_id: "s",
  replay_gain: "n",
  sample_rate: "n",
  track_title: "s",
  track_number: "n",
  info_url: "s",
  year: "n",
  track_type_id: "tt",
};

var stringCriteriaOptions = {
  0: $.i18n._("Select modifier"),
  contains: $.i18n._("contains"),
  "does not contain": $.i18n._("does not contain"),
  is: $.i18n._("is"),
  "is not": $.i18n._("is not"),
  "starts with": $.i18n._("starts with"),
  "ends with": $.i18n._("ends with"),
};

var numericCriteriaOptions = {
  0: $.i18n._("Select modifier"),
  is: $.i18n._("is"),
  "is not": $.i18n._("is not"),
  "is greater than": $.i18n._("is greater than"),
  "is less than": $.i18n._("is less than"),
  "is in the range": $.i18n._("is in the range"),
};

var dateTimeCriteriaOptions = {
  0: $.i18n._("Select modifier"),
  before: $.i18n._("before"),
  after: $.i18n._("after"),
  between: $.i18n._("between"),
  is: $.i18n._("is"),
  "is not": $.i18n._("is not"),
  "is greater than": $.i18n._("is greater than"),
  "is less than": $.i18n._("is less than"),
  "is in the range": $.i18n._("is in the range"),
};

var stringIsNotOptions = {
  0: $.i18n._("Select modifier"),
  is: $.i18n._("is"),
  "is not": $.i18n._("is not"),
};

let tracktypes = {};

for (var key in TRACKTYPES) {
  if (TRACKTYPES.hasOwnProperty(key)) {
    tracktypes[key] = TRACKTYPES[key].name;
  }
}

var stringTracktypeOptions = Object.assign(
  { 0: "Select Track Type" },
  tracktypes,
);
