@extends('User::Employee._employeeLayout')
@section('title')
    <title>@if((new App\Modules\User\helper)->checkHost() )
                           @if((new App\Modules\User\helper)->checkHost() )
                           {{env('WEBSITE_TITLE')}} | @endif @endif {{__('messages.employee')}} {{__('messages.dashboard')}}</title>
@endsection

@section('extra-style-links')
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css" rel="stylesheet"/>
    <link href="../assets/plugins/bootstrap/css/loader.css" rel="stylesheet"/>

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

    {{--    main view   --}}
    <div class="page-inner no-page-title">
        <div id="main-wrapper">
            <div class="content-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style-1">
                        <li class="breadcrumb-item">{{ __('messages.home') }}</li>
                        <li class="breadcrumb-item active" aria-current="page">
                            {{ __('messages.employee') }}  {{  trans_choice('messages.detail', 10) }} {{ __('messages.full') }}

                        </li>
                        <p id="user-id" style="display: none" name="{{$user_details['data']['id']}}"></p>
                    </ol>
                </nav>
                <h1 class="page-title mr-3" id="userId"
                    value="{{$user_details['data']['id']}}">{{$user_details['data']['full_name']}}</h1>
                <div class="col-md-4 float-right p-0">
                    <div class="form-control" id="dateRange" style="cursor: pointer">
                        <i class="fa fa-calendar"></i>&nbsp; <span></span>
                        <i class="fa fa-caret-down"></i>
                        <span id="from" hidden></span>
                        <span id="to" hidden></span>
                    </div>
                </div>
            </div>

            {{--            REMOVED SUB SECTIONS UI AND INCLUDING FROM ANOTHER FILE     --}}
            @include('User::EmployeeFullDetailsPage._middleSubSections')

        </div>
        <!-- Main Wrapper -->
    </div>

@endsection

@section('post-load-scripts')
    <script type="text/javascript" src="../assets/plugins/fancybox2/source/jquery.fancybox.pack.js?v=2.1.7"></script>
    <script type="text/javascript"
            src="../assets/plugins/fancybox/source/helpers/jquery.fancybox-buttons.js?v=1.0.5"></script>
    <script type="text/javascript"
            src="../assets/plugins/fancybox2/source/helpers/jquery.fancybox-media.js?v=1.0.6"></script>
    <script type="text/javascript"
            src="../assets/plugins/fancybox/source/helpers/jquery.fancybox-thumbs.js?v=1.0.7"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.43/moment-timezone-with-data.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://www.amcharts.com/lib/4/core.js"></script>
    <script src="https://www.amcharts.com/lib/4/charts.js"></script>
    <script src="//www.amcharts.com/lib/4/themes/animated.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9"></script>

@endsection

@section('page-scripts')

    <script>
        let apilimitmsg = '{{__('messages.apilimitmsg')}}';
        var lblProductive="{{ __('messages.productive') }}";
        var lblUnProductive="{{ __('messages.unproductive') }}";
        var lblNeutral="{{ __('messages.neutral') }}";
        let envAdminIds = [];
        let adminId = '<?php echo Session::get((new \App\Modules\User\helper)->getHostName())['token']['organization_id']; ?>';
        <?php
        foreach(explode(',', env('SPECIAL_ADMIN')) as $key => $val){ ?>
        envAdminIds.push(Number('<?php echo $val; ?>'));
            <?php } ?>
        let envRemainingTime = Number('{{env('REMAINING_SECONDS')}}');
        let envApiHost = '{{env('API_HOST_V3')}}';
        const MO_LIMIT = '<?php echo (new App\Modules\User\helper)->checkEnvPermission('ONE_DRIVE_SCREENSHOT_LIMIT'); ?>'; // Check ONE DRIVE SCREENSHOT Limit
        const ENV_EMPLOYEE = '<?php echo env('Employee') ?>';
        const MO_TITLE ="{!! __('messages.toTimeIfoMO') !!}";
        let SS_MAX_LIMIT = '{{env('EXTENDED_SCREEN_LIMIT', 10)}}';
        let fromDt = '{{__('messages.from')}}';
        let toDt = '{{__('messages.to')}}';
    </script>

    <script src="../assets/js/incJSFile/SuccessAndErrorHandlers/_swalHandlers.js"></script>
    <script src="../assets/js/incJSFile/CommonEmployeeDetailsCode.js"></script>
    <script src="../assets/js/incJSFile/EmployeeDetailJs/EmployeeLoginFullDetails.js"></script>
    <script src="../assets/js/final-timezone.js"></script>

    <script>let SiteIndicator = '{{env("APP_ENV")}}';   // to know whether dev or main site</script>
    <script src="https://cdn.jsdelivr.net/npm/vue@2.6.14/dist/vue.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script src="https://cdn.jsdelivr.net/npm/vue-apexcharts"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.13.6/underscore-min.js" integrity="sha512-2V49R8ndaagCOnwmj8QnbT1Gz/rie17UouD9Re5WxbzRVUGoftCu5IuqqtAM9+UC3fwfHCSJR1hkzNQh/2wdtg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
@endsection
