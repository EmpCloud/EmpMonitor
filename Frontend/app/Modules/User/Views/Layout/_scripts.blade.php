<script src="https://code.jquery.com/jquery-3.1.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jQuery-slimScroll/1.3.8/jquery.slimscroll.min.js"></script>
<script src="../assets/js/concept.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sockjs-client@1/dist/sockjs.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/aes.js"></script>
<script src="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/js/bootstrap4-toggle.min.js"></script>
<script src="../assets/js/incJSFile/_inputValidations.validator.js"></script>

<script>
     {{--    For temporary fix for Kongvison logo    --}}
    if (window.location.href.includes('kongvisions')) $('.logo-box img').css('height', '100%');
    let DASHBOARD_JS = JSON.parse('{{__('messages.js')}}'.replace(/&quot;/g, '"'));
    let DASHBOARD_JS_ERROR = JSON.parse('{{__('messages.dashboardJsError')}}'.replace(/&quot;/g, '"'));
    let DYNAMIC_LOGO = '../assets/images/logos/{{ md5($_SERVER['HTTP_HOST']) }}.png';
     var lblProductive="{{ __('messages.productive') }}";
     var lblUnProductive="{{ __('messages.unproductive') }}";
     var lblNeutral="{{ __('messages.neutral') }}";
     var lblIdle="{{ __('messages.idles') }}";
    let noData = '{{__('messages.Nodata')}}';
    let warning_Swal_msg = '{{__('messages.warning_Swal_msg')}}';
    let NotAbleToLoad = '{{__('messages.NotAbleToLoad')}}';
    let noValue = '{{__('messages.no')}}';
    let DEPARTMENT_MSG = '{{__('messages.department')}}';
    let LOCATION_MSG = '{{__('messages.Location')}}';
    let SELECT_MSG = '{{__('messages.select')}}';
     let SELECT_ALL = '{{__('messages.select_all')}}';
    let EMPLOYEE_MSG = '{{__('messages.employee')}}';
    let ROLE_MSG = '{{__('messages.role')}}';
    let ALL = '{{__('messages.all')}}';
    let EMPLOYEE_CODE_MSG = '{{__('messages.code')}}';
    let EMPLOYEE_NAME_MSG = '{{__('messages.name')}}';
    let EMPLOYEE_DETAILS_ERROR = JSON.parse('{{__('messages.employeeDetailsErrorMsg')}}'.replace(/&quot;/g, '"'));
    let EMPLOYEE_DETAILS_CONST = JSON.parse('{{__('messages.employeeDetailsConstMsg')}}'.replace(/&quot;/g, '"'));
    let DELETE_MSG_JS = JSON.parse('{{__('messages.deletJs')}}'.replace(/&quot;/g, '"'));
    let PRODUCTIVITY_ERROR_MSG = JSON.parse('{{__('messages.productvityErrorMsg')}}'.replace(/&quot;/g, '"'));
    let AUTO_EMAIL_SEE_ALL_LESS = JSON.parse('{{__('messages.allOrLess')}}'.replace(/&quot;/g, '"'));
    let TIMEZONE_LOCALE_MSG = '{{__('messages.timezone')}}';
    let APP_LOCALE_MSG = '{{__('messages.application')}}';
    let WEB_LOCALE_MSG = '{{__('messages.website')}}';
    let DATATABLE_LOCALIZE_MSG = JSON.parse('{{__('messages.dataTableMsg')}}'.replace(/&quot;/g, '"'));
    let PRODUCTIVITY_RULE_JS_MSG = JSON.parse('{{__('messages.jsProductiveMsg')}}'.replace(/&quot;/g, '"'));
    let productivityMessage = JSON.parse('{{__('messages.productivityMessage')}}'.replace(/&quot;/g, '"'));
    let downloadProductivityMsg = JSON.parse('{{__('messages.downloadProductiveMsg')}}'.replace(/&quot;/g, '"'));

    let AssignEmployee = '{{__('messages.AssignEmployee')}}';
    let productivityReport_Locale = '{{__('messages.productivityReport')}}';
    let NOTIFICATION_LOCALE = '{{__('messages.notification')}}';
    let EMP_MSG_LOCALE = JSON.parse('{{__('messages.empMsg')}}'.replace(/&quot;/g, '"'));

    let EMPLOYEE_FULL_DETAILS_ERROR = JSON.parse('{{__('messages.employeeFullDetailsErrorMsg')}}'.replace(/&quot;/g, '"'));
    let ALERTS_TOAST_JS = JSON.parse('{{__('messages.alerts_toast_JS')}}'.replace(/&quot;/g, '"'));
    let WEB_APP_MODULE = JSON.parse('{{__('messages.web_app_module')}}'.replace(/&quot;/g, '"'));
    let TASK_PAGE_JS = JSON.parse('{{__('messages.taskpage_JS')}}'.replace(/&quot;/g, '"'));
    let ACTIVITY_LOGS_JS = JSON.parse('{{__('messages.activity_logs_js')}}'.replace(/&quot;/g, '"'));
    let REPORT_GEN_MSG = JSON.parse('{{__('messages.reportGenMsg')}}'.replace(/&quot;/g, '"'));
    let REPORT_PDF_CSV = JSON.parse('{{__('messages.reportsPdfCsv')}}'.replace(/&quot;/g, '"'));
    let generateCsv = '{{__('messages.generateCsv')}}';
    let generatingCsv = '{{__('messages.generatingCsv')}}';
     let WEEK_DAYS_NAME = JSON.parse('<?php echo e(__('messages.days_name')); ?>'.replace(/&quot;/g, '"'));

     //    Alerts Rules and Messages Localization Objects
    let ALERT_TITLES_LOCALIZATION = JSON.parse('{{__('messages.alerts_titles_localization')}}'.replace(/&quot;/g, '"'));
    let EMP_ATTENDANCE_JS = JSON.parse('{{__('messages.emp_attendance_js')}}'.replace(/&quot;/g, '"'));
    let IDLE_TO_PRODUCTIVE = JSON.parse('{{__('messages.idleProductive')}}'.replace(/&quot;/g, '"'));
    let TABLE_HEADER_DOWNLOAD = JSON.parse('{{__('messages.Attendance_downoad_js')}}'.replace(/&quot;/g, '"'));
    let TABLE_HEADER_ATTENDANCE = JSON.parse('{{__('messages.Attendance_downoad_Updated_js')}}'.replace(/&quot;/g, '"'));
    let EXPORT_WITH_HEADER = JSON.parse('{{__('messages.exportExcelHeader')}}'.replace(/&quot;/g, '"'));
    let EXPORT_EXCEL_VALIDATION = JSON.parse('{{__('messages.exportExcelValidation')}}'.replace(/&quot;/g, '"'));
    let ATTENDANCEREPORT = JSON.parse('{{__('messages.ATTENDANCEREPORTTEXT')}}'.replace(/&quot;/g, '"'));
    let is_admin = "<?php if(Session::has(env('Admin')) && (new App\Modules\User\helper)->getHostName() == env('Admin')) echo true ?>";
   
    document.addEventListener('DOMContentLoaded', function() {
        if (typeof $.fn.dataTable !== 'undefined') {
            $.fn.dataTable.ext.errMode = 'none';
        }
    });
    
</script>


<script>  
    let userType = '<?php echo (new App\Modules\User\helper)->getHostName();?>'; 
    let isClient = '<?php echo \Illuminate\Support\Facades\Session::has("client") ?>';
    // These 2 for Localization
    let pagination = JSON.parse('{{__('messages.pagination')}}'.replace(/&quot;/g, '"'));
    let dateRangesLocalization = JSON.parse('{{__('messages.dateRanges')}}'.replace(/&quot;/g, '"'));

    let DATATABLE_LANG;
    $.getJSON("../assets/js/incJSFile/_dataTable_lang.json", function (data) {
        DATATABLE_LANG = "../assets/js/incJSFile/DataTableLanguages/" + data["en"];
    });

    let imagePath = '{{ env('API_HOST_V3') }}'; 
    let language_ar = JSON.parse('<?php echo json_encode(Session::get('locale'))?>');
   
    function getCookie(cname) {
        let name = cname + "=";
        let decodedCookie = decodeURIComponent(document.cookie);
        let ca = decodedCookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) == ' ') {
                c = c.substring(1);
            }
            if (c.indexOf(name) === 0) {
                return c.substring(name.length, c.length);
            }
        }
        return "";
    }

    $(document).ready(function () {
        $('[data-toggle="tooltip"]').tooltip({
            trigger: 'hover'
        });

        {{--added for new button animate gif--}}
        anime.timeline({loop: true})
            .add({
                targets: '.ml15 .word',
                scale: [0, 1],
                opacity: [0.5, 1],
                easing: "easeOutCirc",
                duration: 300,
                delay: 300
            }).add({
            targets: '.ml15',
            opacity: 0.9,
            duration: 300,
            easing: "easeOutExpo",
            delay: 100
        }); 
    });

    function clearStorage() {
        localStorage.setItem(userType + '_ALERT_COUNT', '0');
        localStorage.setItem('importantNote', '0');
        localStorage.setItem(userType + '_ALERT_LIST', JSON.stringify([]));
    }

    //    end here
    function convertSecToMMAndSS(s = 0) {
        let m = Math.floor(s / 60); //Get remaining minutes
        s -= m * 60;
        return (m < 10 ? '0' + m : m) + ":" + (s < 10 ? '0' + s : s); //zero padding on minutes and seconds
    }

</script> 
 
<!-- ************************** -->

<!-- FOR NEW SIDEBAR FEEDBACK MODAL  -->
<script>
    $(document).ready(function() {
    $(".question a").click(function() {
            $(this).toggleClass("active_qa");
        });
        $(".smiley").click(function() {
            $(this).toggleClass("test");
            $(this).siblings().removeClass("test");
        });
    });
</script>
<script>
    $(function() {
  // whenever we hover over a menu item that has a submenu
  $('#sidebar_menus li').on('mouseover', function() {
    let $menuItem = $(this),
        $submenuWrapper = $('> .sub-menu', $menuItem);
    // grab the menu item's position relative to its positioned parent
    let menuItemPos = $menuItem.position();
    // place the submenu in the correct position relevant to the menu item
    $submenuWrapper.css({
      top: menuItemPos.top,
      left: menuItemPos.left + Math.round($menuItem.outerWidth() * 0.75),
    });
  });
});
</script>
<script>
    $('#toggleNav').on('click' , function(){
        $('.mobile-nav').toggleClass('d-flex');
    })
    $('#toggleNavEmployee').on('click' , function(){
        $('.employee-mobile-nav').toggleClass('show--nav');
    })
    $('body').on({
    'mousewheel': function(e) {
        $('[role="calendar"]').hide();$('#date_joinCalender').blur();
    }
    })

    $('body').on({
    'mousewheel': function(e) {
        $('div.daterangepicker.ltr.show-ranges.opensright.show-calendar').hide();
    }
})
    // Auto-focus and auto-move between OTP fields
    document.querySelectorAll('.otp').forEach(function (input) {
        input.addEventListener('keyup', function (e) {
            let current = e.target;
            let next = current.dataset.next;
            let previous = current.dataset.previous;

            // Only allow numeric values
            if (/[^0-9]/.test(current.value)) {
                current.value = '';
                return;
            }

            // Move to the next field when filled
            if (current.value.length === 1 && next) {
                document.getElementById(next).focus();
            }

            // Backspace to move to previous field
            if (e.key === "Backspace" && previous) {
                document.getElementById(previous).focus();
            }
        });
    });

    // Enable submit button if all fields are filled
    const otpInputs = document.querySelectorAll('.otp');
    const verifyOtp = document.getElementById('verifyOtp');

    otpInputs.forEach(input => {
        input.addEventListener('input', checkOTPComplete);
    });
    try { NewTimesheetsTab.hidden = true; } catch (e) { }
try { MobileHistoryTab.hidden = true; } catch (e) { }
try { BreakHistoryTab.hidden = true; } catch (e) { }
try { DeleteTimeHistoryTab.hidden = true; } catch (e) { }

try { sidebar_menus.childNodes[13].hidden = true; } catch (e) { }
try { sidebar_menus.childNodes[7].hidden = true; } catch (e) { }
try { sidebar_menus.childNodes[23].hidden = true; } catch (e) { }
try { sidebar_menus.childNodes[25].hidden = true; } catch (e) { }
try { document.getElementsByClassName("tClaim-border-gray")[0].childNodes[7].hidden = true; } catch (e) { }
try { silh_download_csv.hidden = true; } catch (e) { }
try { document.querySelectorAll('a[title="Download Detection"]').forEach(el => el.hidden = true); } catch (e) {}
 

</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/2.0.2/anime.min.js"></script>
<script src="../assets/js/incJSFile/DataTableLanguages/_dateRangeLocalization.js"></script>
