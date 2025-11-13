@extends('User::Layout._layout')
@section('title')
<title>
    @if ((new App\Modules\User\helper())->checkHost())
    {{ env('WEBSITE_TITLE') }} |
    @endif {{ __('messages.timesheets') }}
</title>
@endsection

@section('extra-style-links')
<link href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css" rel="stylesheet" />
<link href="../assets/css/jqpagination.css" rel="stylesheet">
<link href="../assets/plugins/bootstrap/css/loader.css" rel="stylesheet" />
@endsection

@section('page-style')
    @include('User::Layout._modernStyles')
    <style>
    /* Page Specific Styles Only */
    .ad_tab th {
        white-space: nowrap;
    }
    
    .ad_tab tbody tr {
        transition: all 0.2s ease;
    }
    
    .ad_tab tbody tr:hover {
        background-color: #f8f9fa;
        transform: scale(1.001);
    }
    
    .ad_tab tbody td {
        padding: 0.85rem 0.75rem;
        vertical-align: middle;
        border-color: #f0f0f0;
        font-size: 13px;
    }
    
    /* Icon Button */
    .add-tab-icon {
        cursor: pointer;
        font-size: 18px;
        padding: 10px;
        border-radius: 8px;
        transition: all 0.3s ease;
    }
    
    .add-tab-icon:hover {
        background-color: #f8f9fa;
        transform: scale(1.1);
    }
    
    /* Dropdown Menu in Table */
    .dropdown-menu li {
        padding: 0.5rem 1rem;
        transition: all 0.2s ease;
    }
    
    .dropdown-menu li:hover {
        background-color: #f8f9fa;
    }

    #cover-spin {
        position: fixed;
        width: 100%;
        left: 0;
        right: 0;
        top: 0;
        bottom: 0;
        background-color: rgba(255, 255, 255, 0.7);
        z-index: 9999;
        display: none;
    }

    @-webkit-keyframes spin {
        from {
            -webkit-transform: rotate(0deg);
        }

        to {
            -webkit-transform: rotate(360deg);
        }
    }

    @keyframes spin {
        from {
            transform: rotate(0deg);
        }

        to {
            transform: rotate(360deg);
        }
    }

    #cover-spin::after {
        content: '';
        display: block;
        position: absolute;
        left: 48%;
        top: 40%;
        width: 40px;
        height: 40px;
        border-style: solid;
        border-color: #667eea;
        border-top-color: transparent;
        border-width: 4px;
        border-radius: 50%;
        -webkit-animation: spin .8s linear infinite;
        animation: spin .8s linear infinite;
    }
    
    /* Info Icon */
    .toDateLimitInfo {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .toDateLimitInfo:hover {
        transform: scale(1.2);
    }
</style>
@endsection

@section('content')
<form id="myform" name="myform" autocomplete="off">
    @csrf
    <div class="page-inner no-page-title attan_history">
        <div id="main-wrapper">
            <div class="content-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style-1" >
                        <li class="breadcrumb-item" ><a href="employee-details" style="color: #0686d8;font-weight: 500; ">
                                {{ __('messages.home') }}</a></li>
                        <li class="breadcrumb-item" aria-current="page">
                            {{ __('messages.timesheets') }}
                        </li>
                    </ol>

                </nav>
            </div>
            <div class="row">
                <div class="col-md-8">
                    <h1 class="page-title"> {{ __('messages.timesheets') }}</h1>
                </div>
            </div>

            <div id="UnaccessModal" style="display: none">
                <div class="alert alert-danger ">
                    <button type="button" class="close" data-dismiss="alert">&times;</button>
                    <strong>Note : </strong>
                    <p id="ErrorMsgForUnaccess"> Indicates a successful or positive action.</p>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body bg-light ">
                            <div class="row">

                                <div class="col-sm col-md-4">
                                    <input type="text" style="display:none" id="hiddenVal" />
                                    <div class="form-group">
                                        <label class="font-weight-bold" for="location_option">
                                            {{ __('messages.employee') }}</label>
                                        <select id="employee" class="form-control select2" onchange="allEmployee()">

                                            <option selected class="active-result" value="0">
                                                {{ __('messages.all') }}
                                            </option>
                                            @if (isset($employeesList['employees']) && count($employeesList['employees']) > 0)
                                            @foreach ($employeesList['employees'] as $empl)
                                            <option class="active-result" value="{{ $empl['id'] }}">
                                                {{ $empl['first_name'] }} {{ $empl['last_name'] }}
                                            </option>
                                            @endforeach
                                            @else
                                            <option disabled>No employees found.</option>
                                            @endif
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label style="font-weight: 700;" for="location_option">
                                        {{ __('messages.date_ranges') }} :<i
                                            class="fa fa-info-circle text-primary ml-2 toDateLimitInfo"
                                            data-toggle="tooltip" title="{{ __('messages.toDateLimitInfo') }}"></i>
                                    </label>
                                    <div class="form-control" id="reportranges" style="cursor: pointer;">
                                        <i class="fa fa-calendar"></i>&nbsp; <span class="small"></span>
                                        <i class="fa fa-caret-down"></i>
                                        <input type="hidden" name="to" id="to" value="">
                                        <input type="hidden" name="from" id="from" value="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" id="pdf_loader" style="display: none;float: right">
                    <div class="loader"></div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="align-self-center">
                                    <p class="mb-0 ml-3"> {{ __('messages.show') }} <select class=""
                                            id="ShowEntriesList">
                                            <option id="10" selected>10</option>
                                            <option id="25">25</option>
                                            <option id="50">50</option>
                                            <option id="100">100</option>
                                            <option id="200">200</option>
                                        </select> {{ __('messages.entries') }}
                                    </p>
                                </div>
                                <div class="col-sm">&nbsp;</div>
                                <div class="col-md-3 input-group">
                                    <input type="text" class="form-control"
                                        placeholder=" {{ __('messages.search') }} ..." value=""
                                        id="SearchTextField" onkeypress="return runScript(event)">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" id="SearchButton"
                                            onclick="SearchText()">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="col-12 text-right">
                                    <p class="mb-0" style="color: red" id="SearchErrorMsg"></p>
                                </div>
                            </div>
                            <div class="">
                                <i onclick="openDropDownModal()" class="fas fa-columns add-tab-icon"
                                    data-toggle="dropdown"></i>
                                <ul class="dropdown-menu list-data pt-0" id="mytimesheetdata">
                                    <li><input class="mr-2 selectCheckbox" checked disabled type="checkbox"
                                            value="" /> {{ __('messages.name') }} </li>
                                    <!-- <li><input class="mr-2 selectCheckbox" type="checkbox" checked="checked"
                                                value="Email" />{{ __('messages.Email id') }} </li>  -->
                                    <!-- <li><input class="mr-2 selectCheckbox" type="checkbox" checked="checked"
                                                value="EmpCode" />{{ __('messages.empCode') }}</li> -->
                                    <li><input class="mr-2 selectCheckbox" type="checkbox" checked="checked"
                                            value="ClockIn" />{{ __('messages.clockin') }}</li>
                                    <li><input class="mr-2 selectCheckbox" type="checkbox" checked="checked"
                                            value="ClockOut" />{{ __('messages.clockout') }}</li>
                                    <li><input class="mr-2 selectCheckbox" type="checkbox" checked="checked"
                                            value="TotalHour" />{{ __('messages.totalHours') }}</li>
                                </ul>
                            </div>
                            <div class="stickyCol-wrapper">
                                <div class="table-wrap stickyCol-scroller">
                                    <table id="history_tracked"
                                        class="table-striped table-bordered ad_tab stickyCol-table" >
                                        <thead>
                                            <tr class="table-primary">
                                                <th class="stickyCol-sticky-col"><label class="d-flex mb-0"><a
                                                            style="cursor: pointer;"
                                                            class="w-100" onclick="sort('Full Name','NamesSort')">
                                                            {{ __('messages.name') }} </a>
                                                        <span class="float-right"><i id="NamesSort"
                                                                class="fas fa-long-arrow-alt-up text-light"></i>
                                                        </span></label></th>
                                                <th class="EmailTable"><label class="d-flex mb-0"><a class="w-100" onclick="sort('Email','EmailsSort')"> {{ __('messages.email') }}</a><span class="float-right"><i id="EmailsSort" class="fas fa-long-arrow-alt-up text-light"></i></span></label></th>
                                                <th class="EmpCodeTable">
                                                    <label class="d-flex mb-0">
                                                        <a class="w-100" onclick="sort('Employee Code','EMPCodesSort')">{{ __('messages.employee') }} {{ __('messages.code') }} </a>
                                                        <span class="float-right ml-2"><i id="EMPCodesSort" class="fas fa-long-arrow-alt-up text-light"></i>
                                                        </span></label>
                                                </th>
                                                <!-- <th class="EmailTable"><label class="d-flex mb-0"><a class="w-100"
                                                                onclick="sort('Email','EmailsSort')">
                                                                {{ __('messages.email') }} </a><span
                                                                class="float-right"><i id="EmailsSort"
                                                                    class="fas fa-long-arrow-alt-up text-light"></i>
                                                            </span></label></th> -->
                                                <!-- <th class="EmpCodeTable">
                                                        <label class="d-flex mb-0">
                                                            <a class="w-100"
                                                                onclick="sort('Employee Code','EMPCodesSort')">{{ __('messages.employee') }}
                                                                {{ __('messages.code') }} </a><span
                                                                class="float-right ml-2"><i id="EMPCodesSort"
                                                                    class="fas fa-long-arrow-alt-up text-light"></i>
                                                            </span></label>
                                                    </th>  -->
                                                <th class="ClockInTable"><label class="d-flex mb-0"><a class="w-100"
                                                            onclick="sort('Start Time','ClockINSort')">{{ __('messages.clockin') }}</a><span
                                                            class="float-right"><i id="ClockINSort"
                                                                class="fas fa-long-arrow-alt-up text-light"></i>
                                                        </span></label></th>
                                                <th class="ClockOutTable"><label class="d-flex mb-0"><a
                                                            class="w-100"
                                                            onclick="sort('End Time','ClockOUTSort')">{{ __('messages.clockout') }}</a><span
                                                            class="float-right"><i id="ClockOUTSort"
                                                                class="fas fa-long-arrow-alt-up text-light"></i>
                                                        </span></label></th>
                                                <th class="TotalHourTable"><a
                                                        onclick="sort('Total Time','TotalHoursSort')">{{ __('messages.totalHours') }}
                                                    </a><span class=""><i id="TotalHoursSort"
                                                            class="fas fa-info-circle ml-1" data-toggle="tooltip"
                                                            data-placement="bottom"
                                                            title="{{ __('messages.clockout') }}- {{ __('messages.clockin') }}"></i>
                                                    </span></th>

                                                <th class="OfficeTimeTable"><a onclick="sort('Office Time','OfficeHoursSort')">{{ __('messages.officeHours') }}</a><span
                                                        class=""><i id="OfficeHoursSort" class="fas fa-info-circle ml-1"
                                                            data-toggle="tooltip" data-placement="bottom" title=" {{__('messages.officeHourInfo')}}"></i>
                                                    </span></th>

                                                <th class="ActiveHourTable"><a onclick="sort('Active Hours','ActiveHoursSort')">{{ __('messages.activeHours') }}
                                                    </a><span
                                                        class=""><i id="ActiveHoursSort" class="fas fa-info-circle ml-1"
                                                            data-toggle="tooltip" data-placement="bottom" title="{{__('messages.activeInfo')}}"></i>
                                                    </span></th>
                                                <th class="text-warning IdleTimeTable"><a onclick="sort('Idle','IdleSort')">{{ __('messages.attendanceIdleHours') }} </a><span
                                                        class=""><i id="IdleSort" class="fas fa-info-circle ml-1"
                                                            data-toggle="tooltip" data-placement="bottom" title="{{__('messages.idleInfo')}}"></i>
                                                    </span></th>
                                                <th class="text-success ProductiveTimeTable"><a onclick="sort('Productive Hours','ProductiveHoursSort')">Productive Hour </a><span
                                                        class=""><i id="ProductiveHoursSort" class="fas fa-info-circle ml-1"
                                                            data-toggle="tooltip" data-placement="bottom" title="{{__('messages.productiveInfo')}}"></i>
                                                    </span></th>
                                                <th class="text-danger UnproductiveTimeTable"><a onclick="sort('Unproductive Hours','UnproductiveHoursSort')">Unproductive Hours </a><span
                                                        class=""><i id="UnproductiveHoursSort" class="fas fa-info-circle ml-1"
                                                            data-toggle="tooltip" data-placement="bottom" title="{{__('messages.unproductiveInfo')}}"></i>
                                                    </span></th>
                                                <th class="NeutralTimeTable"><a onclick="sort('Neutral Hours','NeutralHoursSort')">Neutral Hours </a><span
                                                        class=""><i id="NeutralHoursSort" class="fas fa-info-circle ml-1"
                                                            data-toggle="tooltip" data-placement="bottom" title="{{__('messages.neutralInfo')}}"></i>
                                                    </span></th>
                                            </tr>
                                        </thead>
                                        <tbody id="attendanceHistory"> </tbody>
                                    </table>
                                </div>
                            </div>
                            <div id="wrapper" class="row">
                                <div class="col-md-6 align-self-center">
                                    <p class="mb-0" id="showPageNumbers"></p>
                                </div>
                                <div class="col-md-6">
                                    <div class="gigantic pagination" id="PaginationShow">
                                        <a href="#" class="first" data-action="first">&laquo;</a>
                                        <a href="#" class="previous" data-action="previous">&lsaquo;</a>
                                        <input type="text" readonly="readonly" />
                                        <a href="#" class="next" data-action="next">&rsaquo;</a>
                                        <a href="#" class="last" data-action="last">&raquo;</a>
                                    </div>
                                </div>
                            </div>


                        </div>
                        <div id="cover-spin"></div>
                        <div class="card-body" style="display: none;">
                            <div id="section1">
                                <div class="row">
                                    <div class="col-5">
                                        <b style="font-size: 35px; text-align: right" id="SheetNamePdf"></b>
                                        <p></p>
                                        <p></p>
                                        <p></p>
                                        <p></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12" id="loader" style="display: none">
                    <div class="loader"></div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@section('post-load-scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
<script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script src="https://cdn.amcharts.com/lib/4/core.js"></script>
<script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
<script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/switchery/0.8.2/switchery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.0.272/jspdf.debug.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js"
    integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/" crossorigin="anonymous">
</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.2.11/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.3.0/jszip.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/moment-timezone@0.5.43/builds/moment-timezone-with-data.min.js"></script>
@endsection

@section('page-scripts')
<script type="text/javascript"></script>
<script>
    let workHours = '{{ __('
    messages.workinghours ') }}';
    let unproductivity = '{{ __('
    messages.unproductivity ') }}';
    $('.select2').select2();
    let adminId = "";
    let envAdminIds = [];
    let downloadFileName = '{{ __('
    messages.attendanceHistoryFileName ') }}';
    let Employee_Absent = '{{ __('
    messages.absent ') }}';
    let REPORT_DOWNLOAD_MSG = JSON.parse('{{ __('
        messages.reportJsRequiredFields ') }}'.replace(/&quot;/g, '"'));
</script>
<script>
    $(function() {
        $('[data-toggle="tooltip"]').tooltip()
    })
</script>
<script src="../assets/js/final-timezone.js"></script>
<script src="../assets/js/incJSFile/SuccessAndErrorHandlers/_swalHandlers.js"></script>
<script src="../assets/js/JqueryPagination/jquery.jqpagination.js"></script>
<script src="../assets/js/incJSFile/JqueryDatatablesCommon.js"></script>
<script src="../assets/js/incJSFile/AttendanceHistory.js"></script>
@endsection