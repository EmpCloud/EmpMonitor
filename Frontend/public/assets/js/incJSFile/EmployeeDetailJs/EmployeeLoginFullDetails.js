function loadWebAppHistory(type) {
    $('#dateRange').show();
    let url ='';
    if(userType == 'employee') url = "/"+userType+'/get-web-app-histories'
    else if(userType == 'admin') url = "/"+userType+'/get-web-app-history'
    $.ajax({
        type: "post",
        url: url,
        data: {data: `employeeId=${$('#userId').attr('value')}&startDate=${$('#from').val()}&endDate=${$('#to').val()}&type=${type}`},

        beforeSend: function () {
            APP_HISTORY_CHECK = true;
            $('#applicationHistoryTableId').DataTable().clear().draw();
            $('#applicationHistoryTableId').DataTable().destroy();
            // $('#chartApp').empty();
            $('#appHistoryTable').empty();
            $('#appHistoryTable').append('<div  class="loader"></div>');
            if(type == 1)  $('#browserHistoryTable').show();
        },
        success: function (response) {
            if(type == 2){
                return browserHistoryData(response);
            }else return applicationHistoryData(response);
        },
        error: function (error) {
            APP_HISTORY_CHECK = false;
            if (error.status === 410) {
                $('#AppHistory').empty();
                $('#AppHistory').append('<p  style="color: red; text-align: center; font-size: 150%; width: 100%; height: 40% " class="mt-5"><b>'+EMPLOYEE_FULL_DETAILS_ERROR.AccessDenied+'</b></p>');
            } else {
                return errorHandler(DASHBOARD_JS.error);
            }
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



