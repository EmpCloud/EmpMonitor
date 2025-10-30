@extends('User::Layout._layout')

@section('title')
    <title>@if((new App\Modules\User\helper)->checkHost() )
                           @if((new App\Modules\User\helper)->checkHost() )
                           {{env('WEBSITE_TITLE')}} | @endif @endif AttendanceSheet </title>
@endsection

@section('extra-style-links')
<link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css" />
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

        #myTable_wrapper table.dataTable tr td,  #myTable_wrapper table.dataTable tr th {
            padding-block: 14px;
        }
    
        #myTable_wrapper table.dataTable thead th,  #myTable_wrapper table.dataTable tfoot th {
            font-weight: bold;
            min-width: 130px;
            max-width: 130px;
            text-align: center;
            font-size: 14px;
        }
        
        #myTable_wrapper table.dataTable.row-border>tbody>tr>*, #myTable_wrapper table.dataTable.display>tbody>tr>* {
            border-top: 1px solid rgba(0, 0, 0, 0.15);
            min-width: 130px;
            max-width: 130px;
            text-align: center;
            font-size: 14px;
        }

        #myTable_wrapper table.dataTable thead tr:first-child, #myTable_wrapper table.dataTable tfoot tr:first-child {
            min-width: auto;
        }
        #myTable_wrapper table.dataTable.row-border>tbody>tr:first-child, #myTable_wrapper table.dataTable.display>tbody>tr:first-child{
            
            min-width: auto;
        }
        #myTable_wrapper > div > div > div > label {
            margin-left: 10px;
        }

      

        #myTable_wrapper .dt-scroll .dt-scroll-body::-webkit-scrollbar {
    width: 3px; /* width of the vertical scrollbar */
    height: 6px; /* height of the horizontal scrollbar */
}

    #myTable_wrapper .dt-scroll .dt-scroll-body::-webkit-scrollbar-track {
    background: #f1f1f1; 
    border-radius: 10px;
}

    #myTable_wrapper .dt-scroll .dt-scroll-body::-webkit-scrollbar-thumb {
    background: #61acfa; 
    border-radius: 10px;
}

thead#headerData tr th:first-child, tbody#bodyData tr td:first-child {
    min-width: 200px;
}
#myTable_wrapper #bodyData tr td.dt-empty {
    text-align: start;
    /* margin-left: 400px; */
    padding-left: 400px;
}
.ui-datepicker .ui-datepicker-buttonpane button.ui-datepicker-current {
    display: none;
}
      
    </style>
@endsection

@section('content')
    <div class="page-inner no-page-title emp_attan_main">
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
                                    <div class="form-group mb-0">
                                        <label><b> {{ __('messages.Location') }}: </b></label>
                                        @if($LocationData['code']===200)
                                            <select class="form-control" id="LocationData">
                                                <option value="0">{{ __('messages.allLocation') }}</option>
                                                @foreach($LocationData['data'] as $location)
                                                    <option id="{{$location['id']}}" value="{{$location['id']}}">{{$location['name']}}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <p style="color: red">{{$LocationData['msg']}}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-sm">
                                    <div class="form-group mb-0">
                                        <label><b> {{ __('messages.department') }}: </b></label>
                                        @if($DepartementData['code'] ===200)
                                            <select class="form-control" id="DepartementData">
                                                <option id="null" value="0"> {{ __('messages.allDept') }}</option>
                                                @foreach($DepartementData['data']['data']  as $dept)
                                                    <option id="{{$dept['id']}}" value="{{$dept['id']}}">{{$dept['name']}}</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <p style="color: red">{{$DepartementData['msg']}}</p>
                                        @endif
                                    </div>
                                </div>
                                @if((new App\Modules\User\helper)->getHostName() === env('Admin'))
                                <div class="col-sm">
                                    <div class="form-group mb-0">
                                        <label class="font-weight-bold text-dark"
                                               for="nonadmins">{{ __('messages.employee') }}</label>
                                        @if($EmployeeData['code']==200 )
                                            <select class="form-control" id="employeeId">
                                                <option selected class="active-result" value="0"> {{ __('messages.all') }}</option>
                                                @foreach($EmployeeData['data'] as $locs)
                                                    <option id="{{$locs['id']}}"
                                                            value="{{$locs['id']}}">{{$locs['first_name']}}
                                                        @if(isset($locs['last_name']))
                                                            {{ $locs['last_name'] }}
                                                        @endif</option>
                                                @endforeach
                                            </select>
                                        @else
                                            <p style="color: red">{{$EmployeeData['msg']}}</p>
                                        @endif
                                    </div>
                                </div>
                                @endif
                                <div class="col-sm">
                                    <div class="form-group mb-0">
                                        <label><b> {{ __('messages.month') }}/ {{ __('messages.year') }}: </b></label>
                                        <input type="text" id="EmployeedateOfjoin" class="monthPicker form-control"
                                               name="date"/>
                                    </div>
                                </div>

                                <div class="col-md-2 mb-3 d-inline export_btn">
                                    <label>&nbsp;</label>
                                    <button id="DownloadButton" onclick="CallAjaxToGetAllData()"
                                            class="btn btn-success btn-block"> {{ __('messages.exportExcel') }}
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
                            <div class="stickyCol-4-wrapper ">
                                <div class="" id="">
                                <table id="myTable" class="display">
                                    <thead id="headerData">   
                                    </thead>
                                    <tbody id="bodyData">
                                    </tbody>
                                </table>
                                    <div class="col-md-12" id="loader" style="display: none">
                                        <div class="loader"></div>
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
   
@endsection

@section('page-scripts')
<script type="text/javascript" src="../assets/js/incJSFile/EmployeeDetailJs/attendanceSheet.js"></script>
<link href="../assets/plugins/bootstrap/css/loader.css" rel="stylesheet"/>

<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script type="text/javascript">
   

    $(document).ready(function () {
            $(".monthPicker").datepicker({
                dateFormat: 'MM yy',
                changeMonth: true,
                changeYear: true,
                minDate: '-180d',
                maxDate: new Date(),
                showDropdowns: true,
                showButtonPanel: true,
                onClose: function (dateText, inst) {
                    let month = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                    let year = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                    $(this).val($.datepicker.formatDate('M/yy', new Date(year, month, 1)));
                    SELECTED_DATE = $.datepicker.formatDate('yymm', new Date(year, month, 1));
                    SELECTED_DAYS_DATE = $.datepicker.formatDate('m/yy', new Date(year, month, 1));
                    TABLE_HEADER = true;
                    employeedata($.datepicker.formatDate('yymm', new Date(year, month, 1)), SELECTED_LOCATION, SELECTED_DEPARTEMENT);
                   
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