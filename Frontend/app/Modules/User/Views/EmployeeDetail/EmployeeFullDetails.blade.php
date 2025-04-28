@extends('User::Layout._layout')
@section('title')
    <title>@if((new App\Modules\User\helper)->checkHost() )
                           @if((new App\Modules\User\helper)->checkHost() )
                           {{env('WEBSITE_TITLE')}} | @endif @endif {{ __('messages.user') }}-{{ __('messages.full') }} {{  trans_choice('messages.detail', 10) }}</title>
@endsection

@section('extra-style-links')
    <link href="../assets/plugins/DataTables/datatables.min.css" rel="stylesheet"/>
    <link href="../assets/plugins/bootstrap/css/loader.css" rel="stylesheet"/>
    <link href="../assets/plugins/intel-tel-input/intlTelInput.css" rel="stylesheet">
    <link href="../assets/css/jqpagination.css" rel="stylesheet">
    <!-- Add fancyBox -->
    <link rel="stylesheet" href="../assets/plugins/fancybox2/source/jquery.fancybox.css?v=2.1.7" type="text/css"
          media="screen"/>
    <!-- Optionally add helpers - button, thumbnail and/or media -->
    <link rel="stylesheet" href="../assets/plugins/fancybox2/source/helpers/jquery.fancybox-buttons.css?v=1.0.5"
          type="text/css" media="screen"/>
    <link rel="stylesheet" href="../assets/plugins/fancybox2/source/helpers/jquery.fancybox-thumbs.css?v=1.0.7"
          type="text/css" media="screen"/>
@endsection

@section('page-style')
    @include('User::EmployeeFullDetailsPage._employeeSSStyles')
@endsection

@section('content')
    @include('User::EmployeeDetail.employeeForms')
    {{--    main view   --}}
    <div class="page-inner no-page-title">
        <input type="hidden" value="1" id="RoleData" data-list="">
        <div id="main-wrapper">
            <div class="content-header">
                 <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style-1">
                    @if(Session::has('employee_session'))
                    <li class="breadcrumb-item"><a href="" style="color: #0686d8;font-weight: 500;">
                                {{ __('messages.home') }}</a></li>
                    @else
                        <li class="breadcrumb-item"><a href="employee-details" style="color: #0686d8;font-weight: 500;">
                                {{ __('messages.home') }}</a></li>
                        <li class="breadcrumb-item"><a href="employee-details" style="color: #0686d8;font-weight: 500;">
                                {{ __('messages.employee') }}</a></li>
                        @endif
                        <li class="breadcrumb-item" aria-current="page">
                            {{ __('messages.empFullDetails') }}
                        </li>
                        <p id="user-id" style="display: none" name="{{$user_details['data']['id']}}"></p>
                    </ol>
                </nav>
                 {{--                To show no permission message       --}}
                <div id="UnaccessModal" style="display: none">
                    <div class="alert alert-danger ">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>Note : </strong>
                        <p id="ErrorMsgForUnaccess"> Indicates a successful or positive action.</p>
                    </div>
                </div>

                 <h1 class="page-title mr-3" id="userId"
                    value="{{$user_details['data']['id']}}">{{$user_details['data']['first_name']." ".$user_details['data']['last_name']}}</h1>
                    @if(Session::has('employee_session'))
                    @else
                    <a href="#" class="btn btn-link btn-sm" onclick="getdetails()" data-toggle="modal" data-target="#editEmpModal">{{ __('messages.edit') }} </a>
                    @endif

                   <div class="col-md-4 float-right p-0">
                        <div class="form-control" id="dateRange" style="cursor: pointer">
                            <i class="fa fa-calendar"></i>&nbsp; <span></span>
                            <i class="fa fa-caret-down"></i>
                            <span id="from" hidden></span>
                            <span id="to" hidden></span>
                        </div>
                   </div>
             </div>
          
        </div>
        {{--    removed Analysis modals and calling from external page  --}}
        @include('User::EmployeeFullDetailsPage._middleSubSections')
 
    </div>

@endsection

@section('post-load-scripts')
    {{--    Java Scripts  removed and using from file   --}}
    <script>
        let phone_number_error_msg = JSON.parse('{{__('messages.phone_number_error_msg')}}'.replace(/&quot;/g, '"'));
    </script>
    @include('User::EmployeeFullDetailsPage._postLoadScripts')
@endsection

@section('page-scripts')
    <script src="../assets/plugins/select2/js/select2.min.js" type="text/javascript"></script>
    {{--    remove common js files and scripts and using from external file     --}}
    @include('User::EmployeeFullDetailsPage._pageScripts') 
    <script>
        let SiteIndicator = '{{env("APP_ENV")}}';   // to know whether dev or main site
        let imagetype = '{{__('messages.imageType')}}';
        let imagesize = '{{__('messages.imageSize')}}';
        let apilimitmsg = '{{__('messages.apilimitmsg')}}';
        let SS_MAX_LIMIT = '{{env('EXTENDED_SCREEN_LIMIT', 10)}}'; // get MAX To Time from env
         const ENV_EMPLOYEE = '<?php echo env('Employee') ?>';
        const MO_TITLE ="{!! __('messages.toTimeIfoMO') !!}";
        let EMP_DROPDOWN_TEXT = JSON.parse('{{__('messages.empFullDetailsJs')}}'.replace(/&quot;/g, '"'));
        let fromDt = '{{__('messages.from')}}';
        let toDt = '{{__('messages.to')}}';
    </script>

@endsection
