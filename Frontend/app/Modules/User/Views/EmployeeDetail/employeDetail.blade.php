@extends('User::Layout._layout')

@section('title')
    <title>@if((new App\Modules\User\helper)->checkHost() )
            @if((new App\Modules\User\helper)->checkHost() )
                {{env('WEBSITE_TITLE')}} | @endif @endif {{ __('messages.employee') }}
        -{{  trans_choice('messages.detail', 1) }} </title>
@endsection

@section('extra-style-links')

    <link href="../assets/plugins/bootstrap/css/loader.css" type="text/css" rel="stylesheet"/>
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css" type="text/css" rel="stylesheet"/>
    <link href="../assets/plugins/intel-tel-input/intlTelInput.css" type="text/css" rel="stylesheet">
    <link href="../assets/plugins/datetimepicker/css/gijgo.min.css" type="text/css" rel="stylesheet" type="text/css"/>
    <link href="../assets/css/jqpagination.css" type="text/css" rel="stylesheet">
    <link href="../assets/css/datatables-custom-pagination.css" type="text/css" rel="stylesheet">

@endsection

@section('page-style')
    @include('User::Layout._modernStyles')
    <style>
        /* Page Specific Styles Only */
        .modal-open[style] {
            padding-right: 0px !important;
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
            float: right;
        }

        input#upload-bulk {
            cursor: pointer;
        }

        label.custom-file-label::after {
            content: "{{__('messages.browse')}}";
            cursor: pointer !important;
        }

        label.custom-file-label {
            z-index: 2;
        }
    </style>
@endsection

@section('content')
    @include('User::EmployeeDetail.employeeForms')
    <div class="page-inner no-page-title">
        <div id="main-wrapper">
            <div class="content-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style-1">
                        <li class="breadcrumb-item"><a href="employee-details" style="color: #0686d8;font-weight: 500;">
                                {{ __('messages.home') }}</a></li>
                        <li class="breadcrumb-item" aria-current="page">{{ __('messages.employee') }}</li>
                    </ol>
                </nav>
                <h1 class="col-md-4 page-title" style="font-size: 21px;margin-left: -15px;color: #111112 !important;">
                    {{ __('messages.employee') }} </h1>
            </div>
            <div>
                @if(Session::has('message'))
                    <p class="alert-danger" style="text-align: center">{{ Session::get('message') }}</p>
                @endif
                    <div id="UnaccessModal" style="display: none">
                        <div class="alert alert-danger ">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <strong>Note : </strong>
                            <p id="ErrorMsgForUnaccess"> Inicates a successful or positive action.</p>
                        </div>
                    </div>

                {{--                <h5 class="alert-danger" id="ErrorMsgForUnaccess" style="text-align: center"></h5></div>--}}
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <form>
                                    @csrf
                                    <div class="form-row">

                                        <div class="col-md-12"> 
                                            <div class="bulk_data">
                                            <div class="btn-group btn-group-sm btn-block">
                                                 
                                                         <button class="btn btn-primary width_full_btn side-step1" onclick=planValidate(1);
                                                                id="add_btn"
                                                                data-toggle="modal"> {{ __('messages.register') }} {{ __('messages.employee') }}
                                                        </button>
                                                  
                                              
                                            </div>
                                            </div>
                                           
                                            <span class="float-right mt-4">
                                                 
                                                
                                                        <button style="display: none" class="btn btn-danger"
                                                                id="delete_btn"
                                                                data-toggle="modal" data-target="#deleteManagerModal" onclick="getListEmp()">{{ __('messages.delete') }}
                                                          </button> 
                                            </span>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12"> 
                        <input type="hidden" value="" id="CollapseMergeEmpId">
                        <div class="card">
                            <div class="card-body">
                                
                                <div class="row">
                                <div class="col-md-6"></div>
                                    <div class="col-md-6">
                                        
                                    </div>
                                    <div class="col-md-12">
                                    <div class="row mb-4">
    </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-sm-6 align-self-center">
                                        <div class="">
                                            <p class="mb-0">{{ __('messages.show') }} <select class=""
                                                                                              id="ShowEntriesList">
                                                    <option id="10" selected>10</option>
                                                    <option id="25">25</option>
                                                    <option id="50">50</option>
                                                    <option id="100">100</option>
                                                    <option id="200">200</option>
                                                </select> {{ __('messages.entries') }}
                                            </p>
                                        </div>
                                    </div> 
                                    <div class="col-md-3 input-group"></div>
                                    <div class="col-md-3 input-group">
                                        <input type="text" class="form-control"
                                               placeholder="{{ __('messages.search') }} ..." value=""
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
                                

                             
                                <div class="stickyCol-wrapper">
                                    <div class="table-wrap stickyCol-scroller">
                                        <table class="table-bordered ad_tab stickyCol-table"
                                                style="width: 100%;"
                                               id="empDetails_Table">
                                            <thead>
                                           

                                            <tr class="table-primary">
                                                <th class="stickyCol-sticky-col"><input name="select_all" id="SelectAllCheckBox" class="multipleCheck mr-4"
                                                                                        type="checkbox">
                                                    <a style="cursor: pointer; color: black;" onclick="sort('Full Name', 'NameSort')"> {{ __('messages.fullName') }} </a>
                                                    <span class="float-right"><i id="NameSort"
                                                                                 class="fas fa-long-arrow-alt-up text-light"></i>
                                                </span></th>
                                                <th  class="EmailTable"><label class="d-flex mb-0"><a class="w-100"
                                                                                                      onclick="sort('Email', 'EmailSort')">{{ __('messages.Email id') }}  </a><span
                                                            class="float-right"><i id="EmailSort"
                                                                                   class="fas fa-long-arrow-alt-up text-light"></i>
                                                    </span></label></th>
                                                 
                                                <th  class="EmpCodeTable"><label class="d-flex mb-0"><a class="w-100"
                                                                                  onclick="sort('EMP-Code', 'EMP-CodeSort')">{{ __('messages.empCode') }}
                                                        </a><span
                                                            class="float-right"><i id="EMP-CodeSort"
                                                                                   class="fas fa-long-arrow-alt-up text-light"></i>
                                                    </span></label></th>

                                                    <th  class="rolesTable">
                                                    <a onclick="sort('Role', 'RoleSort')">{{ __('messages.role') }} </a><span
                                                        class="float-right"><i id="RoleSort"
                                                                               class="fas fa-long-arrow-alt-up text-light"></i>
                                                </span></th>
                                                <th class="AgentDateTable" style="display: none;"><label class="d-flex mb-0"><a
                                                            class="w-100"
                                                            onclick="sort('AgentDate-asc', 'AgentDate-desc')">{{ __('messages.agentinstalldate') }}
                                                        </a><span
                                                            class="float-right"><i id="AgentDate-desc"
                                                                                   class="fas fa-long-arrow-alt-up text-light"></i>
                                                </span></label>
                                                </th>
                                                <th class="AgentUpdateDateTable" style="display: none;"><label class="d-flex mb-0"><a
                                                            class="w-100"
                                                            onclick="sort('AgentUpdateDate-asc', 'AgentUpdateDate-desc')">{{ __('messages.agentupdatedate') }}
                                                        </a><span
                                                            class="float-right"><i id="AgentUpdateDate-desc"
                                                                                   class="fas fa-long-arrow-alt-up text-light"></i>
                                                </span></label>
                                                </th>
                                            
                                                <th class="OSNameTable"><label class="d-flex mb-0"><a
                                                            class="w-100"
                                                            onclick="sort('Os-asc', 'Os-desc')">OS
                                                        </a><span
                                                            class="float-right"><i id="OS_Name"
                                                                                   class="fas fa-long-arrow-alt-up text-light"></i>
                                                </span></label>
                                                </th> 
                                                    <th class="ComputerNameTable"><label class="d-flex mb-0"><a
                                                                class="w-100"
                                                                onclick="sort('Computer Name', 'Computer_Name')">{{ __('messages.computerName') }}
                                                            </a><span
                                                                class="float-right"><i id="Computer_Name"
                                                                                       class="fas fa-long-arrow-alt-up text-light"></i>
                                                </span></label>
                                                    </th>

                                                    <th class="versionTable"><label class="d-flex mb-0">
                                                            <a class="w-100"
                                                               onclick="sort('Agent Version', 'Agent-VersionSort')">{{ __('messages.version') }}</a><span
                                                                class="float-right"><i id="Agent-VersionSort"
                                                                                       class="fas fa-long-arrow-alt-up text-light"></i>
                                                </span>
                                                        </label>
                                                    </th> 
                                                    <th class="action">Action</th>



                                              
                                            </tr>
                                            </thead>
                                            <tbody id="fetch_Details">
                                            </tbody>
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
                                            <input type="text" readonly="readonly"/>
                                            <a href="#" class="next" data-action="next">&rsaquo;</a>
                                            <a href="#" class="last" data-action="last">&raquo;</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12" id="loader" style="display: inline">
                        <div class="loader"></div>
                    </div>
                </div>
            </div>
        </div>

     

        <br/>

        {{--end of degrade model--}}
        {{--for active employee--}}
        <form id="active-mod" method="POST">
            @csrf
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="modal fade" id="ActiveManagerModal" tabindex="-1" role="dialog"
                 aria-labelledby="ActiveEmpModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered " role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ActiveEmpModalLabel"><A>{{__('messages.activateEmp')}}</A></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>{{__('messages.activateQuestion')}}?
                            </p>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="status" value="2">
                            <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{__('messages.no')}}</button>
                            <button type="submit" id="activeMod" class="btn btn-primary" name="delete" value="Save">
                                {{__('messages.activate')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        {{--For Suspend multiple dailouge box--}}
        <form id="" method="POST">
            @csrf
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="modal fade" id="SuspenManagerModal" tabindex="-1" role="dialog"
                 aria-labelledby="ActiveEmpModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered " role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="ActiveEmpModalLabel">
                                <A>{{__('messages.suspend')}} {{__('messages.employee')}}</A></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>{{__('messages.suspendEmpInfo')}}?</p>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="status" value="2">
                            <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{__('messages.no')}}</button>
                            <button type="submit" id="SuspendModal" class="btn btn-primary" name="sesp" value="Save">
                                {{__('messages.suspend')}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        {{--deleting single user--}}
        <form method="get" action="" id="emp-singleDelete">
            @csrf
            <meta name="csrf-token" content="{{ csrf_token() }}">
            <div class="modal fade" id="DeleteSingleModal" tabindex="-1" role="dialog" aria-labelledby=""
                 aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id=""><A>{{ __('messages.delete') }}</A></h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>{{ __('messages.deleteMsg') }} <br>
                            <p style="color: red"> {{ __('messages.note') }}:- <span style="color: black">{{ __('messages.deleteMsgs') }}
                              </span></p>
                        </div>
                        <div class="modal-footer">
                            {{--                        <input type="hidden" class="delete-loc btn btn-primary hide-loc" id="hide" name="DetedId">--}}
                            <input type="hidden" class="delete-loc btn btn-primary hide-loc" name="DetedId">
                            <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ __('messages.no') }}</button>
                            <button type="submit" id="emp-singleDeletee"
                                    class="btn btn-primary">{{ __('messages.delete') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Madhu -->
        <form method="post" action="">
            @csrf
            <div class="modal fade" id="deleteManagerModal" tabindex="-1" role="dialog"
                 aria-labelledby="DeleteEmpModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered " role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title"
                                id="DeleteEmpModalLabel">{{__('messages.delete')}} {{__('messages.employee')}}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="clearList()">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p>{{__('messages.deleteQuestion')}}?</p>
                            <ol id="selectedEmployeeList"></ol>
                            <p style="color: red"> {{ __('messages.note') }}:- <span style="color: black">{{__('messages.deleteInfo')}}
                              </span></p>
                            <br>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal" onclick="clearList()">{{__('messages.no')}}</button>
                            <button type="submit" id="deleteModal" class="btn btn-primary deleteMultiple-loc"
                                    name="save_value">{{__('messages.yes')}}
                            </button>

                        </div>
                    </div>
                </div>
            </div>
        </form>
      
        {{--added for multiple manager selection with team lead option    --}}
        <form method="post" id="Manager-select">
            @csrf
            <div class="modal fade" id="MultiManagerModal">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h4 class="modal-title">{{ __('messages.assign') }} {{ __('messages.employee') }} {{ __('messages.to') }} {{ __('messages.anyone') }}</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>
                        <!-- Modal body -->
                        <div class="modal-body">
                            <div class="form-group">
                                <label>{{ __('messages.select') }} {{ __('messages.role') }} </label>
                                <select style="width: 450px !important;" class="form-control" id="CompletRoles1">
                                </select>
                            </div>
                            <div class="form-group">
                                <label>{{ __('messages.select') }} {{ __('messages.employee') }}</label>
                                <select class="form-control js-example-tokenizer" style="width: 450px !important;"
                                        multiple="multiple" id="AppendManagerList">
                                </select>
                            </div>
                        </div>
                        <!-- Modal footer -->
                        <div class="modal-footer">
                            <input type="hidden" name="Form" value="1">
                            <input type="hidden" class="Assin-loc btn btn-primary hide-loc" id="AssignedId"
                                   name="User_id">
                            <button type="button" class="btn btn-secondary"
                                    data-dismiss="modal">{{ __('messages.no') }}</button>
                            <button type="submit" id="AssignButton"
                                    class="btn btn-primary">{{ __('messages.assign') }}</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="modal fade show" id="DeletedUserList" tabindex="-2" role="dialog" aria-labelledby="Close"
             aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel"
                            style="text-align: left">{{__('messages.Deleted_users')}}</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm">
                                <h3> </h3>
                            </div>
                            <div class="col-sm-2 text-right"></div>
                            <div class="col-sm-3 text-right">
                                <div class="" id="reportrange" style="cursor: pointer">
                                    <i class="fa fa-calendar"></i>&nbsp; <span></span>
                                    <i class="fa fa-caret-down"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="table-wrap table-responsive">
                                <table id="DeleteUserTable" class="table table-striped">
                                    <thead>
                                    <tr>
                                        <th>{{__('messages.empName')}}</th>
                                        <th>{{__('messages.employee')}} {{__('messages.email')}}</th>
                                        <th>{{__('messages.computerName')}} </th>
                                        <th>{{__('messages.date')}} </th>
                                        <th> {{__('messages.deleted_by')}} </th>
                                    </tr>
                                    </thead>
                                    <tbody id="DeleteUserListTable">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="bs-example" data-example-id="hoverable-table" id="assignemployeetable">
                            <div id="assignemployedatatable_wrapper"
                                 class="dataTables_wrapper form-inline no-footer">
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" value="" id="OpenedManagerModalId">
            </div>
        </div>

    </div>

@endsection



@section('post-load-scripts')
    <script src="../assets/plugins/switchery/switchery.min.js" type="text/javascript"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js" type="text/javascript"></script>
    <script src="../assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    {{-- <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script> --}}
    <script src="../assets/plugins/datetimepicker/js/gijgo.min.js" type="text/javascript"></script>
    <script src="../assets/plugins/intel-tel-input/intlTelInput.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>
@endsection

@section ('page-scripts')

    {{-- <script type="text/javascript">
        $('.closeall').click(function(){
          $('.collapse.in')
            .collapse('hide');
        });
        $('.openall').click(function(){
          $('.collapse:not(".in")')
            .collapse('show');
        });
        </script> --}}
    <script>
        let language = JSON.parse('{{__('messages.js')}}'.replace(/&quot;/g, '"'));
        let EMP_DROPDOWN_TEXT = JSON.parse('{{__('messages.empFullDetailsJs')}}'.replace(/&quot;/g, '"'));
        let envAdminId = [];
        let data = true;
        let userId = 1;
        let organizationId = 1;
        let customOrgId = 1; 
        let exportSheetName = '{{env('SHEET_NAME')}}';
        let envApiHost = '{{env('API_HOST_V3')}}';
        let noOfUsers = ""; 
         let checkHostVariable = '<?php echo((new \App\Modules\User\helper)->checkHost()) ?>';
        let phone_number_error_msg = JSON.parse('{{__('messages.phone_number_error_msg')}}'.replace(/&quot;/g, '"'));
        let imagetype_create = '{{__('messages.imageType')}}';
        let imagesize_create = '{{__('messages.imageSize')}}';
        let shifterr = '{{__('messages.shifterr')}}';
        let deletem = '{{__('messages.delete')}}';
        let deletemsg = '{{__('messages.deleteMsg')}}';
        let deletenote = '{{__('messages.deleteMsgs')}}';
        let note = '{{__('messages.note')}}';
        let delcancel = '{{__('messages.cancel')}}';
        let logoutJs  =  JSON.parse('{{__('messages.logoutJs')}}'.replace(/&quot;/g, '"'));
      
        $(function () {
            let start = moment().subtract(6, 'days');
            let end = moment();
            $('#reportrange').daterangepicker({
                maxDate: end,
                startDate: start,
                endDate: end,
                ...dateRangeLocalization,
            }, cb);
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        });



        function cb(start, end) {
            START_DATE=start.format('YYYY-MM-D') ;
            END_DATE=end.format('YYYY-MM-D') ;
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
            deletedUserList(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'));
            $('#DeleteUserTable').DataTable().clear().draw();
            $('#DeleteUserTable').DataTable().destroy();
            UserCompleteList = [];
        }

    
    </script>
    <script src="../assets/js/final-timezone.js" type="text/javascript"></script>
    <script src="//unpkg.com/xlsx/dist/xlsx.full.min.js" type="text/javascript"></script>
    <script src="../assets/js/incJSFile/SuccessAndErrorHandlers/_swalHandlers.js" type="text/javascript"></script>
    <script src="../assets/js/incJSFile/EmployeeDetailJs/employeeDetail.js" type="text/javascript"></script>
    <script src="../assets/js/incJSFile/_dataFiltration.js" type="text/javascript"></script>
    <script src="../assets/js/incJSFile/JqueryDatatablesCommon.js" type="text/javascript"></script>
    <script src="../assets/js/JqueryPagination/jquery.jqpagination.js" type="text/javascript"></script>
    <script src="../assets/js/incJSFile/EmployeeDetailJs/commonFunction_Forms.js" type="text/javascript"></script>
    <script src="../assets/plugins/daterangepicker/moment.min.js"></script>
    <script src="../assets/plugins/daterangepicker/moment-timezone-with-data.js"></script>
    <script src="../assets/plugins/daterangepicker/daterangepicker.js"></script>

@endsection
