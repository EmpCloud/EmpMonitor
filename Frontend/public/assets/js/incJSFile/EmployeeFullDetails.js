$(document).ready(function () {
    $("#EmployeedateOfjoin").datepicker({
        maxDate: new Date,
        uiLibrary: 'bootstrap4',
    });

    $('#TimesheetsTab').click();
})

function loadTimeSheetData() {
    $('#dateRange').show();
    let startTime = new Date($('#from').val()).toISOString();
    let endTime = new Date($('#to').val()).toISOString();
    let url = userType == 'employee' ? "/" + userType + '/get-time-sheets-data-employee' : "/" + userType + '/get-time-sheets-data';
    $.ajax({
        type: "post",
        url: url,
        data: {data: `skip=0&limit=10&employee_id=${$('#userId').attr('value')}&start_date=${startTime}&end_date=${endTime}`},
        beforeSend: function () {
            TIME_SHEET_CHECK = true;
            // Destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable('#timeSheetDataTable')) {
                $('#timeSheetDataTable').DataTable().destroy();
            }
            $('#timeSheetsData').empty();
            $('#timeSheetsData').append('<tr><td colspan="8"><div  class="loader"></div></td></tr>');
        },
        success: function (response) {
            return timeSheetData(response);
        },
        error: function (jqXHR) {
            if(jqXHR.status == 410)  {
                // Destroy existing DataTable if it exists
                if ($.fn.DataTable.isDataTable('#timeSheetDataTable')) {
                    $('#timeSheetDataTable').DataTable().destroy();
                }
                $('#Timesheet').empty();
                $('#timeSheetsData').empty();
                $('#Timesheet').append('<p  style="color: red; text-align: center; font-size: 150%; width: 100%; height: 40% " class="mt-5"><b>'+jqXHR.responseJSON.error+'</b></p>');
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
                $("#ErrorMsgForUnaccess").html(jqXHR.responseJSON.error)
            } else {
                // Destroy existing DataTable if it exists
                if ($.fn.DataTable.isDataTable('#timeSheetDataTable')) {
                    $('#timeSheetDataTable').DataTable().destroy();
                }
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
                return errorHandler(EMPLOYEE_FULL_DETAILS_ERROR.somethingWrong);
            }
            TIME_SHEET_CHECK = false;
        }
    });
}
function loadBrowserHistory(type=2) {
     $('#dateRange').show();
    $.ajax({
        type: "post",
        url: "/" + userType + '/get-web-app-history',
        data: {data: `employeeId=${$('#userId').attr('value')}&startDate=${$('#from').val()}&endDate=${$('#to').val()}&type=${type}`},

        beforeSend: function () {
            $('#browserHistoryTableLoader').css('display', 'block');
            $('#webHistoryChartLoader').css('display', 'block');
            WEB_HISTORY_CHECK = true;
            $('#browserHistoryDataTableData').empty();
            // $('#browserHistoryDataTableData').append("<tr><td colspan='7' style='text-align: center' class='text-primary'>"+EMPLOYEE_FULL_DETAILS_ERROR.BrowserDataLoading+" </td></tr>");
            $('#webHistoryChart').empty();
        },
        success: function (response) {
            return browserHistoryData(response);
        },
        error: function (jqXHR) {
            if(jqXHR.status == 410)  {
                $('#BrowserHistory').empty();
                $('#BrowserHistory').append('<p  style="color: red; text-align: center; font-size: 150%; width: 100%; height: 40% " class="mt-5"><b>'+jqXHR.responseJSON.error+'</b></p>');

                $("#ErrorMsgForUnaccess").html()
            }else {
                return errorHandler(EMPLOYEE_FULL_DETAILS_ERROR.somethingWrong);
            }
            WEB_HISTORY_CHECK = false;
        }
    });
}

function loadAppHistory(type=1) {
    $('#dateRange').show();
    $.ajax({
        type: "post",
        url: "/" + userType + '/get-web-app-history',
        data: {data: `employeeId=${$('#userId').attr('value')}&startDate=${$('#from').val()}&endDate=${$('#to').val()}&type=${type}`},
        beforeSend: function () {
            APP_HISTORY_CHECK = true;
            $('#applicationHistoryTableId').DataTable().clear().draw();
            $('#applicationHistoryTableId').DataTable().destroy();
            $('#appHistoryTable').empty();
            $('#appHistoryTable').append('<div  class="loader"></div>');
        },
        success: function (response) {
            return applicationHistoryData(response);
        },
        error: function (jqXHR) {
            if(jqXHR.status == 410)  {
                $('#AppHistory').empty();
                $('#AppHistory').append('<p  style="color: red; text-align: center; font-size: 150%; width: 100%; height: 40% " class="mt-5"><b>'+jqXHR.responseJSON.error+'</b></p>');
                $("#ErrorMsgForUnaccess").html()
            }else {
                return errorHandler(EMPLOYEE_FULL_DETAILS_ERROR.somethingWrong);
            }
            APP_HISTORY_CHECK = false;
        }
    });
}



// datepicker
$( "#singleDateCalender" ).datepicker({
    changeMonth: true,
    changeYear: true,
    dateFormat: 'yy-mm-dd',
    // minDate: -20,
    maxDate: "0D"
});


 
$('#dateRange').on('apply.daterangepicker', function () {
    $('.btn.btn-info.nav-link.active').click();
});

