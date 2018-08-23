/* Start : Used to load case document list */
function list_teamdocument(node) {
    var team_id = jQuery("#team_id").val();
    var team_loc = jQuery("#team_loc").val();
    if (!node) {
        node = 0;
    }
    location.href = baseUrl + 'team-documents/index&team_id=' + team_id + '&team_loc=' + team_loc + '&node_id=' + node;
}

function export_team_task() {
    $('#team-task-form').html(null);
		$('#team-task-form').html($('#media_container').find('.table-responsive').html());
		setTimeout(function(){
			$('#team-task-form').submit();
		},100);
   // $('#team-task-form').submit();
    /*$.ajax({
     url: httpPath + "export-excel/team-tasks-export",
     type:'post',
     data:serilize_form,
     cache: false,
     dataType: 'html',
     success: function (data) {
     return false;
     }
     });*/

}

function loadProjects() {
    var team_id = jQuery('#team_id').val();
    var team_loc = jQuery('#team_loc').val();
    location.href = baseUrl + 'team-projects/index&team_id=' + team_id + '&team_loc=' + team_loc + '&active=active';
}
/* Load TeamTask landing page HNL */
function loadTasks() {
    var team_id = jQuery('#team_id').val();
    var team_loc = jQuery('#team_loc').val();
    location.href = baseUrl + 'team-tasks/index&team_id=' + team_id + '&team_loc=' + team_loc;
}
/* Apply Team Priority Task */
function applyteampriority(task_ids, team_id, team_loc) {

    if (!$("#applyteampriority").length) {
        $('body').append("<div id='applyteampriority'></div>");
    }

    $.ajax({
        url: httpPath + "team-projects/loadteampriority&task_id=" + task_ids + "&team_id=" + team_id + "&team_loc=" + team_loc,
        // async: false,
        cache: false,
        dataType: 'html',
        success: function (data) {
            if (data != "") {
                $('#applyteampriority').html(data);

                $("#applyteampriority").dialog({
                    autoOpen: true,
                    resizable: false,
                    width: "50em",
                    height: 302,
                    modal: true,
                    create: function (event, ui) {
                        $('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                        $('.ui-dialog-titlebar-close').attr("title", "Close");
                        $('.ui-dialog-titlebar-close').attr("aria-label", "Close");
                    },
                    beforeClose: function (event) {
                        if (event.keyCode == 27)
                            trigger = 'esc';
                        if (trigger != 'Update')
                            checkformstatus(event);
                    },
                    buttons: [
                        {
                            text: "Cancel",
                            "title": "Cancel",
                            "class": 'btn btn-primary',
                            click: function () {
                                trigger = 'Cancel';
                                $(this).dialog("close");
                            }
                        },
                        {
                            text: "Update",
                            "title": "Update",
                            "class": 'btn btn-primary',
                            click: function ()
                            {
                                trigger = 'Update';
                                var select = $('#loadteampriority').val();

                                if ($('#remove_team_priority').is(':checked'))
                                    var remove = 'checked';
                                else
                                    var remove = '';

                                //if(!select && select != 'undefined') {
                                //		return false;
                                //	}
                                //var remove = false;

                                $.ajax({
                                    url: httpPath + "team-projects/updateteampriority",
                                    type: 'post',
                                    data: {'task_id': task_ids, 'team_id': team_id, 'team_loc': team_loc, 'team_prioriy': select, 'remove_team_priority': remove},
                                    cache: false,
                                    dataType: 'html',
                                    success: function (data) {
                                        $(".ui-dialog-titlebar-close").trigger('click');
                                        location.reload();
                                    }
                                });

                                $(this).dialog("close");
                            }
                        }
                    ],
                    submit: [

                    ],
                    close: function () {
                        $(this).dialog('destroy').remove();
                        // Close code here (incidentally, same as Cancel code)
                    }
                }).parent().find('.ui-dialog-title').html("Apply Team Priority");
                $("#applyteampriority").dialog("open");
            }
        }
    });
}

function changeteampriority(task_id) {


}
function task_assignments()
{
    var team_id = jQuery("#team_id").val();
    var team_loc = jQuery("#team_loc").val();
    location.href = baseUrl + 'team-overview/taskassignments&team_id=' + team_id + '&team_loc=' + team_loc;
}
function task_assignments_completed()
{
    var team_id = jQuery("#team_id").val();
    var team_loc = jQuery("#team_loc").val();
    location.href = baseUrl + 'team-overview/taskassigncompleted&team_id=' + team_id + '&team_loc=' + team_loc;
}
function team_distribute()
{
    var team_id = jQuery("#team_id").val();
    var team_loc = jQuery("#team_loc").val();
    location.href = baseUrl + 'team-overview/taskdistribute&team_id=' + team_id + '&team_loc=' + team_loc;
}
function followup_distribute()
{
    var team_id = jQuery("#team_id").val();
    var team_loc = jQuery("#team_loc").val();
    location.href = baseUrl + 'team-overview/followupdistribute&team_id=' + team_id + '&team_loc=' + team_loc;
}

function assignby_projectsize()
{
    var team_id = jQuery("#team_id").val();
    var team_loc = jQuery("#team_loc").val();
    location.href = baseUrl + 'team-overview/assignbyprojectsize&team_id=' + team_id + '&team_loc=' + team_loc;
}

jQuery(document).ready(function () {

    $('input[type=radio][name=bulkcompletetask]').change(function () {
        if (this.value == 'alltask') {
            $('#bulkcompletetask-closed-dialog').find('label[for="rdo_selectedcompletetask"]').removeClass('focus');
            $('#bulkcompletetask-closed-dialog').find('label[for="rdo_bulkcompletetask"]').addClass('focus');
        } else if (this.value == 'selectedtask') {
            $('#bulkcompletetask-closed-dialog').find('label[for="rdo_selectedcompletetask"]').addClass('focus');
            $('#bulkcompletetask-closed-dialog').find('label[for="rdo_bulkcompletetask"]').removeClass('focus');
        }
    });

    $('input[type=radio][name=bulkunassigntask]').change(function () {
        if (this.value == 'alltask') {
            $('#bulkunassign-dialog').find('label[for="rdo_selectedunassigntask"]').removeClass('focus');
            $('#bulkunassign-dialog').find('label[for="rdo_bulkunassigntask"]').addClass('focus');
        } else if (this.value == 'selectedtask') {
            $('#bulkunassign-dialog').find('label[for="rdo_selectedunassigntask"]').addClass('focus');
            $('#bulkunassign-dialog').find('label[for="rdo_bulkunassigntask"]').removeClass('focus');
        }
    });

    $('input[type=radio][name=bulktransittask]').change(function () {
        if (this.value == 'alltask') {
            $('#bulktransition-dialog').find('label[for="bulktransitionselectedtask"]').removeClass('focus');
            $('#bulktransition-dialog').find('label[for="bulktransitionalltask"]').addClass('focus');
        } else if (this.value == 'selectedtask') {
            $('#bulktransition-dialog').find('label[for="bulktransitionselectedtask"]').addClass('focus');
            $('#bulktransition-dialog').find('label[for="bulktransitionalltask"]').removeClass('focus');
        }
    });

    $('input[type=radio][name=bulkassigntask]').change(function () {
        if (this.value == 'alltask') {
            $('#bulkassign-dialog').find('label[for="bulkassignselectedtask"]').removeClass('focus');
            $('#bulkassign-dialog').find('label[for="bulkassignalltask"]').addClass('focus');
        } else if (this.value == 'selectedtask') {
            $('#bulkassign-dialog').find('label[for="bulkassignselectedtask"]').addClass('focus');
            $('#bulkassign-dialog').find('label[for="bulkassignalltask"]').removeClass('focus');
        }
    });

    /*$('.assignedonly_content').hide();*/
    $('.unassignedonly_content').hide();

    jQuery(".myTeamModules").on('click', function () {
        var team_id = $('#team_id').val();
        var team_loc = $('#team_loc').val();
        var module = jQuery(this).data('module');
        jQuery('.myTeamModules').removeClass('active');
        if (module == 'track_project') {
            var keys = $('.grid-view').yiiGridView('getSelectedRows');
            if (!keys.length) {
                alert('Please select at least 1 record to perform this action.');
            } else if (keys.length > 1) {
                alert('Please select a single record to perform this action.');
            } else {
                //var qs = getQueryStrings();
                //console.log(qs);return false;
                showLoader();
                location.href = baseUrl + 'track/index&taskid=' + keys + '&team_id=' + team_id + '&team_loc=' + team_loc + '&option=Team';
            }
        }
        if (module == 'post_project_comment') {
            var keys = $('.grid-view').yiiGridView('getSelectedRows');
            if (!keys.length) {
                alert('Please select at least 1 record to perform this action.');
            } else if (keys.length > 1) {
                alert('Please select a single record to perform this action.');
            } else {
                showLoader();
                location.href = baseUrl + 'team-projects/post-comment&task_id=' + keys + '&team_id=' + team_id + '&team_loc=' + team_loc;
            }
        }
        if (module == 'team_summary_comment') {
            location.href = baseUrl + 'summary-comment/index&team_id=' + team_id + '&team_loc=' + team_loc;
        }
        if (module == 'list_projects') {
            showLoader();
            loadProjects();
        }
        if (module == 'project_instructions') {
            var keys = $('.grid-view').yiiGridView('getSelectedRows');
            if (!keys.length) {
                alert('Please select at least 1 record to perform this action.');
            } else if (keys.length > 1) {
                alert('Please select a single record to perform this action.');
            } else {
                showLoader();
                location.href = baseUrl + 'team-projects/instrution&task_id=' + keys + '&team_id=' + team_id + '&team_loc=' + team_loc;
            }
        }
        if (module == 'apply_teampriority') {
            var team_id = $('#team_id').val();
            var team_loc = $('#team_loc').val();
            var keys = $('.grid-view').yiiGridView('getSelectedRows');
            if (!keys.length) {
                alert('Please select at least 1 record to perform this action.');
            } else {
                applyteampriority(keys, team_id, team_loc);
            }
        }
        if (module == 'team_tasks') {
            showLoader();
            loadTasks();
        }
        if (module == 'bulk_complete_tasks') {
            var bulkcompletetaskdialog = $('#bulkcompletetask-closed-dialog');
            if (bulkcompletetaskdialog.hasClass('hide')) {
                bulkcompletetaskdialog.removeClass('hide');
            }
            if($('#team-task-form').length){
                $('#team-task-form').html(null);
                $('#team-task-form').html($('#media_container').find('.table-responsive').find('.filters').html());
            }
            completedTasks(bulkcompletetaskdialog);
        }
        if (module == 'transfer_location_tasks') {
            if($('#team-task-form').length){
                $('#team-task-form').html(null);
                $('#team-task-form').html($('#media_container').find('.table-responsive').find('.filters').html());
            }
            transferLocationTasks();
        }
        if (module == 'assign_tasks') {
            var bulkassigndialog = $('#bulkassign-dialog');
            if (bulkassigndialog.hasClass('hide')) {
                bulkassigndialog.removeClass('hide');
            }
            var team_id = jQuery('#team_id').val();
            var team_loc = jQuery('#team_loc').val();
            jQuery.ajax({
                url: baseUrl + 'team-tasks/bulkassign',
                type:'get',
                data:{'team_id':team_id, 'team_loc':team_loc},
                beforeSend: function (data) {
                    showLoader();
                },
                success:function(resdata){
                    hideLoader();
                    if($('#team-task-form').length){
                        $('#team-task-form').html(null);
		                $('#team-task-form').html($('#media_container').find('.table-responsive').find('.filters').html());
                    }
                    bulkassigndialog.html(resdata);
                    AssignTasks(bulkassigndialog);
                }
            });
            //AssignTasks(bulkassigndialog);
        }
        if (module == 'transition_tasks') {
            var bulktransitiondialog = $('#bulktransition-dialog');
            if (bulktransitiondialog.hasClass('hide')) {
                bulktransitiondialog.removeClass('hide');
            }
            var team_id = jQuery('#team_id').val();
            var team_loc = jQuery('#team_loc').val();
            jQuery.ajax({
                url: baseUrl + 'team-tasks/bulktransition',
                type:'get',
                data:{'team_id':team_id, 'team_loc':team_loc},
                beforeSend: function (data) {
                    showLoader();
                },
                success:function(resdata){
                    hideLoader();
                    if($('#team-task-form').length){
                        $('#team-task-form').html(null);
		                $('#team-task-form').html($('#media_container').find('.table-responsive').find('.filters').html());
                    }
                    bulktransitiondialog.html(resdata);
                    TransitionTasks(bulktransitiondialog);
                }
            });
            
        }
        if (module == 'unassign_tasks') {
            var bulkunassigndialog = $('#bulkunassign-dialog');
            if (bulkunassigndialog.hasClass('hide')) {
                bulkunassigndialog.removeClass('hide');
            }
            if($('#team-task-form').length){
                $('#team-task-form').html(null);
                $('#team-task-form').html($('#media_container').find('.table-responsive').find('.filters').html());
            }
            UnAssignTasks(bulkunassigndialog);
        }
    });


});
/*Bulk UnAssign Task For Tasks Teams */
function UnAssignTasks(bulkunassigndialog)
{
    var team_id = jQuery('#team_id').val();
    var team_loc = jQuery('#team_loc').val();
    var keys = $('.grid-view').yiiGridView('getSelectedRows');
    var count = keys.length;
    var sel_row = "";
    bulkunassigndialog.dialog({
        title: 'Bulk Unassign Tasks',
        autoOpen: true,
        resizable: false,
        width: "50em",
        height: 302,
        modal: true,
        create: function (event, ui) {
            $('#bulkunassign-dialog').prev().find('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
            $('#bulkunassign-dialog').prev().find('.ui-dialog-titlebar-close').attr("title", "Close");
            $('#bulkunassign-dialog').prev().find('.ui-dialog-titlebar-close').attr("aria-label", "Close");
        },
        buttons: [
            {
                text: "Cancel",
                "title": "Cancel",
                "class": 'btn btn-primary',
                click: function () {
                    bulkunassigndialog.dialog("close");
                }
            },
            {
                text: "Update",
                "title": "Update",
                "class": 'btn btn-primary',
                click: function () {
                    var taskoperation = $('#bulkunassign-dialog input[type="radio"]:checked').val();
                    var totalcount = $('#totalItemCountteam').html();
                    if (taskoperation == 'selectedtask') {
                        var msg = "Are you sure you want to Bulk UnAssign the selected " + count + " Tasks in the grid?";
                        var selected_type = "selected";
                        // teamprojects-grid-container
                        $('#teamtaskprojects-grid input:checkbox:checked("input[name="selection"]")').each(function () {
                            if ($(this).attr("name") != 'selection_all') {
                                if (sel_row == "") {
                                    sel_row = $(this).val();
                                } else {
                                    sel_row += "," + $(this).val();
                                }
                            }
                        });
                        var postData = '&team_id=' + team_id + '&team_loc=' + team_loc + '&taskunitIds=' + sel_row + '&type=' + selected_type;
                    } else {
                        var msg = "Are you sure you want to Bulk UnAssign All record(s)?";
                        var selected_type = "bulkall";                       
                        var postData = '&team_id=' + team_id + '&team_loc=' + team_loc + '&type=' + selected_type+'&'+$('#team-task-form').serialize();
                    }

                    if (confirm(msg)) {
                        jQuery.ajax({
                            url: baseUrl + 'team-tasks/chkcanunassigntasks',
                            data: postData,
                            type: 'post',
                            dataType: 'json',
                            beforeSend: function (data) {
                                showLoader();
                            },
                            success: function (data) {
                                if (data.success != 'success') {
                                    alert(data.error);
                                    return false;
                                }
                                bulkunassigndialog.dialog("close");
                                location.reload();
                            },
                            complete: function (data) {
                                hideLoader();
                            }
                        });
                    }

                }
            }
        ],
        open: function () {
            //teamassigneduser-pajax
            bulkunassigndialog.find('#allbulkassigntask').html($('#teamtaskprojects-pajax .summary b#totalItemCountteam').text());
            if (count == 0) {
                bulkunassigndialog.find('#rdo_bulkunassigntask').prop('checked', true);
                bulkunassigndialog.find('label[for="rdo_bulkunassigntask"]').addClass('checked');
                bulkunassigndialog.find('#rdo_selectedunassigntask').prop('disabled', true);
                bulkunassigndialog.find('label[for="rdo_selectedunassigntask"]').addClass('disabled');
                bulkunassigndialog.find('#rdo_selectedunassigntask').prop('checked', false);
                bulkunassigndialog.find('label[for="rdo_selectedunassigntask"]').removeClass('checked');

                bulkunassigndialog.find('label[for="rdo_selectedunassigntask"]').removeClass('focus');
                bulkunassigndialog.find('#rdo_bulkunassigntask').focus();
                bulkunassigndialog.find('label[for="rdo_bulkunassigntask"]').addClass('focus');
            } else {
                bulkunassigndialog.find('#rdo_selectedunassigntask').prop('disabled', false);
                bulkunassigndialog.find('#rdo_selectedunassigntask').prop('checked', true);
                bulkunassigndialog.find('#rdo_bulkunassigntask').prop('checked', false);
                bulkunassigndialog.find('label[for="rdo_bulkunassigntask"]').removeClass('checked');
                bulkunassigndialog.find('label[for="rdo_bulkunassigntask"]').removeClass('focus');
                bulkunassigndialog.find('label[for="rdo_selectedunassigntask"]').removeClass('disabled');
                bulkunassigndialog.find('label[for="rdo_selectedunassigntask"]').addClass('checked');

                bulkunassigndialog.find('label[for="rdo_bulkunassigntask"]').removeClass('focus');
                bulkunassigndialog.find('#rdo_selectedunassigntask').focus();
                bulkunassigndialog.find('label[for="rdo_selectedunassigntask"]').addClass('focus');
                //$('#rdo_bulkunassigntask').trigger('blur');
            }
            bulkunassigndialog.find('#unassignselectedtask').html(count);
            //bulkunassigndialog.find('button:contains("Cancel")').focus();
            //$('.ui-dialog').find('.btn').blur();
        }
    });
}
/* Bull Transition Task For Tasks teams*/
function TransitionTasks(bulktransitiondialog) {

    var team_id = jQuery('#team_id').val();
    var team_loc = jQuery('#team_loc').val();
    var keys = $('.grid-view').yiiGridView('getSelectedRows');
    var count = keys.length;
    var sel_row = "";

    bulktransitiondialog.dialog({
        title: 'Bulk Transition Tasks',
        autoOpen: true,
        resizable: false,
        width: "50em",
        height: 456,
        modal: true,
        create: function (event, ui) {
            //if($('.ui-dialog-titlebar-close').html() != '<span class="ui-button-icon-primary ui-icon"></span>'){
            $('#bulktransition-dialog').prev().find('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
            $('#bulktransition-dialog').prev().find('.ui-dialog-titlebar-close').attr("title", "Close");
            $('#bulktransition-dialog').prev().find('.ui-dialog-titlebar-close').attr("aria-label", "Close");
            //}

            // $otherDialogContainer.dialog( "option", "closeText", "hidess" );


        },
        buttons: [
            {
                text: "Cancel",
                "title": "Cancel",
                "class": 'btn btn-primary',
                click: function () {
                    bulktransitiondialog.dialog("close");
                }
            },
            {
                text: "Update",
                "title": "Update",
                "class": 'btn btn-primary',
                click: function () {                    
                    var userassigned_new = $('#bulktransition-dialog li.active').length;
                    var frmassigntask = $('#frmbulktransittasks').serialize();
                    var userassigned = $('#bulktransition-dialog input[type="checkbox"]:checked');
                    var taskoperation = $('#frmbulktransittasks input[type="radio"]:checked').val();
                    var totalcount = $('#totalItemCountteam').html();
                    var user_id = $('#bulktransition-dialog li.active').data('id');
                    var user_id_new = postdata = "";
                    $('#bulktransition-dialog li.active').each(function () {
                        if (user_id_new == "") {
                            user_id_new = $(this).data('id');
                        } else {
                            user_id_new += "," + $(this).data('id');
                        }
                    });
                    if (taskoperation == 'selectedtask') {
                        var msg = "Are you sure you want to Bulk Transition the selected " + count + " Tasks in the grid?";
                        var selected_type = "selected";
                        //teamprojects-grid-container
                        $('#teamtaskprojects-grid input:checkbox:checked("input[name="selection"]")').each(function () {
                            if ($(this).attr("name") != 'selection_all') {
                                if (sel_row == "") {
                                    sel_row = $(this).val();
                                } else {
                                    sel_row += "," + $(this).val();
                                }                            
                            }
                        });
                        postdata = frmassigntask + '&team_id=' + team_id + '&team_loc=' + team_loc + '&taskunitIds=' + sel_row + '&type=' + selected_type + '&user_id=' + user_id_new;
                    } else {                       
                        var selected_type = "bulkall";
                        postdata = frmassigntask + '&team_id=' + team_id + '&team_loc=' + team_loc + '&type=' + selected_type + '&user_id=' + user_id_new+'&'+$('#team-task-form').serialize();
                        var msg = "Are you sure you want to Bulk Transition All record(s)?";
                                         
                        //teamprojects-grid-container
//                        $('#teamtaskprojects-grid input:checkbox("input[name="selection"]")').each(function () {
//                            if ($(this).attr("name") != 'selection_all') {
//                                if (sel_row == "") {
//                                    sel_row = $(this).val();
//                                } else {
//                                    sel_row += "," + $(this).val();
//                                }
//                            }
//                        });
                    }
                    if (userassigned_new == 0) {
                        alert("Please select a User to perform this action.");
                    } else if ((count > 0 && taskoperation == 'selectedtask' && userassigned_new > count) || userassigned_new > totalcount) {
                        alert("The number of Users selected exceeds the number of Tasks to Transition. Please reduce the number of Users to complete perform a Bulk Transition Process.");
                    } else {
                        if (confirm(msg)) {
                            jQuery.ajax({
                                url: baseUrl + 'team-tasks/chkcantransitiontasks',
                                data: postdata,
                                type: 'post',
                                dataType: 'json',
                                beforeSend: function (data) {
                                    showLoader();
                                },
                                success: function (data) {
                                    if (data.error != '') {
                                        alert(data.error);
                                        return false;
                                    }
                                    bulktransitiondialog.dialog("close");
                                    location.reload();
                                },
                                complete: function (data) {
                                    hideLoader();
                                }
                            });
                        }
                    }
                }
            }
        ],
        open: function () {
            bulktransitiondialog.find('#transitionalltask').html($('#teamtaskprojects-pajax .summary b#totalItemCountteam').text());

            if (count == 0) {
                bulktransitiondialog.find('#bulktransitionalltask').prop('checked', true);
                bulktransitiondialog.find('label[for="bulktransitionalltask"]').addClass('checked');
                bulktransitiondialog.find('#bulktransitionselectedtask').prop('disabled', true);
                bulktransitiondialog.find('label[for="bulktransitionselectedtask"]').addClass('disabled');
                bulktransitiondialog.find('#bulktransitionselectedtask').prop('checked', false);
                bulktransitiondialog.find('label[for="bulktransitionselectedtask"]').removeClass('checked');

                bulktransitiondialog.find('label[for="bulktransitionselectedtask"]').removeClass('focus');
                bulktransitiondialog.find('#bulktransitionalltask').focus();
                bulktransitiondialog.find('label[for="bulktransitionalltask"]').addClass('focus');

            } else {

                bulktransitiondialog.find('#bulktransitionselectedtask').prop('checked', true);
                bulktransitiondialog.find('label[for="bulktransitionselectedtask"]').addClass('checked');
                bulktransitiondialog.find('label[for="bulktransitionselectedtask"]').addClass('focus');
                bulktransitiondialog.find('#bulktransitionselectedtask').prop('disabled', false);
                bulktransitiondialog.find('label[for="bulktransitionselectedtask"]').removeClass('disabled');
                bulktransitiondialog.find('#bulktransitionalltask').prop('checked', false);
                bulktransitiondialog.find('label[for="bulktransitionalltask"]').removeClass('checked');

                bulktransitiondialog.find('label[for="bulktransitionalltask"]').removeClass('focus');
                bulktransitiondialog.find('#bulktransitionselectedtask').focus();
                bulktransitiondialog.find('label[for="bulktransitionselectedtask"]').addClass('focus');

                //$('#bulktransitionalltask').trigger('blur');
            }
            bulktransitiondialog.find('#transitionselectedtask').html(count);
            //bulktransitiondialog.find('button:contains("Cancel")').focus();
            //$('.ui-dialog').find('.btn').blur();
        }
    });
}


/* Bulk Assign Task */
function AssignTasks(bulkassigndialog) {

    var team_id = jQuery('#team_id').val();
    var team_loc = jQuery('#team_loc').val();
    var keys = $('.grid-view').yiiGridView('getSelectedRows');
    var count = keys.length;
    var sel_row = "";
    bulkassigndialog.dialog({
        title: 'Bulk Assign Tasks',
        autoOpen: true,
        resizable: false,
        height: 456,
        width: "50em",
        modal: true,
        create: function (event, ui) {
            $('#bulkassign-dialog').prev().find('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
            $('#bulkassign-dialog').prev().find('.ui-dialog-titlebar-close').attr("title", "Close");
            $('#bulkassign-dialog').prev().find('.ui-dialog-titlebar-close').attr("aria-label", "Close");
        },
        buttons: [
            {
                text: "Cancel",
                "title": "Cancel",
                "class": 'btn btn-primary',
                click: function () {
                    bulkassigndialog.dialog("close");
                }
            },
            {
                text: "Update",
                "title": "Update",
                "class": 'btn btn-primary',
                click: function () {
                    //console.log($('#teamprojects-grid').yiiGridView('getSelectedRows'));
                    var frmassigntask = $('#frmbulkassigntasks').serialize();
                    var userassigned_new = $('#bulkassign-dialog li.active').length;
                    var userassigned = $('#bulkassign-dialog input[type="checkbox"]:checked');
                    var taskoperation = $('#frmbulkassigntasks input[type="radio"]:checked').val();
                    var totalcount = $('#totalItemCountteam').html();
                    var user_id_new = "";
                    $('#bulkassign-dialog li.active').each(function () {
                        if (user_id_new == "") {
                            user_id_new = $(this).data('id');
                        } else {
                            user_id_new += "," + $(this).data('id');
                        }
                    });

                    if (taskoperation == 'selectedtask') {
                        var msg = "Are you sure you want to Bulk Assign the selected " + count + " Tasks in the grid?";
                        var selected_type = "selected";
                        /* IRT 411 Changes */
                        $('#teamtaskprojects-grid input:checkbox:checked("input[name="selection"]")').each(function () { // teamprojects-grid-container
                            if ($(this).attr("name") != 'selection_all') {
                                if (sel_row == "") {
                                    sel_row = $(this).val();
                                } else {
                                    sel_row += "," + $(this).val();
                                }
                            }
                        });
                        var postdata = frmassigntask + '&team_id=' + team_id + '&team_loc=' + team_loc + '&taskunitIds=' + sel_row + '&type=' + selected_type + '&user_id=' + user_id_new;                       
                    } else {
                        var msg = "Are you sure you want to Bulk Assign All record(s)?";
                        var selected_type = "bulkall";
                        /* IRT 411 Changes */
//                        $('#teamtaskprojects-grid input:checkbox("input[name="selection"]")').each(function () { // teamprojects-grid-container
//                            if ($(this).attr("name") != 'selection_all') {
//                                if (sel_row == "") {
//                                    sel_row = $(this).val();
//                                } else {
//                                    sel_row += "," + $(this).val();
//                                }
//                            }
//                            //count++;
//                        });
                        var postdata = frmassigntask + '&team_id=' + team_id + '&team_loc=' + team_loc + '&type=' + selected_type + '&user_id=' + user_id_new+'&'+$('#team-task-form').serialize();
                    }
                    if (userassigned_new == 0) {
                        alert("Please select a User to perform this action.");
                    } else if ((count > 0 && taskoperation == 'selectedtask' && userassigned_new > count) || userassigned_new > totalcount) {
                        alert("The number of Users selected exceeds the number of Tasks to Assign. Please reduce the number of Users to complete perform a Bulk Assign Process.");
                    } else {
                        if (confirm(msg)) {
                            jQuery.ajax({
                                url: baseUrl + 'team-tasks/chkcanassigntasks',
                                data: postdata,
                                type: 'post',
                                dataType: 'json',
                                beforeSend: function (data) {
                                    showLoader();
                                },
                                success: function (data) {
                                    if (data.error != '') {
                                        alert(data.error);
                                        return false;
                                    }
                                    bulkassigndialog.dialog("close");
                                    location.reload();
                                },
                                complete: function (data) {
                                    hideLoader();
                                }
                            });
                        }
                    }
                }
            }
        ],
        open: function () {
            //teamassigneduser-pajax
            bulkassigndialog.find('#assignalltask').html($('#teamtaskprojects-pajax .summary b#totalItemCountteam').text());

            if (count == 0) {
                bulkassigndialog.find('#bulkassignalltask').prop('checked', true);
                bulkassigndialog.find('label[for="bulkassignalltask"]').addClass('checked');
                bulkassigndialog.find('#bulkassignselectedtask').prop('disabled', true);
                bulkassigndialog.find('label[for="bulkassignselectedtask"]').addClass('disabled');
                bulkassigndialog.find('#bulkassignselectedtask').prop('checked', false);
                bulkassigndialog.find('label[for="bulkassignselectedtask"]').removeClass('checked');

                bulkassigndialog.find('label[for="bulkassignselectedtask"]').removeClass('focus');
                bulkassigndialog.find('#bulkassignselectedtask').focus();
                bulkassigndialog.find('label[for="bulkassignalltask"]').addClass('focus');
            } else {
                bulkassigndialog.find('#bulkassignselectedtask').prop('disabled', false);
                bulkassigndialog.find('#bulkassignselectedtask').prop('checked', true);
                bulkassigndialog.find('label[for="bulkassignselectedtask"]').addClass('checked');
                bulkassigndialog.find('#bulkassignalltask').prop('checked', false);
                bulkassigndialog.find('label[for="bulkassignalltask"]').removeClass('checked');
                bulkassigndialog.find('label[for="bulkassignalltask"]').removeClass('focus');
                bulkassigndialog.find('label[for="bulkassignselectedtask"]').removeClass('disabled');

                bulkassigndialog.find('label[for="bulkassignalltask"]').removeClass('focus');
                bulkassigndialog.find('#bulkassignselectedtask').focus();
                bulkassigndialog.find('label[for="bulkassignselectedtask"]').addClass('focus');
                //$('#bulkassignalltask').trigger('blur');		
            }
            bulkassigndialog.find('#assignselectedtask').html(count);
            //bulkassigndialog.find('button:contains("Cancel")').focus();
            //$('.ui-dialog').find('.btn').blur();
        }
    });
}
/*Code for Bulk Transfer Location Tasks*/
function transferLocationTasks() {
    var team_id = jQuery('#team_id').val();
    var team_loc = jQuery('#team_loc').val();
    var keys = $('.grid-view').yiiGridView('getSelectedRows');
    var count = keys.length;
    var sel_row = "";
    var wft = $('#tasksunitssearch-workflow_task').val().toString();
    if (!$("#applytransferLocation").length) {
        $('body').append("<div id='applytransferLocation'></div>");
    }
    $.ajax({
        url: httpPath + "team-tasks/bulktransferlocation&team_id=" + team_id + "&team_loc=" + team_loc,
        type: 'post',
        cache: false,
        data: {'service_location': wft},
        dataType: 'html',
        success: function (data) {
            if (data != "") {
                $('#applytransferLocation').html(data);

                $("#applytransferLocation").dialog({
                    autoOpen: true,
                    resizable: false,
                    title: 'Transfer Task Location',
                    width: "50em",
                    height: 302,
                    modal: true,
                    create: function (event, ui) {
                        $('#applytransferLocation').prev().find('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
                        $('#applytransferLocation').prev().find('.ui-dialog-titlebar-close').attr("title", "Close");
                        $('#applytransferLocation').prev().find('.ui-dialog-titlebar-close').attr("aria-label", "Close");
                    },
                    buttons: [
                        {
                            text: "Cancel",
                            "title": "Cancel",
                            "class": 'btn btn-primary',
                            click: function () {
                                trigger = 'Cancel';
                                $(this).dialog("close");
                            }
                        },
                        {
                            text: "Update",
                            "title": "Update",
                            "class": 'btn btn-primary',
                            click: function () {
                                var location_id = $('#applytransferLocation #team_location_dropdown').val();
                                var taskoperation = $('#applytransferLocation input[type="radio"]:checked').val();
                                if (location_id == "") {
                                    alert('Please Select Location.');
                                    return false;
                                }
                                var postdata = {};
                                if (taskoperation == 'selectedtask') {
                                    var selected_type = "selected";
                                    var msg = "Are you sure you want to Bulk Transfer the selected " + count + " Tasks in the grid?";
                                    // teamprojects-grid-container
                                    $('#teamtaskprojects-grid input:checkbox:checked("input[name="selection"]")').each(function () {
                                        if ($(this).attr("name") != 'selection_all') {
                                            if (sel_row == "") {
                                                sel_row = $(this).val();
                                            } else {
                                                sel_row += "," + $(this).val();
                                            }
                                        }
                                    });
                                    var postdata = {'team_id': team_id, 'team_loc': team_loc, 'taskunitIds': sel_row, 'type': selected_type, 'loc': location_id};
                                } else {
                                    var selected_type = "bulkall";
                                    var msg = "Are you sure you want to Bulk Transfer all record(s)?";
                                    var postdata = '&team_id=' + team_id + '&team_loc=' + team_loc + '&type=' + selected_type + '&loc=' + location_id+'&'+$('#team-task-form').serialize();
                                }
                                if (confirm(msg))
                                {
                                    jQuery.ajax({
                                        url: baseUrl + 'team-tasks/chkbulktransfertasks',
                                        data: postdata,
                                        type: 'post',
                                        dataType: 'json',
                                        beforeSend: function (data) {
                                            showLoader();
                                        },
                                        success: function (data) {
                                            if (data.error != '') {
                                                alert(data.error);
                                                return false;
                                            }
                                            $('#applytransferLocation').dialog("close");
                                            location.reload();
                                        },
                                        complete: function (data) {
                                            hideLoader();
                                        }
                                    });
                                }
                                $(this).dialog("close");
                            }
                        }
                    ],
                    open: function () {
                        $('#applytransferLocation').find('#allbulktransfertask').html($('#teamtaskprojects-pajax .summary b#totalItemCountteam').text());
                        if (count == 0) {
                            $('#applytransferLocation').find('#rdo_bulktransfertask').prop('checked', true);
                            $('#applytransferLocation').find('label[for="rdo_bulktransfertask"]').addClass('checked');
                            $('#applytransferLocation').find('#rdo_selectedtransfertask').prop('disabled', true);
                            $('#applytransferLocation').find('label[for="rdo_selectedtransfertask"]').addClass('disabled');
                            $('#applytransferLocation').find('#rdo_selectedtransfertask').prop('checked', false);
                            $('#applytransferLocation').find('label[for="rdo_selectedtransfertask"]').removeClass('checked');

                            $('#applytransferLocation').find('label[for="rdo_selectedtransfertask"]').removeClass('focus');
                            $('#applytransferLocation').find('#rdo_bulktransfertask').focus();
                            $('#applytransferLocation').find('label[for="rdo_bulktransfertask"]').addClass('focus');
                        } else {
                            $('#applytransferLocation').find('#rdo_selectedtransfertask').prop('disabled', false);
                            $('#applytransferLocation').find('label[for="rdo_selectedtransfertask"]').removeClass('disabled');
                            $('#applytransferLocation').find('#rdo_selectedtransfertask').prop('checked', true);
                            $('#applytransferLocation').find('label[for="rdo_selectedtransfertask"]').addClass('checked');
                            $('#applytransferLocation').find('#rdo_bulktransfertask').prop('checked', false);
                            $('#applytransferLocation').find('label[for="rdo_bulktransfertask"]').removeClass('checked');

                            $('#applytransferLocation').find('label[for="rdo_bulktransfertask"]').removeClass('focus');
                            $('#applytransferLocation').find('#rdo_selectedtransfertask').focus();
                            $('#applytransferLocation').find('label[for="rdo_selectedtransfertask"]').addClass('focus');
                        }
                        $('#applytransferLocation').find('#transferselectedtask').html(count);
                    },
                    close: function () {
                        $(this).dialog('destroy').remove();
                    }
                });
            }
        }
    });
}
/* Code for Bulk Completed Tasks */
function completedTasks(bulkcompletetaskdialog) {

    var team_id = jQuery('#team_id').val();
    var team_loc = jQuery('#team_loc').val();
    var keys = $('.grid-view').yiiGridView('getSelectedRows');
    var count = keys.length;
    var sel_row = "";


    bulkcompletetaskdialog.dialog({
        title: 'Bulk Complete Tasks',
        autoOpen: true,
        resizable: false,
        width: "50em",
        height: 302,
        modal: true,
        create: function (event, ui) {
            $('#bulkcompletetask-closed-dialog').prev().find('.ui-dialog-titlebar-close').append('<span class="ui-button-icon-primary ui-icon"></span>');
            $('#bulkcompletetask-closed-dialog').prev().find('.ui-dialog-titlebar-close').attr("title", "Close");
            $('#bulkcompletetask-closed-dialog').prev().find('.ui-dialog-titlebar-close').attr("aria-label", "Close");
        },
        buttons: [
            {
                text: "Cancel",
                "title": "Cancel",
                "class": 'btn btn-primary',
                click: function () {
                    bulkcompletetaskdialog.dialog("close");
                }
            },
            {
                text: "Update",
                "title": "Update",
                "class": 'btn btn-primary',
                click: function () {
                    var taskoperation = $('#bulkcompletetask-closed-dialog input[type="radio"]:checked').val();
                    // console.log(taskoperation);return false;
                    var postdata = '';
                    if (taskoperation == 'selectedtask') {
                        var selected_type = "selected";
                        var msg = "Are you sure you want to Bulk Complete the selected " + count + " Tasks in the grid?";
                        // teamprojects-grid-container
                        $('#teamtaskprojects-grid input:checkbox:checked("input[name="selection"]")').each(function () {
                            if ($(this).attr("name") != 'selection_all') {
                                if (sel_row == "") {
                                    sel_row = $(this).val();
                                } else {
                                    sel_row += "," + $(this).val();
                                }
                            }
                        });
                        postdata = {'team_id': team_id, 'team_loc': team_loc, 'taskunitIds': sel_row, 'type': selected_type};
                    } else {
                        var selected_type = "all";
                        var msg = "Are you sure you want to Bulk Complete All record(s)?";
//						$('#teamtaskprojects-grid input:checkbox("input[name="selection"]")').each(function(){
//                                                    if($(this).attr("name")!='selection_all') {
//                                                        if(sel_row=="") {
//                                                                sel_row=$(this).val();
//                                                        } else {
//                                                            sel_row+=","+$(this).val();
//                                                        }
//                                                    }
//						});
                        postdata = '&team_id=' + team_id + '&team_loc=' + team_loc + '&type=' + selected_type;
                        postdata = $('#team-task-form').serialize() + postdata;
                    }
                    if (confirm(msg))
                    {
                        jQuery.ajax({
                            url: baseUrl + 'team-tasks/chkcancompletetasks',
                            data: postdata,
                            type: 'post',
                            dataType: 'json',
                            beforeSend: function (data) {
                                showLoader();
                            },
                            success: function (data) {
                                if (data.finalresult != 'OK') {
                                    alert(data.error);
                                }
                                bulkcompletetaskdialog.dialog("close");
                                location.reload();
                            },
                            complete: function (data) {
                                hideLoader();
                            }
                        });
                    }
                }
            }
        ],
        open: function () {

            bulkcompletetaskdialog.find('#allbulkcompletetask').html($('#teamtaskprojects-pajax .summary b#totalItemCountteam').text());

            if (count == 0) {
                bulkcompletetaskdialog.find('#rdo_bulkcompletetask').prop('checked', true);
                bulkcompletetaskdialog.find('label[for="rdo_bulkcompletetask"]').addClass('checked');
                bulkcompletetaskdialog.find('#rdo_selectedcompletetask').prop('disabled', true);
                bulkcompletetaskdialog.find('label[for="rdo_selectedcompletetask"]').addClass('disabled');
                bulkcompletetaskdialog.find('#rdo_selectedcompletetask').prop('checked', false);
                bulkcompletetaskdialog.find('label[for="rdo_selectedcompletetask"]').removeClass('checked');

                bulkcompletetaskdialog.find('label[for="rdo_selectedcompletetask"]').removeClass('focus');
                bulkcompletetaskdialog.find('#rdo_bulkcompletetask').focus();
                bulkcompletetaskdialog.find('label[for="rdo_bulkcompletetask"]').addClass('focus');
            } else {
                bulkcompletetaskdialog.find('#rdo_selectedcompletetask').prop('disabled', false);
                bulkcompletetaskdialog.find('label[for="rdo_selectedcompletetask"]').removeClass('disabled');
                bulkcompletetaskdialog.find('#rdo_selectedcompletetask').prop('checked', true);
                bulkcompletetaskdialog.find('label[for="rdo_selectedcompletetask"]').addClass('checked');
                bulkcompletetaskdialog.find('#rdo_bulkcompletetask').prop('checked', false);
                bulkcompletetaskdialog.find('label[for="rdo_bulkcompletetask"]').removeClass('checked');

                bulkcompletetaskdialog.find('label[for="rdo_bulkcompletetask"]').removeClass('focus');
                bulkcompletetaskdialog.find('#rdo_selectedcompletetask').focus();
                bulkcompletetaskdialog.find('label[for="rdo_selectedcompletetask"]').addClass('focus');
                //$('#rdo_bulkcompletetask').trigger('blur');		
            }
            bulkcompletetaskdialog.find('#selectedtask').html(count);
            //bulkcompletetaskdialog.find('#rdo_selectedcompletetask').focus();
            //$('.ui-dialog').find('.btn').blur();
        }
    });

}


function getQueryStrings()
{
    var assoc = {};
    var str = "";
    var decode = function (s) {
        return decodeURIComponent(s.replace(/\+/g, " "));
    };
    var queryString = location.search.substring(1);
    var keyValues = queryString.split('&');

    for (var i in keyValues) {
        var key = keyValues[i].split('=');
        if (key.length > 1) {
            if (str == "") {
                if (decode(key[0]) == 'r') {
                    str = decode(key[1]);
                } else {
                    str = decode(key[0]) + "||" + decode(key[1]);
                }
            } else {
                str += "||" + decode(key[0]) + "=" + decode(key[1]);
            }
            assoc[decode(key[0])] = decode(key[1]);
        }
    }

    return str;
}
