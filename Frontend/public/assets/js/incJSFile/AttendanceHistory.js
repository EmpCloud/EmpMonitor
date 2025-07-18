var locID = 0;
var deptID = 0;
var userID = 0;
var fromDate = "";
var toDate = "";
var appendData = "";
var from;
var to;
var pdfCsv = 0;
var message = "";
let SHOW_ENTRIES = 10;
let TOTAL_COUNT_EMAILS = 0; 		 // Your table list total count
let SORT_NAME = '';   			 //Holds on which field we are going to sort
let SORT_ORDER = '';			 //Holds on  which order we are going to do
let SORTED_TAG_ID = '';  		 // Holds the previous tad id  to disable the arrow if we go to another feild for sorting
let PAGE_COUNT_CALL = true, COMPLETE_RECORD_DATA;
let ADD_REMOVE_COLUMN = ["Email", "EmpCode", "ClockIn", "ClockOut", "TotalHour"];
let ORIGINAL_TABLE_HEADER = ["Email", "EmpCode", "ClockIn", "ClockOut", "TotalHour"];


$(function () {
    var start = moment().subtract(0, 'days');
    var end = moment();
    const dateLimit = { days: 30 };
    function cb(start, end) {
        $('#reportranges span').html(start.format('MMMM DD, YYYY') + ' - ' + end.format('MMMM DD, YYYY'));
        $('#from').val(start.format('YYYY-MM-DD'));
        $('#to').val(end.format('YYYY-MM-DD'));
        from = $('#from').val();
        to = $('#to').val();
        // if (active_function == 1) {
            attendanceReports(locID, deptID, userID, SHOW_ENTRIES, 0, null);
            makeDatatableDefault();
            $('#showPageNumbers').hide();
            $('#attendanceHistory').hide();

        // }
        // if (active_function == 0) {
        //     active_function = 1
        // }
    }

    $('#reportranges').daterangepicker({
        startDate: start,
        endDate: end,
        minDate :  moment().subtract(180, 'days'),
        maxDate: end,
        dateLimit: dateLimit,
        ranges: {
            [dateRangesLocalization.Today]: [moment(), moment()],
            [dateRangesLocalization.Yesterday]: [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            [dateRangesLocalization.Last_7_Days]: [moment().subtract(7, 'days'), moment().subtract(1, 'days')],
            [dateRangesLocalization.Last_30_Days]: [moment().subtract(30, 'days'), moment().subtract(1, 'days')],
            [dateRangesLocalization.This_Month]: [moment().startOf('month'), moment().endOf('month')],
            [dateRangesLocalization.Last_Month]: [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        locale: {
            'customRangeLabel': dateRangesLocalization.Custom_Range,
            "applyLabel": dateRangesLocalization.apply,
            "cancelLabel": dateRangesLocalization.cancel,
        },
    }, cb);
    cb(start, end);
});
$(document).ready(function () {
    // if (PAGE_COUNT_CALL === true) {
    //     if (response.code === 200) {
    //         TOTAL_COUNT_EMAILS = response.data.totalCount;
    //         paginationSetup();
    //         TOTAL_COUNT_EMAILS < SHOW_ENTRIES ? $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' ' + 1 + ' ' + DATATABLE_LOCALIZE_MSG.to + ' ' + TOTAL_COUNT_EMAILS + ' ' + DATATABLE_LOCALIZE_MSG.of + ' ' + TOTAL_COUNT_EMAILS)
    //             : $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' ' + 1 + ' ' + DATATABLE_LOCALIZE_MSG.to + ' ' + SHOW_ENTRIES + ' ' + DATATABLE_LOCALIZE_MSG.of + ' ' + TOTAL_COUNT_EMAILS);
    //     } else {
    //         $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' ' + 0 + ' ' + DATATABLE_LOCALIZE_MSG.to + ' ' + 0 + ' ' + DATATABLE_LOCALIZE_MSG.of + ' ' + 0);
    //         MAIL_DATA = "";
    //         paginationSetup();
    //         // $('.pagination').jqPagination();
    //     }
    // }

    $("#employee").select2({width: "200"});
     $('#SearchButton').click(function () {
        $('#SearchButton').attr('disabled', false);
    });
    let runScript = (e) => {
        if (e.keyCode == 13) SearchText();
    }

    attendanceReports();
});

function getAllLocations() {
    $('#showPageNumbers').hide();
    locID = $('#locationdept').val();
    deptID = 0;
    userID = 0;
    makeDatatableDefault();
    getalldept(locID);
    users(locID, deptID);
    attendanceReports(locID, deptID, userID, SHOW_ENTRIES, 0, null);
}

function getAllDepartments() {
    $('#showPageNumbers').hide();
    deptID = $('#getDepartments').val();
    userID = 0;
    users(locID, deptID);
    makeDatatableDefault();
    attendanceReports(locID, deptID, userID, SHOW_ENTRIES, 0, null);
}

function allEmployee() {
    $('#showPageNumbers').hide();
    userID = $('#employee').val();
    makeDatatableDefault();
    attendanceReports(locID, deptID, userID, SHOW_ENTRIES, 0, null);
}

function getalldept(SelectlocID) {
    var locData = locID;
    if (locData === 0) {
        locData = "";
    }
    $.ajax({
        type: "post",
        url: "/" + userType + "/get-department-by-location",
        data: {
            id: locData
        },
        success: function (response) {
            var location = " ";
            appendData = "";
            location = response.data.data;
            if (response.code === 200 && location.length !== 0 && location[0].id) {
                appendData += '<option selected class="active-result" value="0">' + AUTO_EMAIL_SEE_ALL_LESS.all  + '</option>';
                for (i = 0; i < location.length; i++) {
                    appendData += ' <option class="active-result" value="' + location[i].id + '">' + location[i].name + '</option>';
                }
            } else if (response.code === 400) {
                appendData += '<option disabled> No Departments </option>';
            } else if (response.code === 500) {
                appendData += '<option disabled>' + response.msg + '</option>';
            } else {
                appendData += '<option selected class="active-result" value="0">' + AUTO_EMAIL_SEE_ALL_LESS.all  + '</option>';
                for (i = 0; i < location.length; i++) {
                    appendData += ' <option class="active-result" value="' + location[i].department_id + '">' + location[i].name + '</option>';
                }
            }
            $('#getDepartments').empty();
            $('#getDepartments').append(appendData);
        },
        error: function (error) {
            if (error.status === 403) {
                appendData += '<option disabled >' + DASHBOARD_JS_ERROR.permissionDenied + '</option>';
            } else {
                appendData += '<option disabled >' + DASHBOARD_JS_ERROR.reload + '</option>';
            }
            $('#getDepartments').append(appendData);
        }
    });
}

function users(locationId, departmentId) {
    $.ajax({
        type: "get",
        url: "/" + userType + "/users",
        data: {
            location_id: locationId,
            depId: departmentId,
        },
        beforeSend: function () {
        },
        success: function (response) {
            appendData = "";
            var data = " ";
            data = response.data;
            if (response.code === 200) {
                appendData += '<option selected class="active-result" value="0">' + AUTO_EMAIL_SEE_ALL_LESS.all  + '</option>';
                for (i = 0; i < data.length; i++) {
                    appendData += ' <option class="active-result" value="' + data[i].id + '">' + data[i].first_name + ' ' + data[i].last_name + '</option>';
                }
            } else {
                appendData += '<option selected value="" disabled="disabled">' + response.msg + '</option>';
            }
            $('#employee').empty();
            $('#employee').append(appendData);
        },
        error: function (error) {
            if (error.status === 403) {
                appendData += '<option selected value="" disabled="disabled">' + DASHBOARD_JS_ERROR.permissionDenied + '</option>';
            } else {
                appendData += '<option selected value="" disabled="disabled">' + DASHBOARD_JS_ERROR.reload + '</option>';
            }
            $('#employee').append(appendData);
        }
    });
}

//call the function once on change of pagination
let CalledUserFunction = (skip, SearchText) => {
    attendanceReports(locID, deptID, userID, SHOW_ENTRIES, skip, SearchText, SORT_NAME, SORT_ORDER);
};

function attendanceReports(SelectlocID, SelectDeptId, SelectUserId, showEntries, skipvalue, searchText) {
    var SelectedfromDate = new Date(from).toISOString();
    var SelectedtoDate = new Date(to).toISOString();
    var urlData;
    if (searchText == "") searchText = null;
    if (SORT_NAME == "" && SORT_ORDER == "" && searchText == null) urlData = `location_id=${SelectlocID}&department_id=${SelectDeptId}&employee_id=${SelectUserId}&start_date=${SelectedfromDate}&end_date=${SelectedtoDate}&limit=${showEntries}&skip=${skipvalue}`
    else if (SORT_NAME == "" && SORT_ORDER == "" && searchText != null) {
        urlData = `location_id=${SelectlocID}&department_id=${SelectDeptId}&employee_id=${SelectUserId}&start_date=${SelectedfromDate}&end_date=${SelectedtoDate}&limit=${showEntries}&skip=${skipvalue}&name=${searchText}`
    } else if (SORT_NAME != "" && SORT_ORDER != "" && searchText == null) {
        urlData = `location_id=${SelectlocID}&department_id=${SelectDeptId}&employee_id=${SelectUserId}&start_date=${SelectedfromDate}&end_date=${SelectedtoDate}&limit=${showEntries}&skip=${skipvalue}&sortColumn=${SORT_NAME}&sortOrder=${SORT_ORDER}`
    } else {
        urlData = `location_id=${SelectlocID}&department_id=${SelectDeptId}&employee_id=${SelectUserId}&start_date=${SelectedfromDate}&end_date=${SelectedtoDate}&limit=${showEntries}&skip=${skipvalue}&name=${searchText}&sortColumn=${SORT_NAME}&sortOrder=${SORT_ORDER}`
    }
    let url = userType == 'employee'? '/attendance-history-employee': '/attendance-history';
    $.ajax({
        type: "post",
        url: '/' + userType + url,
        data: {
            data: urlData
        },
        beforeSend: function () {
            $("#loader").css('display', 'block');
            $('#attendanceHistory').hide();
        },
        success: function (response) {
            $("#SearchButton").attr('disabled',false);
            $('#showPageNumbers').show();
            $('#attendanceHistory').show();
            $("#loader").css('display', 'none');
            appendData = "";
            var data = response.data;
            if (response.code === 200) {
                var ReportsData = data.data;
                if (ReportsData) {
                    ReportsData.forEach(function (attHistory) {
                        let startTime = moment(attHistory.start_time).tz('Asia/Kolkata').format('DD-MM-YYYY HH:mm:ss');
                        let endTime = moment(attHistory.end_time).tz('Asia/Kolkata').format('DD-MM-YYYY HH:mm:ss');
                        const time1 = moment(attHistory.end_time);
                        const time2 = moment(attHistory.start_time); 
                        const duration = moment.duration(time1.diff(time2));
                        const hours = duration.asHours();
                        appendData +='<tr>'; 
                        appendData += '<td class="stickyCol-sticky-col""><a title="View Full Details" href="get-employee-details?id=' + attHistory.id + '">' + attHistory.first_name + ' ' + attHistory.last_name + ' </a></td>';
                        if (ADD_REMOVE_COLUMN.includes('Email')) appendData += '<td class="EmailTable" style="width: 90px;">' + attHistory.email + '</td>';
                        if (ADD_REMOVE_COLUMN.includes('EmpCode')) appendData += '<td class="EmpCodeTable" style="width: 90px;">' + attHistory.employee_code + '</td>';
                        if (ADD_REMOVE_COLUMN.includes('ClockIn')) appendData += '<td class="ClockInTable" style="width: 90px;">' + startTime + '</td>';
                        if (ADD_REMOVE_COLUMN.includes('ClockOut')) appendData += '<td class="ClockOutTable" style="width: 90px;">' + endTime + '</td>'; 
                        if (ADD_REMOVE_COLUMN.includes('TotalHour')) appendData += '<td class="TotalHourTable" style="width: 90px;">' +  formatSecondsToHHMMSS(attHistory.total_usage)  + '</td>' ;
                        appendData += '<td class="IdleTimeTable" style="width: 90px;">' + formatSecondsToHHMMSS(attHistory.office_usage) + '</td>'; 
                        appendData += '<td class="IdleTimeTable" style="width: 90px;">' + formatSecondsToHHMMSS(attHistory.active_usage) + '</td>'; 
                        appendData += '<td class="IdleTimeTable" style="width: 90px;">' + formatSecondsToHHMMSS(attHistory.idle_usage) + '</td>'; 
                        appendData +=  '</tr>';
                    });
                }

                $('#attendanceHistory').empty();
                $('#attendanceHistory').append(appendData);
                if (PAGE_COUNT_CALL === true) {
                    TOTAL_COUNT_EMAILS = response.data.totalCount;
                    paginationSetup();
                    TOTAL_COUNT_EMAILS < SHOW_ENTRIES ? $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' ' + 1 + ' ' + DATATABLE_LOCALIZE_MSG.to + ' ' + TOTAL_COUNT_EMAILS + ' ' + DATATABLE_LOCALIZE_MSG.of + ' ' + TOTAL_COUNT_EMAILS)
                        : $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' ' + 1 + ' ' + DATATABLE_LOCALIZE_MSG.to + ' ' + SHOW_ENTRIES + ' ' + DATATABLE_LOCALIZE_MSG.of + ' ' + TOTAL_COUNT_EMAILS);
                }

            } else {
                TOTAL_COUNT_EMAILS = 0;
                $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' ' + 0 + ' ' + DATATABLE_LOCALIZE_MSG.to + ' ' + 0 + ' ' + DATATABLE_LOCALIZE_MSG.of + ' ' + 0);
                MAIL_DATA = "";
                paginationSetup();
                $('.pagination').jqPagination('destroy');
                message = response.msg; 
                 appendData += '<tr><td></td><td></td><td></td><td> ' + response.msg + ' </td><td></td><td></tr>';
                $('#attendanceHistory').empty();
                $('#attendanceHistory').append(appendData);
            }
        },
        error: function (error) {
            if (error.status === 403) {
                appendData += '<option disabled >' + DASHBOARD_JS_ERROR.permissionDenied + '</option>';
            } else {
                appendData += '<option disabled >' + DASHBOARD_JS_ERROR.reload + '</option>';
            }
            $('#employee').append(appendData);
        }
    });
}
function formatSecondsToHHMMSS(seconds) {
    const hrs = String(Math.floor(seconds / 3600)).padStart(2, '0');
    const mins = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
    const secs = String(seconds % 60).padStart(2, '0');
    return `${hrs}:${mins}:${secs}`;
}

//Added and removing table headers
let tableHeaderUpdated = () => {
    attendanceReports(locID, deptID, userID, SHOW_ENTRIES, 0, $("#SearchTextField").val(), SORT_NAME, SORT_ORDER);
    setTimeout(function () {
        $("#mytimesheetdataDownload").hide();
        $("#Manager_List_Id, #Split_List_Id").show();
        $("#checklocDownload").prop('checked',false);
    },10)
};
 

$(document).mouseup(function(e){
    var dropdown = $("#mytimesheetdataDownload");
    var checkbox =$("#checklocDownload");

    if(!dropdown.is(e.target) && dropdown.has(e.target).length === 0 ){
        dropdown.hide();
    }
    else if (dropdown.is(e.target) && (dropdown.has(e.target) && (checkbox.is(e.target)))){
        if (checkbox.prop('checked')) {
            dropdown.show();
            checkbox.prop('checked',true);
                } else {
                    checkbox.prop('checked', false);
                }
    } 

});
