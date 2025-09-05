var appendData = "";
var PAGE_ID = 1;
var SITE_TYPE = "";
var DEPARTMENT_ID = "";
var DEPART_A = [];
var USERS = [];
var d = [];
var DOMAIN = "";
var NAME_SEARCH = ""
let currentPageCount = $('#count').val();
var RANKING = "";

let FILTERED_RESPONSE = "";
let UPDATED_RESPONSE = [];  //to store the updated records

let SHOW_ENTRIES = "10";
let SORT_NAME = '';
let SORT_ORDER = '';
let SORTED_TAG_ID = '';
let PAGE_COUNT_CALL = false;
let ACTIVE_PAGE = "";
let STORE_GLOBALS;
let ENTRIES_DELETED, START_DATE, TO_DATE, LOCATION_URL_ID = null, DEPARTMENT_URL_ID = null, EMPLOYEE_URL_ID = null;

$('#searchByname').keypress(function (e) {
    var key = e.which;
    if (key == 13)  // the enter key code
    {
        nameSearch();
    }
});
paginationSetup();
if (TOTAL_COUNT_EMAILS == 0) $("#showPageNumbers").html(' '+DATATABLE_LOCALIZE_MSG.showing+ ' 0  to 0 of 0');
else {
    TOTAL_COUNT_EMAILS < SHOW_ENTRIES ? $("#showPageNumbers").html(' '+DATATABLE_LOCALIZE_MSG.showing+ ' ' + 1 + ' '+DATATABLE_LOCALIZE_MSG.to+' ' + TOTAL_COUNT_EMAILS + ' '+DATATABLE_LOCALIZE_MSG.of+' ' + TOTAL_COUNT_EMAILS)
        : $("#showPageNumbers").html(' '+DATATABLE_LOCALIZE_MSG.showing+ ' ' + 1 + ' '+DATATABLE_LOCALIZE_MSG.to+' ' + SHOW_ENTRIES + ' '+DATATABLE_LOCALIZE_MSG.of+' ' + TOTAL_COUNT_EMAILS);
}
let CalledUserFunction = (skip, searchtext, modal) => {
    // here you have to call your function which your using to the list like
    NAME_SEARCH = $('#SearchTextFieldInside').val();
    UPDATED_RESPONSE = [];
    getProductivity(SHOW_ENTRIES, skip, NAME_SEARCH, SORT_NAME, SORT_ORDER, modal);

};
$(function () {
    let start = moment().subtract(29, 'days');
    let end = moment();
    START_DATE = start.format('YYYY-MM-DD');
    TO_DATE = end.format('YYYY-MM-DD');
    $('#reportrange').daterangepicker({
        maxDate: end,
        startDate: start,
        endDate: end,
        ...dateRangeLocalization,
    }, cb);
    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    $('.select2').select2();
    $(".js-example-tokenizer").select2({
        tags: true,
        tokenSeparators: [",", " "]
    });
});


function cb(start, end) {
    $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
    START_DATE = start.format('YYYY-MM-DD');
    TO_DATE = end.format('YYYY-MM-DD');
    URLBasedTimeUsage(null);
}

function filterWeb(type, domain) {
    // alert(2);
    if (type !== '') {
        SITE_TYPE = type;

    }

    if (domain != '') {
        $('.btn').removeClass('active');
        $('#' + domain).addClass('active');
        DOMAIN = domain;
        domain === "All" ? ($("#UpdateBtn").css('display', 'inline'), $('#ExportBtn').css('display', 'inline')) : ($("#UpdateBtn").css("display", 'none')  , $('#ExportBtn').css('display', 'none'));
    }
    makeDatatableDefault();

    getProductivity(SHOW_ENTRIES, 0, "", SORT_NAME, SORT_ORDER);

}

function filterRanking() {
    RANKING = $('#rankingSelect').find(":selected").val();
    makeDatatableDefault();
    getProductivity(SHOW_ENTRIES, 0, "", SORT_NAME, SORT_ORDER);
}
let isAlwaysActive;
function filterActiveRanking() {
    makeDatatableDefault();
    getProductivity(SHOW_ENTRIES, 0, "", SORT_NAME, SORT_ORDER);
}





function nameSearch() {
    RANKING = null ;
    $('#rankingSelect').val('null');
    NAME_SEARCH = $('#searchByname').val();
    makeDatatableDefault();

    getProductivity(SHOW_ENTRIES, 0, "", SORT_NAME, SORT_ORDER);


}

function getProductivity(SHOW_ENTRIES, skip, searchtext, SORT_NAME, SORT_ORDER) {
    $.ajax({
        url: "/" + userType + "/productivity",
        method: 'post',
        data: {
            skip,
            site_type: SITE_TYPE,
            name: NAME_SEARCH,
            sortName: SORT_NAME,
            sortOrder: SORT_ORDER,
            limit: SHOW_ENTRIES,
           
        },
        beforeSend: function () {
                   },
        success: function (resp) {
            $("#SearchButton").attr('disabled',false);
            $('#showPageNumbers').show();
            $('#appendDataProRanking').show();
            $("#loader").css('display', 'none');
            appendData = "";
            var data = response.data;
            if (response.code === 200) {
                var ReportsData = data.data;
                if (ReportsData) {
                    ReportsData.forEach(function (attHistory) {
                       appendData += `<tr> `;
                     appendData += user.name.length <= 28 ? `<td> <i class="fas fa-globe"></i> ${user.name}</a></td>` : `<td title="${user.name}"> <i class="fas fa-globe"></i> ${user.name.substring(0, 28)}... </a></td>`;
                     appendData += `<td class="w-75"> <form action="/action_page.php"> <div class="form-check-inline text-success">
                                    <label class="form-check-label" for="productive">
                                    <input type="radio" class="form-check-input globalPro" onchange="changeProd('1-global-${app_id}','${pre_Request_option}');" name="radioPro" value="1-global-${app_id}" `;

                if (user.status == 1) {
                    appendData += ` checked`;
                }

                appendData += `>${PRODUCTIVITY_RULE_JS_MSG.productive} </label> </div>
                                <div class="form-check-inline text-warning">
                                <label class="form-check-label" for="neutral">
                                <input type="radio" class="form-check-input globalPro" onchange="changeProd('0-global-${app_id}','${pre_Request_option}');" name="radioPro" value="0-global-${app_id}" `;

                if (user.status == 0) {
                    appendData += ` checked`;
                }

                appendData += ` >${PRODUCTIVITY_RULE_JS_MSG.neutral} </label> </div>
                         <div class="form-check-inline text-danger">
                         <label class="form-check-label" for="unproductive">
                         <input type="radio" class="form-check-input globalPro" onchange="changeProd('2-global-${app_id}','${pre_Request_option}');" name="radioPro" value="2-global-${app_id}" `;


                if (user.status == 2) {
                    appendData += ` checked`;
                }

                appendData += `>${PRODUCTIVITY_RULE_JS_MSG.unproductive} </label> </div>
                            <div class="form-check-inline text-dark"> <a onclick="changeRadio('custom${app_id}');" class="collapsed table-link" data-toggle="collapse" href="#tel${app_id}">
                            <label class="form-check-label" for="custom">
                            <input type="radio" class="form-check-input" id="custom${app_id}" onclick="changeRadio('custom${app_id}');" name="radioPro" value="cust_dept"`;
                              appendData += `</form>  </tr>`;

                    });
                }

                $('#appendDataProRanking').empty();
                $('#appendDataProRanking').append(appendData);
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
                $('#appendDataProRanking').empty();
                $('#appendDataProRanking').append(appendData);
            }
        },
        error: function (error) {
            if (error.status === 403) {
                appendData += '<option disabled >' + DASHBOARD_JS_ERROR.permissionDenied + '</option>';
            } else {
                appendData += '<option disabled >' + DASHBOARD_JS_ERROR.reload + '</option>';
            }
            $('#employee').append(appendData);
        },
        error: function (err) {
            TOTAL_COUNT_EMAILS = 0;
            paginationSetup();
            $('.pagination').jqPagination();
            $("#showPageNumbers").html(' '+DATATABLE_LOCALIZE_MSG.showing+ ' ' + 0 + ' '+DATATABLE_LOCALIZE_MSG.to+' ' + 0 + ' '+DATATABLE_LOCALIZE_MSG.of+' ' + 0);
        }
    });
}





const tempArray = [];
const unique = [];

function changeRadio(val) {
    setTimeout(() => {
        $('#' + val).prop("checked", true);
    }, 0);
    customizeAfterOpenDropDown(val.split("custom")[1])
}


function changeProd(id,category ) {
     if($('#domainTime').is(':visible')) alwaysActive = ( valueOftime.split(':')[0] * 3600) + (valueOftime.split(':')[1] * 60);
    $.ajax({
        url: "/" + userType + "/productivity-update",
        method: 'post',
        data: {
           id,category
        },
        success: function (resp) {
            console.log(resp);
            if (resp.code !== 200) {
                Swal.fire({
                    icon: 'error',
                    title: resp.message,
                    showConfirmButton: true
                });
            } else {
                successSwal(resp.message);
            
            }
        },
        error: function (jqXHR) {
            if (jqXHR.status == 410) {
                $("#editModule").modal('hide');
                $("#UnaccessModal").empty();
                $("#UnaccessModal").css('display', 'block');
                $("#UnaccessModal").append('<div class="alert alert-danger text-center"><button type="button" class="close" data-dismiss="alert" >&times;</button><b  id="ErrorMsgForUnaccess"> ' + jqXHR.responseJSON.error + '</b></div>')
            } else errorSwal("Please, Reload and try again...")
        }
    });
}






//success swal alert message
function successSwal(msg) {
    Swal.fire({
        position: 'inherit',
        icon: 'success',
        title: msg,
        showConfirmButton: false,
        timer: 1500
    });
}

//error swal alert
function errorSwal(msg, text) {
    const message = msg != null ? msg : "Something went wrong..."
    const textMessage = text != null ? text : "";
    setTimeout(function () {
        Swal.fire({
            icon: 'error',
            title: message,
            text: textMessage,
            showConfirmButton: true,
        });
    }, 500)
}



