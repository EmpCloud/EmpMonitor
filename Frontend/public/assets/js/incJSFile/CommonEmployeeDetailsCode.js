$(document).ready(function () {
    $('[data-toggle="tooltip"]').tooltip()
    $('[data-toggle="tooltip"]').tooltip();
    $('#fromTime' + moment().format('HH')).attr('selected', true); 
    $('#toTime').append(`<option selected>${String(Number(moment().format('HH')) + 1).padStart(2, '0')}:00</option>`);
 });

let PRODUCTIVITY = false, TIME_SHEET_CHECK = true,
    SCREEN_SHOTS_CHECK = true, WEB_HISTORY_CHECK = true,
    APP_HISTORY_CHECK = true, KEY_LOGGER_CHECK = true,
    SELECTED_DATE = null, ROLE_CHECK = false,
    DEPT_CHECK = false, DEPT_ID_CHECK = null,
    LOCATION_CHECK = false, USER_DETAILS_CHECK = false,
    CURRENT_TAB = null, REMAINING_TIMER,
    ANALYSIS_CHECK = true, SHOW_ENTRIES = "10",
    TOTAL_COUNT_EMAILS, SORT_NAME = '',
    SORT_ORDER = '', SORTED_TAG_ID = '',
    appendData = '', PAGE_COUNT_CALL = true,
    urlAnlysisData, SEARCH_TEXT = null, ACTIVE_STORAGE;

$(function () { 
    let start = moment().subtract(0, "days");
    let end = moment().subtract(0, 'days');

    function cb(start, end) {
        $("#dateRange span").html(
            start.format("YYYY-MM-DD") + " - " + end.format("YYYY-MM-DD")
        );
        $('#from').val(start.format("YYYY-MM-DD"));
        $('#to').val(end.format("YYYY-MM-DD"));
        PRODUCTIVITY = false;
        TIME_SHEET_CHECK = false; 

        $('div').find('a').each(function () {
             if ($(this).hasClass('active')) {
                 switch ($(this).attr('href')) { 
                    case '#Timesheets' :
                        CURRENT_TAB = '#Timesheets'; 
                        loadTimeSheetData();
                        break;
                    case '#BrowserHistory' :
                        CURRENT_TAB = '#BrowserHistory';
                        loadBrowserHistory();
                        break;
                    case '#AppHistory':
                        CURRENT_TAB = '#AppHistory';
                        loadAppHistory();
                        break;
                     
                    default:
                        break;
                }
            }
        });
    }

    $("#dateRange").daterangepicker({
        maxDate: end,
        startDate: start,
        endDate: end,
        ...dateRangeLocalization,
    }, cb);

    cb(start, end);
});

function timeSheetData(response) {
    if (response.code === 200) {
        if (response.data && response.data.data.length > 0) {
            // Destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable('#timeSheetDataTable')) {
                $('#timeSheetDataTable').DataTable().destroy();
            }
            $('#timeSheetsData').empty();
            
            response.data.data.map(timeSheet => {
                let startTime = moment(timeSheet.start_time).tz('Asia/Kolkata').format('DD-MM-YYYY HH:mm:ss');
                let endTime = moment(timeSheet.end_time).tz('Asia/Kolkata').format('DD-MM-YYYY HH:mm:ss');
                
                const time1 = moment(timeSheet.end_time);
                const time2 = moment(timeSheet.start_time);
                let total_hours = timeSheet.total_usage ? formatSecondsToHHMMSS(timeSheet.total_usage) : "00:00:00";
                let active_hours = timeSheet.active_usage ? formatSecondsToHHMMSS(timeSheet.active_usage) : "00:00:00";
                let idle_hours = timeSheet.idle_usage ? formatSecondsToHHMMSS(timeSheet.idle_usage) : "00:00:00";
                let productive_hours = timeSheet.productive_usage ? formatSecondsToHHMMSS(timeSheet.productive_usage) : "00:00:00";
                let unproductive_hours = timeSheet.unproductive_usage ? formatSecondsToHHMMSS(timeSheet.unproductive_usage) : "00:00:00";
                let neutral_hours = timeSheet.neutral_usage ? formatSecondsToHHMMSS(timeSheet.neutral_usage) : "00:00:00";

                $('#timeSheetsData').append('<tr class="text-center">\n' + ' <td>' + startTime + '</td><td>' + endTime + '</td><td title="' +  total_hours + '">' + total_hours + '</td><td>' + active_hours + '</td><td>' + idle_hours + `</td> <td>${productive_hours}</td> <td>${unproductive_hours}</td> <td>${neutral_hours}</td> </tr>` );
            });
            
            // Initialize DataTable after data is loaded
            $("#timeSheetDataTable").DataTable({
                "lengthMenu": [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
                "language": {
                    "url": DATATABLE_LANG,
                    "paginate": {
                        "first": "&laquo;",
                        "last": "&raquo;",
                        "next": "&rsaquo;",
                        "previous": "&lsaquo;"
                    }
                },
                "columns": [
                    { "data": 0, "orderable": true },   // Clock In
                    { "data": 1, "orderable": true },   // Clock Out
                    { "data": 2, "orderable": true },   // Total Hours
                    { "data": 3, "orderable": true },   // Active Hours
                    { "data": 4, "orderable": true },   // Idle Hours
                    { "data": 5, "orderable": true },   // Productive Hours
                    { "data": 6, "orderable": true },   // Unproductive Hours
                    { "data": 7, "orderable": true }    // Neutral Hours
                ],
                "order": [],
                "destroy": true,
                "initComplete": function () {
                    $("#timeSheetDataTable").wrap("<div style='overflow:auto; width:100%;position:relative;'></div>");
                },
            });
            TIME_SHEET_CHECK = true;
        } else {
            // Destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable('#timeSheetDataTable')) {
                $('#timeSheetDataTable').DataTable().destroy();
            }
            $('#timeSheetsData').empty();
            $('#timeSheetsData').append('<tr><td colspan="8" style="text-align: center; color: red"><b>' + EMPLOYEE_FULL_DETAILS_ERROR.timeSheetNotFound + ' </b></td></tr>');
            $("#timeSheetDataTable").DataTable({
                "language": {
                    "url": DATATABLE_LANG,
                    "paginate": {
                        "first": "&laquo;",
                        "last": "&raquo;",
                        "next": "&rsaquo;",
                        "previous": "&lsaquo;"
                    }
                },
                "columns": [
                    { "data": null, "orderable": false, "defaultContent": "" },
                    { "data": null, "orderable": false, "defaultContent": "" },
                    { "data": null, "orderable": false, "defaultContent": "" },
                    { "data": null, "orderable": false, "defaultContent": "" },
                    { "data": null, "orderable": false, "defaultContent": "" },
                    { "data": null, "orderable": false, "defaultContent": "" },
                    { "data": null, "orderable": false, "defaultContent": "" },
                    { "data": null, "orderable": false, "defaultContent": "" }
                ],
                "destroy": true
            });
        }
    } else if (response.code === 400) {
        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#timeSheetDataTable')) {
            $('#timeSheetDataTable').DataTable().destroy();
        }
        $('#timeSheetsData').empty();
        $('#timeSheetsData').append('<tr><td colspan="8" style="text-align: center; color: red"><b>' + EMPLOYEE_FULL_DETAILS_ERROR.timeSheetNotFound + '  </b></td></tr>');
        $("#timeSheetDataTable").DataTable({
            "language": {
                "url": DATATABLE_LANG,
                "paginate": {
                    "first": "&laquo;&laquo;",
                    "last": "&raquo;&raquo;",
                    "next": "&raquo;",
                    "previous": "&laquo;"
                }
            },
            "columns": [
                { "data": null, "orderable": false, "defaultContent": "" },
                { "data": null, "orderable": false, "defaultContent": "" },
                { "data": null, "orderable": false, "defaultContent": "" },
                { "data": null, "orderable": false, "defaultContent": "" },
                { "data": null, "orderable": false, "defaultContent": "" },
                { "data": null, "orderable": false, "defaultContent": "" },
                { "data": null, "orderable": false, "defaultContent": "" },
                { "data": null, "orderable": false, "defaultContent": "" }
            ],
            "destroy": true
        });
    } else if (response.code === 500) {
        // Destroy existing DataTable if it exists
        if ($.fn.DataTable.isDataTable('#timeSheetDataTable')) {
            $('#timeSheetDataTable').DataTable().destroy();
        }
        $('#timeSheetsData').empty();
        $('#timeSheetsData').append('<tr><td colspan="8" style="text-align: center; color: red"><b>' + EMPLOYEE_FULL_DETAILS_ERROR.errWhileFetching + ' <br/> <a href="#" onclick="loadTimeSheetData()">' + EMPLOYEE_FULL_DETAILS_ERROR.reloadSection + ' </a> </b></td></tr>');
        $("#timeSheetDataTable").DataTable({
            "language": {
                "url": DATATABLE_LANG,
                "paginate": {
                    "first": "&laquo;&laquo;",
                    "last": "&raquo;&raquo;",
                    "next": "&raquo;",
                    "previous": "&laquo;"
                }
            },
            "columns": [
                { "data": null, "orderable": false, "defaultContent": "" },
                { "data": null, "orderable": false, "defaultContent": "" },
                { "data": null, "orderable": false, "defaultContent": "" },
                { "data": null, "orderable": false, "defaultContent": "" },
                { "data": null, "orderable": false, "defaultContent": "" },
                { "data": null, "orderable": false, "defaultContent": "" },
                { "data": null, "orderable": false, "defaultContent": "" },
                { "data": null, "orderable": false, "defaultContent": "" }
            ],
            "destroy": true
        });
        TIME_SHEET_CHECK = false;
    } else {
        $("#timeSheetDataTable").DataTable({
            "language": {
                "url": DATATABLE_LANG,
                "paginate": {
                    "first": "&laquo;&laquo;",
                    "last": "&raquo;&raquo;",
                    "next": "&raquo;",
                    "previous": "&laquo;"
                }
            },
            "columns": [
                { "data": null, "orderable": false, "defaultContent": "" },
                { "data": null, "orderable": false, "defaultContent": "" },
                { "data": null, "orderable": false, "defaultContent": "" },
                { "data": null, "orderable": false, "defaultContent": "" },
                { "data": null, "orderable": false, "defaultContent": "" },
                { "data": null, "orderable": false, "defaultContent": "" },
                { "data": null, "orderable": false, "defaultContent": "" },
                { "data": null, "orderable": false, "defaultContent": "" }
            ],
            "destroy": true
        });
        TIME_SHEET_CHECK = false;
        return errorHandler(response.msg);
    }
}
function formatSecondsToHHMMSS(seconds) {
    if (isNaN(seconds) || seconds < 0) {
        return "00:00:00";
    }
    const hrs = String(Math.floor(seconds / 3600)).padStart(2, '0');
    const mins = String(Math.floor((seconds % 3600) / 60)).padStart(2, '0');
    const secs = String(seconds % 60).padStart(2, '0');
    return `${hrs}:${mins}:${secs}`;
}

function browserHistoryData(response) {
    if (response.code == 200) {
        if (response.data && response.data.length > 0) {
            $('#browserHistoryTableLoader').css('display', 'none');
            // $('#browserHistoryTable').empty();
            // $('#browserHistoryDataTableData').empty();
            // $('#browserHistoryTableId').DataTable().clear().draw();
            // $('#browserHistoryTableId').DataTable().destroy();
            response.data.map(webData => { 
                let startDate = moment(new Date(webData.start_time)).format('YYYY-MM-DD HH:mm:ss');
                let endDate = moment(new Date(webData.end_time)).format('YYYY-MM-DD HH:mm:ss');
             
                let urlFormat = webData.url.length < 40 ? webData.url.toString().substr(0, 40) : webData.url.toString().substr(0, 40).concat('...');

                let titleFormat = webData.application_name.length < 40 ? webData.application_name : webData.application_name.toString().substr(0, 40).concat('...');
                $('#browserHistoryDataTableData').append('<tr style="font-size: 14px !important;"><td style="text-transform: capitalize">' + webData.application_name + '</td><td style="text-transform: capitalize;" title="' + webData.application_name + '">' + titleFormat + '</td><td width="400px"><a href="' + webData.url + '" title="' + urlFormat  + '" target="_blank">' + urlFormat   + '</td>' +
                    '<td>' + startDate + '</td><td>' + endDate + '</td>'+ `<td>${formatSecondsToHHMMSS(webData.active_seconds)}</td>  <td>${formatSecondsToHHMMSS(webData.idle_seconds)}</td> <td>${formatSecondsToHHMMSS(webData.productive_seconds)}</td>   <td>${formatSecondsToHHMMSS(webData.unproductive_seconds)}</td> <td>${formatSecondsToHHMMSS(webData.neutral_seconds)}</td>   ` +'<td style="">' + webData.keystrokes + '</td><td style="">' + webData.keystrokesCount + '</td><td style="">' + webData.buttonClicks + '</td><td style="">' + webData.mouseMovementsCount + '</td></tr>');
            });
            $("#browserHistoryTableId").DataTable({
                "lengthMenu": [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
                "order": [[3, "desc"]],
                "language": {
                    "url": DATATABLE_LANG,
                    "paginate": {
                        "first": "&laquo;&laquo;",
                        "last": "&raquo;&raquo;",
                        "next": "&raquo;",
                        "previous": "&laquo;"
                    }
                }
            });
            WEB_HISTORY_CHECK = true;
        } else {
            // $('#browserHistoryDataTableData').empty();
            $('#browserHistoryDataTableData').append('<tr><td colspan="14" style="text-align: center"> ' + EMPLOYEE_FULL_DETAILS_ERROR.browserHistoryNotFound + ' </td></tr>');
            $('#webHistoryChart').empty();
            $('#webHistoryChartLoader').css('display', 'none');
            $('#browserHistoryTableLoader').css('display', 'none');
            $('#webHistoryChart').append('<p  style="color: red; text-align: center; font-size: 150%; width: 100%; height: 10% " ><b>' + EMPLOYEE_FULL_DETAILS_ERROR.webHistoryNotFound + '</b></p>');
        }
    } else if (response.code == 400) {
        $('#webHistoryChartLoader').css('display', 'none');
        $('#browserHistoryTableLoader').css('display', 'none');
        $('#webHistoryChart').empty();
        $('#webHistoryChart').append('<p  style="color: red; text-align: center; font-size: 150%; width: 100%; height: 10% " ><b>' + EMPLOYEE_FULL_DETAILS_ERROR.webHistoryNotFound + ' </b></p>');
    } else if (response.code == 500) {
        $('#webHistoryChartLoader').css('display', 'none');
        $('#browserHistoryTableLoader').css('display', 'none');
        $('#webHistoryChart').empty();
        $('#webHistoryChart').append('<p  style="color: red; text-align: center; font-size: 150%; width: 100%; height: 10% " ><b> ' + EMPLOYEE_FULL_DETAILS_ERROR.errWhileFetching + ' <br/> <a href="#" onclick="loadBrowserHistory()">' + EMPLOYEE_FULL_DETAILS_ERROR.reloadSection + ' </a> </b></p>');
        WEB_HISTORY_CHECK = false;
    } else {
        WEB_HISTORY_CHECK = false;
        return errorHandler(response.msg);
    }
}

function applicationHistoryData(response) {
    if (response.code == 200) {
        if (response.data && response.data.length > 0) {
            $('#appHistoryTable').empty();
            $('#applicationHistoryTableId').DataTable().destroy();
            $('#applicationHistoryDataTableData').empty();
            response.data.map(appData => { 
                $('#appHistoryTable').append('<tr><td class="td-url" title="' + appData.application_name + '" style="text-transform: capitalize"><i class="fas fa-mobile-alt mr-2"></i>' + appData.application_name + '</td></tr>');
                let startDate = moment(new Date(appData.start_time)).format('YYYY-MM-DD HH:mm:ss');
                let endDate = moment(new Date(appData.end_time)).format('YYYY-MM-DD HH:mm:ss');
                $('#applicationHistoryDataTableData').append('<tr style="text-align: center; font-size: 14px !important;"><td style="text-transform: capitalize">' + appData.application_name.replace(".exe", "") + '</td><td style="text-transform: capitalize;">' + appData.application_name + '</td><td>' + startDate + '</td><td>' + endDate + '</td>'+ `<td>${formatSecondsToHHMMSS(appData.active_seconds)}</td>  <td>${formatSecondsToHHMMSS(appData.idle_seconds)}</td> <td>${formatSecondsToHHMMSS(appData.productive_seconds)}</td>   <td>${formatSecondsToHHMMSS(appData.unproductive_seconds)}</td> <td>${formatSecondsToHHMMSS(appData.neutral_seconds)}</td>   `  +'<td style="">' + appData.keystrokes + '</td><td style="">' + appData.keystrokesCount + '</td><td style="">' + appData.buttonClicks + '</td><td style="">' + appData.mouseMovementsCount + '</td></tr>');
                  
            });

            $("#applicationHistoryTableId").DataTable({
                "lengthMenu": [[10, 25, 50, 100, 200], [10, 25, 50, 100, 200]],
                "order": [[2, "desc"]],
                "language": {
                    "url": DATATABLE_LANG,
                    "paginate": {
                        "first": "&laquo;&laquo;",
                        "last": "&raquo;&raquo;",
                        "next": "&raquo;",
                        "previous": "&laquo;"
                    }
                }
            });
            APP_HISTORY_CHECK = true;
        } else {
            $('#appHistoryTable').empty();
            $('#applicationHistoryDataTableData').empty();
            $('#applicationHistoryDataTableData').append('<tr><td colspan="13" style="text-align: center"> ' + EMPLOYEE_FULL_DETAILS_ERROR.appHistoryNotFound + '</td></tr>'); 
        }
    } else if (response.code == 400) {
        $('#appHistoryTable').empty(); 
    } else if (response.code == 500) {
        $('#appHistoryTable').empty();
        $('#applicationHistoryDataTableData').empty();
        $('#applicationHistoryDataTableData').append('<tr><td colspan="13" style="text-align: center"> ' + EMPLOYEE_FULL_DETAILS_ERROR.appHistoryNotFound + '</td></tr>'); 
        APP_HISTORY_CHECK = false;
    } else {
        APP_HISTORY_CHECK = false;
        return errorHandler(response.msg);
    }
}

$(".toggle-password-show-edit, .toggle-password-show-edit-c").click(function () {
    $(this).toggleClass("fa-eye fa-eye-slash");
    let input = $($(this).attr("toggle"));
    if (input.attr("type") == "password") {
        input.attr("type", "text");
    } else {
        input.attr("type", "password");
    }
});
