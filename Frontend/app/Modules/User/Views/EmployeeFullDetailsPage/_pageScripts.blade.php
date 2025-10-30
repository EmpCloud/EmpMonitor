<script>
    let envAdminIds = [];
    let adminId = 1;
    let envRemainingTime = Number('{{env('REMAINING_SECONDS')}}');
    let envApiHost = '{{env('API_HOST_V3')}}';
    let ps="{{ __('messages.positive') }} {{ __('messages.sentence') }}";
    let ns="{{ __('messages.negative') }} {{ __('messages.sentence') }}";
    let ow="{{ __('messages.offensive') }} {{  trans_choice('messages.word', 10) }}";
    var lblProductive="{{ __('messages.productive') }}";
    var lblUnProductive="{{ __('messages.unproductive') }}";
    var lblNeutral="{{ __('messages.neutral') }}";
    var work_time="{{ __('messages.work_time') }}";
    var headerApplication="{{ __('messages.application') }}";
    var headerWebsite="{{ __('messages.website') }}";
    var lblAthuentic="{{ __('messages.authentic') }}";
    var lblUnreliable="{{ __('messages.unreliable') }}";
    var lblOther="{{ __('messages.other') }}";
    var lblPositive="{{ __('messages.positive') }}";
    var lblNegative="{{ __('messages.negative') }}";
    var lblNegative="{{ __('messages.negative') }}";
    var lblNeutral="{{ __('messages.neutral') }}";
    var lblNormal="{{ __('messages.normal') }}";
    var lblOffensive="{{ __('messages.offensive') }}";
</script>


<script src="../assets/js/incJSFile/JqueryDatatablesCommon.js"></script>
<script src="../assets/js/JqueryPagination/jquery.jqpagination.js"></script>
<script src="../assets/js/incJSFile/SuccessAndErrorHandlers/_swalHandlers.js"></script>
<script src="../assets/js/incJSFile/CommonEmployeeDetailsCode.js"></script>
<script src="../assets/js/incJSFile/EmployeeFullDetails.js"></script>
<script src="../assets/js/final-timezone.js"></script>
<script src="../assets/js/incJSFile/EmployeeDetailJs/commonFunction_Forms.js"></script>
<script src="../assets/js/incJSFile/EmployeeDetailJs/EmployeeLoginFullDetails.js"></script>
<script src="../assets/plugins/vue-apexcharts/vue.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/vue-apexcharts"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.13.6/underscore-min.js" integrity="sha512-2V49R8ndaagCOnwmj8QnbT1Gz/rie17UouD9Re5WxbzRVUGoftCu5IuqqtAM9+UC3fwfHCSJR1hkzNQh/2wdtg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
