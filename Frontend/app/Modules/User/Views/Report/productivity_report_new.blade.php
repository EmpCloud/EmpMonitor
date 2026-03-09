@extends('User::Layout._layout')
@section('title')
    <title>@if((new App\Modules\User\helper)->checkHost() )
            @if((new App\Modules\User\helper)->checkHost() )
                {{env('WEBSITE_TITLE')}} |
            @endif
        @endif {{__('messages.productivityReport')}} </title>
@endsection
@section('data-table')
    <link href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css" rel="stylesheet"/>
    <link href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css" rel="stylesheet"/>
    
@endsection
@section('page-style')
    <link href="https://cdn.jsdelivr.net/gh/gitbrent/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css"
          rel="stylesheet">
          <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
          <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet" />
    <style>
        #chartdiv {
            width: 100%;
            height: 500px;
        }
        span.select2-selection__arrow {
    top: 20px !important;
}
.select2-selection.select2-selection--multiple .select2-selection__rendered input{
    margin-top:7px!important;
}
    </style>
@endsection
@section('content')

    <div class="page-inner no-page-title prod_report">
        <div id="main-wrapper">
            <div class="content-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style-1">
                        <li class="breadcrumb-item active"><a href="dashboard"
                                                              style="color: #0686d8;font-weight: 500;">
                                {{__('messages.home')}}</a></li>
                        <li class="breadcrumb-item" aria-current="page">
                            {{__('messages.productivity')}}
                        </li>
                    </ol>
                </nav>
                <h1 class="page-title">{{__('messages.productivity')}}</h1>
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
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select class="form-control select2" onchange="locChanged(this);">
                                            <option selected value="All">{{ __('messages.allLocation') }}</option>

                                          @if(isset($location_departmnet['code']) && $location_departmnet['code'] == 200)
                                        @if(!empty($location_departmnet['data']))
                                        @foreach($location_departmnet['data'] as $location)
                                        <option value="{{ $location['id'] }}"> {{ $location['location_name'] }} </option>
                                        @endforeach
                                        @endif
                                        @endif
                                        </select>
                                    </div>
                                </div>
                            
                                <div class="col-md-3">
                                    <div class="form-group">
                                            <select id="departmentsAppend"
                                                    class="form-control"
                                                     data-placeholder="{{ __('messages.SelectDept') }}" disabled>
                                            @if(isset($departments['code']) && $departments['code'] == 200)
                                        @if(!empty($departments['data']))
                                        @foreach($departments['data'] as $department)
                                        <option value="{{ $department['id'] }}"> {{ $department['name'] }} </option>
                                        @endforeach
                                        @endif
                                        @endif
                                            </select>
                                   </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <select class="form-control select2" id="empoption"
                                                onchange="employeeChangeFunction(this);" disabled>
                                            <option value="">{{__('messages.select')}} {{__('messages.employee')}}</option>
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

                              
                                    <div class="col-md-3">
                                        <div class="d-flex align-items-center position-relative">
                                            <div class="form-control form-group" id="reportrange"
                                                 style="cursor: pointer">
                                                <i class="fa fa-calendar"></i>&nbsp; <span style="font-size:12px;"
                                                                                           class=""></span>
                                                <i class="fa fa-caret-down"></i>
                                                <input type="hidden" name="to" id="to" value="">
                                                <input type="hidden" name="from" id="from" value="">
                                            </div>
                                            <i class="fa fa-info-circle text-primary ml-2 toDateLimitInfo"
                                               data-toggle="tooltip" title="{{__('messages.toDateLimitInfo')}}"></i>
                                        </div>
                                    </div>
                              
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Chart code start-->
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-body">
                            <p id="chartMessage" style="color: red;text-align: center;font-size:20px">
                            <p>
                            <div style="height: 500px; width: 100%;direction: ltr;" id="chartdiv"></div>
                        </div>
                    </div>
                </div>
                <!-- Chart code end-->
                <div class="col-md-12">
                    <div class="card">
                        <div
                            class="card-body d-flex justify-content-between verticle-align-middle align-items-center prod_repo_flex">
                            <div class="">
                                <p class="mb-0">{{__('messages.show')}} <select class="" id="ShowEntriesList">
                                        <option id="10" selected>10</option>
                                        <option id="25">25</option>
                                        <option id="50">50</option>
                                        <option id="100">100</option>
                                        <option id="200">200</option>
                                    </select> {{__('messages.entries')}}
                                </p>
                            </div>
                            <!-- ===========Export report buttons =============-->
                          
                        </div>
                        <div class="col-md-12 text-right pr-0" id="exportSpinner"
                             style="display: none;margin-bottom: 10px;">
                            <div class="clearfix">
                                <div class="spinner-border float-right" role="status">
                                    <span class="sr-only">Loading...</span>
                                </div>
                            </div>
                        </div>

                        <!-- ===========End Export report buttons =============-->
                        <!-- =========================-------------- -->
                        <div class="col-md-12">
                            <div class="table-wrap table-responsive">
                                <table id="desktop_control_table" class="table table-striped table-bordered w-100">
                                    <thead>
                                    <tr class="table-primary">
                                        <th id="dynamicHeading">{{__('messages.Location')}}</th>
                                        <th>{{__('messages.officeTime')}}</th>
                                        <th class="text-success">{{__('messages.productive')}}</th>
                                        <th class="text-success">{{__('messages.productivity')}}%</th>
                                        <th class="text-danger">{{__('messages.unproductive')}}</th>
                                        <th class="text-danger">{{__('messages.unproductive')}}%</th>
                                        <th class="text-secondary">{{__('messages.neutral')}}</th>
                                        <th class="text-secondary">{{__('messages.Idletimes')}} </th>
                                        <th class="text-secondary">{{__('messages.count')}}</th>
                                   
                                     </tr>
                                    </thead>
                                    <tbody id="reportTableBody">
                                    @if($response['total_count'] != 0)
                                        @foreach( $response['productivity_table'] as $key => $value)
                                            <tr>
                                                <td>{{ $value['name'] }}</td>
                                                <td>{{ $value['Total'] }} Hr</td>
                                                <td>{{ $value['Productive'] }} Hr</td>
                                                <td>{{ round($value['Pro_percent'], 2)  }} %</td>
                                                <td>{{ $value['Unproductive'] }} Hr</td>
                                                <td>{{ round($value['unpro_percent'], 2) }} %</td>
                                                <td>{{ $value['Neutral'] }} Hr</td>
                                                <td>{{ $value['Idle'] }} Hr</td>
                                                <td>{{ $value['count'] }} Hr</td> 
                                              
                                                

                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                            <td>{{__('messages.Nodata')}}</td>
                                            <td></td>
                                            <td></td>
                                            <td></td>
                                         
                                            <td></td>
                                        </tr>
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12 text-right pr-0">
                            <div style="display: none" id="loader" class="spinner-border text-primary"
                                 role="status">
                                <span class="sr-only">Loading...</span>
                            </div>
                        </div>
                        <div id="wrapper" class="row p-3">
                            <div class="col-md-6 align-self-center real-show">
                                <p class="mb-0" id="showPageNumbers"></p>
                            </div>
                            <div class="col-md-6 real-pagination">
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
    <!-- Main Wrapper -->
    <div class="" style="display: none;">
        <div class="table-responsive">
            <table class="table table-striped w-100" id="my_id_table_to_export">
                <thead id="header"></thead>
                <tbody id="body"></tbody>
            </table>
        </div>
    </div>

@endsection
// ... existing code ...

@section('page-scripts')
    <!-- Javascripts -->
    <script src="../assets/js/incJSFile/_dataFiltration.js" type="text/javascript"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js" 
            integrity="sha256-lSjKY0/srUM9BE3dPm+c4fBo1dky2v27Gdjm2uoZaL0=" 
            crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js" 
            integrity="sha512-CryKbMe7sjZDMFr5WX2sXQtvN2pU8fYDSj0wslemsTdI+Heaz5fM6mQQxP0vmlF6dzXw1Mx7hFkT/4FIuXhXvQ==" 
            crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.15/lodash.min.js"
            integrity="sha256-VeNaFBVDhoX3H+gJ37DpT/nTuZTdjYro9yBruHjVmoQ=" crossorigin="anonymous"></script>
    <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/1.5.3/jspdf.debug.js"
            integrity="sha384-NaWTHo/8YCBYJ59830LTz/P4aQZK1sS0SneOgAvhsIl3zBu8r9RevNg5lHCHAuQ/"
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.2.11/jspdf.plugin.autotable.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <script>
        // Wait for all scripts to load before initializing
        $(document).ready(function () {
            // Initialize select2 after library is loaded
            if (typeof $.fn.select2 !== 'undefined') {
                $('.select2').select2();
                
                $('#departmentsAppend').select2({
                    minimumResultsForSearch: false
                });
            } else {
                console.error('Select2 library not loaded');
            }
            
            // Initialize bootstrap toggle
            if (typeof $.fn.bootstrapToggle !== 'undefined') {
                $('#datewisereport').bootstrapToggle({
                    on: '{{__('messages.on')}}',
                    off: '{{__('messages.off')}}'
                });
            }
        });
    </script>
    <script>
        let TOTAL_COUNT_EMAILS = '<?php echo $response['total_count']; ?>';
        let TimeUsed = '{{__('messages.timeUsed')}}';
     
    </script>
    <script src="../assets/js/incJSFile/JqueryDatatablesCommon.js"></script>
    <script src="../assets/js/JqueryPagination/jquery.jqpagination.js"></script>
    <link href="../assets/css/jqpagination.css" rel="stylesheet">
    <script src="../assets/js/incJSFile/productivityReportNew.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.10.3/xlsx.full.min.js"></script>

    <!--===========================================  -->
@endsection