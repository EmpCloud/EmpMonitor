let PROJECTS_CHECK = false;
let PROJECT_MEMBERS_CHECK = true;
let NEW_TASK_STATUS = 0;
let MODULE_CHECK = true;
let UPDATE_CREATE_DATE, UPDATE_END_DATE, UPDATE_PROJECT_NAME, TODO_NAME, TODO_START_DATE, TODO_END_DATE;
UPDATE_CREATE_DATE = UPDATE_END_DATE = UPDATE_PROJECT_NAME = TODO_NAME = TODO_START_DATE = TODO_END_DATE = null;
let MANAGERS_LIST_CHECK = false;
let USERS_LIST_CHECK = false;
let MODAL_OPENED=1;

// for datatable variables
let SHOW_ENTRIES = "10";                 //To hold the show entries
let TOTAL_COUNT_EMAILS; 		 // Your table list total count
let SORT_NAME = '';   			 //Holds on which field we are going to sort
let SORT_ORDER = '';			 //Holds on  which order we are going to do
let SORTED_TAG_ID = '';  		 // Holds the previous tad id  to disable the arrow if we go to another field for sorting
let PAGE_COUNT_CALL = true; 		 //tells whether page count call or not means
let SEARCH_TEXT="";

$(document).ready(function () {
    // $("#newTaskDescription").keyup(function(){
    //     if(description_count >= $(this).val().length) {
    //         $("#count").text(TASK_LOCALIZATION.characters + ":" + (description_count - $(this).val().length));
    //     }else{
    //         $("#count").text(TASK_LOCALIZATION.total_characters + ":" + ($(this).val().length));
    //     }
    // });
    // $("#taskUpdateDescription").keyup(function(){
    //     if(description_count >= $(this).val().length) {
    //         $("#count1").text(TASK_LOCALIZATION.characters + ":" + (description_count - $(this).val().length));
    //     }else{
    //         $("#count1").text(TASK_LOCALIZATION.total_characters + ":" + ($(this).val().length));
    //     }
    // });
    // $("#projectDescription").keyup(function(){
    //     if(description_count >= $(this).val().length) {
    //         $("#count2").text(TASK_LOCALIZATION.characters + ":" + (description_count - $(this).val().length));
    //     }else{
    //         $("#count2").text(TASK_LOCALIZATION.total_characters + ":" + ($(this).val().length));
    //     }
    // });
    // addProjectModalLoad();
    getAllModules(projectDetails.id);
    $('#taskUpdateEndDate, #taskUpdateStartDate, #newTaskStartDate, #newTaskEndDate, #projectStartDate, #projectEndDate').attr('min', new Date().toISOString().split("T")[0]);

    getALLToDos($('#editProject').attr('value'), 0, 0,$("#module_id").val());
    (PROJECT_MEMBERS_CHECK == true) ? getAllMembersOfSingleProject($('#editProject').attr('value')) : {};

    // append loaded data when clicking on edit button ( applicable for all tasks )
    $("body").on('click', '.editTask', function () {
        $('#uptaskNameErrors, #uptaskAssigneeNameErrors, #updtodoNameErro').html(" ");
        let currentDiv = $(this).closest(".taskDiv");
        $('#updateTaskId').attr('value', currentDiv.find(".taskName").attr('id'));
        let assigneeId = currentDiv.find(".taskAssigneeId").attr('id');
        TODO_NAME = currentDiv.find(".taskName").text();
        $('#' + assigneeId).attr('selected', true);
        $('#taskUpdateStatus').val(currentDiv.find(".taskStatus").text());
        $('#taskUpdatePriority').val(currentDiv.find(".taskPriority").text());
        document.getElementsByClassName("taskModuleName")[0].innerHTML = currentDiv.find(".moduleName").text().substring(0, 15)+((currentDiv.find(".moduleName").text().length > 15) ? "..." : "");
        $("#taskModuleName").attr("title",currentDiv.find(".moduleName").text());
        $('#taskUpdateName').val(currentDiv.find(".taskName").text());
        $('#taskUpdateDescription').val(currentDiv.find('.taskDescription').text());
        //date done
        TODO_START_DATE = moment(currentDiv.find(".taskStartDate").text().replace('Start Date : ', ''), 'DD MMM YYYY').format('YYYY-MM-DD');
        $('#taskUpdateStartDate').val(TODO_START_DATE);
        TODO_END_DATE = moment(currentDiv.find(".taskEndDate").text().replace('End Date : ', ''), 'DD MMM YYYY').format('YYYY-MM-DD');
        $('#taskUpdateEndDate').val(TODO_END_DATE);
        $('#taskUpdateAssigneeName').val(assigneeId);
        $('#count1').text("");

    });
});

function projectDetailsInTask() {
    $('#taskProgressError, #taskManagerError, #taskProjectNameError, #ProjectNameError').html(" ");
    getMangAndEmpList(projectDetails.id);
}
// update option is disabled now, need to update it
function updateProject() {
    if($('#projectName').val() != "") {
        $('#ProjectNameError').attr('hidden', true);
    } else {
        $('#taskProjectNameError').html(" ");
        $('#ProjectNameError').removeAttr('hidden');
        return false;
    }
    // if( $("#projectDescription").val().length > description_count){
    //     customErrorHandler(TASK_LOCALIZATION.exceeded);
    //     return false;
    // }
    let updateData = {};
    projectDetails.start_date.split("T")[0] !== $('#projectStartDate').val() ? updateData.start_date = $('#projectStartDate').val() : {};
    projectDetails.end_date.split("T")[0] !== $('#projectEndDate').val() ? updateData.end_date = $('#projectEndDate').val() : {};
    projectDetails.name !== $('#projectName').val() ? updateData.name = $('#projectName').val() : {};
    let selectedMembers = [];
    let managerId = null;
    if(is_admin != 1) {
        selectedMembers.push($("#LoginId").val())
    }else{
        $('#updateProManagersLists').select2('data').forEach(manager => {
            selectedMembers.push(Number(manager.id));
        });
    }
    selectedMembers.length > 0 ? managerId = 1 : {};
    $('#updateProMembersLists').select2('data').forEach(member => {
        selectedMembers.push(Number(member.id));
    });
    selectedMembers = selectedMembers.filter( function( item, index, inputArray ) {
        return inputArray.indexOf(item) == index;
    });
    updateData.manager_id = managerId;
    updateData.user_ids = selectedMembers,
        $.ajax({
            type: "post",
            url: '/' + userType +'/update-project',
            data: {
                project_id: Number($('#editProject').attr('value')),
                status: Number($('#projectStatusList').find(":selected").val()),
                description: $('#projectDescription').val(),
                ...updateData,
                progress: Number($('#projectProgressValue').find(":selected").attr('value')),
            },
            beforeSend: function () {
                $('#taskProgressError, #taskManagerError, #taskProjectNameError, #ProjectNameError').html(" ");
            },
            success: function (response) {
                response.progress !== undefined ? $('#updateProjectProgressError').removeAttr('hidden') : $('#updateProjectProgressError').attr('hidden', true);
                if (response.code == 200) {
                    $('#pro_detail_update').modal('toggle');
                    $("#" + $('#editProject').attr('value') + "").remove();
                    if (response.data) {

                        //set updated values to the project Modal ****
                        (response.data.name != undefined) ? ((document.getElementsByClassName("headerChange")[0].innerHTML = response.data.name) && ($('#projectName').val(response.data.name))) :{};
                        (response.data.progress != undefined) ? $('#projectProgressValue').val(response.data.progress) :{};
                        (response.data.status != undefined) ? $('#projectStatusList').val(response.data.status) :{};
                        (response.data.start_date != undefined) ? $('#projectStartDate').val(response.data.start_date) :{};
                        (response.data.end_date != undefined) ? $('#projectEndDate').val(response.data.end_date) :{};
                        (response.data.description != undefined) ? $('#projectDescription').val(response.data.description) :{};
                        customSuccessHandler(TASK_LOCALIZATION.project_success);
                    }
                } else if (response.code == 400) {
                    return errorHandler(response.msg)
                } else if (response.code == 201) {
                    for (i = 0; i < 4; i++) {
                        switch (response.msg[i]) {
                            case projects+' '+name_invalid:
                                $('#taskProjectNameError').html(projects+' '+name_invalid);
                                break;
                            case projects+' '+name_min:
                                $('#taskProjectNameError').html(projects+' '+name_min);
                                break;
                            case projects+' '+name_max:
                                $('#taskProjectNameError').html(projects+' '+name_max);
                                break;
                            case manager_field:
                                $('#taskManagerError').html(manager_field);
                                break;
                            case progress:
                                $('#taskProgressError').html(progress);
                                break;
                        }
                    }
                }
            },
            error: function () {
                return customErrorHandler(TASK_LOCALIZATION.project_error)
            }
        });
}

function newTask(project_id, status) {
    $("#newTaskName").val("");
    $("#newTaskStartDate").val("MM-DD-YYYY");
    $("#newTaskEndDate").val("MM-DD-YYYY");
    $("#newTaskDescription").val("");
    $('#taskNameErrors, #taskStartErrors, #taskEndErrors, #taskAssigneErrors').html(" ");
    NEW_TASK_STATUS = status;
    (PROJECT_MEMBERS_CHECK == true) ? getAllMembersOfSingleProject(project_id) : {};
    // MODULE_CHECK ? getAllModules(project_id) : {}
    $("#newTaskModule").val($("#filterModules").val());
}

function getAllMembersOfSingleProject(project_id) {
    $.ajax({
        type: "post",
        url: '/' + userType +'/get-project-members',
        data: {project_id},
        beforeSend: function () {
        },
        success: function (response) {
            if (response.code == 200) {
                if (response.data && response.data.length > 0) {
                    $('#newTaskAssignee').empty();
                    $('#taskUpdateAssigneeName').empty();
                    response.data.map(member => {
                        $('#newTaskAssignee').append('<option  id="' + member.employee_id + '" value="' + member.employee_id + '"> ' + member.employee_name + '</option>');
                        $('#taskUpdateAssigneeName').append('<option  id="' + member.employee_id + '" value="' + member.employee_id + '"> ' + member.employee_name + '</option>');
                    });
                    PROJECT_MEMBERS_CHECK = false;
                }
            } else if (response.code == 400) {
                $('#filterUserList').empty();
                $('#createAssigneeList').empty();
                $('#editAssigneeList').empty();
                $('#filterUserList').append('<option disabled selected >'+TASK_LOCALIZATION.no_user+'</option>');
                $('#createAssigneeList').append('<option disabled selected value=false>'+TASK_LOCALIZATION.no_user+'</option>');
                $('#editAssigneeList').append('<option disabled selected value=false>'+TASK_LOCALIZATION.no_user+'</option>');
            } else {
                errorHandler(response.msg);
            }
        },
        error: function () {
            return customErrorHandler(TASK_LOCALIZATION.project_error);
        }
    });
}

function getAllModules(project_id) {
    $.ajax({
        type: "post",
        url: '/' + userType +'/get-modules',
        data: {project_id},
        beforeSend: function () {
        },
        success: function (response) {
            if (response.code == 200) {
                if (response.data && response.data.length > 0) {
                    $('#newTaskModule').empty();
                    response.data.map(module => {
                        $('#newTaskModule').append('<option  id="' + module.id + '" value="' + module.id + '"> ' + module.name + '</option>');
                        $('#filterModules').append('<option  id="' + module.id + '" value="' + module.id + '"> ' + module.name + '</option>');
                        $('#filterModules').val($("#module_id").val());
                    });
                    MODULE_CHECK = false;
                }
            } else if (response.code == 400) {
                $('#newTaskModule').empty();
                $('#empty').empty();
                $('#newTaskModule').append('<option selected value="0" disabled>'+TASK_LOCALIZATION.no_module+'</option>');
                // customErrorHandler(TASK_LOCALIZATION.add_modules);
            } else {
                errorHandler(response.msg);
            }
        },
        error: function () {
            return customErrorHandler(TASK_LOCALIZATION.project_error);
        }
    });
}

function taskData(response) {
    let i = 0;
    response.data.map(task => {
        let nestedTaskData = "";
        let status = task.status === 0 ? 'Hold' : task.status === 1 ? 'Process' : task.status === 2 ? 'Completed' : 'Todo';
        let description = (task.description == null) ? "-" : task.description;
        let startData = moment(task.start_date).format('DD MMM YYYY');
        let endDate = moment(task.end_date).format('DD MMM YYYY');
        task.project_module_name = (task.project_module_name == null) ? '' : task.project_module_name;
        nestedTaskData += '<div class="portlet taskDiv" ondragstart="dragStarted()" draggable="true" id="task' + task.task_id + '" value="'+status+'">' +
            '<div class="portlet-header mb-0 text-right">\n' +
            '                                    <p class="mb-0"><a class="editTask" data-toggle="modal"\n' +
            '                                                       data-target="#todo_list_update"><i\n' +
            '                                                class="far fa-edit text-success"></i></a>\n' +
            '\n' +
            '                                        <a id="" data-toggle="modal" onclick="deleteTodo(' + task.task_id + ');"><i\n' +
            '                                                class="far fa-trash-alt text-danger"></i></a>\n' +
            '                                    </p>\n' +
            '                                </div>\n' +
            '                                <div class="card mx-0 mb-3 alert-secondary portlet-header text-center portlet-content">\n' +
            '\n' +
            '                                    <p style="display: none" class="moduleName" >' + task.project_module_name + '</p>\n' +
            '                                    <p class="taskName" id="' + task.task_id + '">' + task.name + '</p>\n' +
            '                                    <p class="taskTime text-success">' + TASK_LOCALIZATION.task_time + ' :' + (((task.time !== null) && (task.time !== undefined)) ? task.time : "00:00:00") + '</p>\n' +
            '                                    <small id="collapseContentt' + i + '">' + description.slice(0, 100);
        if(description.length>100) nestedTaskData +='<span id="dots' + i + '">...</span><span id="more' + i + '" style="display:none">' + description.slice(101, (description).length) + '</span><a class="text-primary" style="cursor: pointer;" onclick="showmore(' + i + ')" id="myBtn' + i + '">Show more</a>';
        nestedTaskData += '                  </small>\n<p class="taskDescription" style="display:none;">' + description +'</p><p class="taskAssigneeId" id="' + task.employee_id + '"> <small>'+TASK_LOCALIZATION.assigned_to+'  : </small>' + task.employee_name + '</p>\n' +
            '                                    <a href="" class="text-primary" onclick="getTaskTimesheets(' + task.task_id + ')" data-toggle="modal" data-target="#TimesheetsModal">'+TASK_LOCALIZATION.task_timesheets+'</a>\n' +
            '                                    <span class="taskStatus" hidden>' + task.status + '</span>\n' +
            '                                    <span class="taskPriority" hidden>' + task.priority + '</span>\n' +
            '                                    <small class="text-danger mb-0 taskStartDate">'+ TASK_LOCALIZATION.start_date +' : ' + startData + '</small>\n' +
            '                                    <small class="text-danger taskEndDate">'+ TASK_LOCALIZATION.end_date +' : ' + endDate + '</small>\n' +
            '                                </div></div>';
        i = i+1;
        $('.empty').empty();
        switch (Number(task.status)) {
            case 0:
                $('#Hold').after(nestedTaskData);
                break;
            case 1:
                $('#Process').after(nestedTaskData);
                break;
            case 2:
                $('#Completed').after(nestedTaskData);
                break;
            case 4:
                $('#Todo').after(nestedTaskData);
                break;
            default:
                break;
        }
    });
}

function appendEmptyToDo(message, color) {
    $('#All, #Hold, #Process, #Completed, #Todo').nextAll().empty();
    $('#All, #Hold, #Process, #Completed, #Todo').after('<div class="empty"><span id="allNoData" style="color: ' + color + '; text-align: center !important;"> ' + message + '</span></div>');
}

function getALLToDos(project_id, userId, status, module_id) {
    $.ajax({
        type: "post",
        url: '/' + userType + '/get-tasks',
        data: {project_id, module_id},
        beforeSend: function () {
        },
        success: function (response) {
            if (response.code == 200) {
                if (response.data && response.data.length > 0) {
                    $('.taskDiv').remove();
                    taskData(response);
                    loadPortlets();
                } else {
                    appendEmptyToDo(TASK_LOCALIZATION.task_add, 'blue');
                }
            } else if (response.code == 400) {
                appendEmptyToDo(TASK_LOCALIZATION.task_add, 'blue');
            } else {
                appendEmptyToDo(TASK_LOCALIZATION.failed_task, 'red');
            }
        },
        error: function () {
            return customErrorHandler(TASK_LOCALIZATION.project_error);
        }
    });
}

function searchTask() {
    let project_id = $('#editProject').attr('value');
    let module_id = $("#module_id").val();
    $.ajax({
        type: "post",
        url: '/' + userType + '/search-tasks-by-name',
        data: {name: $('#searchText').val(), project_id, module_id},
        beforeSend: function () {
        },
        success: function (response) {
            if (response.code == 200) {
                if (response.data && response.data.length > 0) {
                    $('#Hold').nextAll().empty();
                    $('#Process').nextAll().empty();
                    $('#Completed').nextAll().empty();
                    $('#Todo').nextAll().empty();
                    taskData(response);
                    loadPortlets();
                } else {
                    appendEmptyToDo(TASK_LOCALIZATION.failed_task, 'red');
                }
            } else if (response.code == 400) {
                appendEmptyToDo(TASK_LOCALIZATION.no_task, 'blue');
            }
        },
        error: function () {
            return customErrorHandler(TASK_LOCALIZATION.project_error)
        }
    });
}

function createTask() {
    // if( $("#newTaskDescription").val().length > description_count){
    //     customErrorHandler(TASK_LOCALIZATION.exceeded);
    //     return false;
    // }

    let newTodoData = {
        name: $('#newTaskName').val(),
        description: ($('#newTaskDescription').val() == "") ? "NA" : $('#newTaskDescription').val().replace(/'/g, "&quot;"),
        start_date: $('#newTaskStartDate').val(),
        end_date: $('#newTaskEndDate').val(),
        project_id: $('#editProject').attr('value'),
        module_id: $('#newTaskModule').children(":selected").attr("value") == 0 || $('#newTaskModule').children(":selected").attr("value") == undefined ? "" : $('#newTaskModule').children(":selected").attr("value"),
        status: NEW_TASK_STATUS,
        progress: 0,
        priority: $("#newTaskPriority").children(":selected").attr("value"),
        employee_id: $('#newTaskAssignee').children(":selected").attr("value"),
        employee_name: $('#newTaskAssignee').children(":selected").text(),
    };

    $.ajax({
        type: "post",
        url: '/' + userType +'/create-task',
        data: newTodoData,
        beforeSend: function () {
            $('#taskNameErrors, #taskStartErrors, #taskEndErrors, #taskAssigneErrors').html(" ");
        },
        success: function (response) {
            response.name !== undefined ? $('#todoNameError').removeAttr('hidden') : $('#todoNameError').attr('hidden', true);
            response.start_date !== undefined ? $('#todoStartDateError').removeAttr('hidden') : $('#todoStartDateError').attr('hidden', true);
            response.end_date !== undefined ? $('#todoEndDateError').removeAttr('hidden') : $('#todoEndDateError').attr('hidden', true);
            response.description !== undefined ? $('#todoDescriptionError').removeAttr('hidden') : $('#todoDescriptionError').attr('hidden', true);
            response.employee_id !== undefined ? $('#todoAssigneeError').removeAttr('hidden') : $('#todoAssigneeError').attr('hidden', true);
            response.module_id !== undefined ? $('#todoModuleNameError').removeAttr('hidden') : $('#todoModuleNameError').attr('hidden', true);
            if (response.code == 200) {
                $('#task_list_add').modal('toggle');
                if (response.data) {
                    $('#empty').empty();
                    response.data.project_module_name = $('#newTaskModule').children(":selected").text();
                    taskData({data: [response.data]});
                    loadPortlets();
                    $("#count").text(" ");
                    customSuccessHandler(TASK_LOCALIZATION.task_success);
                }
            } else if (response.code === 201) {
                for (i = 0; i < 6; i++) {
                    switch (response.msg[i]) {
                        case tasks+' '+name_required:
                            $('#taskNameErrors').html(tasks+' '+name_required);
                            break;
                        case tasks+' '+name_invalid:
                            $('#taskNameErrors').html(tasks+' '+name_invalid);
                            break;
                        case tasks+' '+name_min:
                            $('#taskNameErrors').html(tasks+' '+name_min);
                            break;
                        case tasks+' '+name_max:
                            $('#taskNameErrors').html(tasks+' '+name_max);
                            break;
                        case start_dates:
                            $('#taskStartErrors').html(start_dates);
                            break;
                        case end_dates:
                            $('#taskEndErrors').html(end_dates);
                            break;
                        case employee+' '+name_required:
                            $('#taskAssigneErrors').html(employee+' '+name_required);
                            break;
                    }
                }
            } else {
                return errorHandler(response.msg)
            }
        },
        error: function (jqXHR) {
            if(jqXHR.status == 410)  {
                console.log("the 410---->",jqXHR.responseJSON.error);
            }
            else return customErrorHandler(TASK_LOCALIZATION.project_error);
        }
    });
}

function deleteTodo(id) {
    $.ajax({
        type: "delete",
        url: '/' + userType +'/delete-task',
        data: {
            task_ids: [id]
        },
        beforeSend: function () {
        },
        success: function (response) {
            if (response.code == 200) {
                $('#task' + id + '').remove();
                return customSuccessHandler(TASK_LOCALIZATION.task_removed);
            } else {
                return errorHandler(response.msg);
            }
        },
        error: function (jqXHR) {
            if(jqXHR.status == 410)  {
                console.log("the 410---->",jqXHR.responseJSON.error);
            }
            else return customErrorHandler(TASK_LOCALIZATION.project_error);
        }
    });
}

function updateTask() {
    if($('#taskUpdateName').val() != "") {
        $('#updtodoNameError').attr('hidden', true);
    }else {
        $('#uptaskNameErrors').html(" ");
        $('#updtodoNameError').removeAttr('hidden');
        return false;
    }
    // if( $("#taskUpdateDescription").val().length > 1995){
    //     customErrorHandler(TASK_LOCALIZATION.exceeded);
    //     return false;
    // }
    let data = {
        task_id: $('#updateTaskId').attr('value'),
        employee_id: $('#taskUpdateAssigneeName').children(":selected").attr("value"),
        status: $('#taskUpdateStatus').find(":selected").attr('id').replace('taskStatus', ''),
        priority: $('#taskUpdatePriority').find(":selected").attr('value'),
        // module_id: $('#taskUpdateAssigneeName').find(":selected").attr('value'),
        description: ($('#taskUpdateDescription').val() == "") ? "-" : $('#taskUpdateDescription').val(),
    };
    TODO_NAME !== $('#taskUpdateName').val() ? data.name = $('#taskUpdateName').val() : {};
    TODO_START_DATE !== $('#taskUpdateStartDate').val() ? data.start_date = $('#taskUpdateStartDate').val() : {};
    TODO_END_DATE !== $('#taskUpdateEndDate').val() ? data.end_date = $('#taskUpdateEndDate').val() : {};
    $.ajax({
        type: "put",
        url: '/' + userType +'/update-task',
        data: data,
        beforeSend: function () {
            $('#uptaskNameErrors, #uptaskAssigneeNameErrors, #updtodoNameError').html(" ");
        },
        success: function (response) {
            response.start_date !== undefined ? $('#updateTodoStartDateError').removeAttr('hidden') : $('#updateTodoStartDateError').attr('hidden', true);
            response.end_date !== undefined ? $('#updateTodoEndDateError').removeAttr('hidden') : $('#updateTodoEndDateError').attr('hidden', true);
            response.description !== undefined ? $('#updateTodoDescriptionError').removeAttr('hidden') : $('#updateTodoDescriptionError').attr('hidden', true);
            response.employee_id !== undefined ? $('#updateTodoAssigneeError').removeAttr('hidden') : $('#updateTodoAssigneeError').attr('hidden', true);
            if (response.code == 200) {
                $('#todo_list_update').modal('toggle');
                $('#task' + $('#updateTaskId').attr('value') + '').remove();
                if (response) {
                    response.data.start_date = $('#taskUpdateStartDate').val();
                    response.data.end_date = $('#taskUpdateEndDate').val();
                    response.data.name = $('#taskUpdateName').val();
                    response.data.employee_name = $('#taskUpdateAssigneeName').children(":selected").text();
                    taskData({data: [response.data]});
                    loadPortlets();
                    $("#count1").text(" ");
                    customSuccessHandler(TASK_LOCALIZATION.task_update);
                }
            } else if (response.code == 201) {
                for (i = 0; i < 4; i++) {
                    switch (response.msg[i]) {
                        case tasks+' '+name_invalid:
                            $('#uptaskNameErrors').html(tasks+' '+name_invalid);
                            break;
                        case tasks+' '+name_min:
                            $('#uptaskNameErrors').html(tasks+' '+name_min);
                            break;
                        case tasks+' '+name_max:
                            $('#uptaskNameErrors').html(tasks+' '+name_max);
                            break;
                        case employee+' '+name_required:
                            $('#uptaskAssigneeNameErrors').html(employee+' '+name_required);
                            break;
                    }
                }
            }else{
                return errorHandler(response.msg)
            }
        },
        error: function (jqXHR) {
            if(jqXHR.status == 410)  {
                console.log("the 410---->",jqXHR.responseJSON.error);
            }
            else return customErrorHandler(TASK_LOCALIZATION.project_error);
        }
    });
}

//drag and drop
function updateTaskStatus(task_id, status) {
    let currentDiv = $('#task' + task_id).closest(".taskDiv");
    let data = {
        task_id,
        status,
        employee_id: currentDiv.find(".taskAssigneeId").attr('id'),
        description: currentDiv.find('.taskDescription').text(),
    };

    $.ajax({
        type: "put",
        url: '/' + userType +'/update-task',
        data: data,
        beforeSend: function () {
        },
        success: function (response) {
            if (response.code == 200) {
                if (response) {
                    let Status = response.data.status == 0 ? 'Hold' : response.data.status == 1 ? 'Process' : response.data.status == 2 ? 'Completed' : 'Todo';
                    $('#task' + task_id).attr('value',Status);
                    //just change the value of status
                    currentDiv.find(".taskStatus").text(status);
                    customSuccessHandler(TASK_LOCALIZATION.task_status);
                }
            } else if (response.code == 400) {
                $('#task' + status + '').remove();
                return errorHandler(response.msg)
            }
        },
        error: function () {
            return customErrorHandler(TASK_LOCALIZATION.project_error);
        }
    });
}

$("#filterModules").change(function () {
    getALLToDos($('#editProject').attr('value'), 0, 0,$(this).children(":selected").attr("value"));
});

function addProjectModalLoad() {
    if (!USERS_LIST_CHECK) getAllUsersByRole(sessionStorage.getItem('employeeRole'));
    if (!MANAGERS_LIST_CHECK) getAllUsersByRole(sessionStorage.getItem('managerRole'));
}

function getAllUsersByRole(role) {
    $.ajax({
        type: "get",
        url: '/' + userType + '/users',
        data: {LocationId: 0, role_id: role, DepartmentId: 0},
        beforeSend: function () {
        },
        success: function (response) {
            if (response.code == 200) {
                if (response.data && response.data.length > 0) {
                    let appendData = '';
                    response.data.map(user => {
                        appendData += '<option value="' + user.u_id + '"> ' + user.first_name +' '+ user.last_name + '</option>'
                    });
                    role === sessionStorage.getItem('managerRole') ? ($('#updateProManagersLists').empty(), $('#updateProManagersLists').append(appendData)) : ($('#updateProMembersLists').empty(), $('#updateProMembersLists').append(appendData));
                    role === sessionStorage.getItem('managerRole') ? MANAGERS_LIST_CHECK = true : USERS_LIST_CHECK = true;
                }
            }
        },
        error: function () {
            return customErrorHandler(PROJECTS_LOCALIZATION.swal_wrong+'...!!!')
        }
    });
}

function getMangAndEmpList(project_id) {
    $.ajax({
        type: "post",
        url: '/' + userType +'/get-project-members',
        data: {project_id},
        beforeSend: function () {
            // $('#updateProManagersLists').empty();
            // $('#updateProMembersLists').empty();
        },
        success: function (response) {
            if (response.code == 200) {
                if (response.data && response.data.length > 0) {
                    let emp_ids = [];
                    let manager_ids = [];
                    response.data.map(emp => {
                        (emp.role_name == "Employee") ? emp_ids.push(emp.employee_id):manager_ids.push(emp.employee_id);
                    });
                    $('#updateProManagersLists').val(manager_ids).trigger('change');
                    $('#updateProMembersLists').val(emp_ids).trigger('change');
                }
            } else if (response.code == 400) {
                $('#updateProManagersLists').val([]).trigger('change');
                $('#updateProMembersLists').val([]).trigger('change');
            } else {
                $('#updateProManagersLists').val([]).trigger('change');
                $('#updateProMembersLists').val([]).trigger('change');
                errorHandler(response.msg);
            }
        },
        error: function () {
            return customErrorHandler(PROJECTS_LOCALIZATION.swal_wrong+'...!!!')
        }
    });
}

// to know the selected modal
function productiveModal(option,skip) {
    // option 1-->productive 2--> unproductive  3---> neutral
    if (option == 1) $("#ModalName").html(TASK_PAGE_JS.productive_time);
    else if (option == 2) $("#ModalName").html(TASK_PAGE_JS.unproductive_time);
    else if (option == 3) $("#ModalName").html(TASK_PAGE_JS.neutralTime)
    else if (option == 4) $("#ModalName").html(TASK_PAGE_JS.idealTime);
    MODAL_OPENED = option; // modal opened
    let id = projectDetails.id; // project id
    $.ajax({
        url: "/" + userType + '/task-details',
        type: 'post',
        data: {option, id, SHOW_ENTRIES,SEARCH_TEXT,skip,SORT_ORDER,SORT_NAME},
        beforeSend: function () {
            $("#tableData").empty();
            $("#SearchButton").attr('disabled',true);
            $("#loader").css('display','inline');
        },
        success: function (response) {
            $("#loader").css('display','none');
            $("#SearchButton").attr('disabled',false);
            if (response.code == 200) {
                response.data.apps.map(function (appData) {
                    let d;
                    if (option == 1) d = Number(appData.pro);
                    else if (option == 2) d = Number(appData.non);
                    else if (option == 3) d = Number(appData.neu)
                    else if (option == 4) d = Number(appData.idle);
                    let h = Math.floor(d / 3600).toString().length === 1 ? ("0" + Math.floor(d / 3600)) : Math.floor(d / 3600);
                    let m = Math.floor(d % 3600 / 60).toString().length === 1 ? ("0" + Math.floor(d % 3600 / 60)) : Math.floor(d % 3600 / 60);
                    let s = Math.floor(d % 3600 % 60).toString().length === 1 ? ("0" + Math.floor(d % 3600 % 60)) : Math.floor(d % 3600 % 60);
                    $("#tableData").append('<tr>');
                    $("#tableData").append('<td>' + appData.task_name + '</td><td>' + appData.app + '</td>');
                    $("#tableData").append('<td>' + h + ":" + m + ":" + s + '</td>');
                    $("#tableData").append('</tr>');

                    if (PAGE_COUNT_CALL === true) {
                        TOTAL_COUNT_EMAILS = response.data.count;
                        paginationSetup(0);  //if your module have delete & Add records make it 1
                        TOTAL_COUNT_EMAILS < SHOW_ENTRIES ? $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' ' + 1 + ' ' + DATATABLE_LOCALIZE_MSG.to + ' ' + TOTAL_COUNT_EMAILS + ' ' + DATATABLE_LOCALIZE_MSG.of + ' ' + TOTAL_COUNT_EMAILS)
                            : $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' ' + 1 + ' ' + DATATABLE_LOCALIZE_MSG.to + ' ' + SHOW_ENTRIES + ' ' + DATATABLE_LOCALIZE_MSG.of + ' ' + TOTAL_COUNT_EMAILS);
                    }
                })

            } else {
                $("#tableData").append('<tr><td></td><td>' + response.msg + '</td><td></td></tr>');
                TOTAL_COUNT_EMAILS = 0;
                paginationSetup(0); //if your module have delete & Add records make it 1
                // $('.pagination').jqPagination();
                $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' ' + 0 + ' ' + DATATABLE_LOCALIZE_MSG.to + ' ' + 0 + ' ' + DATATABLE_LOCALIZE_MSG.of + ' ' + 0);

            }
        },
        error: function () {
            TOTAL_COUNT_EMAILS = 0;
            paginationSetup(0); //if your module have delete & Add records make it 1
            $('.pagination').jqPagination();
            $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' ' + 0 + ' ' + DATATABLE_LOCALIZE_MSG.to + ' ' + 0 + ' ' + DATATABLE_LOCALIZE_MSG.of + ' ' + 0);
            $("#loader").css('display','none');
            $("#SearchButton").attr('disabled',false);
            $("#tableData").append('<tr><td></td><td>' + TASK_PAGE_JS.NoDataAvailable+ '</td><td></td></tr>');
        }
    })

}

//call the function once on change of pagination
let CalledUserFunction = (skip, SearchText) => {
    SEARCH_TEXT = SearchText;
    productiveModal(MODAL_OPENED,skip, SearchText);
};
// to know the modal was closed
$('#ProductiveTime').on('hidden.bs.modal', function (e) { makeDatatableDefault(); SHOW_ENTRIES="10";$("#"+SHOW_ENTRIES).attr('selected',false); $("#10").attr('selected',true);SEARCH_TEXT=null});

function showmore(id) {
    var dots = document.getElementById("dots" + id);
    var moreText = document.getElementById("more" + id);
    var btnText = document.getElementById("myBtn" + id);

    if (dots.style.display === "none") {
        dots.style.display = "inline";
        btnText.innerHTML = "show more";
        moreText.style.display = "none";
    } else {

        dots.style.display = "none";
        btnText.innerHTML = "show less";
        moreText.style.display = "inline";
    }
}

function  getTaskTimesheets(task_id) {
    $.ajax({
        url: "/" + userType + '/task-timesheets',
        type: 'post',
        data: {task_id},
        beforeSend: function () {
            $("#loader").css('display','inline');
            $('#taskData').empty();
        },
        success: function (response) {
            $("#taskLoader").css('display','none');
            if (response.code == 200) {
                let data = "";
                let duration = "";
                response.data.timesheets.map(function (log) {
                    duration = convertTime(Number(log.duration));
                    data += '<tr><td>' + moment(log.start_time).tz(response.data.timezone).format('DD-MM-YYYY HH:mm:ss') + '</td><td>' + moment(log.end_time).tz(response.data.timezone).format('DD-MM-YYYY HH:mm:ss') + '</td><td>' + duration + '</td></tr>';
                })
                $("#taskData").append(data);
                $('#taskTable').dataTable();
            } else {
                $("#taskLoader").css('display','none');
                $("#taskData").append('<tr><td></td><td>' + response.msg + '</td><td></td></tr>');}
        },
        error: function () {
            $("#taskLoader").css('display','none');
            $("#taskData").append('<tr><td></td><td>' + TASK_PAGE_JS.NoDataAvailable+ '</td><td></td></tr>');
        }
    })
}

function convertTime(d) {
    let h = Math.floor(d / 3600).toString().length === 1 ? ("0" + Math.floor(d / 3600)) : Math.floor(d / 3600);
    let m = Math.floor(d % 3600 / 60).toString().length === 1 ? ("0" + Math.floor(d % 3600 / 60)) : Math.floor(d % 3600 / 60);
    let s = Math.floor(d % 3600 % 60).toString().length === 1 ? ("0" + Math.floor(d % 3600 % 60)) : Math.floor(d % 3600 % 60);
    return h + ":" + m + ":" + s;
}
