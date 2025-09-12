var totalEmployees = 0;
var adminSession = null;
var AssignedMnagerList = [];
var UserCompleteList = [];
var Assigned = [];
var TEAMLEAD = [];
var TL_OR_M = "";
var COMPLETE_ROLES_LIST = $("#RoleData").data('list');
let fileinput1 = false ; 
let SHOW_ENTRIES = "10";
let TOTAL_COUNT_EMAILS;  // total count of users
let ACTIVE_PAGE = 1;
let PAGE_COUNT_CALL = true;
let ENTRIES_DELETED;
let SORT_NAME = '';
let SORT_ORDER = '';
let SORTED_TAG_ID = '';
let SELECTED_CHECKBOXES = [];  // TO store the all selected checkboxes from the differenect pages
let ACTIVE_STATUS = 1;
let SEARCH_TEXT = null; 
let ADD_REMOVE_COLUMN = ["Email","EmpCode","ComputerName","version","roles"];
let ORIGINAL_TABLE_HEADER =  ["Email","EmpCode","ComputerName","version"];
let START_DATE = moment().subtract(6, 'days').format('YYYY-MM-D'), END_DATE= moment().format('YYYY-MM-D');
let COLLAPSE_MERGE_ID=[];
//global variables end
let validation = ["First Name", "Last Name", "Full Name", "UserName", "Email", "Employee Code", "Employee Unique Id", "Location", "Department", "Role"];

 
$(document).ready(function () {
    checkAdmin();
    $("#date_joinCalender").datepicker({
        uiLibrary: 'bootstrap4',
        maxDate: new Date,
    });
    $("#expiry_period").datepicker({
        uiLibrary: 'bootstrap4',
    });
});

function changeNoOfDays(e){
     const selectedOption = e.target.options[e.target.selectedIndex];
     const noOfDays = selectedOption.getAttribute('data-days');
     $("#expiry_period").val(moment().add(noOfDays, 'days').format('DD/MM/YYYY'))
}
//  for eye icons
$(".toggle-password-show, .toggle-password-show-c").click(function () {
    $(this).toggleClass("fa-eye fa-eye-slash");
    var input = $($(this).attr("toggle"));
    if (input.attr("type") == "password") {
        input.attr("type", "text");
    } else {
        input.attr("type", "password");
    }
});
$(".toggle-password-show-edit, .toggle-password-show-edit-c").click(function () {
    $(this).toggleClass("fa-eye fa-eye-slash");
    var input = $($(this).attr("toggle"));
    if (input.attr("type") == "password") {
        input.attr("type", "text");
    } else {
        input.attr("type", "password");
    }
});
// // Vanilla Javascript
let tel = document.querySelector("#telephone");
let iti = window.intlTelInput(tel, {
    utilsScript: "../assets/plugins/intel-tel-input/utils.js",
    initialCountry: "in",
    separateDialCode: true,
    customContainer: "col-md-12 no-padding intelinput-styles"
});
let errorMsg = document.querySelector("#error-msg"),
    validMsg = document.querySelector("#valid-msg");
let errorMap = [phone_number_error_msg.invalid_number, phone_number_error_msg.invalid_country_code, phone_number_error_msg.too_short, phone_number_error_msg.too_long];
let reset = function () {
    $('#Contact').text("");
    $("#Contact").attr('data-value', "");
    tel.classList.remove("error");
    errorMsg.innerHTML = "";
    errorMsg.classList.add("visable");
    validMsg.classList.add("visable");
};
// on blur: validate
tel.addEventListener('blur', function () {
    reset();
    if (tel.value.trim()) {
        if (iti.isValidNumber()) {
            $("#validContact").val(1);
            validMsg.classList.remove("visable");
            $('#valid-msg').html('valid');
        } else {
            $("#validContact").val(5);
            $('#valid-msg').text("");
            tel.classList.add("error");
            let errorCode = iti.getValidationError();
            errorMsg.innerHTML = errorMap[errorCode];
            errorMsg.classList.remove("visable");
        }
    }
});

// {{-- added for the checkbox multiple--}}
$('.multipleCheck, .multipleCheckManager').click(function (e) {
    e.stopPropagation();
    if ($(this).hasClass('multipleCheck')) {
        $(this).is(':checked') ? $(".open-checkBoxModalld").prop("checked", true) : $(".open-checkBoxModalld").prop("checked", false);
        actionsEnable()
    } else {
        $(this).is(':checked') ? $(".open-SelectedcheckBoxModalld").prop("checked", true) : $(".open-SelectedcheckBoxModalld").prop("checked", false);
        CheckboxEnable()
    }
});
$(document.body).on('click', '.open-checkBoxModalld,.open-SelectedcheckBoxModalld', function (e) {
    e.stopPropagation();
    if ($(this).hasClass('open-SelectedcheckBoxModalld')) {
        if (jQuery(".open-SelectedcheckBoxModalld").length == jQuery(".open-SelectedcheckBoxModalld:checked").length) {
            jQuery(".multipleCheckManager").prop("checked", true);
        } else {
            jQuery(".multipleCheckManager").prop("checked", false);
        }
    } else {
        if (jQuery(".open-checkBoxModalld").length == jQuery(".open-checkBoxModalld:checked").length) {
            jQuery(".multipleCheck").prop("checked", true);
        } else {
            jQuery(".multipleCheck").prop("checked", false);
        }
    }
});

//checking user is admin or manager
function checkAdmin() {
    // if (userType === "admin") {
    //     getUsers( SHOW_ENTRIES, 0, "", "", "")
    // } else {
    getUsers( SHOW_ENTRIES, 0, "", "", "", ACTIVE_STATUS);
    // getManagerUsers(userId, 0, 0, 0);
    // }
}

//get locations in employee registration form
function getLocationsRegistrationS() {
    $.ajax({
        type: "get",
        url: "/" + userType + '/get-all-locations',
        beforeSend: function () {
            $('#locations-addEmp').empty();
        },
        success: function (response) {
            if (response.code == 200) {
                var locationData = response.data;
                var location;
                var departementID;
                location += '<option selected disabled=true>' + SELECT_MSG + '' + LOCATION_MSG + '</option>';
                for (var i = 0; i < locationData.length; i++) location += '<option id="' + locationData[i].id + '">' + locationData[i].name + '</option>';
                $('#locations-addEmp').append(location);
            }
        },
        error: function () {
            errorSwal()
        }
    });
}


$("#locations").change(function () {

    let id = $(this).children(":selected").attr("id");
    LocationId = $(this).children(":selected").attr("id");
    RoleId = $("#roles").children(":selected").attr("id");
    DepartementId = ""; 
    makeDatatableDefault();
    SEARCH_TEXT = null;
    getUsers( SHOW_ENTRIES, 0, "", "", "", ACTIVE_STATUS);
    getDepartments(LocationId, 0,RoleId);
});

//Function for getting roles
function getRoles(id, check, employeeId) {
    $("#SingleAppendTLlist").empty();
    $(".js-example-tokenizer").val([]);
    $("#AppendManagerList").select2({
        dropdownParent: $("#AppendManagerList").parent()
    });

    if (COMPLETE_ROLES_LIST.length == 0) {
        $.ajax({
            type: "get",
            url: "/" + userType + '/get-roles',
            beforeSend: function () {
                $("#AllRolesAppend").empty();
                $('#CompletRoles1').empty();
                $('#CompletRoles2').empty();
            },
            success: function (response) {
                if (response.code == 200) {
                    COMPLETE_ROLES_LIST = response.data;
                    AssignRoles(id, check, employeeId)
                }
            },
            error: function () {
                errorSwal()
            }
        });
    } else AssignRoles(id, check, employeeId);
}

//function for roles append at the assign and manager roles
let AssignRoles = (id, check, employeeId) => {
    $("#AllRolesAppend").empty();
    $('#CompletRoles1').empty();
    $('#CompletRoles2').empty();
    let roleData = COMPLETE_ROLES_LIST;

    if (id != null) {
        (employeeId != null) ? getdetails(employeeId, 2) : null;

        let RolesPermission = null;
        RolesPermission += '<option id="" selected disabled>' + SELECT_MSG + '' + ROLE_MSG + '</option selected>';
        roleData.forEach(function (data) {
            if (data.name != "Admin" && data.id != id) RolesPermission += '<option id="' + data.id + '">' + data.name + '</option>';
        })

        // $("#AllRolesAppend").append(RolesPermission);
    } else {
        let roles;
        let rolesRegistration, AssignRoles;
        AssignRoles += '<option selected disabled>' + SELECT_MSG + ' ' + ROLE_MSG + '</option>';
        for (let i = 0; i < roleData.length; i++) {
            if (roleData[i].name != "Admin" && (check !== 1 && roleData[i].name.toLowerCase() != "employee")) AssignRoles += '<option id="' + roleData[i].id + '">' + roleData[i].name + '</option>';
        }
        $('#CompletRoles1').append(AssignRoles);
        $('#CompletRoles2').append(AssignRoles);
    }
}

//onchange function for getting the selected role
$("#CompletRoles1").change(function () {
    roleList(1, $(this).children(":selected").attr("id"))            // one for getting all role list multiple list
});
$("#CompletRoles2").change(function () {
    roleList(2, $(this).children(":selected").attr("id"))       // 2 for selected roe list single dropdown modal

});

//    function to select role list
function roleList(selected, RoleId) {
    var AssignedId = $("#AssignedId").val() != null ? $("#AssignedId").val() : null;
    $.ajax({
        type: "get",
        url: "/" + userType + "/Manager-list",
        data: {
            RoleId: RoleId, AssignedId: AssignedId
        },
        beforeSend: function () {
            $("#AppendManagerList").empty();
            $("#SingleAppendTLlist").empty();
            $(".js-example-tokenizer").val([]);
            $("#AppendManagerList").select2({
                dropdownParent: $("#AppendManagerList").parent()
            });
        },
        success: function (response) {
            if (response.code = 200) {
                let data = response.data;
                $("#SingleAppendTLlist").append('<option>' + SELECT_MSG + ' ' + EMPLOYEE_MSG + '</option>');
                var AssignedList = response.superiorCode == 200 ? response.superiorData : [];

                for (var i = data.length - 1; i >= 0; i--) {
                    for (var j = 0; j < AssignedList.length; j++) {
                        if (data[i] && (data[i].id === AssignedList[j].user_id)) {
                            data.splice(i, 1);
                        }
                    }
                }
                if (AssignedList.length != 0) {
                    AssignedList.forEach(function (Assigned) {
                        $("#AppendManagerList").append('<option selected value="' + Assigned.user_id + '" id="' + Assigned.user_id + '">' + Assigned.first_name + " " + Assigned.last_name + '</option>');
                    })
                }


                data.forEach(function (roles) {
                    $("#AppendManagerList").append('<option  value="' + roles.id + '" id="' + roles.id + '">' + roles.full_name + '</option>');
                    $("#SingleAppendTLlist").append('<option value="' + roles.id + '"  id="' + roles.id + '">' + roles.full_name + '</option>');
                })
            }

        },
        error: function () {
            errorSwal("Ajax error with getting role list...")
        }
    })
}

// onchange function for roleID in registration
$("#role-addEmp").change(function () {
    var id = $(this).children(":selected").attr("id");
});

//roles for edit the employee
$("#role-EditEmployee").change(function () {
    var id = $(this).children(":selected").attr("id");
});

//on-change for Roles for fetching the details in dashboard
$("#roles").change(function () {  
    makeDatatableDefault();
    SEARCH_TEXT = null;
    getUsers( SHOW_ENTRIES, 0, "", "", "", ACTIVE_STATUS);
});

//for getting multiple departement id's
$("#departmentsAppend").change(function () {

    deptIds = [];
    let batchID = $('#departmentsAppend').select2('data');
    batchID.forEach(function (dept) {
        deptIds.push(dept.id);
    });
    let id_s = deptIds.toString();
    DepartementId = id_s;
    document.cookie = "DepartmentCookie =" + CryptoJS.AES.encrypt(id_s, COOKIE_CONFIG_KEY) +";secure;samesite=Strict";
    // (userType === "admin") ? (getUsers( SHOW_ENTRIES, 0, null), makeDatatableDefault()) : getManagerUsers(userId, LocationId, RoleId, DepartementId);
    makeDatatableDefault();
    SEARCH_TEXT = null;
    getUsers( SHOW_ENTRIES, 0, "", "", "", ACTIVE_STATUS);
    sessionStorage.setItem("DepartementId", id_s);

});

//This function is for checking the status times with database time
function status(times) {
    var localDate = new Date(times);
    var hours = localDate.getHours();
    var min = localDate.getMinutes();
    var today = new Date();
    var todayHour = today.getHours();
    var todayMin = today.getMinutes();
    var difTimeMax = Math.max(todayMin, min);
    var difTimeMin = Math.min(todayMin, min);
    var difTime = difTimeMax - difTimeMin;
    return {
        First: hours == todayHour,
        Second: difTime
    }
}

//for getting user list
function getUsers(showEntries, skipvalue, searchText, sortName, sortOrder, activeStatus, CollapseMerge = 0, EmployeeCode = null) {
    let urlRoute = '/EmployeeDetail';
    $.ajax({
        type: "get", 
        url: "/" + userType + urlRoute,
        data: { 
            showEntries,
            skipvalue,
            searchText,
            sortName,
            sortOrder,
            activeStatus,
            EmployeeCode,
            CollapseMerge 
        },
        beforeSend: function () { 
            if(CollapseMerge !== 1) {
                $('#fetch_Details').empty();
                $('#empDetails_Table').DataTable().clear().draw();
                $('#empDetails_Table').DataTable().destroy();
                UserCompleteList = [];
                $("#loader").css('display', 'block');
            }
        },
        success: function (response) {
            EmplooyeeDetails(response, CollapseMerge);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            if (jqXHR.status == 410) {
                $("#loader").css('display', 'none');
                $("#addBulkRegBtn").attr("disabled", true);
                $("#editBulkRegBtn").attr("disabled", true);
                $("#add_btn").attr("disabled", true); 
                $('#fetch_Details').empty();
                $('#fetch_Details').append("<tr style='text-align:center;'> <td colspan=12> You don't have the permission to browse the data (Contact Admin)</td></tr>");
            } else errorSwal("Please, Reload and try again...");
            TOTAL_COUNT_EMAILS = 0;
            $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' ' + 0 + ' ' + DATATABLE_LOCALIZE_MSG.to + ' ' + 0 + ' ' + DATATABLE_LOCALIZE_MSG.of + ' ' + 0);
            paginationSetup();
            MAIL_DATA = "";
        }
    });
    $("#SearchButton").attr('disabled', false);
}

let EmplooyeeDetails = (response, CollapseMergeOption) => {
    $("#loader").css('display', 'none');
    if (response.code == 200) {
        empData = userType === "admin" ? response.data.employees: response.data.user_data;
        UserCompleteList.push(empData);
        totalEmployees = response.data.totalCount;
        let appendData = "", empCode = "", id = "";
        
        for (let i = 0; i < empData.length; i++) {
            let collapseMergeTag; 
            id = empData[i].id; 
            if(Number($("#CollapseMergeEmpId").val()) === id) continue;
            if(CollapseMergeOption === 1)COLLAPSE_MERGE_ID.push(id);
            empData[i].employee_code != "" ? empCode = empData[i].employee_code : empCode = "null";
                 appendData +=
                    '<tr '+collapseMergeTag+' >' +
                    '<td class="stickyCol-sticky-col" id="td'+ id + '">' + '<input  class="open-checkBoxModalld mr-4" type="checkbox" name="checkbox" id="SelectCheckbox' + id + '" onclick="actionsEnable(' + id + ')" value="' + id + '" />';
                        appendData += '<a type="cursor" title="' + empData[i]['first_name'] + " " + EMPLOYEE_DETAILS_CONST.ViewDetails + '" href="get-employee-details?id=' + id + '" id="fn' + id + '">' + empData[i]['first_name'] + '</a> ' ;
                    appendData += '</td>';
                if (ADD_REMOVE_COLUMN.includes('Email')) (empData[i].email == null || empData[i].email == "null") ? appendData += '<td style="width:176px" class="EmailTable" id="em' + id + '">-</td>' : appendData += '<td style="width:176px" class="EmailTable" id="em' + id + '">' + empData[i]['email'] + '</td>';
                 
                if (ADD_REMOVE_COLUMN.includes('EmpCode')) empCode == null ? appendData += '<td style="width:127px" class="EmpCodeTable" id="ec' + id + '">-</td>' : appendData += '<td style="width:127px" class="EmpCodeTable" id="ec' + id + '">' + empCode + '</td>';
                if (ADD_REMOVE_COLUMN.includes('roles')) {
                    (empData[i].role == null || empData[i].role == "null") ? appendData += '<td style="width:176px" class="RoleTable" id="ro' + id + '">-</td>' : appendData += '<td style="width:176px" class="RoleTable" id="ro' + id + '">' + empData[i]['role'] + '</td>'
                       
                   }
                if (ADD_REMOVE_COLUMN.includes('ComputerName')) empData[i].computer_name == null ? appendData += '<td  style="width:138px" class="ComputerNameTable" id="sn' + id + '">-</td>' : appendData += '<td  style="width:138px" class="ComputerNameTable" id="sn' + id + '">--</td>';
                if (ADD_REMOVE_COLUMN.includes('version') ) empData[i].software_version == null ? appendData += '<td style="width:57px" class="versionTable" id="sv' + id + '">-</td>' : appendData += '<td style="width:57px" class="versionTable" id="sv' + id + '">1.0.0</td>';;
                 appendData+=`<td>--</td>`
                appendData += '<td class="td-action text-center" id="act' + id + '" class="">';
                appendData += '<a  onclick="getdetails(' + id + ', 2, 0)" id="editedId"  class="open-editModal text-success mr-2"  href="#"  data-toggle="modal" data-target="#editEmpModal" title="' + EMPLOYEE_DETAILS_CONST.empEdit + '"  data-id="' + id + '"><i class="fas fa-user-edit fa-fw" data-toggle="tooltip" data-placement="top" ></i></a>';
                appendData += '<a id="delete" class="open-editModal text-danger mr-2" href="#"  data-toggle="modal" data-target="#DeleteSingleModal" title="' + EMPLOYEE_DETAILS_CONST.empDelete + '" data-id="' + id + '"> <i class="far fa-trash-alt" data-toggle="tooltip" data-placement="top"></i></a>';
           
            appendData += '</td></tr>';
        }

        if(CollapseMergeOption === 1) {
            $('#Table' + $("#collapseMergeColumnId").val()).empty();
            $('#Table' + $("#collapseMergeColumnId").val()).append(appendData);
        }else {
            $('#fetch_Details').empty();
            $('#fetch_Details').append(appendData);
        }
        if (PAGE_COUNT_CALL === true) {
            TOTAL_COUNT_EMAILS = response.data.totalCount;
            ENTRIES_DELETED = (TOTAL_COUNT_EMAILS < SHOW_ENTRIES) ? TOTAL_COUNT_EMAILS : SHOW_ENTRIES;
            paginationSetup();
            TOTAL_COUNT_EMAILS < SHOW_ENTRIES ? $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' ' + 1 + ' ' + DATATABLE_LOCALIZE_MSG.to + ' ' + TOTAL_COUNT_EMAILS + ' ' + DATATABLE_LOCALIZE_MSG.of + ' ' + TOTAL_COUNT_EMAILS)
                : $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' ' + 1 + ' ' + DATATABLE_LOCALIZE_MSG.to + ' ' + SHOW_ENTRIES + ' ' + DATATABLE_LOCALIZE_MSG.of + ' ' + TOTAL_COUNT_EMAILS);
        }
        if (SELECTED_CHECKBOXES.length != 0) {
            SELECTED_CHECKBOXES.map(function (value) {
                $("#" + value).css("background-color", "#dfe6ec");
                $('#SelectCheckbox' + value).prop('checked', true);
            })
        } else $("#SelectAllCheckBox").prop('checked', false);
        setTimeout(function () {
            if (jQuery(".open-checkBoxModalld").length == jQuery(".open-checkBoxModalld:checked").length) {
                jQuery(".multipleCheck").prop("checked", true);
            }
        }, 100)
    } else if (response.code == 400) {
        //    error message
        $("#SelectAllCheckBox").prop('checked', false);
        TOTAL_COUNT_EMAILS = 0;
        $('#fetch_Details').append("<tr style='text-align:center;'> <td colspan=10 >" + noData + "! </td></tr>");
        $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' ' + 0 + ' ' + DATATABLE_LOCALIZE_MSG.to + ' ' + 0 + ' ' + DATATABLE_LOCALIZE_MSG.of + ' ' + 0);
        MAIL_DATA = "";
        paginationSetup();
        // $('.pagination').jqPagination();
    }
}

// collapse merge columns
let collapseMergeColumns = (tableAppendId,id) => {
    $("#CollapseMergeEmpId").val(id);
    getUsers( SHOW_ENTRIES, 0, $("#SearchTextField").val(), SORT_NAME, SORT_ORDER, ACTIVE_STATUS, 1 , id);
    $("#collapseMergeColumnId").val(tableAppendId);
}

//function for loading the roles and getting the details of the user
function editLoad(id) {
    getdetails(id)
}

//For plane validating
function planValidate(value) {
    $('#empReg').attr("disabled", false);
    $("#f_upload").siblings(".custom-file-label").html("");
    $('#error-msg').text("");
    $('#valid-msg').text("");
    $('#usrname').text("");
    $('#FullName').text("");
    $('#EmailAddress').text("");
    $('#Paswd').text("");
    $('#CPaswd').text("");
    $('#Contact').text("");
    $('#EmpCodeError').text("");
    $('#ErrorLocationId').text("");
    $('#DeptIdErrorMessage').text("");
    $('#AddressBox').text("");
    $('#RoleIdError').text("");
    $('#Join').text("");
    $('#ErrorTimeZone').text("");
    $("#emp-register").trigger("reset");
    $('#addEmpModal').modal('show');
    
}
 

//   onclick event for upgrade to manager
$(document).on("click", ".open-upgradeModal", function () {
    var id = $(this).data('id');
    $(".upgrade-loc").attr('value', id);
    $(".degrade-loc").attr('value', id);
});

//  onclick event for edit details of employee for sending id
$(document).on("click", ".open-editModal", function () {
    var id = $(this).data('id');
    $(".edit-loc").attr('value', id);
    $(".Assin-loc").attr('value', id);
    $(".delete-loc").attr('value', id);
});

//onclick function for delete particular user
$(document).on("click", ".open-deleteModal", function () {
    var id = $(this).data('id');
    $(".rename-loc").attr('value', id);
});

//employee reg submit add new employeee
$(document).on('submit', '#emp-register', function (e) {
    e.preventDefault(); 
    let form = document.getElementById('emp-register');
    let formData = new FormData(form); 
    let TimeZoneOffset = $('#timeZoneAppend option:selected').attr('data-offset');
    let TimeZoneName = $('#timeZoneAppend option:selected').attr('data-zone');
    let ContactCheck = $("#validContact").val(); 
    formData.append('TimeZoneOffset', TimeZoneOffset);
    formData.append('TimeZoneName', TimeZoneName);
    formData.append('ContactCheck', ContactCheck); 

    $.ajax({
        type: "post",
        url: "/" + userType + '/register-Employee',
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        beforeSend: function () {
            $('#empReg').attr("disabled", true);
            $("#loaderForm1").css('display', 'block');
            ClearErrorForm();
        },
        success: function (response) {
            ErrrorsOfForms(response)
            $('#empReg').attr("disabled", false);
            if (response.code == 200 || response.code == 406) {
                $('#addEmpModal').modal('hide');
                $("#loaderForm1").css('display', 'none');
                $('#empReg').attr("disabled", false);
                $('table#empDetails_Table tr#one' + response.data.user_id + '').add();
                //incrementing totalEmployees on success.
                totalEmployees += 1;
                console.log(response.msg);
                
                response.code == 200 ? successTourMessage(response.msg) : errorSwal(response.msg);
                document.getElementById('emp-register').reset();
                ClearErrorForm();
                deleteAddShowEntries(2)
                //for append the registered data without reloading
                let appendData = "";
                let id = response.data.id;
                let empData = response.data;
                let projectname = empData['project_name'] != "" ? empData['project_name'] : "-";
                appendData += '<tr id=' + id + '><td class="stickyCol-sticky-col" id="fn' + id + '">' + '<input  class="open-checkBoxModalld mr-4" type="checkbox" name="checkbox" id="SelectCheckbox' + id + '" onclick="actionsEnable(' + id + ')" value="' + id + '" /> <a href="get-employee-details?id='+id+'" id="fn' + id + '" >' + empData['firstName'] + ' ' + empData['lastName'] + '</a></td>';
                 if (ADD_REMOVE_COLUMN.includes('Email')) appendData += '<td id="em' + id + '">' + empData['email'] + '</td>';
                if (ADD_REMOVE_COLUMN.includes('EmpCode')) appendData += '<td id="ec' + id + '">' + empData['employeeCode'] + '</td>';
                if (ADD_REMOVE_COLUMN.includes('roles')) appendData += '<td id="er' + id + '">' + empData['role'] + '</td>';
                 appendData += '<td style="width:127px" class="SystemArchitechtureTable" id="sa' + id + '">-</td>';
                if (ADD_REMOVE_COLUMN.includes('ComputerName')) appendData += '<td id="sn' + id + '">-</td>';
                if (ADD_REMOVE_COLUMN.includes('version')) appendData += '<td id="sv' + id + '">-</td>';
                appendData += '<td id="act' + id + '" class="text-center">';
                if (userType === "admin" || $("#PermissionData").data('list').some(obj => obj.permission === "employee_modify")) {
                    appendData += '<a  onclick="getdetails(' + id + ', 2, 0)" id="editedId"  class="open-editModal text-success mr-2"  href="#"  data-toggle="modal" data-target="#editEmpModal" title="' + EMPLOYEE_DETAILS_CONST.empEdit + '"  data-id="' + id + '"><i class="fas fa-user-edit fa-fw" data-toggle="tooltip" data-placement="top" ></i></a>';
                }     
                if (userType === "admin" || $("#PermissionData").data('list').some(obj => obj.permission === "employee_delete")) {
                    appendData += '<a id="delete" class="open-editModal text-danger mr-2" href="#"  data-toggle="modal" data-target="#DeleteSingleModal" title="' + EMPLOYEE_DETAILS_CONST.empDelete + '" data-id="' + id + '"> <i class="far fa-trash-alt" data-toggle="tooltip" data-placement="top"></i></a>';
                }
                appendData += '</td></tr>';

                $('#fetch_Details').prepend(appendData);
            } else if (response.code == 400 || response.code == 404) {
                errorSwal(response.msg, response.error)
                $('#empReg').attr("disabled", false);
            }
            $('#empReg').attr("disabled", false);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            if (jqXHR.status == 410) {
                $("#UnaccessModal").empty();
                $("#UnaccessModal").css('display', 'block');
                $("#UnaccessModal").append('<div class="alert alert-danger text-center"><button type="button" class="close" data-dismiss="alert" >&times;</button><b  id="ErrorMsgForUnaccess"> ' + jqXHR.responseJSON.error + '</b></div>')
            } else errorSwal("Please, Reload and try again...");
            $('#empReg').attr("disabled", false);
            $('#addEmpModal').modal('hide');
            $("#loaderForm1").css('display', 'none');
        }
    });
});

function successTourMessage(msg,step) {
    Swal.fire({
        icon: 'success',
        title: DASHBOARD_JS[msg] || msg || 'success',
        showConfirmButton: true,
        confirmButtonText: DASHBOARD_JS.ok ?? 'OK',
        timer: 4000
    }).then(function () {
        intro_status ? (VALUE_ADDED_DONT_HOLD ? goToStep(step) : (step != 5 ? goToStep(step) : '')) : '';
    });
}
function ClearErrorForm() {
    $('#valid-msg').text("");
    $('#error-msg').text("");
    $('#usrname').text("");
    $('#FullName').text("");
    $('#EmailAddress').text("");
    $('#Paswd').text("");
    $('#CPaswd').text("");
    $('#Contact').text("");
    $('#EmpCodeError').text("");
    $('#ErrorLocationId').text("");
    $('#DeptIdErrorMessage').text("");
    $('#AddressBox').text("");
    $('#RoleIdError').text("");
    $('#Join').text("");
    $('#ErrorTimeZone').text("");
}

function ErrrorsOfForms(response) {
    $("#loaderForm1").css('display', 'none');
    $.each(response, function (index, value) {
        switch (index) {
            case "name":
                if (response.name[0] == 'The name field is required.') document.getElementById('usrname').innerHTML = EMPLOYEE_DETAILS_ERROR.firstName;
                if (response.name[0] == 'The name cannot be longer than 32 characters.') document.getElementById('usrname').innerHTML = EMPLOYEE_DETAILS_ERROR.firstNameLength;
                break;
            case "Full_Name":
                document.getElementById('FullName').innerHTML = EMPLOYEE_DETAILS_ERROR.lastName;
                break;
            case "email":
                document.getElementById('EmailAddress').innerHTML = EMPLOYEE_DETAILS_ERROR.emailError;
                break;
            case "password":
                if (response.password[0] == 'The password field is required.') document.getElementById('Paswd').innerHTML = EMPLOYEE_DETAILS_ERROR.password_field_required;
                if (response.password[0] == 'The password format is invalid.') document.getElementById('Paswd').innerHTML = EMPLOYEE_DETAILS_ERROR.password_invalid;
                break;
            case "confirmPassword":
                document.getElementById('CPaswd').innerHTML = EMPLOYEE_DETAILS_ERROR.passMissmatch;
                break;
            case "number":
                document.getElementById('Contact').innerHTML = response.number;
                break;
            case "empCode":
                document.getElementById('EmpCodeError').innerHTML = EMPLOYEE_DETAILS_ERROR.empCodeError;
                break;
            case "locId":
                document.getElementById('ErrorLocationId').innerHTML = EMPLOYEE_DETAILS_ERROR.empLocationError;
                break;
            case "depId":
                document.getElementById('DeptIdErrorMessage').innerHTML = EMPLOYEE_DETAILS_ERROR.empDeptError;
                break;
            case "address":
                document.getElementById('AddressBox').innerHTML = EMPLOYEE_DETAILS_ERROR.empAdressError;
                break;
            case "roleId":
                document.getElementById('RoleIdError').innerHTML = EMPLOYEE_DETAILS_ERROR.empRoleError;
                break;
            case "date":
                document.getElementById('Join').innerHTML = EMPLOYEE_DETAILS_ERROR.empDOJError;
                break;
            case "TimeZoneOffset":
                document.getElementById('ErrorTimeZone').innerHTML = EMPLOYEE_DETAILS_ERROR.empTimezoneError;

        }
    })

}



//single user delete single
$(document).on('submit', '#emp-singleDelete', function (e) {
    e.preventDefault();
    var form = document.getElementById('emp-singleDelete');
    var formData = new FormData(form);
    $.ajax({
        type: "post",
        url: "/" + userType + '/Emp-Delete',
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            $('#emp-singleDeletee').attr("disabled", true);
            $('#DeleteSingleModal').modal('hide');
        },
        success: function (response) {
            getUsers( SHOW_ENTRIES,0,"","","",0);
            Swal.fire({
                icon: 'success',
                title: response.message,
                showConfirmButton: true
            })
            $('#emp-singleDeletee').attr("disabled", false);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            if (jqXHR.status == 410) {
                $("#UnaccessModal").empty();
                $("#UnaccessModal").css('display', 'block');
                $("#UnaccessModal").append('<div class="alert alert-danger text-center"><button type="button" class="close" data-dismiss="alert" >&times;</button><b  id="ErrorMsgForUnaccess"> ' + jqXHR.responseJSON.error + '</b></div>')
            } else errorSwal(DASHBOARD_JS_ERROR.reload);
            $('#emp-singleDeletee').attr("disabled", false);
            $('#DeleteSingleModal').modal('hide');
        }
    });
});



//enable the Actions if the user selected any checkbox.
function actionsEnable(id) {
    if (id == undefined) {
        if ($(".multipleCheck").is(':checked')) {
            let params = $('input:checkbox').serializeArray();
            $.map(params, function (List) {
                $("#" + List.value).css("background-color", "#dfe6ec");
                if (List.name !== "select_all") SELECTED_CHECKBOXES.push(parseInt(List.value));
            });
        } else {
            let removeArray = [];
            let idSelector = function () {
                (ACTIVE_STATUS === 1) ? $("#" + this.value).css("background-color", "inherit") : $("#" + this.value).css("background-color", "#fff9b2");
                return removeArray.push(parseInt(this.value));
            };
            $(":checkbox:not(:checked)").map(idSelector).get();
            SELECTED_CHECKBOXES = SELECTED_CHECKBOXES.filter((el) => !removeArray.includes(el));
        }
    } else {
        if ($('#SelectCheckbox' + id).is(":checked")) {
            $("#" + id).css("background-color", "#dfe6ec");
            SELECTED_CHECKBOXES.push(id);
        } else {
            (ACTIVE_STATUS === 1) ? $("#" + id).css("background-color", "inherit") : $("#" + id).css("background-color", "#fff9b2");
            SELECTED_CHECKBOXES.splice(SELECTED_CHECKBOXES.indexOf(id), 1);
        }
    }
    $.each($("input[name = 'checkbox']"), function () {
        if ($("input[name = 'checkbox']").is(':checked') || SELECTED_CHECKBOXES.length != 0) {
            $('#editBulkRegBtn').css("display", 'none');

            if (ACTIVE_STATUS === 1) $('#suspend_btn').css("display", 'inline');
            if (ACTIVE_STATUS === 2) $('#Active_btn').css("display", 'inline');
            if (userType === "admin") {
                $('#add_btn').css("display", 'none');
                $('#addBulkRegBtn').css("display", 'none');
                $('#delete_btn').css("display", 'inline');
                if (ACTIVE_STATUS !== 2) {
                    $('#Manager_btn').css("display", "inline");
                    $('#TeamLeader_btn').css("display", "inline");
                    $('#shift_btn').css("display", "inline");
                }
            } else {
                if ($("#PermissionData").data('list').some(obj => obj.permission === "employee_create")) {
                    $('#add_btn').css("display", 'none');
                    $('#addBulkRegBtn').css("display", 'none');
                }
                if ($("#PermissionData").data('list').some(obj => obj.permission === "employee_delete")) $('#delete_btn').css("display", 'inline');
                if ($("#PermissionData").data('list').some(obj => obj.permission === "employee_modify")) {
                    $('#editBulkRegBtn').css("display", 'none');
                    if (ACTIVE_STATUS === 1) $('#suspend_btn').css("display", 'inline');
                    if (ACTIVE_STATUS === 2) $('#Active_btn').css("display", 'inline');
                    if (ACTIVE_STATUS !== 2) {
                        $('#Manager_btn').css("display", "inline");
                        $('#TeamLeader_btn').css("display", "inline");
                        $('#shift_btn').css("display", "inline");
                    }
                }

            }
            return;
        } else {
            ActionsDisable()
        }
    });
}


//function for enable the unassign button when select checkbox in unassign modal
function CheckboxEnable() {
    $.each($("input[name = 'Selectedcheckbox']"), function () {
        if ($("input[name = 'Selectedcheckbox']").is(':checked')) {
            $('#UnassignButton').css("display", 'inline');
            return;
        } else {
            $('#UnassignButton').css("display", 'none');
        }
    });
}




//    actions once operations suceess
function ActionsDisable() {
    SELECTED_CHECKBOXES = [];
    if (userType === "admin") {
        $('#add_btn').css("display", 'inline');
        $('#addBulkRegBtn').css("display", 'inline');
        $('#delete_btn').css("display", 'none');
        $('#editBulkRegBtn').css("display", 'inline');
        $('#suspend_btn').css("display", 'none');
        $('#Active_btn').css("display", 'none');
        $('#Manager_btn').css("display", "none");
        $('#shift_btn').css("display", "none");
        $('#TeamLeader_btn').css("display", "none");
    } else {
        if ($("#PermissionData").data('list').some(obj => obj.permission === "employee_create")) {
            $('#add_btn').css("display", 'inline');
            $('#addBulkRegBtn').css("display", 'inline');
        }
        if ($("#PermissionData").data('list').some(obj => obj.permission === "employee_delete")) $('#delete_btn').css("display", 'none');
        if ($("#PermissionData").data('list').some(obj => obj.permission === "employee_modify")) {
            $('#editBulkRegBtn').css("display", 'inline');
            $('#suspend_btn').css("display", 'none');
            $('#Active_btn').css("display", 'none');
            $('#Manager_btn').css("display", "none");
            $('#TeamLeader_btn').css("display", "none");
            $('#shift_btn').css("display", "none");
        }
    }
}


//    function for assign the mangers and team-leades for the single users
function AssignManagerTL(form, MangerId, RoleType, userIds) {
    let formData = new FormData(form);
    formData.append("MangerIds", MangerId);
    formData.append("role_type", RoleType);
    formData.append("userIds", userIds);
    $.ajax({
        type: 'post',
        url: "/" + userType + '/assign-manager-teamLead-Employee',
        data: formData,
        cache: false,
        processData: false,
        contentType: false,
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            $("#Manager-selectIcon").attr("disabled", true);
            $("#AssignButton").attr("disabled", true);
            $(".multipleCheck").prop("checked", false);
        },
        success: function (response) {
            $("#AssignButton").attr("disabled", false);
            if (response.code == 200) {
                successSwal(response.msg);
                var inputData = response.inputData, appendData = "", id = null;
                $('#SingleManagerModal').modal('hide');
                let data = response.data;
                //Form 1 --> assign mulitple roles and single role=type to single employee
                //Form 2-->assign single roleMember and single roletype for the multiple employees
                if (response.inputData.Form == 2) {
                    inputData.userIds.split(",").forEach(function (InputData) {
                        UserCompleteList[0].forEach(function (CompleteData) {
                            appendData = "";
                            id = "";
                            if (InputData == CompleteData.id) {
                                id = InputData;
                                $('#SelectCheckbox' + id + '').prop('checked', false);
                                if (userType === "admin" || $("#PermissionData").data('list').some(obj => obj.permission === "employee_user_setting")) {
                                    appendData += '<a href="track-user-setting?id=' + id + '" title="' + EMP_MSG_LOCALE.trackInfo + '   " class="mr-2"><i class="fas fa-cog text-primary"></i></a>';
                                }

                                if (CompleteData.role.toLowerCase() == "employee") {
                                    appendData += '<a id="editedId" href="#" onclick="ManagerList(' + id + ',2)" class="open-editModal text-warning mr-2 disabled_effect"  data-toggle="modal" data-target="#ManagerUsersModal"  data-id="' + id + '"><i class="fa fa-eye" style="margin-left: 2px; color:black " data-toggle="tooltip" data-placement="top" title="' + EMP_MSG_LOCALE.empNotAssigned + '" ></i></a>';
                                    if (userType === "admin" || $("#PermissionData").data('list').some(obj => obj.permission === "employee_assign_employee")) {
                                        appendData += '<a id="editedId" href="#" onclick=" getRoles()" class="open-editModal text-danger mr-2"  data-toggle="modal" data-target="#MultiManagerModal"  data-id="' + id + '"><i class="far fa-arrow-alt-circle-down fa-fw" data-toggle="tooltip" data-placement="top" title="' + AssignEmployee + '" ></i></a>';
                                    }
                                } else {
                                    appendData += '<a id="editedId" href="#" onclick="ManagerList(' + id + ',2)" class="open-editModal text-warning mr-2"  data-toggle="modal" data-target="#ManagerUsersModal"  data-id="' + id + '"><i class="fa fa-eye" style="margin-left: 2px; color:black " data-toggle="tooltip" data-placement="top" title="' + EMP_MSG_LOCALE.empNotAssigned + '" ></i></a>';
                                    if (userType === "admin" || $("#PermissionData").data('list').some(obj => obj.permission === "employee_assign_employee")) {
                                        appendData += '<a id="editedId" href="#" onclick=" getRoles()" class="open-editModal text-danger mr-2"  data-toggle="modal" data-target="#MultiManagerModal"  data-id="' + id + '"><i class="far fa-arrow-alt-circle-down fa-fw" data-toggle="tooltip" data-placement="top" title="' + AssignEmployee + '" ></i></a>';
                                    }
                                }

                                if (CompleteData.status == 1) {
                                    if (userType === "admin" || $("#PermissionData").data('list').some(obj => obj.permission === "employee_change_role")) {
                                        appendData += '<a id="upgrade" class="open-upgradeModal text-success mr-2 "  href="#"  data-toggle="modal" onclick="getRoles(' + CompleteData.role_id + ')" data-target="#upgradeManagerModal" title="' + EMPLOYEE_DETAILS_CONST.empRoleUpdate + '"  data-id="' + id + '"><i class="fas fa-arrow-up fa-fw" data-toggle="tooltip" data-placement="top" ></i></a>';
                                    }
                                } else appendData += '<a id="upgrade" class="open-upgradeModal text-success mr-2 disabled_effect "  href="#"  data-toggle="modal" data-target="#upgradeManagerModal" title="' + EMPLOYEE_DETAILS_CONST.empRoleUpdate + '"  data-id="' + id + '"><i class="fas fa-arrow-up fa-fw" data-toggle="tooltip" data-placement="top" ></i></a>';
                                if (userType === "admin" || $("#PermissionData").data('list').some(obj => obj.permission === "employee_modify")) {
                                    appendData += '<a  onclick="getdetails(' + id + ', 2, 0)" id="editedId"  class="open-editModal text-success mr-2"  href="#"  data-toggle="modal" data-target="#editEmpModal" title="' + EMPLOYEE_DETAILS_CONST.empEdit + '"  data-id="' + id + '"><i class="fas fa-user-edit fa-fw" data-toggle="tooltip" data-placement="top" ></i></a>';
                                }
                                if (userType === "admin" || $("#PermissionData").data('list').some(obj => obj.permission === "employee_delete")) {
                                    appendData += '<a id="delete" class="open-editModal text-danger mr-2" href="#"  data-toggle="modal" data-target="#DeleteSingleModal" title="' + EMPLOYEE_DETAILS_CONST.empDelete + '" data-id="' + id + '"> <i class="far fa-trash-alt" data-toggle="tooltip" data-placement="top"></i></a>';
                                }
                                $('#act' + id + '').html(appendData);
                                $('#sus' + id + '').html(appendData);
                            }
                        })
                    })
                } else if (response.inputData.Form == 1) {
                    UserCompleteList[0].forEach(function (CompleteData) {
                        id = "";
                        if (CompleteData.id == inputData.User_id) {
                            id = inputData.User_id;
                            appendData = "";
                            if (userType === "admin" || $("#PermissionData").data('list').some(obj => obj.permission === "employee_user_setting")) {
                                appendData += '<a href="track-user-setting?id=' + id + '" title="' + EMPLOYEE_DETAILS_CONST.trackUser + '" class="mr-2"><i class="fas fa-cog text-primary"></i></a>';
                            }
                            if (CompleteData.role.toLowerCase() == "employee") {
                                appendData += '<a id="editedId" href="#" onclick="ManagerList(' + id + ',2)" class="open-editModal text-warning mr-2 disabled_effect"  data-toggle="modal" data-target="#ManagerUsersModal"  data-id="' + id + '"><i class="fa fa-eye" style="margin-left: 2px; color:black " data-toggle="tooltip" data-placement="top" title="' + EMP_MSG_LOCALE.empNotAssigned + '" ></i></a>';
                                if (userType === "admin" || $("#PermissionData").data('list').some(obj => obj.permission === "employee_assign_employee")) {
                                    appendData += '<a id="editedId" href="#" onclick=" getRoles()" class="open-editModal text-danger mr-2"  data-toggle="modal" data-target="#MultiManagerModal"  data-id="' + id + '"><i class="far fa-arrow-alt-circle-down fa-fw" data-toggle="tooltip" data-placement="top" title="' + AssignEmployee + '" ></i></a>';
                                }
                            } else {
                                if (CompleteData.is_employee_assigned_by) appendData += '<a id="editedId" href="#" onclick="ManagerList(' + id + ',2)" class="open-editModal text-danger mr-2"  data-toggle="modal" data-target="#ManagerUsersModal"  data-id="' + id + '"><i class="fa fa-eye"  data-toggle="tooltip" data-placement="top" title="' + EMP_MSG_LOCALE.viewAssigned + '" ></i></a>';
                                else appendData += '<a id="editedId" href="#" onclick="ManagerList(' + id + ',2)" class="open-editModal mr-2"  data-toggle="modal" data-target="#ManagerUsersModal"  data-id="' + id + '"><i class="fa fa-eye" data-toggle="tooltip" style="color: black" data-placement="top" title="' + EMPLOYEE_DETAILS_CONST.empAssignedTo + '" ></i></a>';
                                if (userType === "admin" || $("#PermissionData").data('list').some(obj => obj.permission === "employee_assign_employee")) {
                                    appendData += '<a id="editedId" href="#" onclick=" getRoles()" class="open-editModal text-danger mr-2"  data-toggle="modal" data-target="#MultiManagerModal"  data-id="' + id + '"><i class="far fa-arrow-alt-circle-down fa-fw" data-toggle="tooltip" data-placement="top" title="' + AssignEmployee + '" ></i></a>';
                                }
                            }
                            if (CompleteData.status == 1) {
                                if (userType === "admin" || $("#PermissionData").data('list').some(obj => obj.permission === "employee_change_role")) {
                                    appendData += '<a id="upgrade" class="open-upgradeModal text-success mr-2 "  href="#"  data-toggle="modal" onclick="getRoles(' + CompleteData.role_id + ')" data-target="#upgradeManagerModal" title="' + EMPLOYEE_DETAILS_CONST.empRoleUpdate + '"  data-id="' + id + '"><i class="fas fa-arrow-up fa-fw" data-toggle="tooltip" data-placement="top" ></i></a>';
                                }
                            } else appendData += '<a id="upgrade" class="open-upgradeModal text-success mr-2 disabled_effect "  href="#"  data-toggle="modal" data-target="#upgradeManagerModal" title="' + EMPLOYEE_DETAILS_CONST.empRoleUpdate + '"  data-id="' + id + '"><i class="fas fa-arrow-up fa-fw" data-toggle="tooltip" data-placement="top" ></i></a>';
                            if (userType === "admin" || $("#PermissionData").data('list').some(obj => obj.permission === "employee_modify")) {
                                appendData += '<a  onclick="getdetails(' + id + ', 2, 0)" id="editedId"  class="open-editModal text-success mr-2"  href="#"  data-toggle="modal" data-target="#editEmpModal" title="' + EMPLOYEE_DETAILS_CONST.empEdit + '"  data-id="' + id + '"><i class="fas fa-user-edit fa-fw" data-toggle="tooltip" data-placement="top" ></i></a>';
                            }
                            if (userType === "admin" || $("#PermissionData").data('list').some(obj => obj.permission === "employee_delete")) {
                                appendData += '<a id="delete" class="open-editModal text-danger mr-2" href="#"  data-toggle="modal" data-target="#DeleteSingleModal" title="' + EMPLOYEE_DETAILS_CONST.empDelete + '" data-id="' + id + '"> <i class="far fa-trash-alt" data-toggle="tooltip" data-placement="top"></i></a>';
                            }
                            $('#act' + id + '').html(appendData);
                            $('#sus' + id + '').html(appendData);
                        }
                    });
                    $('#MultiManagerModal').modal('hide');
                }
                //code for the change html for the magers
                {
                    var id = "", appendDataa = "";
                    response.inputData.MangerIds.split(",").forEach(function (InputSuperior) {

                        UserCompleteList[0].forEach(function (CompleteData) {
                            if (InputSuperior == CompleteData.id) {
                                id = "", appendDataa = "";
                                id = InputSuperior;
                                appendDataa = "";
                                appendDataa += '<a href="track-user-setting?id=' + id + '" title="' + EMP_MSG_LOCALE.trackInfo + '   " class="mr-2"><i class="fas fa-cog text-primary"></i></a>';
                                if (CompleteData.role.toLowerCase() == "employee") {
                                    appendDataa += '<a id="editedId" href="#" onclick="ManagerList(' + id + ',2)" class="open-editModal text-warning mr-2"  data-toggle="modal" data-target="#ManagerUsersModal"  data-id="' + id + '"><i class="fa fa-eye" style="margin-left: 2px; color:black " data-toggle="tooltip" data-placement="top" title="' + EMP_MSG_LOCALE.empNotAssigned + '" ></i></a>';
                                    appendDataa += '<a id="editedId" href="#" onclick=" getRoles()" class="open-editModal text-danger mr-2"  data-toggle="modal" data-target="#MultiManagerModal"  data-id="' + id + '"><i class="far fa-arrow-alt-circle-down fa-fw" data-toggle="tooltip" data-placement="top" title="' + AssignEmployee + '" ></i></a>';
                                } else {
                                    appendDataa += '<a id="editedId" href="#" onclick="ManagerList(' + id + ',2)" class="open-editModal text-danger mr-2"  data-toggle="modal" data-target="#ManagerUsersModal"  data-id="' + id + '"><i class="fa fa-eye"  data-toggle="tooltip" data-placement="top" title="' +  EMP_MSG_LOCALE.viewAssigned + '" ></i></a>';
                                    appendDataa += '<a id="editedId" href="#" onclick=" getRoles()" class="open-editModal text-danger mr-2"  data-toggle="modal" data-target="#MultiManagerModal"  data-id="' + id + '"><i class="far fa-arrow-alt-circle-down fa-fw" data-toggle="tooltip" data-placement="top" title="' + AssignEmployee + '" ></i></a>';
                                }
                                if (CompleteData.status == 1) appendDataa += '<a id="upgrade" class="open-upgradeModal text-success mr-2 "  href="#"  data-toggle="modal" onclick="getRoles(' + CompleteData.role_id + ',null, '+inputData.User_id+')" data-target="#upgradeManagerModal" title="' + EMPLOYEE_DETAILS_CONST.empRoleUpdate + '"  data-id="' + id + '"><i class="fas fa-arrow-up fa-fw" data-toggle="tooltip" data-placement="top" ></i></a>';
                                else appendDataa += '<a id="upgrade" class="open-upgradeModal text-success mr-2 disabled_effect "  href="#"  data-toggle="modal" data-target="#upgradeManagerModal" title="' + EMPLOYEE_DETAILS_CONST.empRoleUpdate + '"  data-id="' + id + '"><i class="fas fa-arrow-up fa-fw" data-toggle="tooltip" data-placement="top" ></i></a>';
                                appendDataa += '<a  onclick="getdetails(' + id + ', 2, 0)" id="editedId"  class="open-editModal text-success mr-2"  href="#"  data-toggle="modal" data-target="#editEmpModal" title="' + EMPLOYEE_DETAILS_CONST.empEdit + '"  data-id="' + id + '"><i class="fas fa-user-edit fa-fw" data-toggle="tooltip" data-placement="top" ></i></a>' +
                                    '<a id="delete" class="open-editModal text-danger mr-2" href="#"  data-toggle="modal" data-target="#DeleteSingleModal" title="' + EMPLOYEE_DETAILS_CONST.empDelete + '" data-id="' + id + '"> <i class="far fa-trash-alt" data-toggle="tooltip" data-placement="top"></i></a>';
                                $('#act' + id + '').html(appendDataa);
                                $('#sus' + id + '').html(appendDataa);
                            }
                        });
                    });
                }
                ActionsDisable();
            } else if (response.code == "409") {
                warningAlert(response.msg);
            } else {
                $('#SingleManagerModal').modal('hide');
                $('#MultiManagerModal').modal('hide');
                errorSwal(response.msg);
            }
            $("#Manager-selectIcon").attr("disabled", false);
            $("#AssignButton").attr("disabled", false);
        },
        error: function (jqXHR, textStatus, errorThrown) {
            if (jqXHR.status == 410) {
                $("#UnaccessModal").empty();
                $("#UnaccessModal").css('display', 'block');
                $("#UnaccessModal").append('<div class="alert alert-danger text-center"><button type="button" class="close" data-dismiss="alert" >&times;</button><b  id="ErrorMsgForUnaccess"> ' + jqXHR.responseJSON.error + '</b></div>')
            } else errorSwal(DASHBOARD_JS_ERROR.reload);
            $("#Manager-selectIcon").attr("disabled", false);
            $("#AssignButton").attr("disabled", false);
            $('#MultiManagerModal').modal('hide');
            $('#SingleManagerModal').modal('hide');
            ActionsDisable();

            userIds.split(",").forEach(function (id) {
                $('#SelectCheckbox' + id + '').prop("checked", false);
            })


        }
    })
}

//call the function once on change of pagination
let CalledUserFunction = (skip, SearchText) => {
    $("#SelectAllCheckBox").prop('checked', false);
    SEARCH_TEXT = SearchText;
    getUsers( SHOW_ENTRIES, skip, SearchText, SORT_NAME, SORT_ORDER, ACTIVE_STATUS);
    $("#mytimesheetdataDownload").hide();
};

let deletedUserList = () =>{
    $.ajax({
        url: '/' + userType + '/delete-user-list-logs',
        type: 'post',
        data: {
            START_DATE, END_DATE
        },
        beforeSend: function () {
            $("#DeleteUserListTable").empty();
            $("#loader").css('display', 'inline');
        },
        success: function (response) {
            $("#loader").css('display', 'none');
            $("#LoaderIcon").css('display', 'none');
            $("#SearchButton").attr('disabled', false);
            if (response.code === 200) {
                let data = response.data.list;
                let computer_name = "",email_address="",admin_email_address="";
                if (data.length !== 0) {
                    data.forEach((List) => {
                        computer_name = List.computer_name !== null && List.computer_name !== "null" ? List.computer_name : "--";
                        email_address = List.email !== null && List.email !== "null" ? List.email : "--";
                        admin_email_address = List.removed_admin_email !== null && List.removed_admin_email !== "null" ? List.removed_admin_email : "--";
                        $("#DeleteUserListTable").append(`<tr><td>${List.full_name}</td><td>${email_address}</td><td>${computer_name}</td><td>${moment(List.created_at).format('YY-MM-DD HH:mm:ss')}</td><td>${admin_email_address}</td></tr>`);
                    });
                } else $("#DeleteUserListTable").append(`<tr><td></td><td></td><td>${TASK_PAGE_JS.NoDataAvailable}</td><td></td><td></td></tr>`);
            } else $("#DeleteUserListTable").append(`<tr><td></td><td></td><td>${TASK_PAGE_JS.NoDataAvailable}</td><td></td><td></td></tr>`);
            $("#DeleteUserTable").dataTable({
                "lengthMenu": [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
                "language": {"url": DATATABLE_LANG} // declared in _scripts.blade file
            });
        },

        error: function () {
            $("#SearchButton").attr('disabled', false);
            $("#loader").css('display', 'none');
            $("#LoaderIcon").css('display', 'none');
        }
    })
};




function errorMessage(msg) {
    Swal.fire({
        icon: 'error',
        title: msg,
        showConfirmButton: true
    });
}

function successMessage(msg) {
    Swal.fire({
        icon: 'success',
        title: msg,
        showConfirmButton: true
    });
}


function getListEmp(){
    (SELECTED_CHECKBOXES.toString()).split(",").forEach(function (id) {
        let empName = $('#td' + id).text();
        $('#selectedEmployeeList').append('<li>' + empName + '</li>');
    })
}
function clearList(){
    $('#selectedEmployeeList').empty();
}

//for multiple user delete Madhu
$(document).on('click', '#deleteModal', function (e) {
    e.preventDefault();
    $.ajax({
        type: "post",
        data: {
            user_ids: SELECTED_CHECKBOXES.toString(),
        },
        url: "/" + userType + '/Delete-multiple',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        beforeSend: function () {
            $('#deleteModal').attr("disabled", true);
            $(".multipleCheck").prop("checked", false);
        },
        success: function (response) {
            getUsers( SHOW_ENTRIES,0,"","","",0);
            Swal.fire({
                icon: 'success',
                title: response.message,
                showConfirmButton: true
            });
            $('#deleteModal').attr("disabled", false);
            $('#deleteManagerModal').modal('hide');
        },
        error: function (jqXHR, textStatus, errorThrown) {
            if (jqXHR.status == 410) {
                $("#UnaccessModal").empty();
                $("#UnaccessModal").css('display', 'block');
                $("#UnaccessModal").append('<div class="alert alert-danger text-center"><button type="button" class="close" data-dismiss="alert" >&times;</button><b  id="ErrorMsgForUnaccess"> ' + jqXHR.responseJSON.error + '</b></div>')
            } else errorSwal(DASHBOARD_JS_ERROR.reload);
            $('#deleteModal').attr("disabled", false);
            $('#deleteManagerModal').modal('hide');
        }
    });
});