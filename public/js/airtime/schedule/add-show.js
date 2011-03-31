/**
*
*	Schedule Dialog creation methods.
*
*/

//dateText mm-dd-yy
function startDpSelect(dateText, inst) {
	var time, date;

	time = dateText.split("-");
	date = new Date(time[0], time[1] - 1, time[2]);

	$("#add_show_end_date").datepicker("option", "minDate", date);
}

function endDpSelect(dateText, inst) {
	var time, date;

	time = dateText.split("-");
	date = new Date(time[0], time[1] - 1, time[2]);

	$("#add_show_start_date").datepicker( "option", "maxDate", date);
}

function createDateInput(el, onSelect) {
	var date;
   
	el.datepicker({
			minDate: new Date(),
			onSelect: onSelect,
			dateFormat: 'yy-mm-dd' 
		});
}

function autoSelect(event, ui) {

	$("#add_show_hosts-"+ui.item.value).attr("checked", "checked");
	event.preventDefault();
}

function findHosts(request, callback) {
	var search, url;

	url = "/User/get-hosts";
	search = request.term;

	$.post(url, 
		{format: "json", term: search}, 
		
		function(json) {
			callback(json.hosts);
		});
	
}

function setAddShowEvents() {
	
    var form = $("#add-show-form");

	form.find("h3").click(function(){
        $(this).next().toggle();
    });

    if(!form.find("#add_show_repeats").attr('checked')) {
        form.find("#schedule-show-when > fieldset:last").hide();
    }

    if(!form.find("#add_show_record").attr('checked')) {
        form.find("#add_show_rebroadcast").hide();
    }

    if(!form.find("#add_show_rebroadcast").attr('checked')) {
        form.find("#schedule-record-rebroadcast > fieldset:not(:first-child)").hide();
    }

    form.find("#add_show_repeats").click(function(){
        $(this).blur();
        form.find("#schedule-show-when > fieldset:last").toggle();

        //must switch rebroadcast displays
        if(form.find("#add_show_rebroadcast").attr('checked')) {

            if($(this).attr('checked')){
                form.find("#schedule-record-rebroadcast > fieldset:eq(1)").hide();
                form.find("#schedule-record-rebroadcast > fieldset:last").show();
            }
            else {
                form.find("#schedule-record-rebroadcast > fieldset:eq(1)").show();
                form.find("#schedule-record-rebroadcast > fieldset:last").hide();
            }
        }
    });

    form.find("#add_show_record").click(function(){
        $(this).blur();
        form.find("#add_show_rebroadcast").toggle();
    });

    form.find("#add_show_rebroadcast").click(function(){
        $(this).blur();
        if($(this).attr('checked') && !form.find("#add_show_repeats").attr('checked')) {
            form.find("#schedule-record-rebroadcast > fieldset:eq(1)").show();
        }
        else if($(this).attr('checked') && form.find("#add_show_repeats").attr('checked')) {
            form.find("#schedule-record-rebroadcast > fieldset:last").show();
        }
        else {
            form.find("#schedule-record-rebroadcast > fieldset:not(:first-child)").hide();
        }
    });

    form.find("#add_show_repeat_type").change(function(){
        if($(this).val() == 2) {
            form.find("#add_show_day_check-label, #add_show_day_check-element").hide();
        }
        else {
            form.find("#add_show_day_check-label, #add_show_day_check-element").show();
        }
    });

    form.find("#add_show_day_check-label").addClass("block-display");
    form.find("#add_show_day_check-element").addClass("block-display clearfix");
    form.find("#add_show_day_check-element label").addClass("wrapp-label");
    form.find("#add_show_day_check-element br").remove();

    form.find("#add_show_no_end").click(function(){
        form.find("#add_show_end_date").toggle();
    });

	createDateInput(form.find("#add_show_start_date"), startDpSelect);
	createDateInput(form.find("#add_show_end_date"), endDpSelect);

    form.find("#add_show_start_time").timepicker({
        amPmText: ['', '']
    });
    form.find("#add_show_duration").timepicker({
        amPmText: ['', ''],
        defaultTime: '01:00' 
    });

    form.find('input[name^="add_show_rebroadcast_absolute_date"]').datepicker({
		//minDate: new Date(),
		dateFormat: 'yy-mm-dd' 
	});
    form.find('input[name^="add_show_rebroadcast_absolute_time"], input[name^="add_show_rebroadcast_time"]').timepicker({
        amPmText: ['', ''],
        defaultTime: '' 
    });
    
	form.find("#add_show_hosts_autocomplete").autocomplete({
		source: findHosts,
		select: autoSelect,
        delay: 200 
	});

	form.find("#schedule-show-style input").ColorPicker({
       onChange: function (hsb, hex, rgb, el) {
		    $(el).val(hex);
	    },
		onSubmit: function(hsb, hex, rgb, el) {
			$(el).val(hex);
			$(el).ColorPickerHide();
		},
		onBeforeShow: function () {
			$(this).ColorPickerSetColor(this.value);
		}
	});


    form.find("#add-show-close")
		.click(function(event){
            event.stopPropagation();
            event.preventDefault();

            var y = $("#schedule_calendar").width();
            var z = $("#schedule-add-show").width();
            $("#schedule_calendar").width(y+z+50);
            $("#schedule_calendar").fullCalendar('render');
			$("#add-show-form").hide();
            makeAddShowButton();
		});

	form.find(".add-show-submit")
		.click(function(event){
            var addShowButton = $(this);
            if (!addShowButton.hasClass("disabled")){
                addShowButton.addClass("disabled");
            } else {
                return;
            }

            event.preventDefault();

			var data = $("form").serializeArray();

            var hosts = $('#add_show_hosts-element input').map(function() {
                if($(this).attr("checked")) {
                    return $(this).val();
                }
            }).get();

            var days = $('#add_show_day_check-element input').map(function() {
                if($(this).attr("checked")) {
                    return $(this).val();
                }
            }).get();

            var start_date = $("#add_show_start_date").val();
            var end_date = $("#add_show_end_date").val();

            $.post("/Schedule/add-show", {format: "json", data: data, hosts: hosts, days: days}, function(json){
                addShowButton.removeClass("disabled");
                if(json.form) {
                    $("#add-show-form")
                        .empty()
                        .append(json.form);

                    setAddShowEvents();

                    $("#add_show_end_date").val(end_date);
                    $("#add_show_start_date").val(start_date);
                    showErrorSections();    
                }
                else {
                     $("#add-show-form")
                        .empty()
                        .append(json.newForm);

                    setAddShowEvents();
                    scheduleRefetchEvents();
                }
            });
		});
}

function showErrorSections() {

    if($("#schedule-show-what .errors").length > 0) {
        $("#schedule-show-what").show();
    }
    if($("#schedule-show-when .errors").length > 0) {
        $("#schedule-show-when").show();
    }
    if($("#schedule-show-who .errors").length > 0) {
        $("#schedule-show-who").show();
    }
    if($("#schedule-show-style .errors").length > 0) {
        $("#schedule-show-style").show();
    }
}

$(document).ready(function() {

	//setAddShowEvents();
});

$(window).load(function() {

	setAddShowEvents();
});
