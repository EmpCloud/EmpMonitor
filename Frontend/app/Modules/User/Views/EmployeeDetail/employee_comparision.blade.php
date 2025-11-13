@extends('User::Layout._layout')

@section('title')
    <title>Productivity Comparison </title>
@endsection
@section('page-style')
    <style>
        .loader {
            right: 50%
        }

        .loaderIcon {
            border: 4px solid #f3f3f3;
            border-radius: 60%;
            border-top: 4px solid #3498db;
            border-bottom: 4px solid #3498db;
            width: 30px;
            height: 30px;
            -webkit-animation: spin 1s linear infinite;
            animation: spin 1s linear infinite;
        }
        .LoaderIconTale {
            border: 4px solid #f3f3f3;
            border-radius: 60%;
            border-top: 4px solid #3498db;
            border-bottom: 4px solid #3498db;
            width: 30px;
            height: 30px;
            -webkit-animation: spin 1s linear infinite;
            animation: spin 1s linear infinite;
            margin-right: -305px;
        }

        .DTFC_LeftBodyLiner {
            overflow-x: hidden;
        }
        @media screen and (max-width: 639px){
        #exportReport{
         position: static !important;
         margin-bottom: 10px;
        }

        }
        @media screen and (max-width: 728px){
            .btn-group.btn-block button[type=button] {
        height: auto !important;
         }
        }
    </style>
@endsection
@section('content')
    <div class="page-inner no-page-title">
        <div id="main-wrapper">
            <div class="content-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style-1">
                        <li class="breadcrumb-item"><a href="employee-details" style="color: #0686d8;font-weight: 500;">
                                {{ __('messages.home') }}</a></li>
                        <li class="breadcrumb-item" aria-current="page">
                          Productivity Comparison
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="row mb-3">
                <div class="col-md-4">
                    <h1 class="page-title mb-0">Productivity Comparison</h1>
                </div>
                <div class="col-sm">
                    <div id="LoaderIcon" style="display: none" class="loaderIcon float-right"></div>
                </div>
            </div>
            <div id="UnaccessModal" style="display: none">
                <div class="alert alert-danger ">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>{{ __('messages.note') }} : </strong>
                    <p id="ErrorMsgForUnaccess"> Inicates a successful or positive action.</p>
                </div>
            </div>
            <div class="row">

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label><b>{{ __('messages.employee') }}:- </b></label>
                                        @if($EmployeeData['code']=='200')
                                            <select class="form-control select2" id="EmployeeData" onchange="empList()">
                                                <option value="0" id="">{{ __('messages.seeAllEmployee') }}</option>
                                                @foreach($EmployeeData['data']['data'] as $emp)
                                                    <option
                                                        id="{{$emp['id']}}" value="{{$emp['id']}}">{{$emp['first_name']}} {{ $emp['last_name']}}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <select class="form-control">
                                                <option selected disabled>{{$EmployeeData['msg']}}</option>
                                            </select>
                                        @endif

                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label><b>{{ __('messages.select') }} {{ __('messages.date') }}:- </b></label>
                                    <div class="form-control" id="dateRange" style="cursor: pointer">
                                        <i class="fa fa-calendar"></i>&nbsp; <span></span>
                                        <i class="fa fa-caret-down"></i>
                                        <span id="from" hidden></span>
                                        <span id="to" hidden></span>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label><b>{{ __('messages.select') }} {{ __('messages.date') }}:- </b></label>
                                    <div class="form-control" id="dateRange1" style="cursor: pointer">
                                        <i class="fa fa-calendar"></i>&nbsp; <span></span>
                                        <i class="fa fa-caret-down"></i>
                                        <span id="from1" hidden></span>
                                        <span id="to1" hidden></span>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label><b>{{ __('messages.employee') }}:- </b></label>
                                        @if($EmployeeData['code']=='200')
                                            <select class="form-control select2" id="EmployeeData1" onchange="empList1()">
                                                <option value="0" id="">{{ __('messages.seeAllEmployee') }}</option>
                                                @foreach($EmployeeData['data']['data'] as $emp)
                                                    <option
                                                        id="{{$emp['id']}}" value="{{$emp['id']}}">{{$emp['first_name']}} {{ $emp['last_name']}}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <select class="form-control">
                                                <option selected disabled>{{$EmployeeData['msg']}}</option>
                                            </select>
                                        @endif

                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card-deck">

                        <div class="card">
                            <div class="card-body">

                                <div class="row empFullDetail-automationCode" style="margin: 25px!important;">
                                    <div class="col">
                                        <div class="card total_time">
                                            <div class="card-body">{{ __('messages.officeTime') }}
                                                <h3 id="totalTime">00:00:00 hr</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card active_time">
                                            <div class="card-body">{{ __('messages.activeTime') }}
                                                <h3 id="activeTime">00:00:00 hr</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card productive_time">
                                            <div class="card-body">
                                                    {{ __('messages.prodTime') }}
                                                    <h3 id="productiveTime">00:00:00 hr</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col" style="margin-top: 25px!important;">
                                        <div class="card non_productive_time">
                                            <div class="card-body">
                                                    {{ __('messages.unProdTime') }}
                                                    <h3 id="nonProductiveTime">00:00:00 hr</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col" style="margin-top: 25px!important;">
                                        <div class="card neutral_time">
                                            <div class="card-body">
                                                    {{ __('messages.neutralTime') }}
                                                    <h3 id="neutralTime">00:00:00 hr</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col" style="margin-top: 25px!important;">
                                        <div class="card productivity">
                                            <div class="card-body">{{ __('messages.productivity') }}
                                                <h3 id="productivity">00:00:00 hr</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card">

                            <div class="card-body">
                                <div class="row empFullDetail-automationCode" style="margin: 25px!important;">
                                    <div class="col">
                                        <div class="card total_time">
                                            <div class="card-body">{{ __('messages.officeTime') }}
                                                <h3 id="totalTime1">00:00:00 hr</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card active_time">
                                            <div class="card-body">{{ __('messages.activeTime') }}
                                                <h3 id="activeTime1">00:00:00 hr</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="card productive_time">
                                            <div class="card-body">
                                                {{ __('messages.prodTime') }}
                                                <h3 id="productiveTime1">00:00:00 hr</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col" style="margin-top: 25px!important;">
                                        <div class="card non_productive_time">
                                            <div class="card-body">
                                                {{ __('messages.unProdTime') }}
                                                <h3 id="nonProductiveTime1">00:00:00 hr</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col" style="margin-top: 25px!important;">
                                        <div class="card neutral_time">
                                            <div class="card-body">
                                                {{ __('messages.neutralTime') }}
                                                <h3 id="neutralTime1">00:00:00 hr</h3>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col" style="margin-top: 25px!important;">
                                        <div class="card productivity">
                                            <div class="card-body">{{ __('messages.productivity') }}
                                                <h3 id="productivity1">00:00:00 hr</h3>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                </div>
            </div>

    </div>

@endsection
@section('post-load-scripts')

    <!-- Javascripts -->
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>

    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="../assets/js/incJSFile/_timeConvertions.js"></script>
@endsection

@section('extra-style-links')
    <link href="../assets/plugins/bootstrap/css/loader.css" type="text/css" rel="stylesheet"/>
@endsection

@section('page-scripts')

    <script src="../assets/js/incJSFile/_dataFiltration.js"></script>
    <script src="../assets/js/incJSFile/SuccessAndErrorHandlers/_swalHandlers.js" type="text/javascript"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
                <script src="http://html2canvas.hertzen.com/dist/html2canvas.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- daterange -->
    <script type="text/javascript">
        let empId1, empId2;
        $('#EmployeeData').select2({
            placeholder: 'Search employee',
            allowClear: false
        });
        $('#EmployeeData1').select2({
            placeholder: 'Search employee',
            allowClear: false
        });

        function empList() {
            const selectedValue = $('#EmployeeData').val();
            const fromDate = $('#from').val();
            const toDate = $('#to').val();
            getProductivity(selectedValue, fromDate, toDate);
            empId1 = selectedValue;
            $('#EmployeeData1').val(selectedValue).trigger('change');
        }

        $('#EmployeeData1').select2('destroy').select2({
            placeholder: 'Search employee',
            allowClear: false
        });

        function empList1() {
            $('#EmployeeData1').select2('destroy').select2({
                placeholder: 'Search employee',
                allowClear: false
            });
            $('#EmployeeData1').select2({
                placeholder: 'Search employee',
                allowClear: false
            });

            const selectedValue = $('#EmployeeData1').val();
            const fromDate = $('#from1').val();
            const toDate = $('#to1').val();
            getProductivity1(selectedValue, fromDate, toDate);
            empId2 = selectedValue;
        }



        $(function () {

            let start = moment().subtract(0, "days");
            let end = moment().subtract(0, 'days');
            function cb(start, end) {
                $("#dateRange span").html(
                    start.format("YYYY-MM-DD") + " - " + end.format("YYYY-MM-DD")
                );
                $('#from').val(start.format("YYYY-MM-DD"));
                $('#to').val(end.format("YYYY-MM-DD"));
            }

            $("#dateRange").daterangepicker({
                maxDate: end,
                startDate: start,
                endDate: end,
                ...dateRangeLocalization,
            }, cb);
            $('#dateRange').on('apply.daterangepicker', function (ev, picker) {
                const fromDate = picker.startDate.format("YYYY-MM-DD");
                const toDate = picker.endDate.format("YYYY-MM-DD");
                const empId = $('#EmployeeData').val();
                getProductivity(empId1, fromDate, toDate);
            });


            cb(start, end);
        });
        $(function () {

            let start = moment().subtract(1, "days");
            let end = moment().subtract(1, 'days');
            function cb(start, end) {
                $("#dateRange1 span").html(
                    start.format("YYYY-MM-DD") + " - " + end.format("YYYY-MM-DD")
                );
                $('#from1').val(start.format("YYYY-MM-DD"));
                $('#to1').val(end.format("YYYY-MM-DD"));
            }

            $("#dateRange1").daterangepicker({
                maxDate: end,
                startDate: start,
                endDate: end,
                ...dateRangeLocalization,
            }, cb);
            $('#dateRange1').on('apply.daterangepicker', function (ev, picker) {
                const fromDate = picker.startDate.format("YYYY-MM-DD");
                const toDate = picker.endDate.format("YYYY-MM-DD");
                const empId = $('#EmployeeData1').val();
                getProductivity1(empId2, fromDate, toDate);
            });

            cb(start, end);
        });
        // document.getElementById("EmployeeData").addEventListener("change", function () {
        //     const selectedValue = this.value;
        //     const dropdown2 = document.getElementById("EmployeeData1");
        //     dropdown2.value = selectedValue;
        // });


        function getProductivity(user_id, startDate, endDate) {
            $('#dateRange').show();
            $.ajax({
                type: "post",
                url: "/" + userType + '/get-productivity',
                data: {data: `employee_id=${user_id}&startDate=${startDate}&endDate=${endDate}`},
                beforeSend: function () {
                },
                success: function (response) {
                    return productivityData(response);
                },
                error: function (jqXHR) {
                    if(jqXHR.status == 410)  {
                        $('#Productivity').empty();
                        $('#Productivity').append('<p  style="color: red; text-align: center; font-size: 150%; width: 100%; height: 40% " class="mt-5"><b>'+jqXHR.responseJSON.error+' </b></p>');
                        $("#ErrorMsgForUnaccess").html(jqXHR.responseJSON.error)
                    } else {
                        return errorHandler(EMPLOYEE_FULL_DETAILS_ERROR.somethingWrong);
                    }
                    PRODUCTIVITY = false;
                }
            });
        }

        function productivityData(response) {
            if (response.code === 200) {
                if (response.data && response.data.length > 0) {
                    productDataChart(response);
                    PRODUCTIVITY = true;
                } else {
                    emptyProductivePage();
                    $('#activityChart').append('<p  style="color: red; text-align: center; font-size: 150%; width: 100%; height: 10% " ><b>' + EMPLOYEE_FULL_DETAILS_ERROR.productivityNotFound + '</b></p>');
                }
            } else if (response.code === 400) {
                emptyProductivePage();
                $('#activityChart').append('<p  style="color: red; text-align: center; font-size: 150%; width: 100%; height: 10% " ><b>' + EMPLOYEE_FULL_DETAILS_ERROR.productivityNotFound + '</b></p>');
            } else if (response.code === 500) {
                emptyProductivePage();
                $('#activityChart').append("<p  style='color: red; text-align: center; font-size: 150%; width: 100%; height: 10% ' ><b>" + EMPLOYEE_FULL_DETAILS_ERROR.errWhileFetching + " <br/>" +
                    " <a href='#'  onclick=" + getProductivity($('#userId').attr('value'), $('#from').val(), $('#to').val()) + ">" + EMPLOYEE_FULL_DETAILS_ERROR.reloadSection + " </a> </b></p>");
                PRODUCTIVITY = false;
            } else {
                PRODUCTIVITY = false;
                return errorHandler(response.msg);
            }
        }
        function emptyProductivePage() {
            $('#activityChart').empty();
            $('#totalTime').empty();
            $('#activeTime').empty();
            $('#productiveTime').empty();
            $('#nonProductiveTime').empty();
            $('#neutralTime').empty();
            $('#productivity').empty();
            $('#mobileTime').empty();
        }
        function productDataChart(response) {
            let totalProductivity = 0;
            let totalProductivityPercentage = 0;
            let totalNonProductivity = 0;
            let neutral = 0;
            let officeTime = 0;
            let activeTime = 0;
            let totalIdlePercentage = 0;
            response.data.map(productivity => {
                totalProductivity += productivity.productive_duration;
                officeTime += productivity.office_time;
                activeTime += productivity.computer_activities_time;
                totalNonProductivity += productivity.non_productive_duration;
                neutral += productivity.neutral_duration;
                totalProductivityPercentage += productivity.productivity;
                totalIdlePercentage += productivity.idle_duration;
            });


            let days = moment($('#to').val()).diff(moment(moment($('#from').val())), 'days') + 1;
            //set 4 boxes
            $('#totalTime,#activeTime,#productiveTime,#nonProductiveTime,#neutralTime,#productivity,#mobileTime').empty();
            $('#totalTime').append(`${String(Math.floor(officeTime / 3600)).padStart(2, '0')}${moment().startOf('day').seconds(officeTime).format(':mm:ss')} hr`).attr('title', convertSecToMMAndSS(officeTime));
            $('#activeTime').append(`${String(Math.floor(activeTime / 3600)).padStart(2, '0')}${moment().startOf('day').seconds(activeTime).format(':mm:ss')} hr`).attr('title', convertSecToMMAndSS(activeTime));
            $('#productiveTime').append(`${String(Math.floor(totalProductivity / 3600)).padStart(2, '0')}${moment().startOf('day').seconds(totalProductivity).format(':mm:ss')} hr`).attr('title', convertSecToMMAndSS(totalProductivity));
            $('#nonProductiveTime').append(`${String(Math.floor(totalNonProductivity / 3600)).padStart(2, '0')}${moment().startOf('day').seconds(totalNonProductivity).format(':mm:ss')} hr`).attr('title', convertSecToMMAndSS(totalNonProductivity));
            $('#neutralTime').append(`${String(Math.floor(neutral / 3600)).padStart(2, '0')}${moment().startOf('day').seconds(neutral).format(':mm:ss')} hr`).attr('title', convertSecToMMAndSS(neutral));
            $('#productivity').append(`${response.poductivity_percentage.total_productivity.toFixed(2)} %`);

        }
        function getProductivity1(user_id, startDate, endDate) {
            $('#dateRange1').show();
            $.ajax({
                type: "post",
                url: "/" + userType + '/get-productivity',
                data: {data: `employee_id=${user_id}&startDate=${startDate}&endDate=${endDate}`},
                beforeSend: function () {
                },
                success: function (response) {
                    return productivityData1(response);
                },
                error: function (jqXHR) {
                    if(jqXHR.status == 410)  {
                        $('#Productivity').empty();
                        $('#Productivity').append('<p  style="color: red; text-align: center; font-size: 150%; width: 100%; height: 40% " class="mt-5"><b>'+jqXHR.responseJSON.error+' </b></p>');
                        $("#ErrorMsgForUnaccess").html(jqXHR.responseJSON.error)
                    } else {
                        return errorHandler(EMPLOYEE_FULL_DETAILS_ERROR.somethingWrong);
                    }
                    PRODUCTIVITY = false;
                }
            });
        }

        function productivityData1(response) {
            if (response.code === 200) {
                if (response.data && response.data.length > 0) {
                    productDataChart1(response);
                    PRODUCTIVITY = true;
                } else {
                    emptyProductivePage1();
                }
            }
        }
        function emptyProductivePage1() {
            $('#activityChart1').empty();
            $('#totalTime1').empty();
            $('#activeTime1').empty();
            $('#productiveTime1').empty();
            $('#nonProductiveTime1').empty();
            $('#neutralTime1').empty();
            $('#productivity1').empty();
            $('#mobileTime1').empty();
        }
        function productDataChart1(response) {
            let totalProductivity = 0;
            let totalProductivityPercentage = 0;
            let totalNonProductivity = 0;
            let neutral = 0;
            let officeTime = 0;
            let activeTime = 0;
            let totalIdlePercentage = 0;
            response.data.map(productivity => {
                totalProductivity += productivity.productive_duration;
                officeTime += productivity.office_time;
                activeTime += productivity.computer_activities_time;
                totalNonProductivity += productivity.non_productive_duration;
                neutral += productivity.neutral_duration;
                totalProductivityPercentage += productivity.productivity;
                totalIdlePercentage += productivity.idle_duration;
            });


            let days = moment($('#to').val()).diff(moment(moment($('#from').val())), 'days') + 1;
            //set 4 boxes
            $('#totalTime1,#activeTime1,#productiveTime1,#nonProductiveTime1,#neutralTime1,#productivity1,#mobileTime1').empty();
            $('#totalTime1').append(`${String(Math.floor(officeTime / 3600)).padStart(2, '0')}${moment().startOf('day').seconds(officeTime).format(':mm:ss')} hr`).attr('title', convertSecToMMAndSS(officeTime));
            $('#activeTime1').append(`${String(Math.floor(activeTime / 3600)).padStart(2, '0')}${moment().startOf('day').seconds(activeTime).format(':mm:ss')} hr`).attr('title', convertSecToMMAndSS(activeTime));
            $('#productiveTime1').append(`${String(Math.floor(totalProductivity / 3600)).padStart(2, '0')}${moment().startOf('day').seconds(totalProductivity).format(':mm:ss')} hr`).attr('title', convertSecToMMAndSS(totalProductivity));
            $('#nonProductiveTime1').append(`${String(Math.floor(totalNonProductivity / 3600)).padStart(2, '0')}${moment().startOf('day').seconds(totalNonProductivity).format(':mm:ss')} hr`).attr('title', convertSecToMMAndSS(totalNonProductivity));
            $('#neutralTime1').append(`${String(Math.floor(neutral / 3600)).padStart(2, '0')}${moment().startOf('day').seconds(neutral).format(':mm:ss')} hr`).attr('title', convertSecToMMAndSS(neutral));
            $('#productivity1').append(`${response.poductivity_percentage.total_productivity.toFixed(2)} %`);

        }
    </script>
@endsection

