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
var GLOBALData;
let ADD_REMOVE_COLUMN = ["Email", "Location", "Department", "EmpCode","computerName", "ClockIn", "ClockOut","CheckInIp","CheckOutIp", "TotalHour", "OfficeTime", "ActiveHour", "Productive", "Unproductive",  "IdleTime", "Neutral", "Offline", "Productivity"];
let ORIGINAL_TABLE_HEADER = ["Email", "Location", "Department", "EmpCode", "ClockIn", "ClockOut","CheckInIp","CheckOutIp", "TotalHour", "OfficeTime", "ActiveHour", "Productive", "Unproductive",  "IdleTime", "Neutral", "Offline", "Productivity"];


$(function () {
    var start = moment().subtract(0, 'days');
    var end = moment();
    const dateLimit = silahdomain ? { days: 65 } : { days: 30 };
    function cb(start, end) {
        $('#reportranges span').html(start.format('MMMM DD, YYYY') + ' - ' + end.format('MMMM DD, YYYY'));
        $('#from').val(start.format('YYYY-MM-DD'));
        $('#to').val(end.format('YYYY-MM-DD'));
        from = $('#from').val();
        to = $('#to').val();
        if (active_function == 1) {
            attendanceReports(locID, deptID, userID, SHOW_ENTRIES, 0, null);
        }
        if (active_function == 0) {
            active_function = 1
        }
    }

    $('#reportranges').daterangepicker({
        startDate: start,
        endDate: end,
        minDate : twelveMonth ? moment().subtract(12, 'months') : nineMonth  ? moment().subtract(9, 'months') : moment().subtract(180, 'days'),
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

    attendanceReports(locID, deptID, userID, SHOW_ENTRIES, 0, null);
    $("#employee").select2({width: "200"});
});

function getAllLocations() {
    locID = $('#locationdept').val();
    deptID = 0;
    userID = 0;
    getalldept(locID);
    users(locID, deptID);
    attendanceReports(locID, deptID, userID, SHOW_ENTRIES, 0, null);
}

function getAllDepartments() {
    deptID = $('#getDepartments').val();
    userID = 0;
    users(locID, deptID);
    attendanceReports(locID, deptID, userID, SHOW_ENTRIES, 0, null);
}

function allEmployee() {
    userID = $('#employee').val();
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



function attendanceReports(SelectlocID, SelectDeptId, SelectUserId) {
    var SelectedfromDate = new Date(from).toISOString();
    var SelectedtoDate = new Date(to).toISOString();
    var urlData;

    urlData = `location_id=${SelectlocID}&department_id=${SelectDeptId}&employee_id=${SelectUserId}&start_date=${SelectedfromDate}&end_date=${SelectedtoDate}`
    let url = '/employee-timeline';
    $.ajax({
        type: "post",
        url: '/' + userType + url,
        data: {
            data: urlData
        },
        beforeSend: function () {
            $("#loader").css('display', 'block');
            let jQueryScript = document.createElement('script');
            jQueryScript.setAttribute('src', 'https://momentjs.com/downloads/moment-timezone.min.js');
            document.head.appendChild(jQueryScript);
            let jQueryScript1 = document.createElement('script');
            jQueryScript1.setAttribute('src', 'https://momentjs.com/downloads/moment-timezone-with-data.min.js');
            document.head.appendChild(jQueryScript1);
            $("#timeattendanceHistory").empty();
            $('#time_history_tracked').DataTable().clear().draw();
            $('#time_history_tracked').DataTable().destroy();
        },
        success: function (response) {
            GLOBALData=response.data.results;
            $("#loader").css('display', 'none');
            appendData = "";
            var data = response.data;
            if (response.code === 200) {
                var ReportsData = data.results;
                if (ReportsData) {
                    ReportsData.forEach(function (attHistory) {
                        appendData +='<tr>';
                        if (userType === ENV_RESELLER) {
                            appendData += '<td class="stickyCol-sticky-col"">' + attHistory.first_name + ' ' + attHistory.last_name + '</td>';
                        } else {
                            appendData += '<td class="stickyCol-sticky-col""><a title="View Full Details" href="get-employee-details?id=' + attHistory.id + '">' + attHistory.first_name + ' ' + attHistory.last_name + ' </a></td>';
                        }
                        if (ADD_REMOVE_COLUMN.includes('Email')) appendData += '<td class="EmailTable" style="width: 90px;">' + attHistory.email + '</td>';
                        if (ADD_REMOVE_COLUMN.includes('EmpCode')) appendData += '<td class="EmpCodeTable" style="width: 90px;">' + attHistory.emp_code + '</td>';
                        if (ADD_REMOVE_COLUMN.includes('Location')) appendData += '<td class="LocationTable" style="text-align:center">' + attHistory.location_name + '</td>';
                        if (ADD_REMOVE_COLUMN.includes('Department')) appendData += '<td class="DepartmentTable" style="text-align:center">' + attHistory.department_name + '</td>';
                        if(COMPUTER_NAME_DATA){
                            if (ADD_REMOVE_COLUMN.includes('computerName'))    appendData += '<td class="EmailTable" style="width: 90px;">' + attHistory.computer_name + '</td>';
                        }
                        if (ADD_REMOVE_COLUMN.includes('ClockIn')) appendData += '<td class="ClockInTable" style="width: 90px;">' +   moment(attHistory.start_time).tz(attHistory.timezone).format('DD-MM-YYYY HH:mm:ss') + '</td>';
                        if (ADD_REMOVE_COLUMN.includes('ClockOut')) appendData += '<td class="ClockOutTable" style="width: 90px;">' +  moment(attHistory.end_time).tz(attHistory.timezone).format('DD-MM-YYYY HH:mm:ss') + '</td>';
                        if(checkEnvPermissionIP){
                            if (ADD_REMOVE_COLUMN.includes('CheckInIp') && $('input[value="CheckInIp"]').is(':checked')){ appendData += '<td class="CheckInIpTable" style="width: 90px;">' + attHistory?.details?.checkInIp.replace(',35.201.73.5', '') + '</td>';$('.CheckInIpTable').show()}else{$('.CheckInIpTable').hide()}
                            if (ADD_REMOVE_COLUMN.includes('CheckOutIp') && $('input[value="CheckOutIp"]').is(':checked')){ appendData += '<td class="CheckOutIpTable" style="width: 90px;">' + attHistory?.details?.checkOutIp.replace(',35.201.73.5', '') + '</td>';$('.CheckOutIpTable').show()}else{$('.CheckOutIpTable').hide()}
                        }
                        if (ADD_REMOVE_COLUMN.includes('OfficeTime')) appendData += ADD_REMOVE_COLUMN.includes('T_minutes') ? '<td class="OfficeTimeTable" style="text-align:center">' + attHistory.office_time + '</td>' : '<td class="OfficeTimeTable" style="text-align:center">' + attHistory.office_time + '</td>';
                        if (ADD_REMOVE_COLUMN.includes('ActiveHour')) appendData += ADD_REMOVE_COLUMN.includes('T_minutes') ? '<td class="ActiveHourTable" style="text-align:center">' + attHistory.computer_activities_time + '</td>' : '<td class="ActiveHourTable" style="text-align:center">' + attHistory.computer_activities_time + '</td>';
                        if (ADD_REMOVE_COLUMN.includes('Productive')) appendData += ADD_REMOVE_COLUMN.includes('T_minutes') ? '<td class="text-success ProductiveTable" style="text-align:center">' + attHistory.productive_duration + '</td>' : '<td class="text-success ProductiveTable" style="text-align:center">' + attHistory.productive_duration + '</td>';
                        if (ADD_REMOVE_COLUMN.includes('Unproductive')) appendData += ADD_REMOVE_COLUMN.includes('T_minutes') ? '<td class="text-danger UnproductiveTable" style="text-align:center">' + attHistory.non_productive_duration + '</td>' : '<td class="text-danger UnproductiveTable" style="text-align:center">' + attHistory.non_productive_duration + '</td>';
                        if (ADD_REMOVE_COLUMN.includes('Neutral')) appendData += ADD_REMOVE_COLUMN.includes('T_minutes') ? '<td class="text-secondary NeutralTable" style="text-align:center">' + attHistory.neutral_duration + '</td>' : '<td class="text-secondary ActiveHourTable" style="text-align:center">' + attHistory.neutral_duration + '</td>';
                        if (ADD_REMOVE_COLUMN.includes('IdleTime')) appendData += ADD_REMOVE_COLUMN.includes('T_minutes') ? '<td  class="text-warning IdleTimeTable" style="text-align:center">' + attHistory.idle_duration + '</td>' : '<td  class="text-warning AttendanceTable" style="text-align:center">' + attHistory.idle_duration + '</td>';
                        // if ( envAdminIds.includes(Number(adminId)) ) {
                        if (ADD_REMOVE_COLUMN.includes('Offline')) appendData += ADD_REMOVE_COLUMN.includes('T_minutes') ? '<td class="OfflineTable"  style="text-align:center">' + attHistory.offline_duration + '</td>' : '<td class="OfflineTable"  style="text-align:center">' + attHistory.offline_duration + '</td>';
                        // }
                        if (ADD_REMOVE_COLUMN.includes('Productivity')) appendData += '<td class="text-primary ProductivityTable" style="text-align:center">' + parseFloat(attHistory.productivity).toFixed(2) + '%</td>' ;
                        if(MOBILE_DATA){
                            appendData += ADD_REMOVE_COLUMN.includes('T_minutes') ? '<td style="text-align:center">' + attHistory.mobileUsageDuration + '</td>' : '<td style="text-align:center">' + (attHistory.mobileUsageDuration) + '</td>';
                        }
                        appendData +='<td style="text-align:center">' + (attHistory.deleteTime || '00:00:00') + '</td>'
                        appendData +=  '</tr>';
                    });
                }
                $('#timeattendanceHistory').append(appendData);
                $("#time_history_tracked").DataTable({
                    "lengthMenu": [[10, 25, 50, 100], [10, 25, 50,100]],
                     "scrollX": "100px",
                    "fixedHeader": "true",
                    "language": {"url": DATATABLE_LANG},
                });

            } else {
            }
        },
        error: function (error) {
            if (error.status === 403) {
                appendData += '<option disabled >' + DASHBOARD_JS_ERROR.permissionDenied + '</option>';
            } else {
                appendData += '<option disabled >' + DASHBOARD_JS_ERROR.reload + '</option>';
            }
        }
    });
}
function downloadSilahCsvPdf(type){
    if(GLOBALData.length==0) return errorHandler("No Data Found");
    if(type==1) silahTimeLinePdf(GLOBALData);
    if(type==2) dowmloadSilahCsv(GLOBALData);
}
function dowmloadSilahCsv(data){
    const csv = [
        [SILAH_PDFCSV.name, SILAH_PDFCSV.id, SILAH_PDFCSV.check_in_time, SILAH_PDFCSV.check_out_time, SILAH_PDFCSV.total_no_of_hours]
    ];
    data.forEach(ele => {
        let totalSeconds = ele.office_time;
        csv.push([
            `${ele.first_name} ${ele.last_name}`,
            ele.emp_code,
            moment(ele.start_time).format('YYYY-MM-DD HH:mm:ss'),
            moment(ele.end_time).format('YYYY-MM-DD HH:mm:ss'),
            totalSeconds + ' hrs',
        ]);
    });
    $('#cover-spin').hide();
    const filename = SILAH_PDFCSV.Time_Line + '.xlsx';
    const workbook = new ExcelJS.Workbook();
    const worksheet = workbook.addWorksheet('Time Line');
    worksheet.addRows(csv);
    const headerStyle = {
      font: {
        bold: true,
        color: { argb: 'FFFFFFFF' }
      },
      fill: {
        type: 'pattern',
        pattern: 'solid',
        fgColor: { argb: 'FFFF0000' }
      },
      alignment: {
        vertical: 'middle',
        horizontal: 'center'
      }
    };
    worksheet.getRow(1).eachCell((cell) => {
      cell.style = headerStyle;
    });
    workbook.xlsx.writeBuffer().then((buffer) => {
      const blob = new Blob([buffer], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = filename;
      a.click();
    });
}

async function silahTimeLinePdf(data) {
    const parsedUrl = new URL(window.location.href);
    const domineName = `${parsedUrl.host}`;
    const doc = new jsPDF('landscape', 'pt', 'a4');
    var startDate = new Date(from).toLocaleDateString();
    var endDate = new Date(to).toLocaleDateString();
    const pageWidth = doc.internal.pageSize.getWidth();
    const pageHeight = doc.internal.pageSize.getHeight();
    const addFooterAndLogo = (currentPage, totalPages) => {
        doc.addImage(silahCheckCircle, 'PNG', 10, pageHeight - 42, 36, 36);
        const pageText = `${currentPage}`;
        doc.setFontSize(15);
        doc.setTextColor(12, 12, 12);
        doc.text(`${pageText}` + SILAH_PDFCSV.page, 48, pageHeight - 20);
        doc.setFontSize(16);
        const dateRangeTextWidth = doc.getTextWidth(SILAH_PDFCSV.all_report_automatically);
        if (language_ar == "ar") {
            doc.setFont('Dubai');
            doc.text(SILAH_PDFCSV.all_report_automatically, (pageWidth - dateRangeTextWidth) / 2, pageHeight - 30, {direction: 'rtl'});
        } else {
            doc.text(SILAH_PDFCSV.all_report_automatically, (pageWidth - dateRangeTextWidth) / 2, pageHeight - 30,);
        }
        const linkText = domineName;
        doc.setFontSize(12);
        const textWidth = doc.getTextWidth(linkText);
        const xPosition = (pageWidth - textWidth) / 2;
        doc.setTextColor(204, 0, 0);
        doc.textWithLink(
            linkText,
            xPosition, // Centered X-coordinate
            pageHeight - 15, // Y-coordinate
            {url: domineName}
        );
    };
    let silahCheckCircle = new Image();
    silahCheckCircle.src = ALERTS_LOGO;
    try {
        doc.setLineWidth(2);
        doc.setDrawColor(0, 0, 0);
        doc.line(0, 61, 400, 61);
        doc.addImage(silahCheckCircle, 'PNG', 720, 20, 82, 82);
        var DubaiFont = await getDubaiMediumFont();
        doc.addFileToVFS('Dubai-Medium.ttf', DubaiFont);
        doc.addFont('Dubai-Medium.ttf', 'Dubai', 'normal');
        doc.addFont('Dubai-Medium.ttf', 'Dubai', 'bold');
        doc.addFont('Dubai-Medium.ttf', 'Dubai', "italic");
        doc.addFont('Dubai-Medium.ttf', 'Dubai', "bolditalic");
        doc.setFontSize(25);
        doc.setTextColor(12, 12, 12);
        if (language_ar == "ar") {
            doc.setFont('Dubai');
            doc.text(SILAH_PDFCSV.report_date_range, 700, 54, {align: 'right', direction: 'rtl'});
        } else {
            doc.text(SILAH_PDFCSV.report_date_range, 700, 54, {align: 'right'});
        }
        doc.setFontSize(23);
        doc.setTextColor(204, 0, 0);
        if (language_ar == "ar") {
            doc.setFont('Dubai');
            doc.text(SILAH_PDFCSV.time_line_report, 700, 84, {align: 'right', direction: 'rtl'});
        } else {
            doc.text(SILAH_PDFCSV.time_line_report, 700, 84, {align: 'right'});
        }
        doc.setFillColor(204, 0, 0);
        doc.roundedRect(70, 130, pageWidth - 140, 70, 1, 1, 'F');
        doc.setFontSize(14);
        doc.setTextColor(255, 255, 255);
        if (language_ar == "ar") {
            doc.setFont('Dubai');
            doc.text(SILAH_PDFCSV.report_date_range, pageWidth - 80, 150, {align: 'right', direction: 'rtl'});
        } else {
            doc.text(SILAH_PDFCSV.report_date_range, pageWidth - 80, 150, {align: 'right'});
        }
        doc.setFontSize(20);
        doc.setTextColor(255, 255, 255);
        doc.text(
            ':',
            pageWidth - 320,
            150,
            {align: 'right'}
        );
        doc.setFontSize(14);
        doc.setTextColor(255, 255, 255);
        doc.text(`${startDate} - ${endDate}`, pageWidth - 350, 150, {align: 'right'});
        doc.setFontSize(20);
        doc.setTextColor(255, 255, 255);
        doc.text(
            ':',
            pageWidth - 320,
            180,
            {align: 'right'}
        );
        doc.setFontSize(14);
        doc.setTextColor(255, 255, 255);
        doc.text(String(new Set(data.map(item => item.id)).size), pageWidth - 350, 180, {align: 'right'});
        doc.setFontSize(14);
        doc.setTextColor(255, 255, 255);
        if (language_ar == "ar") {
            doc.setFont('Dubai');
            doc.text(SILAH_PDFCSV.no_of_employee_report, pageWidth - 80, 180, {align: 'right', direction: 'rtl'});
        } else {
            doc.text(SILAH_PDFCSV.no_of_employee_report, pageWidth - 80, 180, {align: 'right'});
        }
        const StartY = 250;

        doc.setFillColor(204, 0, 0);
        doc.setDrawColor(0, 0, 0);
        doc.rect(20, 220, pageWidth - 70, 30, 'FD');
        doc.setFontSize(14);
        doc.setTextColor(255, 255, 255);
        if (language_ar == "ar") {
            doc.setFont('Dubai');
            doc.text(SILAH_PDFCSV.table, pageWidth / 2, 239, {align: 'center', direction: 'rtl'});
        } else {
            doc.text(SILAH_PDFCSV.table, pageWidth / 2, 239, {align: 'center'});
        }
        let bodyData = data.map((ele) => {
            let totalTime= ele.office_time;
            return [
                totalTime + ' hrs',
                moment(ele.end_time).format('YYYY-MM-DD HH:mm:ss'),
                moment(ele.start_time).format('YYYY-MM-DD HH:mm:ss'),
                moment(ele.start_time).format('YYYY-MM-DD'),
                ele.emp_code,
                `${ele.first_name} ${ele.last_name}`
            ];
        });
        const tableData = [SILAH_PDFCSV.total_no_of_hours, SILAH_PDFCSV.check_out_time, SILAH_PDFCSV.check_in_time, SILAH_PDFCSV.date_of_the_day, SILAH_PDFCSV.id, SILAH_PDFCSV.name]

        doc.autoTable({
            head: [tableData],
            body: bodyData,
            startY: StartY,
            theme: 'grid',
            bodyStyles: {lineColor: [0, 0, 0], lineWidth: 1}, halign: language_ar == "ar" ? "right" : "center",
            tableWidth: 770,
            margin: {left: 20},
            styles: {
                font: "Dubai",
                textDirection: language_ar === "ar" ? "rtl" : "ltr",
                halign: language_ar === "ar" ? "right" : "center",
            },
            headStyles: {
                fillColor: [204, 0, 0],
                textColor: [255, 255, 255],
                fontSize: 9,
                lineColor: [0, 0, 0],
                lineWidth: 1,
                minCellHeight: 30, halign: language_ar == "ar" ? "right" : "center",
            },
            columnStyles: {
                0: {cellWidth: 'auto'},
                1: {cellWidth: 'auto'},
                2: {cellWidth: 'auto'},
                3: {cellWidth: 'auto'},
                4: {cellWidth: 'auto'},
                5: {cellWidth: 'auto'},
            },
            didDrawPage: function (data) {
                const currentPage = doc.internal.getCurrentPageInfo().pageNumber;
                const totalPages = doc.internal.getNumberOfPages();
                addFooterAndLogo(currentPage, totalPages);
            },
        });
        doc.save(SILAH_PDFCSV.Time_Line + '.pdf');
    } catch (error) {
        console.error('Error while creating PDF:', error);
    }
}
let convertTimeHMS = (s) => {
    let h = Math.floor(s / 3600); //Get whole hours
    s -= h * 3600;
    let m = Math.floor(s / 60); //Get remaining minutes
    s -= m * 60;
    return h + ":" + (m < 10 ? '0' + m : m) + ":" + (s < 10 ? '0' + s : s); //zero padding on minutes and seconds
}
function attendanceReportsDownload(value) {
    // $('#cover-spin').show(0);
    var SelectedfromDate = new Date(from).toISOString();
    var SelectedtoDate = new Date(to).toISOString();

    $.ajax({
        type: "post",
        url: "/" + userType + "/employee-timeline-download",
        data: {
            data: `location_id=${locID}&department_id=${deptID}&employee_id=${userID}&start_date=${SelectedfromDate}&end_date=${SelectedtoDate}`
        },
        beforeSend: function () {

        },
        success: function (response) {
            appendData = "" , appendDataHeader = "";
            var data = response.data;
            if (response.code === 200) {
                var ReportsData = data.results;
                COMPLETE_RECORD_DATA = ReportsData;
                if (ReportsData) {
                    console.log(ReportsData);
                    pdfCsv = 0;
                    appendDataHeader += `<tr>`;
                    appendDataHeader += `<th>${TABLE_HEADER_DOWNLOAD.name}</th>`;
                    appendDataHeader += `<th>${TABLE_HEADER_DOWNLOAD.Email_id}</th>`;
                    appendDataHeader += `<th>${TABLE_HEADER_DOWNLOAD.employeeCode}</th>`;
                    appendDataHeader += `<th>${TABLE_HEADER_DOWNLOAD.location}</th>`;
                    appendDataHeader += `<th>${TABLE_HEADER_DOWNLOAD.department}</th>`;
                    appendDataHeader += `<th>${TABLE_HEADER_DOWNLOAD.clockin}</th>`;
                    appendDataHeader += `<th>${TABLE_HEADER_DOWNLOAD.clockout}</th>`
                    appendDataHeader += `<th style="width: 90px;"> ${TABLE_HEADER_ATTENDANCE.Time}</th>`
                    appendDataHeader += `<th style="width: 90px;">${TABLE_HEADER_ATTENDANCE.activeHours} ${TABLE_HEADER_ATTENDANCE.Time}</th>`
                    appendDataHeader += `<th  class="text-success">${TABLE_HEADER_DOWNLOAD.productive}</th>`
                    appendDataHeader += `<th class="text-danger">${TABLE_HEADER_DOWNLOAD.unProdHour}</th>`
                    appendDataHeader += `<th class="text-secondary">${TABLE_HEADER_DOWNLOAD.neutral}</th>`
                    appendDataHeader += `<th class="text-warning">${TABLE_HEADER_DOWNLOAD.idles}  ${TABLE_HEADER_ATTENDANCE.Time} </th>`
                    appendDataHeader += `<th>${TABLE_HEADER_DOWNLOAD.offline}  ${TABLE_HEADER_ATTENDANCE.Time}</th>`
                    appendDataHeader += ` <th class="text-primary">${TABLE_HEADER_DOWNLOAD.productivity} %</th>`;
                    if (MOBILE_DATA) {
                        appendDataHeader += ` <th class="text-primary">` + mobileHrs + `</th>`;
                    }
                    appendDataHeader += ` </tr>`;
                    ReportsData.forEach(function (attHistory) {
                        let emails = ((attHistory.email == null) || (attHistory.email == "null")) ? '-' : attHistory.email;
                        appendData += '<tr attendanceId="' + attHistory.attendance_id + '" id="' + attHistory.id + '" ><td><a title="View Full Details" href="get-employee-details?id=' + attHistory.id + '">' + attHistory.first_name + ' ' + attHistory.last_name + ' </a></td>';
                        appendData += '<td style="width: 90px;">' + emails + '</td>';
                        appendData += '<td style="width: 90px;">' + attHistory.emp_code + '</td>';
                        appendData += '<td style="text-align:center">' + attHistory.location_name + '</td>';
                        appendData += '<td style="text-align:center">' + attHistory.department_name + '</td>';
                        appendData += attHistory.start_time !== null ? '<td style="width: 90px;">' + attHistory.start_time + '</td>' : '<td style="text-align:center">' + Employee_Absent + '</td>';
                        appendData += attHistory.end_time !== null ? '<td style="width: 90px;">' + attHistory.end_time + '</td>' : '<td style="text-align:center">' + Employee_Absent + '</td>';
                        appendData += attHistory.office_time !== null ? '<td style="text-align:center">' + attHistory.office_time + '</td>' : '<td style="text-align:center">' + Employee_Absent + '</td>';
                        appendData += attHistory.computer_activities_time !== null ? '<td style="text-align:center">' + attHistory.computer_activities_time + '</td>' : '<td style="text-align:center">' + Employee_Absent + '</td>';
                        appendData += attHistory.productive_duration !== null ? '<td class="text-success" style="text-align:center">' + attHistory.productive_duration + '</td>' : '<td style="text-align:center">' + Employee_Absent + '</td>';
                        appendData += attHistory.non_productive_duration !== null ? '<td class="text-danger" style="text-align:center">' + attHistory.non_productive_duration + '</td>' : '<td style="text-align:center">' + Employee_Absent + '</td>';
                        appendData += attHistory.neutral_duration !== null ? '<td class="text-secondary" style="text-align:center">' + attHistory.neutral_duration + '</td>' : '<td style="text-align:center">' + Employee_Absent + '</td>';
                        appendData += attHistory.idle_duration !== null ? '<td  class="text-warning" style="text-align:center">' + attHistory.idle_duration + '</td>' : '<td style="text-align:center">' + Employee_Absent + '</td>';
                        appendData += attHistory.offline !== null ? '<td style="text-align:center">' + attHistory.offline_duration + '</td>' : '<td style="text-align:center">' + Employee_Absent + '</td>';
                        appendData += attHistory.productivity !== null ? '<td class="text-primary" style="text-align:center">' + attHistory.productivity.toFixed(2) + '%</td>' : '<td style="text-align:center">' + Employee_Absent + '</td>';
                        if (MOBILE_DATA) {
                            appendData += '<td>' + attHistory.mobileUsageDuration + '</td>';
                        }
                    });
                }
            } else {
                $('#cover-spin').hide();
                pdfCsv = 1;
                message = response.msg;
                appendData += '<option disabled>' + response.msg + '</option>';
            }
            console.log(appendDataHeader);
            $('#timeattendanceHistory1').empty();
            $('#timeattendanceHistory1').append(appendData);
            $('#timePDFDownloadHeader').empty();
            $('#timePDFDownloadHeader').append(appendDataHeader);
            (value == 1) ? (download_table_as_csv()) : (download_table_as_pdf());
        },
        error: function (jqXHR) {
            $('#cover-spin').hide();
            if (jqXHR.status == 410) {
                $("#UnaccessModal").empty();
                $("#UnaccessModal").css('display', 'block');
                $("#UnaccessModal").append('<div class="alert alert-danger text-center"><button type="button" class="close" data-dismiss="alert" >&times;</button><b  id="ErrorMsgForUnaccess"> ' + jqXHR.responseJSON.error + '</b></div>')
            } else {
                appendData += '<option disabled >' + DASHBOARD_JS_ERROR.reload + '</option>';
            }
            $('#employee').append(appendData);
        }
    });
}
function download_table_as_csv() {
    if (pdfCsv === 0) {
        var rows = document.querySelectorAll('table#time_history_tracked1 tr');
        var csv = [];
        for (var i = 0; i < rows.length; i++) {
            var row = [], cols = rows[i].querySelectorAll('td, th');
            for (var j = 0; j < cols.length; j++) {
                var data = cols[j].innerText;
                row.push(data);
            }
            csv.push(row);
        }
        $('#cover-spin').hide();
        const filename = downloadFileName + '.xlsx';
        const ws_name = "attendance history";
        const wb = XLSX.utils.book_new(),
            ws = XLSX.utils.aoa_to_sheet(csv);
        XLSX.utils.book_append_sheet(wb, ws, ws_name);
        XLSX.writeFile(wb, filename);
    } else {
        warningHandler(message);
    }
}

async function download_table_as_pdf() {
    if (pdfCsv === 0) {
        let img = new Image();
        img.src = DYNAMIC_LOGO;


        let totalPagesExp = "{total_pages_count_string}";
        let leftMargin = 15;
        let doc = new jsPDF('p', 'pt', 'a2');
        if (language_ar == "ar" || silahdomain) {
            var TajawalFont = await getTajawalFont();
            doc.addFileToVFS('Tajawal-Regular-normal.ttf', TajawalFont);
            doc.addFont('Tajawal-Regular-normal.ttf', 'Tajawal', 'normal');
            doc.addFont('Tajawal-Regular-normal.ttf', 'Tajawal', 'bold');
            doc.addFont('Tajawal-Regular-normal.ttf', 'Tajawal', "italic");
            doc.addFont('Tajawal-Regular-normal.ttf', 'Tajawal', "bolditalic");
            doc.setFont('Tajawal');
        }
        doc.text(80, 30, downloadFileName, {align: 'left'}, 100, 20)
        doc.setFontSize(13);
        doc.text(97, 73, REPORT_DOWNLOAD_MSG.fromDate + " :-", {align: 'right'}, 100, 20);
        doc.text(85, 101, REPORT_DOWNLOAD_MSG.locatiom + " :-", {align: 'right'}, 100, 20);
        doc.text(93, 129, REPORT_DOWNLOAD_MSG.employee + " :-", {align: 'right'}, 100, 20);
        // doc.setFontType("normal");
        doc.text(175, 73, from, {align: 'right'}, 100, 20);
        let length1 = 150 + ($("#locationdept").children(':selected').text().replace('See', '').length - 6) * 5.5;
        doc.text(length1, 101, $("#locationdept").children(':selected').text().replace('See', ''), {align: 'right'}, 100, 20);
        let length2 = 150 + ($("#employee").children(':selected').text().replace('See', '').length - 6) * 5.5;
        doc.text(length2, 129, $("#employee").children(':selected').text().replace('See', ''), {align: 'right'}, 100, 20);
        $("#FromDatePdf").html(from);
        $("#locationPdf").html($("#locationdept").children(':selected').text().replace('See', ''));
        $("#employeePdf").html($("#employee").children(':selected').text().replace('See', ''));

        doc.text(360, 73, REPORT_DOWNLOAD_MSG.toDate + " :-", {align: 'right'}, 100, 20);
        doc.text(360, 101, REPORT_DOWNLOAD_MSG.department + " :-", {align: 'right'}, 100, 20);
        doc.setFontType("normal");
        doc.text(440, 73, to, {align: 'right'}, 100, 20);
        let length = 436 + ($("#getDepartments").children(':selected').text().replace('See', '').length - 6) * 5.5;
        doc.text(length, 101, $("#getDepartments").children(':selected').text().replace('See', ''), {align: 'right'}, 100, 20);
        doc.fromHTML($('#section1').html(), 30, 20, {
            'width': 4000,
        });
        doc.setFontSize(13);
        // if (envAdminIds.includes(Number(adminId))) {
        doc.addImage(img, 'PNG', 1020, 50, 140, 45);

        if (language_ar == "ar" || silahdomain) {
            doc.autoTable({
                html: '#time_history_tracked1', theme: "grid",
                startY: 160,
                startX: 8,
                tableWidth: '500',
                columnStyles: {
                    0: {cellWidth: 80},
                    1: {cellWidth: 90},
                    2: {cellWidth: 50},
                    3: {cellWidth: 40},
                    4: {cellWidth: 60},
                    5: {cellWidth: 60},
                    6: {cellWidth: 52},
                    7: {cellWidth: 50},
                    8: {cellWidth: 63},
                    9: {cellWidth: 63},
                    10: {cellWidth: 60},
                    11: {cellWidth: 60},
                    12: {cellWidth: 49},
                    13: {cellWidth: 55},
                    14: {cellWidth: 55},
                    15: {cellWidth: 52},
                    16: {cellWidth: 60},
                    17: {cellWidth: 62},
                    18: {cellWidth: 62},
                    19: {cellWidth: 68},

                },
                styles: {
                    overflow: 'linebreak',
                    font: 'arial',
                    cellPadding: 5,
                    font: 'Tajawal',
                    // overflowColumns: 'linebreak'
                }, didDrawPage: function (data) {
                    let str = "Page " + data.pageCount;
                    if (typeof doc.putTotalPages === 'function') {
                        str = str + " of " + totalPagesExp;
                    }
                    doc.text(str, leftMargin, doc.internal.pageSize.height - 10);
                }
            });
        } else {
            doc.autoTable({
                html: '#time_history_tracked1', theme: "grid",
                startY: 160,
                startX: 8,
                tableWidth: '500',
                columnStyles: {
                    0: {cellWidth: 80},
                    1: {cellWidth: 90},
                    2: {cellWidth: 50},
                    3: {cellWidth: 40},
                    4: {cellWidth: 60},
                    5: {cellWidth: 60},
                    6: {cellWidth: 52},
                    7: {cellWidth: 50},
                    8: {cellWidth: 63},
                    9: {cellWidth: 55},
                    10: {cellWidth: 55},
                    11: {cellWidth: 55},
                    12: {cellWidth: 49},
                    13: {cellWidth: 55},
                    14: {cellWidth: 55},
                    15: {cellWidth: 52},
                    16: {cellWidth: 60},
                    17: {cellWidth: 55},
                    18: {cellWidth: 58},
                    19: {cellWidth: 55},

                },
                styles: {
                    overflow: 'linebreak',
                    font: 'arial',
                    cellPadding: 5,
                    // overflowColumns: 'linebreak'
                }, didDrawPage: function (data) {
                    let str = "Page " + data.pageCount;
                    if (typeof doc.putTotalPages === 'function') {
                        str = str + " of " + totalPagesExp;
                    }
                    doc.text(str, leftMargin, doc.internal.pageSize.height - 10);
                }
            });
        }
        if (typeof doc.putTotalPages === 'function') {
            doc.putTotalPages(totalPagesExp);
        }
        doc.save(downloadFileName);
        $('#cover-spin').hide();
    } else {
        warningHandler(message);
    }
}

//Added and removing table headers
let tableHeaderUpdated = () => {
    attendanceReports(locID, deptID, userID, SHOW_ENTRIES, 0, $("#SearchTextField").val(), SORT_NAME, SORT_ORDER);
    setTimeout(function () {
        $("#mytimesheetdataDownload").hide();
        $("#Manager_List_Id, #Split_List_Id").show();
        $("#checklocDownload").prop('checked', false);
    }, 10)
};

// To make the split sheets in excel format based on date
function s2ab(s) {
    var buf = new ArrayBuffer(s.length);
    var view = new Uint8Array(buf);
    for (var i = 0; i != s.length; ++i) view[i] = s.charCodeAt(i) & 0xFF;
    return buf;
}
