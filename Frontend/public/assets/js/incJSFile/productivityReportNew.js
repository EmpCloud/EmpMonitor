let from = "";
let to = "";
let department = "";
let loc = "";
let userId = "";
let PAGE_ID = 1;
let countt = 0;
let SHOW_ENTRIES = "10";
let SORT_NAME = '';
let SORT_ORDER = '';
let SORTED_TAG_ID = '';
let PAGE_COUNT_CALL = false;
let appendData = "";
let globalChart = null;

$(function () {
    paginationSetup();
    if (TOTAL_COUNT_EMAILS == 0) {
        $('.pagination').jqPagination();
        $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' 0  to 0 of 0');
    } else {
        TOTAL_COUNT_EMAILS < SHOW_ENTRIES ? $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' ' + 1 + ' ' + DATATABLE_LOCALIZE_MSG.to + ' ' + TOTAL_COUNT_EMAILS + ' ' + DATATABLE_LOCALIZE_MSG.of + ' ' + TOTAL_COUNT_EMAILS) :
            $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' ' + 1 + ' ' + DATATABLE_LOCALIZE_MSG.to + ' ' + SHOW_ENTRIES + ' ' + DATATABLE_LOCALIZE_MSG.of + ' ' + TOTAL_COUNT_EMAILS);
    }

    var start = moment().subtract(7, "days");
    var end = moment().subtract(1, 'days');

    function cb(start, end) {
        $("#reportrange span").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
        );
        $('#from').val(start.format('YYYY-MM-DD'));
        $('#to').val(end.format('YYYY-MM-DD'));
        from = $('#from').val();
        to = $('#to').val();

        getReportRecord(userId, department, loc, from, to);
        if (countt != 0) {
            PAGE_ID = 1;
            makeDatatableDefault();
            getProductivityTable("", userId, department, loc, from, to, SHOW_ENTRIES, 0, "");
        }
        countt++;
    }

    $("#reportrange").daterangepicker({
            startDate: start,
            endDate: end,
            maxDate: moment(),
            dateLimit: {days: '30'},
            ...dateRangeLocalization,
        },
        cb
    );
    cb(start, end);
});
$(function () {
    paginationSetup();
    if (TOTAL_COUNT_EMAILS == 0) {
        $('.pagination').jqPagination();
        $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' 0  to 0 of 0');
    } else {
        TOTAL_COUNT_EMAILS < SHOW_ENTRIES ? $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' ' + 1 + ' ' + DATATABLE_LOCALIZE_MSG.to + ' ' + TOTAL_COUNT_EMAILS + ' ' + DATATABLE_LOCALIZE_MSG.of + ' ' + TOTAL_COUNT_EMAILS) :
            $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' ' + 1 + ' ' + DATATABLE_LOCALIZE_MSG.to + ' ' + SHOW_ENTRIES + ' ' + DATATABLE_LOCALIZE_MSG.of + ' ' + TOTAL_COUNT_EMAILS);
    }

    var start = moment().subtract(7, "days");
    var end = moment().subtract(1, "days");

    var starts = moment().subtract(7, "days");
    var ends = moment().subtract(1, "days");

    function cb(start, end) {
        $("#reportdaterangess span").html(
            start.format("MMMM D, YYYY") + " - " + end.format("MMMM D, YYYY")
        );
        $('#from').val(starts.format('YYYY-MM-DD'));
        $('#to').val(ends.format('YYYY-MM-DD'));
        from = $('#from').val();
        to = $('#to').val();

        getReportRecord(userId, department, loc, from, to);
        if (countt != 0) {
            PAGE_ID = 1;
            makeDatatableDefault();
            getProductivityTable("", userId, department, loc, from, to, SHOW_ENTRIES, 0, "");
        }
        countt++;
    }

    $("#reportdaterangess").daterangepicker({
            startDate: start,
            minDate: '3M',
            maxDate: moment(),
            endDate: end,
            ranges: {
                'This Month': [moment().startOf('month'), moment().endOf('month')],
                'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Quaterly': [moment().subtract(90, 'days'), moment()],
            },
            showCustomRangeLabel: false
        },
        cb
    );

    cb(start, end);
});

let CalledUserFunction =
    (skip, searchtext) => {
        // here you have to call your function which your using to the list like
        // getUsers(LocationId, RoleId, DepartementId,SHOW_ENTRIES,skip,searchtext,SORT_NAME,SORT_ORDER)
        getProductivityTable("", userId, department, loc, from, to, SHOW_ENTRIES, skip, searchtext);

        // it will gives us skip value your have to keep that skip value and pass the value to your API
        // and show entries is your limit  that will gives you when you changed page length in table.

    }
    //for getting multiple departement id's


    function getReportRecord(user_id, dept_ids, loc_id, start_date, end_date) {
        const deptIdArray = dept_ids.split(','); // Convert string to array
        
        const fetchData = (dept_id) => {
            return $.ajax({
                url: `/${userType}/productivity-report-data-new`,
                method: 'post',
                data: {
                    user_id: user_id,
                    dept_id: dept_id,
                    loc_id: loc_id,
                    start_date: start_date,
                    end_date: end_date,
                }
            });
        };
        
        $('#chartMessage').text('');
        
        Promise.all(deptIdArray.map(fetchData))
            .then(responses => {
                let completeData = [];
                responses.forEach(resp => {
                    if (resp.code === 200) {
                        resp.data.forEach(data => {
                            completeData.push({
                                'year': data.date,
                                'Productive': (Number(data.productiveTime.split(":")[0]) + Number(data.productiveTime.split(":")[1] / 60)),
                                'Unproductive': (Number(data.nonProductiveTime.split(":")[0]) + Number(data.nonProductiveTime.split(":")[1] / 60)),
                                'Neutral': (Number(data.neutralTime.split(":")[0]) + Number(data.neutralTime.split(":")[1] / 60)),
                                'productiveTime': data.productiveTime,
                                'nonProductiveTime': data.nonProductiveTime,
                                'neutralTime': data.neutralTime,
                                'dept_id': data.dept_id // Keeping track of department
                            });
                        });
                    }
                });
                
                am4core.ready(function () {
                    // Dispose previous chart if exists
                    if (globalChart) {
                        globalChart.dispose();
                    }
                    
                    am4core.useTheme(am4themes_animated);
                    var chart = am4core.create("chartdiv", am4charts.XYChart);
                    globalChart = chart; // Store chart reference
                    chart.data = completeData;
                    
                    var categoryAxis = chart.xAxes.push(new am4charts.CategoryAxis());
                    categoryAxis.dataFields.category = "year";
                    categoryAxis.renderer.grid.template.location = 0;
                    
                    var valueAxis = chart.yAxes.push(new am4charts.ValueAxis());
                    valueAxis.min = 0;
                    valueAxis.renderer.ticks.template.strokeOpacity = 0.5;
                    valueAxis.renderer.minGridDistance = 40;
                    valueAxis.title.text = TimeUsed;
                    
                    function createSeries(field, name, color, timeField) {
                        var series = chart.series.push(new am4charts.ColumnSeries());
                        series.name = name;
                        series.dataFields.valueY = field;
                        series.dataFields.categoryX = "year";
                        series.sequencedInterpolation = true;
                        series.stroke = color;
                        series.fill = color;
                        series.stacked = true;
                        series.columns.template.width = am4core.percent(60);
                        series.columns.template.tooltipText = `[bold]{name}[/]\n[font-size:14px]{categoryX}: {${timeField}}`;
                        
                        var labelBullet = series.bullets.push(new am4charts.LabelBullet());
                        labelBullet.label.text = `{${timeField}}`;
                        labelBullet.locationY = 0.5;
                        labelBullet.label.hideOversized = true;
                    }
                    
                    var interfaceColors = new am4core.InterfaceColorSet();
                    createSeries("Productive", PRODUCTIVITY_RULE_JS_MSG.productive, am4core.color("#26c36c"), "productiveTime");
                    createSeries("Unproductive", PRODUCTIVITY_RULE_JS_MSG.unproductive, am4core.color("#f22f3f"), "nonProductiveTime");
                    createSeries("Neutral", PRODUCTIVITY_RULE_JS_MSG.neutral, am4core.color("#CCCCCC"), "neutralTime");
                    
                    chart.scrollbarX = new am4core.Scrollbar();
                    chart.legend = new am4charts.Legend();
                });
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: PRODUCTIVITY_ERROR_MSG.getReport,
                    showConfirmButton: true
                });
            });
    }
    

function locChanged(sel) {
    department = "";
    userId = "";
    loc = sel.value;
    PAGE_ID = 1;
    if (sel.value !== "All") {
        $("#dynamicHeading").html(DEPARTMENT_MSG);
        $("#departmentsAppend").prop("disabled", false);
    } else {
        $("#departmentsAppend").prop("disabled", true);
        $("#empoption").prop("disabled", true);
        $("#dynamicHeading").html(LOCATION_MSG);
    }
    $('#empoption option:first').prop('selected', true);
    makeDatatableDefault();
    getProductivityTable("", userId, department, loc, from, to, SHOW_ENTRIES, 0, "", SORT_NAME, SORT_ORDER);
    getDepartmentByLocation(loc);
    getEmpByLocDeptChange(loc, department);
    getReportRecord(userId, department, loc, from, to);
}

function depChange(sel) {
    userId = "";
    department = sel.value;
    $('#empoption option:first').prop('selected', true);
    PAGE_ID = 1;
    if (sel.value !== "All") {
        $("#dynamicHeading").html(EMPLOYEE_MSG);
        getReportRecord(userId, department, loc, from, to);
        $("#empoption").prop("disabled", false);

    } else {
        getReportRecord(userId, department, loc, from, to);
        $("#empoption").prop("disabled", true);
        $("#dynamicHeading").html(DEPARTMENT_MSG);

    }
    makeDatatableDefault();
    getProductivityTable("", userId, department, loc, from, to, SHOW_ENTRIES, 0, "", SORT_NAME, SORT_ORDER);
    getEmpByLocDeptChange(loc, department);

}
$("#departmentsAppend").change(function () {
    userId = "";
    deptIds = [];
    let batchID = $('#departmentsAppend').select2('data');
    $('#empoption option:first').prop('selected', true);
    batchID.forEach(function (dept) {
        deptIds.push(dept.id);
    });
    PAGE_ID = 1;
    let id_s = deptIds.length !== 0 ? deptIds.toString() : "All";
    department = id_s;
    if (id_s !== "All") {
        $("#dynamicHeading").html(EMPLOYEE_MSG);
        getReportRecord(userId, department, loc, from, to);
        $("#empoption").prop("disabled", false);

    } else {
        getReportRecord(userId, department, loc, from, to);
        $("#empoption").prop("disabled", true);
        $("#dynamicHeading").html(DEPARTMENT_MSG);

    }
    
    makeDatatableDefault();
    getProductivityTable("", userId, department, loc, from, to, SHOW_ENTRIES, 0, "", SORT_NAME, SORT_ORDER);
    getEmpByLocDeptChange(loc, department);
});

function EmployeeChange(sel) {
    userId = [];
    let SelectedManagerIDs = [];
    let id = $("#empoption").select2('data');
    id.forEach(function (dept) {
        SelectedManagerIDs.push(dept.id);
    });
    userId = SelectedManagerIDs.length !== 0 ? SelectedManagerIDs.toString() : "All";
    PAGE_ID = 1;
    if (sel.value !== "All") {
        $("#dynamicHeading").html("Date");
        getReportRecord(userId, department, loc, from, to);
    }
    makeDatatableDefault();

    getProductivityTable("", userId, department, loc, from, to, SHOW_ENTRIES, 0, "", SORT_NAME, SORT_ORDER);
}

//added by piyush to select one employee at a time
function employeeChangeFunction(sel) {
    userId = sel.value;
    PAGE_ID = 1;
    if (sel.value !== "All") {
        getReportRecord(userId, department, loc, from, to);
        $("#dynamicHeading").html("Date");
    }
    makeDatatableDefault();
    getProductivityTable("", userId, department, loc, from, to, SHOW_ENTRIES, 0, "", SORT_NAME, SORT_ORDER);
}


 


function getDepartmentByLocation(loc_id) {
    $.ajax({
        url: "/" + userType + "/get-dept-by-loc-id",
        method: 'post',
        data: {
            loc_id: loc_id,
        },
        beforeSend: function () {
        },
        success: function (resp) {
            if (resp.code === 200) {
                $('#departmentsAppend').find('option').not(':nth-child(1)').remove();
                $.each(resp.data.data, function (key, value) {

                    $('#departmentsAppend')
                        .append($("<option></option>")
                            .attr("value", value.department_id)
                            .text(value.name));
                });
            } else {
                $('#departmentsAppend').find('option').not(':nth-child(1)').remove();
                $('#departmentsAppend')
                    .append($("<option></option>")
                        .attr("value", "")
                        .text("No employee"));
            }
        },
        error: function (err) {
        }
    });
}

function getEmpByLocDeptChange(loc_id, dept_id) {
    $.ajax({
        url: "/" + userType + "/get-emp-by-loc-id",
        method: 'post',
        data: {
            loc_id: loc_id,
            dept_id: dept_id

        },
        beforeSend: function () {
            $('#empoption').empty();
            $("#checkAll").attr('disabled', false);
            $("#checkAll").prop('checked', false);
        },
        success: function (resp) {
            if (resp.code === 200 && resp.data.code === 200) {
                // for selecting multiple employee just uncommnet this
                // $.each(resp.data.data, function (key, value) {
                //     $('#empoption').append(`<li><input class="mr-2 checkSingle" value="${value.id}" type="checkbox"> ${value.first_name + " " + value.last_name}</li>`);

                //for selecting one employee at a time
                $('#empoption').find('option').not(':nth-child(1)').remove();
                $('#empoption').append($("<option></option>")
                    .attr("value", '')
                    .text('Select Employee'));
                $.each(resp.data.data, function (key, value) {
                    $('#empoption').append($("<option></option>")
                        .attr("value", value.id)
                        .text(value.first_name + " " + value.last_name));
                });
                checkBoxSelection();
            } else {
                $('#empoption').find('option').not(':nth-child(1)').remove();
                $('#empoption').append($("<option></option>")
                    .attr("value", "")
                    .text("No employee"));
            }
        },
        error: function (err) {
        }
    });
    onKeyUpSearch();
}

//Get productivity table data
function getProductivityTable(type, user_id, dept_id, loc_id, start_date, end_date, SHOW_ENTRIES, skip, searchtext, SORT_NAME, SORT_ORDER) {
    const deptIdsArray = dept_id.split(',').map(id => id.trim()); // Convert string to array
    
       $('.computernameshow').css('display', 'block');
    const fetchDataForDept = (deptId) => {
        return $.ajax({
            url: `/${userType}/productivity-report-data-list-new`,
            method: 'post',
            data: {
                skip: skip,
                user_id: user_id,
                dept_id: deptId,  // Single department ID per request
                loc_id: loc_id,
                start_date: start_date,
                end_date: end_date,
                limit: SHOW_ENTRIES,
                sortOrder: SORT_ORDER,
                sortName: SORT_NAME,
                nonAdminId: $('#nonadmins').val(),
            }
        });
    };
    
    $('#reportTableBody').empty();
    $("#loader").css("display", "block");
    
    // Execute all API calls in parallel
    Promise.all(deptIdsArray.map(fetchDataForDept))
        .then(responses => {
            $("#loader").css("display", "none");
    
            let allData = [];
            let totalEmails = 0;
    
            responses.forEach(resp => {
                if (resp.code === 200) {
                    allData = allData.concat(resp.productivity_table);
                    totalEmails += resp.total_count || 0;
                }
            });
    
            if (PAGE_COUNT_CALL === true) {
                TOTAL_COUNT_EMAILS = totalEmails;
                paginationSetup();
                $("#showPageNumbers").html(
                    ` ${DATATABLE_LOCALIZE_MSG.showing} 1 ${DATATABLE_LOCALIZE_MSG.to} 
                    ${Math.min(SHOW_ENTRIES, TOTAL_COUNT_EMAILS)} 
                    ${DATATABLE_LOCALIZE_MSG.of} ${TOTAL_COUNT_EMAILS}`
                );
            }
    
            // Data generation
            let appendData = "";
            _.forEach(allData, function (user) {
                appendData += `<tr>`;
                appendData += `<td>${user.name}</td>`;
                appendData += `<td>${user.Total} Hr</td>`;
                appendData += `<td>${user.Productive} Hr</td>`;
                appendData += `<td>${user.Pro_percent} %</td>`;
                appendData += `<td>${user.Unproductive} Hr</td>`;
                appendData += `<td>${user.unpro_percent} %</td>`;
                appendData += `<td>${user.Neutral} Hr</td>`;
                appendData += `<td>${user.idle_duration} Hr</td>`;
                appendData += `<td>${user.count}</td>`;
                appendData += `</tr>`;
            });
    
            if (allData.length === 0) {
                appendData = `<tr>
                    <td></td> <td></td> <td></td>
                    <td>${noData}</td> <td></td> <td></td>
                    <td></td> <td></td> <td></td>`;
                appendData += `</tr>`;
                TOTAL_COUNT_EMAILS = 0;
                paginationSetup();
                $("#showPageNumbers").html(
                    ` ${DATATABLE_LOCALIZE_MSG.showing} 0 ${DATATABLE_LOCALIZE_MSG.to} 0 
                    ${DATATABLE_LOCALIZE_MSG.of} 0`
                );
            }
    
            $('#desktop_control_table').dataTable().fnDraw();
            $('#desktop_control_table').DataTable().destroy();
            $('#reportTableBody').empty();
            $('#reportTableBody').append(appendData);
        })
        .catch(error => {
            console.error("Error fetching data:", error);
            TOTAL_COUNT_EMAILS = 0;
            paginationSetup();
            $('.pagination').jqPagination();
            $("#showPageNumbers").html(
                ` ${DATATABLE_LOCALIZE_MSG.showing} 0 ${DATATABLE_LOCALIZE_MSG.to} 0 
                ${DATATABLE_LOCALIZE_MSG.of} 0`
            );
        });
    
}

//To check and select all checkbox functionality
let checkBoxSelection = () => {
    $("#checkAll").click(function () {
        $(".checkSingle").prop('checked', $(this).prop('checked'));
        EmployeeID();
    });
    $(".checkSingle").click(function () {
        $('.checkSingle:checked').length == $('.checkSingle').length ? $("#checkAll").prop('checked', true) : $("#checkAll").prop('checked', false);
        EmployeeID();
    });
}

// To call the table on change of employee ID
let EmployeeID = () => {
    let SelectedId = [];
    $(".checkSingle:checked").each(function () {
        SelectedId.push($(this).val());
    });
    userId = SelectedId.toString();
    makeDatatableDefault();
    getProductivityTable("", userId, department, loc, from, to, SHOW_ENTRIES, 0, "", SORT_NAME, SORT_ORDER);
    PAGE_ID = 1;
    if (userId !== "All") {
        getReportRecord(userId, department, loc, from, to);
    }
};

//<!-- employee search -->
$(document).ready(function () {
    onKeyUpSearch();
    checkBoxSelection()
});

let onKeyUpSearch = () => {
    $("#myInput").on("keyup", function () {
        var value = $(this).val().toLowerCase();
        $("#myemp li").filter(function () {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
        });
    });
}
