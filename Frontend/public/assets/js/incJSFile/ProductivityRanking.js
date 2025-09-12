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
if (TOTAL_COUNT_EMAILS == 0) $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' 0  to 0 of 0');
else {
    TOTAL_COUNT_EMAILS < SHOW_ENTRIES ? $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' ' + 1 + ' ' + DATATABLE_LOCALIZE_MSG.to + ' ' + TOTAL_COUNT_EMAILS + ' ' + DATATABLE_LOCALIZE_MSG.of + ' ' + TOTAL_COUNT_EMAILS)
        : $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' ' + 1 + ' ' + DATATABLE_LOCALIZE_MSG.to + ' ' + SHOW_ENTRIES + ' ' + DATATABLE_LOCALIZE_MSG.of + ' ' + TOTAL_COUNT_EMAILS);
}
let CalledUserFunction = (skip, searchtext, modal) => {
    // here you have to call your function which your using to the list like
    UPDATED_RESPONSE = [];
    getProductivity(SHOW_ENTRIES, skip);

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
    if (type !== '') {
        SITE_TYPE = type;

    }
    if (domain != '') {
        $('.btn').removeClass('active');
        $('#' + domain).addClass('active');
        DOMAIN = domain;
        domain === "All" ? ($("#UpdateBtn").css('display', 'inline'), $('#ExportBtn').css('display', 'inline')) : ($("#UpdateBtn").css("display", 'none'), $('#ExportBtn').css('display', 'none'));
    }
    makeDatatableDefault();
    getProductivity(SHOW_ENTRIES, 0);
}

function filterRanking() {
    RANKING = $('#rankingSelect').find(":selected").val();
    makeDatatableDefault();
    getProductivity(SHOW_ENTRIES, 0);
}

let isAlwaysActive;
function filterActiveRanking() {
    makeDatatableDefault();
    getProductivity(SHOW_ENTRIES, 0);
}

function nameSearch() {
    RANKING = null;
    $('#rankingSelect').val('null');
    NAME_SEARCH = $('#searchByname').val();
    makeDatatableDefault();
    getProductivity(SHOW_ENTRIES, 0);
}

function getProductivity(SHOW_ENTRIES, skip) {
    $.ajax({
        url: "/" + userType + "/productivity",
        method: 'post',
        data: {
            skip,
            limit: SHOW_ENTRIES,

        },
        beforeSend: function () {
        },
        success: function (resp) {
            $("#SearchButton").attr('disabled', false);
            $('#showPageNumbers').show();
            $('#appendDataProRanking').show();
            $("#loader").css('display', 'none');
            appendData = "";
            if (resp.code === 200) {
                let data = resp.data;
                if (data) {
                    data.forEach(function (user) {
                        appendData += `<tr> `;
                        appendData += user.name.length <= 28
                            ? `<td><i class="fas fa-globe"></i> ${user.name}</td>`
                            : `<td title="${user.name}"><i class="fas fa-globe"></i> ${user.name.substring(0, 28)}...</td>`;

                        appendData += `<td class="w-75">
        <form action="/action_page.php">
            <div class="form-check-inline text-success">
                <label class="form-check-label">
                    <input type="radio" class="form-check-input globalPro" onchange="changeProd('${user._id}',1);" 
                        name="radioPro-${user._id}" value="1-global-${user._id}" ${user.category == 1 ? 'checked' : ''}>
                    ${PRODUCTIVITY_RULE_JS_MSG.productive}
                </label>
            </div>

            <div class="form-check-inline text-warning">
                <label class="form-check-label">
                    <input type="radio" class="form-check-input globalPro" onchange="changeProd('${user._id}',0);" 
                        name="radioPro-${user._id}" value="0-global-${user._id}" ${user.category == 0 ? 'checked' : ''}>
                    ${PRODUCTIVITY_RULE_JS_MSG.neutral}
                </label>
            </div>

            <div class="form-check-inline text-danger">
                <label class="form-check-label">
                    <input type="radio" class="form-check-input globalPro" onchange="changeProd('${user._id}',2);" 
                        name="radioPro-${user._id}" value="2-global-${user._id}" ${user.category == 2 ? 'checked' : ''}>
                    ${PRODUCTIVITY_RULE_JS_MSG.unproductive}
                </label>
            </div>
        </form>
    </td></tr>`;
                    });

                }

                $('#appendDataProRanking').empty();
                $('#appendDataProRanking').append(appendData);
                if (PAGE_COUNT_CALL === true) {
                    TOTAL_COUNT_EMAILS = resp.data.totalCount;
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
                message = resp.msg;
                appendData += '<tr><td></td><td></td><td> ' + resp.msg + ' </td></tr>';
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
            $("#showPageNumbers").html(' ' + DATATABLE_LOCALIZE_MSG.showing + ' ' + 0 + ' ' + DATATABLE_LOCALIZE_MSG.to + ' ' + 0 + ' ' + DATATABLE_LOCALIZE_MSG.of + ' ' + 0);
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


function changeProd(id, category) {
    $.ajax({
        url: "/" + userType + "/productivity-update",
        method: 'post',
        data: {
            id, category
        },
        success: function (resp) {
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



