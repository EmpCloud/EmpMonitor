@extends('User::Layout._layout')

@section('title')
    <title>@if((new App\Modules\User\helper)->checkHost() )
                           @if((new App\Modules\User\helper)->checkHost() )
                           {{env('WEBSITE_TITLE')}} | @endif @endif Attendance </title>
@endsection

@section('extra-style-links')
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css"
          href="//cdn.jsdelivr.net/bootstrap.daterangepicker/1/daterangepicker-bs3.css"/>
    <link href="../assets/css/jqpagination.css" type="text/css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
@endsection

@section('page-style')
    <style>
        .fa {
            margin-left: -2px;
            margin-right: -1px;
        }

        .table-condensed thead tr:nth-child(2),
        .table-condensed tbody {
            display: none
        }
        .emp_attan {
        overflow-x: scroll;
        }

        /*a.w-100 {*/
        /*    width: 1000px !important;*/
        /*}*/
    </style>
@endsection

@section('content')
    <div class="page-inner no-page-title">
        <input type="hidden" id="TodayDate" name="{{$TodayDate}}">
        <div id="main-wrapper">
            <div class="content-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style-1">
                        <li class="breadcrumb-item"><a href="employee-details" style="color: #0686d8;font-weight: 500;">
                                {{ __('messages.home') }}</a></li>
                        <li class="breadcrumb-item" aria-current="page">
                            {{ __('messages.attendance') }}
                        </li>
                    </ol>
                </nav>
            </div>
            <div id="UnaccessModal" style="display: none">
                <div class="alert alert-danger ">
                    <button type="button" class="close" data-dismiss="alert" >&times;</button>
                    <strong>Note : </strong><p id="ErrorMsgForUnaccess"> Indicates a successful or positive action.</p>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <h1 class="page-title"> {{ __('messages.attendance') }}</h1>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm">
                                    <div class="form-group mb-3">
                                        <label><b> {{ __('messages.month') }}/ {{ __('messages.year') }}: </b></label>
                                        <input type="text" id="EmployeedateOfjoin" class="monthPicker form-control"
                                               name="date"/>
                                    </div>
                                </div>

                                <div class="col-sm">
                                    <div class="form-group mb-3">
                                        <label><b> {{ __('messages.Location') }}: </b></label>
                                        @if($LocationData['code']===200)
                                            <select class="form-control" id="LocationData">
                                                <option value="null" id="">{{ __('messages.allLocation') }}</option>
                                                @foreach($LocationData['data'] as $location)
                                                    <option id="{{$location['id']}}">{{$location['name']}}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <select class="form-control" id="LocationData"></select>
                                            <p style="color: red">{{$LocationData['msg']}}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm">
                                    <div class="form-group mb-0">
                                        <label><b> {{ __('messages.department') }}: </b></label>
                                        @if($DepartementData['code'] ===200)
                                            <select class="form-control" id="DepartementData">
                                                <option id="null"> {{ __('messages.allDept') }}</option>
                                                @foreach($DepartementData['data']['data']  as $dept)
                                                    <option id="{{$dept['id']}}">{{$dept['name']}}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <select class="form-control" id="DepartementData">
                                                <p style="color: red">{{$DepartementData['msg']}}</p>
                                            </select>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-md-2 mb-3 d-inline">
                                    <label>&nbsp;</label>
                                    <button id="DownloadButton" onclick="CallAjaxToGetAllData()"
                                            class="btn btn-success btn-block"> {{ __('messages.export') }}  Excel
                                    </button>
                                    <p style="display: none; color: red;text-align: center" id="ErrorMessage"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="align-self-center">
                                    <p class="mb-0 ml-3"> {{ __('messages.show') }} <select class="" id="ShowEntriesList">
                                            <option id="10" selected>10</option>
                                            <option id="25">25</option>
                                            <option id="50">50</option>
                                            <option id="100">100</option>
                                            <option id="200">200</option>
                                        </select>  {{ __('messages.entries') }}
                                    </p>
                                </div>
                                <div class="col-sm">
                                    <div id="LoaderIcon" style="display: none" class="loaderIcon mr-2"
                                         style="display:block"></div>

                                </div>
                                <div class="col-md-3 input-group">
                                    <input type="text" class="form-control" placeholder=" {{ __('messages.search') }}..." value=""
                                           id="SearchTextField" onkeypress="return runScript(event)">
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" id="SearchButton"
                                                onclick="SearchText()">
                                            <i class="fa fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-12 text-right"><p class="mb-0" style="color: red"
                                                                  id="SearchErrorMsg"></p></div>
                            </div>
                            <div class="stickyCol-4-wrapper emp_attan">
                                <div class="table-responsive stickyCol-4-scroller" id="tableRemove">
                                    <table id="datatableData"
                                           class="table table-bordered nowrap table-striped stickyCol-4-table table-no-sp">
                                        <thead class="table-primary" id="AppendHeader">

                                        </thead>
                                        <tbody id="AttendanceListData">

                                        </tbody>
                                    </table>
                                    <div class="col-md-12" id="loader" style="display: none">
                                        <div class="loader"></div>
                                    </div>
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
                                        <input type="text" readonly="readonly"/>
                                        <a href="#" class="next" data-action="next">&rsaquo;</a>
                                        <a href="#" class="last" data-action="last">&raquo;</a>
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
    <script src="../assets/plugins/daterangepicker/moment.min.js"></script>
    <script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/1/daterangepicker.js"></script>
    <script type="text/javascript" src="//unpkg.com/xlsx/dist/xlsx.full.min.js"></script>
    <script src="../assets/js/incJSFile/JqueryDatatablesCommon.js" type="text/javascript"></script>
    <script src="../assets/js/JqueryPagination/jquery.jqpagination.js" type="text/javascript"></script>
@endsection

@section('page-scripts')
    <script type="text/javascript" src="../assets/js/incJSFile/EmployeeDetailJs/emp_attendance.js"></script>
    <link href="../assets/plugins/bootstrap/css/loader.css" rel="stylesheet"/>

    <script type="text/javascript">
        let attendance_local = {};
        attendance_local.P = '{{__('messages.p')}}';
        attendance_local.L = '{{__('messages.l')}}';
        attendance_local.H = '{{__('messages.h')}}';
        attendance_local.A = '{{__('messages.a')}}';
        attendance_local.O = '{{__('messages.o')}}';
        attendance_local.D = '{{__('messages.d')}}';
        attendance_local.EL = '{{__('messages.el')}}';
        attendance_local['-'] = '-';
        $(document).ready(function () {
            $(".monthPicker").datepicker({
                dateFormat: 'MM yy',
                changeMonth: true,
                changeYear: true,
                minDate: '-180d',
                maxDate: new Date(),
                showDropdowns: true,
                // showButtonPanel: true,
                onClose: function (dateText, inst) {
                    let month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    let year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).val($.datepicker.formatDate('M/yy', new Date(year, month, 1)));
                    SELECTED_DATE = $.datepicker.formatDate('yymm', new Date(year, month, 1));
                    SELECTED_DAYS_DATE = $.datepicker.formatDate('m/yy', new Date(year, month, 1));
                    TABLE_HEADER = true;
                    makeDatatableDefault()
                    $("#AppendHeader").empty();
                    EmployeeAttendanceList($.datepicker.formatDate('yymm', new Date(year, month, 1)), SELECTED_LOCATION, SELECTED_DEPARTEMENT, SHOW_ENTRIES, "", "", SORT_NAME, SORT_ORDER);

                }
            });

            $(".monthPicker").focus(function () {
                $(".ui-datepicker-calendar").hide();
                $("#ui-datepicker-div").position({
                    my: "center top",
                    at: "center bottom",
                    of: $(this)
                });
            });
        });
    </script>
@endsection
