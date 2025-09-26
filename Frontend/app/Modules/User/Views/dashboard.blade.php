@extends('User::Layout._layout')
@section('title')
    <title>@if((new App\Modules\User\helper)->checkHost() )
            {{env('WEBSITE_TITLE')}} | @endif {{ __('messages.dashboard') }}</title>
@endsection
@section('extra-style-links')

    <style>

        .toolbar {
            text-align: center;
        }

        dl, ol, ul {
            margin-top: 0;
            margin-bottom: 0.5rem;
        }

        .suspended_user {
            line-height: 1.6;
            margin-bottom: -24px;
        }

        .suspended_user i {
            color: #fff9b2;
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
            margin-left: 450px;
            position: absolute;
        }
    </style>
@endsection
@section('content')
    <div class="page-inner no-page-title dashboard_main1">
        <div id="main-wrapper">
            <div class="content-header">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb breadcrumb-style-1">
                    </ol>
                </nav>
                <h1 class="page-title">{{ __('messages.dashboard') }}</h1>
            </div>
            @if(Session::has('message') && Session::get('message') !== [])
                <p class="alert-danger" style="text-align: center">{{ Session::get('message') }}</p>
            @endif
        </div>
        <div class="card">
            <div class="card-body">
            <div class="row px-3">
            <div class="col border border-primary p-2 box totalEnrollments" id="side-step1">
                <div class="ds-stat text-center">
                    @if(Session::has(env('Admin')) && (new App\Modules\User\helper)->getHostName() == env('Admin') )
                        <a href="#" class="ds-stat-name text-primary"
                           onclick="allUsers('{{__('messages.enrollments')}}');">
                            <h3 class="ds-stat-number text-primary"
                                id="registered-employees"></h3>
                                {{ __('messages.enrollments') }}
                            </a>
                    @else
                        <a href="#" class="ds-stat-name text-primary"
                           onclick="allUsers('{{__('messages.assignedEmployees')}}');">
                            <h3 class="ds-stat-number text-primary"
                                id="registered-employees"></h3>
                                {{ __('messages.assignedEmployees') }}
                            </a>
                    @endif
                </div>
            </div>
            <div class="col border border-success p-2 box currentlyActive" id="side-step2">
                <div class="ds-stat text-center">
                    <a href="#" class="ds-stat-name text-success" onclick="online();">
                        <h3 class="ds-stat-number"
                            id="online-employees"></h3>
                            {{ __('messages.active') }}
                        </a>
                </div>
            </div>
            <div class="col border border-warning p-2 box currentlyIdle" id="side-step3">
                <div class="ds-stat text-center">
                    {{--                                        <span class="ds-stat-name">Offline Employees</span>--}}
                    <a href="#" class="ds-stat-name text-warning" onclick="offline();">
                        <h3 class="ds-stat-number"
                            id="offline-employees"></h3>
                            {{ __('messages.idle') }}
                        </a>
                </div>
            </div>
            <div class="col border border-info p-2 box currentlyOffline" id="side-step4">
                <div class="ds-stat text-center">
                    {{--                                        <span class="ds-stat-name">Offline Employees</span>--}}
                    <a href="#" class="ds-stat-name text-info mb-0"
                       onclick="currentlyOffline();">
                        <h3 class="ds-stat-number"
                            id="currently-offline-employees"></h3>
                            {{ __('messages.currentOffline') }}
                        </a>
                </div>
            </div>
            <div class="col border border-danger p-2 box empAbsent" id="side-step5">
                <div class="ds-stat text-center">
                    <a href="#" class="ds-stat-name text-danger" onclick="absent();">
                        <h3 class="ds-stat-number" id="idle-employees"></h3>
                        {{ __('messages.absent') }}
                    </a>
                </div>
            </div>
            @if(Session::has(env('Admin')) && ((new App\Modules\User\helper)->getHostName() === env('Admin')) )
                <div class="col border border-secondary p-2 box empSuspended" id="side-step6">
                    <div class="ds-stat text-center">
                        <a href="#" class="ds-stat-name text-secondary"
                           onclick="suspend()">
                            <h3 class="ds-stat-number"
                                id="suspended-employees"></h3>
                                {{ __('messages.suspended') }}
                            </a>
                    </div>
                </div>
            @endif
        </div>
            </div>
        </div>


        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-sm-4">
                        <div class="card">
                            <div class="card-body pb-0">
                                <h4 class="text-center">{{ __('messages.snapshot') }}</h4>
                                <div style="height: 300px; width: 100%;" id="activityChart"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="card">
                            <div class="card-body">
                                    <h4 class="text-center">{{ __('messages.breakDown') }}</h4>
                                    <div class="table-responsive-sm">
                                        <table class="table table-borderless text-center mb-0 activity-break-down-table">
                                            <thead>
                                            <tr>
                                                <th class="lft_with_bg">{{ __('messages.activity') }}</th>
                                                <th>{{ __('messages.today') }}</th>
                                                <th>{{ __('messages.yesterday') }}</th>
                                                <th>{{ __('messages.week') }}</th>
                                            </tr>
                                            </thead>
                                            <tbody id="activity_breakdown_data">
                                            <tr>
                                                <td colspan="4" id="activityTableLoader"
                                                    style="text-align: center "><span
                                                        class="spinner-border text-primary"></span></td>
                                            </tr>
                                            <tr class="text-dark" id="Office_Time_Data"></tr>
                                            <tr class="text-primary" id="Active_Time_Data"></tr>
                                            <tr class="text-warning" id="Idle_Time_Data"></tr>
                                            <tr class="text-success" id="Productive_Time_Data"></tr>
                                            <tr class="text-danger" id="Non_Productive_Time_Data"></tr>
                                            <tr class="text-secondary" id="Neutral_Time_Data"></tr>
                                            </tbody>
                                        </table>
                                    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="neu-border py-1">{{ __('messages.topTen') }} {{ __('messages.topProductivity') }} </h4>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn_view btn-block neu-border" data-toggle="modal"
                                        data-target="#top10Web" onclick="filtrationWebAppData(1)"
                                        id="viewDetialButton1"><i class="fas fa-globe-asia mr-2"></i>{{ __('messages.ViewDetails') }}
                                </button>
                            </div>
                        </div>
                        <div class="btn-group btn-block btn-group-sm mb-3 btn_group1">
                            <button type="button" id="todayProEmp" class="btn btn_gray active"
                                    onclick="productiveUnproductiveEmployees('Today', 'productive_employees_data', 1, $('#productive_location').val(), $('#productive_department').val())">
                                {{ __('messages.today') }}
                            </button>
                            <button type="button" id="yesterdayProEmp" class="btn btn_gray"
                                    onclick="productiveUnproductiveEmployees('Yesterday', 'productive_employees_data', 1, $('#productive_location').val(), $('#productive_department').val())">
                                {{ __('messages.yesterday') }}
                            </button>
                            <button type="button" id="weekProEmp" class="btn btn_gray"
                                    onclick="productiveUnproductiveEmployees('Week', 'productive_employees_data', 1, $('#productive_location').val(), $('#productive_department').val())">
                                {{ __('messages.week') }}
                            </button>
                        @if((new App\Modules\User\helper)->checkEnvPermission('PRODUCTVE_UNPRODUCTIVE_EMPLOYEE_MONTH'))
                            <button type="button" id="monthProEmp" class="btn btn_gray"
                                    onclick="productiveUnproductiveEmployees('Month', 'productive_employees_data', 1, $('#productive_location').val(), $('#productive_department').val())">
                                {{ __('messages.thismonth') }}
                            </button>
                        @endif
                        </div>
                        <div class="inner_table">
                            <div class="row">
                            <div class="col-sm">
                                <div class="form-group">
                                    <select class="form-control" id="productive_location"
                                            onchange="getDepartmentsLoc($('#productive_location').val(), 'Productive')">
                                        <option selected
                                                value="0">{{ __('messages.allLocation') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="form-group">
                                    <select class="form-control" id="productive_department"
                                            onchange="productiveUnproductiveEmployees(PRODUCTIVE_EMPLOYEES_DURATION, 'productive_employees_data', 1, $('#productive_location').val(), $('#productive_department').val())">
                                    </select>
                                </div>
                            </div>
                            </div>
                            <table class="table table-striped table-sm mb-0">
                            <thead>
                            <tr class="bg-success">
                                <th>{{ __('messages.empName') }}</th>
                                <th>{{ __('messages.time') }} ({{ __('messages.hour') }})</th>
                            </tr>
                            </thead>
                            <tbody id="productive_employees_data">
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h4 class=" ">{{ __('messages.topTen') }} {{ __('messages.topNonProductivity') }}</h4>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn_view btn-block neu-border" data-toggle="modal"
                                        data-target="#top10Web" onclick="filtrationWebAppData(2)"
                                        id="viewDetialButton2"><i class="fas fa-globe-asia mr-2"></i>{{ __('messages.ViewDetails') }}
                                </button>
                            </div>
                        </div>
                        <div class="btn-group btn-block btn-group-sm mb-3 btn_group1">
                            <button type="button" id="todayUnProEmp" class="btn btn_gray active"
                                    onclick="productiveUnproductiveEmployees('Today', 'unproductive_employees_data', 2, $('#slacking_location').val(), $('#slacking_department').val())">
                                {{ __('messages.today') }}
                            </button>
                            <button type="button" id="yesterdayUnProEmp" class="btn btn_gray"
                                    onclick="productiveUnproductiveEmployees('Yesterday', 'unproductive_employees_data', 2, $('#slacking_location').val(), $('#slacking_department').val())">
                                {{ __('messages.yesterday') }}
                            </button>
                            <button type="button" id="weekUnProEmp" class="btn btn_gray"
                                    onclick="productiveUnproductiveEmployees('Week', 'unproductive_employees_data', 2, $('#slacking_location').val(), $('#slacking_department').val())">
                                {{ __('messages.week') }}
                            </button>
                        @if((new App\Modules\User\helper)->checkEnvPermission('PRODUCTVE_UNPRODUCTIVE_EMPLOYEE_MONTH'))
                            <button type="button" id="monthUnProEmp" class="btn btn_gray"
                                    onclick="productiveUnproductiveEmployees('Month', 'unproductive_employees_data', 2, $('#slacking_location').val(), $('#slacking_department').val())">
                                {{ __('messages.thismonth') }}
                            </button>
                        @endif
                        </div>
                        <div class="inner_table">
                            <div class="row">
                            <div class="col-sm">
                                <div class="form-group">
                                    <select class="form-control" id="slacking_location"
                                            onchange="getDepartmentsLoc($('#slacking_location').val(), 'Slacking')">
                                        <option selected
                                                value="0">{{ __('messages.allLocation') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-sm">
                                <div class="form-group">
                                    <select class="form-control" id="slacking_department"
                                            onchange="productiveUnproductiveEmployees(UNPRODUCTIVE_EMPLOYEES_DURATION, 'unproductive_employees_data', 2, $('#slacking_location').val(), $('#slacking_department').val())">
                                    </select>
                                </div>
                            </div>
                            </div>

                            <table class="table table-striped table-sm mb-0">
                            <thead>
                            <tr class="bg-success">
                                <th>{{ __('messages.employee') }} {{ __('messages.name') }}</th>
                                <th>{{ __('messages.time') }} ({{ __('messages.hour') }})</th>
                            </tr>
                            </thead>
                            <tbody id="unproductive_employees_data">
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h4 class="neu-border py-1">{{ __('messages.topTen') }} {{ __('messages.topActive') }} </h4>
                                </div>
                            </div>
                            <div class="btn-group btn-block btn-group-sm mb-3 btn_group1">
                                <button type="button" id="todayActiveEmp" class="btn btn_gray active"
                                        onclick="activeUnActiveEmployees('Today', 'active_employees_data', 1, $('#active_location').val(), $('#active_department').val())">
                                    {{ __('messages.today') }}
                                </button>
                                <button type="button" id="yesterdayActiveEmp" class="btn btn_gray"
                                        onclick="activeUnActiveEmployees('Yesterday', 'active_employees_data', 1, $('#active_location').val(), $('#active_department').val())">
                                    {{ __('messages.yesterday') }}
                                </button>
                                <button type="button" id="weekActiveEmp" class="btn btn_gray"
                                        onclick="activeUnActiveEmployees('Week', 'active_employees_data', 1, $('#active_location').val(), $('#active_department').val())">
                                    {{ __('messages.week') }}
                                </button>
                            </div>
                            <div class="inner_table">
                                <div class="row">
                                <div class="col-sm">
                                    <div class="form-group">
                                        <select class="form-control" id="active_location"
                                                onchange="getDepartmentsLoc($('#active_location').val(), 'active')">
                                            <option selected
                                                    value="0">{{ __('messages.allLocation') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="form-group">
                                        <select class="form-control" id="active_department"
                                                onchange="activeUnActiveEmployees(ACTIVE_EMPLOYEES_DURATION, 'active_employees_data', 1, $('#active_location').val(), $('#active_department').val())">
                                        </select>
                                    </div>
                                </div>
                                </div>

                                <table class="table table-striped table-sm mb-0">
                                <thead>
                                <tr class="bg-success">
                                    <th>{{ __('messages.empName') }}</th>
                                    <th>{{ __('messages.time') }} ({{ __('messages.hour') }})</th>
                                </tr>
                                </thead>
                                <tbody id="active_employees_data">
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h4 class=" ">{{ __('messages.topTen') }} {{ __('messages.topNonActive') }}</h4>
                                </div>
                            </div>
                            <div class="btn-group btn-block btn-group-sm mb-3 btn_group1">
                                <button type="button" id="todayUnActiveEmp" class="btn btn_gray active"
                                        onclick="activeUnActiveEmployees('Today', 'unactive_employees_data', 2, $('#unactive_location').val(), $('#unactive_department').val())">
                                    {{ __('messages.today') }}
                                </button>
                                <button type="button" id="yesterdayUnActiveEmp" class="btn btn_gray"
                                        onclick="activeUnActiveEmployees('Yesterday', 'unactive_employees_data', 2, $('#unactive_location').val(), $('#unactive_department').val())">
                                    {{ __('messages.yesterday') }}
                                </button>
                                <button type="button" id="weekUnActiveEmp" class="btn btn_gray"
                                        onclick="activeUnActiveEmployees('Week', 'unactive_employees_data', 2, $('#unactive_location').val(), $('#unactive_department').val())">
                                    {{ __('messages.week') }}
                                </button>
                            </div>
                            <div class="inner_table">
                                <div class="row">
                                <div class="col-sm">
                                    <div class="form-group">
                                        <select class="form-control" id="unactive_location"
                                                onchange="getDepartmentsLoc($('#unactive_location').val(), 'unactive')">
                                            <option selected
                                                    value="0">{{ __('messages.allLocation') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="form-group">
                                        <select class="form-control" id="unactive_department"
                                                onchange="activeUnActiveEmployees(UNACTIVE_EMPLOYEES_DURATION, 'unactive_employees_data', 2, $('#unactive_location').val(), $('#unactive_department').val())">
                                        </select>
                                    </div>
                                </div>
                                </div>

                                <table class="table table-striped table-sm mb-0">
                                <thead>
                                <tr class="bg-success">
                                    <th>{{ __('messages.employee') }} {{ __('messages.name') }}</th>
                                    <th>{{ __('messages.time') }} ({{ __('messages.hour') }})</th>
                                </tr>
                                </thead>
                                <tbody id="unactive_employees_data">
                                </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body" style="height: 380px">
                        <h4 class="">{{ __('messages.locPerform') }}</h4>
                        <div class="btn-group btn-block btn-group-sm mb-3 btn_group1">
                            <button type="button" id="todayPerformance" class="btn btn_gray active"
                                    onclick="performanceLocationAndDepartment('Today', 'location_option_data', 'location', $('#location_option').val())">
                                {{ __('messages.today') }}
                            </button>
                            <button type="button" id="yesterdayPerformance" class="btn btn_gray"
                                    onclick="performanceLocationAndDepartment('Yesterday', 'location_option_data', 'location', $('#location_option').val())">
                                {{ __('messages.yesterday') }}
                            </button>
                            <button type="button" id="weekPerformance" class="btn btn_gray"
                                    onclick="performanceLocationAndDepartment('Week', 'location_option_data', 'location', $('#location_option').val())">
                                {{ __('messages.week') }}
                            </button>
                        </div>
                        <div class="inner_table">
                            <div class="row">
                            <div class="col-sm">
                            </div>
                            <div class="col-sm">
                                <div class="form-group">
                                    <select class="form-control" id="location_option"
                                            onchange="performanceLocationAndDepartment(LOCATION_PERFORMANCE_DURATION, 'location_option_data', 'location', $('#location_option').val())">
                                        <option value="pro">{{ __('messages.productive') }}</option>
                                        <option value="non">{{ __('messages.unproductive') }}</option>
                                        <option value="neu">{{ __('messages.neutral') }}</option>
                                    </select>
                                </div>
                            </div>
                            </div>

                            <table class="table table-striped table-sm mb-0">
                            <thead>
                            <tr class="bg-success">
                                <th>{{ __('messages.Location') }}</th>
                                <th>{{ __('messages.time') }} ({{ __('messages.hour') }})</th>

                            </tr>
                            </thead>
                            <tbody id="location_option_data">
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body" style="height: 380px">
                        <h4 class="">{{ __('messages.deptPerform') }}</h4>
                        <div class="btn-group btn-block btn-group-sm mb-3 btn_group1">
                            <button type="button" id="todayDeptPerformance" class="btn btn_gray active"
                                    onclick="performanceLocationAndDepartment('Today', 'department_option_data', 'department', $('#department_option').val())">
                                {{ __('messages.today') }}
                            </button>
                            <button type="button" id="yesterdayDeptPerformance" class="btn btn_gray"
                                    onclick="performanceLocationAndDepartment('Yesterday', 'department_option_data', 'department', $('#department_option').val())">
                                {{ __('messages.yesterday') }}
                            </button>
                            <button type="button" id="weekDeptPerformance" class="btn btn_gray"
                                    onclick="performanceLocationAndDepartment('Week', 'department_option_data', 'department', $('#department_option').val())">
                                {{ __('messages.week') }}
                            </button>
                        </div>
                        <div class="inner_table">
                            <div class="row">
                            <div class="col-sm">
                            </div>
                            <div class="col-sm">
                                <div class="form-group">
                                    <select class="form-control" id="department_option"
                                            onchange="performanceLocationAndDepartment(DEPARTMENT_PERFORMANCE_DURATION, 'department_option_data', 'department', $('#department_option').val())">
                                        <option value="pro">{{ __('messages.productive') }}</option>
                                        <option value="non">{{ __('messages.unproductive') }}</option>
                                        <option value="neu">{{ __('messages.neutral') }}</option>
                                    </select>
                                </div>
                            </div>
                            </div>

                            <table class="table table-striped table-sm mb-0">
                            <thead>
                            <tr class="bg-success">
                                <th>{{ __('messages.department') }}</th>
                                <th>{{ __('messages.time') }} ({{ __('messages.hour') }})</th>

                            </tr>
                            </thead>
                            <tbody id="department_option_data">
                            </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-8">
                                <h4 class=" neu-border py-1">{{ __('messages.topTenWebUsage') }} </h4>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn_view btn-block neu-border" data-toggle="modal"
                                        data-target="#top10Web" onclick="filtrationWebAppData(3)"
                                        id="viewDetialButton3"><i class="fas fa-globe-asia mr-2"></i>{{ __('messages.ViewDetails') }}
                                </button>
                            </div>
                        </div>
                        <div class="btn-group btn-block btn-group-sm mb-3 btn_group1">
                            <button type="button" id="todayTopWebs" class="btn btn_gray active"
                                    onclick="getTopWebSitesAndApplications('Today', 'chartWebsite', 2);">{{ __('messages.today') }}
                            </button>
                            <button type="button" id="yesterdayTopWebs" class="btn btn_gray"
                                    onclick="getTopWebSitesAndApplications('Yesterday', 'chartWebsite', 2);">{{ __('messages.yesterday') }}
                            </button>
                            <button type="button" id="weekTopWebs" class="btn btn_gray"
                                    onclick="getTopWebSitesAndApplications('Week', 'chartWebsite', 2);">{{ __('messages.week') }}
                            </button>
                        </div>
                        <div style="height: 420px; width: 100%; overflow: hidden auto" id="chartWebsite"></div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">

                        <div class="row">
                            <div class="col-md-8">
                                <h4 class="neu-border py-1">{{ __('messages.topTenAppUsage') }}</h4>
                            </div>
                            <div class="col-md-4">
                                <button type="button" class="btn btn_view btn-block neu-border" data-toggle="modal"
                                        data-target="#top10Web" onclick="filtrationWebAppData(4)"
                                        id="viewDetialButton4"><i class="fas fa-globe-asia mr-2"></i>{{ __('messages.ViewDetails') }}
                                </button>
                            </div>
                        </div>
                        <div class="btn-group btn-block btn-group-sm mb-3 btn_group1">
                            <button type="button" id="todayTopApps" class="btn btn_gray active"
                                    onclick="getTopWebSitesAndApplications('Today', 'chartApplication', 1);">{{ __('messages.today') }}
                            </button>
                            <button type="button" id="yesterdayTopApps" class="btn btn_gray"
                                    onclick="getTopWebSitesAndApplications('Yesterday', 'chartApplication', 1);">
                                {{ __('messages.yesterday') }}
                            </button>
                            <button type="button" id="weekTopApps" class="btn btn_gray"
                                    onclick="getTopWebSitesAndApplications('Week', 'chartApplication', 1);">{{ __('messages.week') }}
                            </button>
                        </div>
                        <div style="height: 420px; width: 100%; overflow-y: auto;" id="chartApplication"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{--    four models used be here  ( 4 boxes )   --}}

    <div class="modal fade" id="commom_modal" data-easein="expandIn">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header d-block users-commom-modal-header-div" style="padding: 7px 10px">
                    <button type="button" class="close float-right" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title users-commom-modal-title text-center text-capitalize text-light font-weight-bold">{{__('messages.idles')}} {{  trans_choice('messages.users', 10) }}</h4>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <div class="table-wrap table-responsive">
                        <table id="common-table" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.Email id') }}</th>
                                <th>{{ __('messages.empCode') }}</th>
                                @if(((new App\Modules\User\helper)->specialAdminWithLessFeatures()) == Session::get((new App\Modules\User\helper)->getHostName())['token']['organization_id'])
                                    <th>{{ __('messages.departmentToSchool') }}</th>
                                    <th>{{ __('messages.locationToDistrict') }}</th>
                                @else
                                    <th>{{ __('messages.department') }}</th>
                                    <th>{{ __('messages.Location') }}</th>
                                @endif
                                <th>{{ __('messages.Status') }}</th>
                            </tr>
                            </thead>
                            <tbody id="common-table-body"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer" style="padding: 7px 10px">
                    <button type="button" class="btn btn-success text-white downloadExcelFunction"
                            onclick="exportEmployeeSheet('online', 1)">{{ __('messages.generateCsv') }}</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('messages.close')}}</button>
                </div>

            </div>
        </div>
    </div>

    {{--Online modal--}}
    <div class="modal fade" id="Online_count">
        <div class="modal-dialog modal-lg  modal-dialog-scrollable">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title activeUsersClass">{{ __('messages.active') }} {{  trans_choice('messages.users', 10) }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <div class="table-wrap table-responsive">
                        <table id="online_users_datatable" class=" table table-striped table-bordered Count-tab">
                            <thead>
                            <tr>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.Email id') }}</th>
                                <th>{{ __('messages.empCode') }}</th>
                                <th>{{ __('messages.department') }}</th>
                                <th>{{ __('messages.Location') }}</th>
                                <th>{{ __('messages.Status') }}</th>
                            </tr>
                            </thead>
                            <tbody id="appendOnlineTR">

                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-success text-white downloadExcelFunction"
                            onclick="exportEmployeeSheet('online', 1)">{{ __('messages.generateCsv') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('messages.close')}}</button>
                </div>
            </div>
        </div>
    </div>

    {{--offline modal--}}
    <div class="modal fade" id="Offline_count">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title offlineUsersClass">{{ __('messages.idles') }} {{  trans_choice('messages.users', 10) }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    {{--                    <h5 style="color:red;">--}}
                    {{--                        <span style="color:red">Offline:</span> <i>Currently these users are not connected to the--}}
                    {{--                            system.</i></h5>--}}
                    <div class="table-wrap table-responsive">
                        <table id="emp-ofline" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.Email id') }}</th>
                                <th>{{ __('messages.empCode') }}</th>
                                <th>{{ __('messages.department') }}</th>
                                <th>{{ __('messages.Location') }}</th>
                                <th>{{ __('messages.Status') }}</th>
                            </tr>
                            </thead>
                            <tbody id="offline_table">

                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-success text-white downloadExcelFunction"
                            onclick="exportEmployeeSheet('idle', 2)">{{ __('messages.generateCsv') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('messages.close')}}</button>
                </div>

            </div>
        </div>
    </div>

    {{--absent modal --}}
    <div class="modal fade" id="Absent_count">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title absentUsersClass">{{ __('messages.absent') }} {{  trans_choice('messages.users', 10) }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <div class="table-wrap table-responsive">
                        <table id="absent_users_datatable" class=" table table-striped table-bordered Count-tab">
                            <thead>
                            <tr>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.Email id') }}</th>
                                <th>{{ __('messages.empCode') }}</th>
                                <th>{{ __('messages.department') }}</th>
                                <th>{{ __('messages.Location') }}</th>
                                <th>{{ __('messages.Status') }}</th>
                            </tr>
                            </thead>
                            <tbody id="appendAbsentTR">

                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-success text-white downloadExcelFunction"
                            onclick="exportEmployeeSheet('absent', 3)">{{ __('messages.generateCsv') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('messages.close')}}</button>
                </div>

            </div>
        </div>
    </div>

    {{--suspended modal --}}
    <div class="modal fade" id="suspended_count">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title suspendUsersClass">{{ __('messages.suspended') }} {{  trans_choice('messages.users', 10) }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <div class="table-wrap table-responsive">
                        <table id="suspended_users_datatable" class=" table table-striped table-bordered Count-tab">
                            <thead>
                            <tr>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.Email id') }}</th>
                                <th>{{ __('messages.empCode') }}</th>
                                <th>{{ __('messages.department') }}</th>
                                <th>{{ __('messages.Location') }}</th>
                                <th>{{ __('messages.Status') }}</th>
                            </tr>
                            </thead>
                            <tbody id="appendSuspendedTR">

                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-success text-white downloadExcelFunction"
                            onclick="exportEmployeeSheet('suspend', 4)">{{ __('messages.generateCsv') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('messages.close')}}</button>
                </div>

            </div>
        </div>
    </div>

    {{--idle modal--}}
    <div class="modal fade" id="idle_count">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title idleUsersClass">{{__('messages.idles')}} {{  trans_choice('messages.users', 10) }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <table id="emp-idle" class="table table-striped table-bordered">
                        <thead>
                        <tr>

                            <th>{{ __('messages.name') }}</th>
                            <th>{{ __('messages.Email id') }}</th>
                            <th>{{ __('messages.empCode') }}</th>
                            <th>{{ __('messages.department') }}</th>
                            <th>{{ __('messages.Location') }}</th>
                            <th>{{ __('messages.Status') }}</th>
                        </tr>
                        </thead>
                        <tbody id="idle_table">

                        </tbody>
                    </table>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('messages.close')}}</button>
                </div>

            </div>
        </div>
    </div>


    {{--Currenly offline modal--}}
    <div class="modal fade" id="Currently_Offline_count">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title currentlyOfflineUsersClass">{{ __('messages.offline') }} {{  trans_choice('messages.users', 10) }}</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    {{--                    <h5 style="color:red;">--}}
                    {{--                        <span style="color:red">Offline:</span> <i>Currently these users are not connected to the--}}
                    {{--                            system.</i></h5>--}}
                    <div class="table-wrap table-responsive">
                        <table id="emp-currently-ofline" class="table table-striped table-bordered">
                            <thead>
                            <tr>
                                <th>{{ __('messages.name') }}</th>
                                <th>{{ __('messages.Email id') }}</th>
                                <th>{{ __('messages.empCode') }}</th>
                                <th>{{ __('messages.department') }}</th>
                                <th>{{ __('messages.Location') }}</th>
                                <th>{{ __('messages.Status') }}</th>
                            </tr>
                            </thead>
                            <tbody id="currently-offline_table" class>

                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="modal-footer">
{{--                    exportEmployeeSheet('currently-offline', 6)--}}
                    <button type="button" class="btn btn-success text-white downloadExcelFunction"
                            onclick="exportEmployeeSheet('currently-offline', 6)">{{ __('messages.generateCsv') }}
                    </button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('messages.close')}}</button>
                </div>

            </div>
        </div>
    </div>

    @if($_SERVER['HTTP_HOST'] == 'app.dev.empmonitor.com')
        <div class="modal fade" id="myfeedback">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                    <!-- Modal Header -->
                    <div class="modal-header">
                        <h4 class="modal-title">{{__('messages.feedback')}}</h4>
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                    </div>

                    <!-- Modal body -->
                    <div class="modal-body">
                        {{__('messages.QuestionFeedback')}}
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                        <a href="feedback">
                            <button type="button" class="btn btn-primary btn-sm">{{__('messages.yes')}}</button>
                        </a>
                        <button type="button" onclick="feedBackStatus(0);" class="btn btn-secondary btn-sm"
                                data-dismiss="modal">{{__('messages.notNow')}}</button>
                    </div>

                </div>
            </div>
        </div>
    @endif

    <!-- Important Note Modal, trigger After loading page-->
    <div class="modal fade" id="importantNoteModal" data-easein="tada" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog " role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger" style="padding: 10px">
                    <span>
                        <span class="fa fa-star checked" style="color: yellow"></span>
                        <span class="fa fa-star checked" style="color: yellow"></span>
                        <span class="fa fa-star checked" style="color: yellow"></span>
                        <span class="fa fa-star checked" style="color: yellow"></span>
                        <span class="fa fa-star checked" style="color: yellow"></span>
                    </span>
                    <h5 class="modal-title text-dark font-weight-bold pl-5 text-capitalize"
                        id="exampleModalLabel">{{__('messages.importantNoteTitle')}}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body bg-warning text-dark text-center noteBody" style="font-size: 18px">
                    {{--                    {{__('messages.importantNote', ['site' => 'ping.empmonitor.com'])}}--}}
                    {{__('messages.serviceDown')}}
                </div>
            </div>
        </div>
    </div>

    <!-- ========================= top web usage user list show ======================= -->

    <div class="modal fade" id="top10Web">
        <div class="modal-dialog modal-xl modal-dialog-scrollable modal-dialog-centered">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">{{ __('messages.TimeUsage') }}<br></h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-3" id="LocationDiv">
                                            <div class="form-group mb-0">
                                                <label style="font-weight: 700;color: #000000 !important;"
                                                       for="location_option"> {{ __('messages.Location') }} :-</label>
                                                @if($response['location']['code']===200)
                                                    <select class="form-control" id="LocationFilterData">
                                                        <option value="null"
                                                                id="null">{{ __('messages.allLocation') }}</option>
                                                        @foreach($response['location']['data']['data'] as $location)
                                                            <option value="{{$location['id']}}"
                                                                    id="{{$location['id']}}">{{$location['name']}}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <select class="form-control" id=""></select>
                                                    <p style="color: red">{{$response['location']['msg']}}</p>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-sm-3" id="DepartmentDiv">
                                            <div class="form-group mb-0">
                                                <label style="font-weight: 700;color: #000000 !important;"
                                                       for="location_option"> {{ __('messages.department') }} :-</label>
                                                @if(count($response['department'])  !== 0)
                                                    <select class="form-control" id="DepartmentAppend">
                                                        <option value="null"
                                                                id="null"> {{ __('messages.allDept') }}</option>
                                                        @foreach($response['department']  as $dept)
                                                            <option value="{{$dept['id']}}"
                                                                    id="{{$dept['id']}}">{{$dept['name']}}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <select class="form-control" id="">
                                                        <p style="color: red">{{__('messages.Nodata')}}</p>
                                                    </select>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-sm-3" id="EmployeeDiv">
                                            <div class="form-group mb-0">
                                                <label style="font-weight: 700;color: #000000 !important;"
                                                       class="font-weight-bold"
                                                       for="location_option"> {{ __('messages.employee') }} :-</label>
                                                @if($response['users']['code']  === 200)
                                                    <select class="form-control" id="EmployeeData">
                                                        <option value="null"
                                                                id="null"> {{ __('messages.allEmployee') }}</option>
                                                        @foreach($response['users']['data']['data']  as $emp)
                                                            <option value="{{$emp['id']}}"
                                                                    id="{{$emp['id']}}">{{$emp['first_name']}} {{$emp['last_name']}}</option>
                                                        @endforeach
                                                    </select>
                                                @else
                                                    <select class="form-control" id="">
                                                        <p style="color: red">{{$response['users']['msg']}}</p>
                                                    </select>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <label style="font-weight: 700;color: #000000 !important;"
                                                   class="font-weight-bold"
                                                   for="location_option"> {{ __('messages.website') }}
                                                / {{ __('messages.application') }} :- </label>
                                            <select class="form-control" id="websitesFiltration">
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                {{--                                <div class="row" >--}}
                                {{--                                    <div>--}}
                                {{--                                        <div class="form-group">--}}
                                {{--                                            <div class="col-md-12">--}}
                                {{--                                                <label style="font-weight: 700;color: #000000 !important;" class="font-weight-bold"--}}
                                {{--                                                       for="location_option"> Websites :- </label>--}}
                                {{--                                                <select class="form-control" id="websitesFiltration">--}}
                                {{--                                                </select>--}}
                                {{--                                            </div>--}}

                                {{--                                        </div>--}}
                                {{--                                    </div>--}}
                                {{--                                </div>--}}
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-body">
                            <div class="table-wrap table-responsive">
                                <table class="table table-bordered neu-table" id="URLbasedTable">
                                    <thead>
                                    <tr class="table-primary">
                                        <th>{{__('messages.name')}}</th>
                                        <th>{{__('messages.Location')}}</th>
                                        <th>{{__('messages.department')}}</th>
                                        <th id="WebsiteTableName">{{__('messages.websiteFull')}}</th>
                                        <th>{{__('messages.productive_time')}}</th>
                                        <th>{{__('messages.unproductive_time')}}</th>
                                        <th>{{__('messages.neutralTime')}}</th>
                                        <th>{{__('messages.Idletimes')}}</th>
                                    </tr>
                                    </thead>
                                    <tbody id="URL_data_tbody">
                                    </tbody>
                                </table>
                                <div id="LoaderIcon" class="loaderIcon mr-0" style="display:inline"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <input type="hidden" value="" id="OpenedModalValue">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">{{__('messages.close')}}</button>
                </div>
            </div>
        </div>
    </div>

    {{--    Excel download Table    --}}
    <table id="sampletable" class="uk-report-table table table-striped" hidden>
        <thead>
        <tr>
            <td colspan="4"> Current Date and Time : &nbsp;&emsp; <span class="currentDateAndTime"></span></td>
        </tr>
        <tr class="mainSection">
            <th style="font-weight: bold">{{ __('messages.name') }}</th>
            <th style="font-weight: bold">{{ __('messages.Email id') }}</th>
            <th style="font-weight: bold">{{ __('messages.empCode') }}</th>
            <th style="font-weight: bold">{{ __('messages.department') }}</th>
            <th style="font-weight: bold">{{ __('messages.Location') }}</th>
        </tr>
        </thead>
        <tbody id="excelExportTableBody">
        </tbody>
    </table>


@endsection
@section('post-load-scripts')

    <script src="https://cdn.amcharts.com/lib/4/core.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/charts.js"></script>
    <script src="https://cdn.amcharts.com/lib/4/themes/animated.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.datatables.net/2.3.4/js/dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.2.0/socket.io.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js" type="text/javascript"></script>

    {{--    Firebase   replace by Websocket - code is of separate files  --}}

    <!-- Add Firebase products that you want to use -->
    <!-- The core Firebase JS SDK is always required and must be listed first -->

    <script src="https://www.gstatic.com/firebasejs/7.19.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.19.1/firebase-messaging.js"></script>
    <script src="https://www.gstatic.com/firebasejs/7.19.1/firebase-analytics.js"></script>
    <link href="https://cdn.datatables.net/2.3.4/css/dataTables.dataTables.min.css" rel="stylesheet"/>
    <link href="../assets/plugins/css/bootstrap/loader.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.15/lodash.min.js"
            integrity="sha256-VeNaFBVDhoX3H+gJ37DpT/nTuZTdjYro9yBruHjVmoQ=" crossorigin="anonymous"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/velocity/1.2.2/velocity.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/velocity/1.2.2/velocity.ui.min.js"></script>
    <script src="../assets/js/incJSFile/_timeConvertions.js"></script>
    <script src="../assets/js/incJSFile/_dataFiltration.js"></script>
@endsection
@section('page-scripts')

    <script>
        let adminId = '<?php if(Session::has(env('Admin')))  echo Session::get("admin")['token']['organization_id']; ?>';
        let envAdminId = '{{env('SPECIAL_ADMIN')}}';
        let isAdmin = "<?php if (Session::has(env('Admin')) && (new App\Modules\User\helper)->getHostName() == env('Admin')) {
            echo true;
        } else {
            echo false;
        }?>";
        let DASHBOARD_LOCALIZE_INFO = JSON.parse('{{__('messages.dashboardInfor')}}'.replace(/&quot;/g, '"'));
        var ALL_DEPT_MSG = '{{__('messages.allDept')}}';
        let showPopUP = '<?php if (!empty($showPopUP)) {
            echo $showPopUP;
        }?>';
        let specialOrganizationCheck = '<?php if ((new App\Modules\User\helper)->checkOrganizationId()) echo 1; ?>';
        let drive_status = "<?php if(Session::has(env('Admin')) && (new App\Modules\User\helper)->getHostName() == env('Admin') && Session::get("admin")['token']['drive_status'] !== null) echo Session::get("admin")['token']['drive_status'] ?>";
    </script>

    <script type="text/javascript" src="{{env('JS_BASE_PATH')}}DashBoard.js"></script>
    <script type="text/javascript" src="{{env('JS_BASE_PATH')}}_cookieHandler.js"></script>

    <script>
        $(document).ready(() => {
            //      --------    Enable or disable the Notification modal --------
            // if (Number(localStorage.getItem('importantNote')) !== 1 && window.location.href.includes('admin/dashboard')) {
            //     $('#importantNoteModal').modal('show');
            //     localStorage.setItem('importantNote', '1');
            // }
            // checkCookie();
            if (is_admin && !tour_completed && localStorage.main_tour != 0) {
                $('#videoPlayer').attr('src','https://www.youtube.com/embed/r7teU6xVTEM');
                $('#showvideo').show();
                $('#skipTour').show();
                $('#skipTourButoon').show();
            }
            if (is_admin && !tour_completed && localStorage.main_tour == 0) {
                setTimeout(()=>{
                    $("body > div.introjs-tooltipReferenceLayer.introjs-fixedTooltip > div > div.introjs-tooltipbuttons").append('<a role="button" id="remindMeLater" onclick="remindMeLater()">Remind Me Later</a><a role="button" id="skipTourForMe" onclick="skipTourForMe()">Skip Tour</a>');
                    $("body > div.introjs-tooltipReferenceLayer.introjs-fixedTooltip > div > div.introjs-tooltipbuttons > a.introjs-button.introjs-nextbutton.introjs-donebutton").text('Start Tour');
                }, 800);
            }
            if (drive_status) {
                Swal.fire({
                    icon: 'error',
                    title: drive_status,
                    showConfirmButton: true,
                    confirmButtonText: DASHBOARD_JS.ok??'OK'
                });
            }
        });
        let NotificationCheck = 'get the value from session / cookie ';

        function checkCookie() {
            let notificationsCookieStatus = getCookie("isAlertRegistered");
        }

    </script>
    {{-- feedback--}}
    <script>
        if (Number(showPopUP) === 1 && isClient !== "1") $('#myfeedback').modal('show');

        function feedBackStatus(status) {
            $.ajax({
                url: "/" + userType + "/feedback",
                data: {
                    'status': 1
                },

                type: 'post',
                beforeSend: function () {

                },
                success: function (resp) {

                    $('#save').prop('disabled', false);
                    if (resp.code === 200) {
                        Swal.fire({
                            icon: 'success',
                            title: resp.message,
                            showConfirmButton: true,
                            confirmButtonText: DASHBOARD_JS.ok??'OK'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: resp.message,
                            showConfirmButton: true,
                            confirmButtonText: DASHBOARD_JS.ok??'OK'
                        });
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) {

                    Swal.fire({
                        icon: 'error',
                        title: 'Something went wrong',
                        showConfirmButton: true,
                        confirmButtonText: DASHBOARD_JS.ok??'OK'
                    });
                }
            });
        }

    </script>
    {{--    Hold the page source    --}}
    <script type="text/javascript">
        function disableCtrlKeyCombination(e) {
            let forbiddenKeys = new Array('a', 'c', 'x', 'v', 'u');
            let key, isCtrl;
            if (window.event) {
                key = window.event.keyCode;
                (window.event.ctrlKey) ? isCtrl = true : isCtrl = false;
            } else {
                key = e.which;
                (e.ctrlKey) ? isCtrl = true : isCtrl = false;
            }

            if (isCtrl) {
                for (let i = 0; i < forbiddenKeys.length; i++) {
                    if (forbiddenKeys[i].toLowerCase() === String.fromCharCode(key).toLowerCase()) {
                        return false;
                    }
                }
            }
            return true;
        }
    </script>

@endsection
